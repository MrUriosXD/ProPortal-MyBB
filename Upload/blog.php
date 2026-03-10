<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/

define("IN_MYBB", 1);
define("IN_BLOG", 1);
define('THIS_SCRIPT', 'blog.php');

$leftblocks = $centerblocks = $rightblocks = "";

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

$templatelist = "pro_blog,pro_blog_left,pro_blog_page,pro_blog_right,pro_blog_block,pro_blog_announcement,calendar_mini,calendar_mini_weekrow,calendar_mini_weekrow_day,calendar_mini_weekdayheader, multipage, multipage_nextpage, multipage_page, multipage_page_current";

require_once $change_dir."/global.php";
require_once MYBB_ROOT."inc/functions_post.php";
require_once MYBB_ROOT."inc/functions_user.php";
require_once MYBB_ROOT."inc/class_parser.php";
$parser = new postParser;
require_once MYBB_ROOT."blog/inc/blog.class.php";
$problog = new ProBlog;

// Load blog language phrases
$lang->load("pro_blog");

// Check is blog installed
if(!$db->table_exists("blog_blocks") || !$db->table_exists("blog_pages") || !$db->table_exists("blog_settings"))
{
	error($lang->is_not_installed);
}

// Fetch the current URL
$blog_url = get_current_location();

add_breadcrumb($lang->nav_blog, "blog.php");

$plugins->run_hooks("pro_blog_start");

// Construct blog settings
$options = array(
	"order_by" => "id",
	"order_dir" => "ASC"
);
$query = $db->simple_select("blog_settings", "*", "", $options);
while($setting = $db->fetch_array($query))
{
	$setting['value'] = str_replace("\"", "\\\"", $setting['value']);
	$settings[$setting['name']] = $setting['value'];
}

$problog->settings = &$settings;

// This allows users to login if the blog is stored offsite or in a different directory
if(isset($mybb->input['action']) && $mybb->input['action'] == "do_login" && $mybb->request_method == "post")
{
	$plugins->run_hooks("blog_do_login_start");

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

	$plugins->run_hooks("blog_do_login_end");

	redirect("blog.php", $lang->redirect_loggedin);
}

// Define blog-related seo urls
if($mybb->settings['seourls'] == "yes" || ($mybb->settings['seourls'] == "auto" && !empty($_SERVER['SEO_SUPPORT']) == 1))
{
	define('PAGE_URL', "page-{page}.html");
}
else
{
	define('PAGE_URL', "blog.php?pages={page}");
}

// Choosing blog template by column setting
if($problog->settings['blogcolumns'] == "left"){ $blogtemplate = "pro_blog_left"; }
elseif($problog->settings['blogcolumns'] == "right"){ $blogtemplate = "pro_blog_right"; }
else{ $blogtemplate = "pro_blog"; }

if($problog->settings['blogcolumns'] == "left" || $problog->settings['blogcolumns'] == "both")
{
	// Getting left blocks
	if ($fetch_blocks = $problog->get_list("SELECT * FROM ".TABLE_PREFIX."blog_blocks WHERE zone='0' AND enabled='1' AND visible REGEXP '\\\b".$mybb->user['usergroup']."\\\b' ORDER BY position")) {
		foreach ($fetch_blocks as $result_blocks) {
			$title = $result_blocks['title'];
			$file = $result_blocks['file'];
			$content = $result_blocks['content'];

			// Collapse block thing
			$expdisplay = '';
			$collapsed_name = "block_{$result_blocks['id']}_c";
			if(isset($collapsed[$collapsed_name]) && $collapsed[$collapsed_name] == "display: show;")
			{
				$expcolimage = "collapse_collapsed.png";
				$expdisplay = "display: none;";
				$expaltext = "[+]";
			}
			else
			{
				$expcolimage = "collapse.png";
				$expaltext = "[-]";
			}

			if($file != "0"){
				if (file_exists(MYBB_ROOT."blog/blocks/block_$file.php")) {
					ob_start();
					include_once(MYBB_ROOT."blog/blocks/block_$file.php");
					$content .= ob_get_contents();
					ob_end_clean();
				} else {
					$content = $lang->block_file_missing;
				}
			} else {
				$content = $result_blocks['content'];
			}

			if($result_blocks['custom'] == "0"){
				eval("\$leftblocks .= \"".$templates->get("pro_blog_block")."\";");
			} else {
				$leftblocks .= "<div style=\"padding-bottom:".$problog->settings['horizontalspace']."px;\">".$content."</div>";
			}
		}
	} else {
		$title = $lang->left_block_none;
		$content = $lang->left_block_none_content;
		eval("\$leftblocks = \"".$templates->get("pro_blog_block")."\";");
	}
}

if($problog->settings['blogcolumns'] == "right" || $problog->settings['blogcolumns'] == "both")
{
	// Getting right blocks
	if ($fetch_blocks = $problog->get_list("SELECT * FROM ".TABLE_PREFIX."blog_blocks WHERE zone='2' AND enabled='1' AND visible REGEXP '\\\b".$mybb->user['usergroup']."\\\b' ORDER BY position")) {
		foreach ($fetch_blocks as $result_blocks) {
			$title = $result_blocks['title'];
			$file = $result_blocks['file'];
			$content = $result_blocks['content'];

			// Collapse block thing
			$expdisplay = '';
			$collapsed_name = "block_{$result_blocks['id']}_c";
			if(isset($collapsed[$collapsed_name]) && $collapsed[$collapsed_name] == "display: show;")
			{
				$expcolimage = "collapse_collapsed.png";
				$expdisplay = "display: none;";
				$expaltext = "[+]";
			}
			else
			{
				$expcolimage = "collapse.png";
				$expaltext = "[-]";
			}

			if($file != "0"){
				if (file_exists(MYBB_ROOT."blog/blocks/block_$file.php")) {
					ob_start();
					include_once(MYBB_ROOT."blog/blocks/block_$file.php");
					$content .= ob_get_contents();
					ob_end_clean();
				} else {
					$content = $lang->block_file_missing;
				}
			} else {
				$content = $result_blocks['content'];
			}

			if($result_blocks['custom'] == "0"){
				eval("\$rightblocks .= \"".$templates->get("pro_blog_block")."\";");
			} else {
				$rightblocks .= "<div style=\"padding-bottom:".$problog->settings['horizontalspace']."px;\">".$content."</div>";
			}
		}
	} else {
		$title = $lang->right_block_none;
		$content = $lang->right_block_none_content;
		eval("\$rightblocks = \"".$templates->get("pro_blog_block")."\";");
	}
}

// Getting center module
$pages = $mybb->input['pages'] ?? '';
if($mybb->input['action'] == "view" && (int)$mybb->input['id'] > 0)
{
	$id = (int)$mybb->input['id'];
	$query = $db->query("
		SELECT p.*, u.username, u.avatar, u.avatardimensions
		FROM ".TABLE_PREFIX."blog_posts p
		LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = p.uid)
		WHERE p.pid='{$id}' AND p.enabled='1'
	");
	$announcement = $db->fetch_array($query);
	if($announcement)
	{
		// Update views
		$db->update_query("blog_posts", array("views" => $announcement['views'] + 1), "pid='{$id}'");

		$announcement['threadlink'] = "blog.php?action=view&id=".$announcement['pid'];
		if($announcement['uid'] == 0)
		{
			$profilelink = $lang->guest;
		}
		else
		{
			$profilelink = build_profile_link($announcement['username'], $announcement['uid']);
		}
		$announcement['subject'] = htmlspecialchars_uni($parser->parse_badwords($announcement['title']));

		// Avatar logic
		if($announcement['avatar'] != '')
		{
			$avatar_dimensions = explode("|", $announcement['avatardimensions']);
			if($avatar_dimensions[0] && $avatar_dimensions[1])
			{
				list($max_width, $max_height) = explode("x", my_strtolower("35x35"));
				if($avatar_dimensions[0] > $max_width || $avatar_dimensions[1] > $max_height)
				{
					require_once MYBB_ROOT."inc/functions_image.php";
					$scaled_dimensions = scale_image($avatar_dimensions[0], $avatar_dimensions[1], $max_width, $max_height);
					$ann_avatar_width_height = "width=\"{$scaled_dimensions['width']}\" height=\"{$scaled_dimensions['height']}\"";
				}
				else
				{
					$ann_avatar_width_height = "width=\"{$avatar_dimensions[0]}\" height=\"{$avatar_dimensions[1]}\"";
				}
			}
			if (!stristr($announcement['avatar'], 'http://') && !stristr($announcement['avatar'], 'https://'))
			{
				$announcement['avatar'] = $mybb->settings['bburl'] . '/' . $announcement['avatar'];
			}
			$avatar = "<img src=\"".htmlspecialchars_uni($announcement['avatar'])."\" alt=\"\" {$ann_avatar_width_height} />";
		}
		else
		{
			$avatar = '';
		}
		$anndate = my_date($mybb->settings['dateformat'], $announcement['dateline']);
		$anntime = my_date($mybb->settings['timeformat'], $announcement['dateline']);
		$numcomments = "- {$announcement['comments_count']} {$lang->replies}";
		$views = "<strong>{$announcement['views']}</strong> {$lang->latest_threads_views}";

		$parser_options = array("allow_html" => 0, "allow_mycode" => 1, "allow_smilies" => 1, "allow_imgcode" => 1, "filter_badwords" => 1);
		$message = $parser->parse_message($announcement['content'], $parser_options);
		$icon = "&nbsp;";

		add_breadcrumb($announcement['subject']);
		eval("\$centerblocks = \"".$templates->get("pro_blog_announcement")."\";");

		eval("\$blog = \"".$templates->get($blogtemplate)."\";");
		$blog = $problog->page_title($blog, $announcement['subject']);
		output_page($blog);
	}
	else
	{
		redirect("blog.php");
	}
}
elseif ($pages !== '')
{
	$pages = $db->escape_string($mybb->input['pages']);
	$query = $db->simple_select("blog_pages", "*", "name='{$pages}'");
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
		add_breadcrumb($title, $problog->page_url($pages));
		eval("\$centerblocks .= \"".$templates->get("pro_blog_page")."\";");

		eval("\$blog = \"".$templates->get($blogtemplate)."\";");
		$blog = $problog->page_title($blog, $title); // Change page title

		$plugins->run_hooks("pro_blog_end");
		output_page($blog);
	} else {
		redirect("blog.php");
	}
}
else
{
	// Getting center blocks
	if ($fetch_blocks = $problog->get_list("SELECT * FROM ".TABLE_PREFIX."blog_blocks WHERE zone='1' AND enabled='1' AND visible REGEXP '\\\b".$mybb->user['usergroup']."\\\b' ORDER BY position")) {
		foreach ($fetch_blocks as $result_blocks) {
			$title = $result_blocks['title'];
			$file = $result_blocks['file'];
			$content = $result_blocks['content'];

			// Collapse block thing
			$expdisplay = '';
			$collapsed_name = "block_{$result_blocks['id']}_c";
			if(isset($collapsed[$collapsed_name]) && $collapsed[$collapsed_name] == "display: show;")
			{
				$expcolimage = "collapse_collapsed.png";
				$expdisplay = "display: none;";
				$expaltext = "[+]";
			}
			else
			{
				$expcolimage = "collapse.png";
				$expaltext = "[-]";
			}

			if($file != "0"){
				if (file_exists(MYBB_ROOT."blog/blocks/block_$file.php")) {
					ob_start();
					include_once(MYBB_ROOT."blog/blocks/block_$file.php");
					$content .= ob_get_contents();
					ob_end_clean();
				} else {
					$content = $lang->block_file_missing;
				}
			} else {
				$content = $result_blocks['content'];
			}

			if($result_blocks['custom'] == "0"){
				eval("\$centerblocks .= \"".$templates->get("pro_blog_block")."\";");
			} else {
				$centerblocks .= "<div style=\"padding-bottom:".$problog->settings['horizontalspace']."px;\">".$content."</div>";
			}
		}
	} else {
		$title = $lang->center_block_none;
		$content = $lang->center_block_none_content;
		eval("\$centerblocks .= \"".$templates->get("pro_blog_block")."\";");
	}

	eval("\$blog = \"".$templates->get($blogtemplate)."\";");

	$plugins->run_hooks("pro_blog_end");

	output_page($blog);
}
?>