<?php
/***************************************************************
 * ProPortal
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/

define("IN_MYBB", 1);
define("IN_PORTAL", 1);
define('THIS_SCRIPT', 'portal.php');

// set the path to your forums directory here (without trailing slash)
$forumdir = "./";

// end editing

$change_dir = "./";

if(!@chdir($forumdir) && !empty($forumdir))
{
	if(@is_dir($forumdir))
	{
		$change_dir = $forumdir;
	}
	else
	{
		die("\$forumdir is invalid!");
	}
}

$templatelist = "pro_portal,pro_portal_left,pro_portal_page,pro_portal_right,pro_portal_block,pro_portal_announcement,calendar_mini,calendar_mini_weekrow,calendar_mini_weekrow_day,calendar_mini_weekdayheader, multipage, multipage_nextpage, multipage_page, multipage_page_current";

require_once $change_dir."/global.php";
require_once MYBB_ROOT."inc/functions_post.php";
require_once MYBB_ROOT."inc/functions_user.php";
require_once MYBB_ROOT."inc/class_parser.php";
$parser = new postParser;
require_once MYBB_ROOT."portal/inc/portal.class.php";
$proportal = new ProPortal;

// Load portal language phrases
$lang->load("pro_portal");

// Check is portal installed
if(!$db->table_exists("portal_blocks") || !$db->table_exists("portal_pages") || !$db->table_exists("portal_settings"))
{
	error($lang->is_not_installed);
}

// Fetch the current URL
$portal_url = get_current_location();

add_breadcrumb($lang->nav_portal, "portal.php");

$plugins->run_hooks("pro_portal_start");

// Construct portal settings
$options = array(
	"order_by" => "id",
	"order_dir" => "ASC"
);
$query = $db->simple_select("portal_settings", "*", "", $options);
while($setting = $db->fetch_array($query))
{
	$setting['value'] = str_replace("\"", "\\\"", $setting['value']);
	$settings[$setting['name']] = $setting['value'];
}

$proportal->settings = &$settings;

// This allows users to login if the portal is stored offsite or in a different directory
if($mybb->input['action'] == "do_login" && $mybb->request_method == "post")
{
	$plugins->run_hooks("portal_do_login_start");

	// Checks to make sure the user can login; they haven't had too many tries at logging in.
	// Is a fatal call if user has had too many tries
	$logins = login_attempt_check();
	$login_text = '';

	if(!username_exists($mybb->input['username']))
	{
		error($lang->error_invalidpworusername.$login_text);
	}
	$user = validate_password_from_username($mybb->input['username'], $mybb->input['password']);
	if(!$user['uid'])
	{
		my_setcookie('loginattempts', $logins + 1);
		$db->write_query("UPDATE ".TABLE_PREFIX."users SET loginattempts=loginattempts+1 WHERE username = '".$db->escape_string($mybb->input['username'])."'");
		if($mybb->settings['failedlogintext'] == 1)
		{
			$login_text = $lang->sprintf($lang->failed_login_again, $mybb->settings['failedlogincount'] - $logins);
		}
		error($lang->error_invalidpassword.$login_text);
	}

	my_setcookie('loginattempts', 1);
	$db->delete_query("sessions", "ip='".$db->escape_string($session->ipaddress)."' AND sid != '".$session->sid."'");
	$newsession = array(
		"uid" => $user['uid'],
	);
	$db->update_query("sessions", $newsession, "sid='".$session->sid."'");
	
	$db->update_query("users", array("loginattempts" => 1), "uid='{$mybb->user['uid']}'");

	// Temporarily set the cookie remember option for the login cookies
	$mybb->user['remember'] = $user['remember'];

	my_setcookie("mybbuser", $user['uid']."_".$user['loginkey'], null, true);
	my_setcookie("sid", $session->sid, -1, true);

	if(function_exists("loggedIn"))
	{
		loggedIn($user['uid']);
	}

	$plugins->run_hooks("portal_do_login_end");

	redirect("portal.php", $lang->redirect_loggedin);
}

// Define portal-related seo urls
if($mybb->settings['seourls'] == "yes" || ($mybb->settings['seourls'] == "auto" && $_SERVER['SEO_SUPPORT'] == 1))
{
	define('PAGE_URL', "page-{page}.html");
}
else
{
	define('PAGE_URL', "portal.php?pages={page}");
}

// Choosing portal template by column setting
if($proportal->settings['portalcolumns'] == "left"){ $portaltemplate = "pro_portal_left"; }
elseif($proportal->settings['portalcolumns'] == "right"){ $portaltemplate = "pro_portal_right"; }
else{ $portaltemplate = "pro_portal"; }

if($proportal->settings['portalcolumns'] == "left" || $proportal->settings['portalcolumns'] == "both")
{
	// Getting left blocks
	if ($fetch_blocks = $proportal->get_list("SELECT * FROM ".TABLE_PREFIX."portal_blocks WHERE zone='0' AND enabled='1' AND visible REGEXP '[[:<:]]".$mybb->user['usergroup']."[[:>:]]' ORDER BY position")) {
		foreach ($fetch_blocks as $result_blocks) {
			$title = $result_blocks['title'];
			$file = $result_blocks['file'];
			$content = $result_blocks['content'];
			
			// Collapse block thing
			$expdisplay = '';
			$collapsed_name = "block_{$result_blocks['id']}_c";
			if(isset($collapsed[$collapsed_name]) && $collapsed[$collapsed_name] == "display: show;")
			{
				$expcolimage = "collapse_collapsed.gif";
				$expdisplay = "display: none;";
				$expaltext = "[+]";
			}
			else
			{
				$expcolimage = "collapse.gif";
				$expaltext = "[-]";
			}
			
			if($file != "0"){
				if (file_exists(MYBB_ROOT."portal/blocks/block_$file.php")) {
					ob_start();
					include_once(MYBB_ROOT."portal/blocks/block_$file.php");
					$content .= ob_get_contents();
					ob_end_clean();
				} else {
					$content = $lang->block_file_missing;
				}
			} else {
				$content = $result_blocks['content'];
			}
			
			if($result_blocks['custom'] == "0"){
				eval("\$leftblocks .= \"".$templates->get("pro_portal_block")."\";");
			} else {
				$leftblocks .= "<div style=\"padding-bottom:".$proportal->settings['horizontalspace']."px;\">".$content."</div>";
			}
		}
	} else {
		$title = $lang->left_block_none;
		$content = $lang->left_block_none_content;
		eval("\$leftblocks = \"".$templates->get("pro_portal_block")."\";");
	}
}

if($proportal->settings['portalcolumns'] == "right" || $proportal->settings['portalcolumns'] == "both")
{
	// Getting right blocks
	if ($fetch_blocks = $proportal->get_list("SELECT * FROM ".TABLE_PREFIX."portal_blocks WHERE zone='2' AND enabled='1' AND visible REGEXP '[[:<:]]".$mybb->user['usergroup']."[[:>:]]' ORDER BY position")) {
		foreach ($fetch_blocks as $result_blocks) {
			$title = $result_blocks['title'];
			$file = $result_blocks['file'];
			$content = $result_blocks['content'];
			
			// Collapse block thing
			$expdisplay = '';
			$collapsed_name = "block_{$result_blocks['id']}_c";
			if(isset($collapsed[$collapsed_name]) && $collapsed[$collapsed_name] == "display: show;")
			{
				$expcolimage = "collapse_collapsed.gif";
				$expdisplay = "display: none;";
				$expaltext = "[+]";
			}
			else
			{
				$expcolimage = "collapse.gif";
				$expaltext = "[-]";
			}
			
			if($file != "0"){
				if (file_exists(MYBB_ROOT."portal/blocks/block_$file.php")) {
					ob_start();
					include_once(MYBB_ROOT."portal/blocks/block_$file.php");
					$content .= ob_get_contents();
					ob_end_clean();
				} else {
					$content = $lang->block_file_missing;
				}
			} else {
				$content = $result_blocks['content'];
			}
			
			if($result_blocks['custom'] == "0"){
				eval("\$rightblocks .= \"".$templates->get("pro_portal_block")."\";");
			} else {
				$rightblocks .= "<div style=\"padding-bottom:".$proportal->settings['horizontalspace']."px;\">".$content."</div>";
			}
		}
	} else {
		$title = $lang->right_block_none;
		$content = $lang->right_block_none_content;
		eval("\$rightblocks = \"".$templates->get("pro_portal_block")."\";");
	}
}

// Getting center module
if($mybb->input['pages'])
{
	$pages = $db->escape_string($mybb->input['pages']);
	$query = $db->simple_select("portal_pages", "*", "name='{$pages}'");
	$page_data = $db->fetch_array($query);
	if($page_data && $page_data['enabled'] == "1")
	{
		$visible = explode(",", $page_data['visible']);
		if(!in_array($mybb->user['usergroup'], $visible))
		{
			error_no_permission();
		}
		$title = $page_data['title'];
		$content = $page_data['content'];
		add_breadcrumb($title, $proportal->page_url($pages));
		eval("\$centerblocks .= \"".$templates->get("pro_portal_page")."\";");
		
		eval("\$portal = \"".$templates->get($portaltemplate)."\";");
		$portal = $proportal->page_title($portal, $title); // Change page title
		
		$plugins->run_hooks("pro_portal_end");
		output_page($portal);
	} else {
		redirect("portal.php");
	}
}
else
{	
	// Getting center blocks
	if ($fetch_blocks = $proportal->get_list("SELECT * FROM ".TABLE_PREFIX."portal_blocks WHERE zone='1' AND enabled='1' AND visible REGEXP '[[:<:]]".$mybb->user['usergroup']."[[:>:]]' ORDER BY position")) {
		foreach ($fetch_blocks as $result_blocks) {
			$title = $result_blocks['title'];
			$file = $result_blocks['file'];
			$content = $result_blocks['content'];
			
			// Collapse block thing
			$expdisplay = '';
			$collapsed_name = "block_{$result_blocks['id']}_c";
			if(isset($collapsed[$collapsed_name]) && $collapsed[$collapsed_name] == "display: show;")
			{
				$expcolimage = "collapse_collapsed.gif";
				$expdisplay = "display: none;";
				$expaltext = "[+]";
			}
			else
			{
				$expcolimage = "collapse.gif";
				$expaltext = "[-]";
			}
			
			if($file != "0"){
				if (file_exists(MYBB_ROOT."portal/blocks/block_$file.php")) {
					ob_start();
					include_once(MYBB_ROOT."portal/blocks/block_$file.php");
					$content .= ob_get_contents();
					ob_end_clean();
				} else {
					$content = $lang->block_file_missing;
				}
			} else {
				$content = $result_blocks['content'];
			}
			
			if($result_blocks['custom'] == "0"){
				eval("\$centerblocks .= \"".$templates->get("pro_portal_block")."\";");
			} else {
				$centerblocks .= "<div style=\"padding-bottom:".$proportal->settings['horizontalspace']."px;\">".$content."</div>";
			}
		}
	} else {
		$title = $lang->center_block_none;
		$content = $lang->center_block_none_content;
		eval("\$centerblocks .= \"".$templates->get("pro_portal_block")."\";");
	}
	
	eval("\$portal = \"".$templates->get($portaltemplate)."\";");
	
	$plugins->run_hooks("pro_portal_end");
	
	output_page($portal);
}
?>