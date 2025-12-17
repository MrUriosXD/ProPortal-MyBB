<?php
/***************************************************************
 * ProPortal
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/
 
if (!defined("IN_PORTAL")) {
	die("<div style=\"border:1px solid #CC0000; padding:3px; margin:0; font-family:Tahoma; width:250px; font-size:12px;\"><strong>Error:</strong> This file cannot be viewed directly!</div>");
}

// get forums user cannot view
$unviewable = get_unviewable_forums();
if($unviewable)
{
	$unviewwhere = " AND fid NOT IN ($unviewable)";
}
// If user is known, welcome them
if($mybb->user['uid'] != 0)
{
	// Get number of new posts, threads, announcements
	$query = $db->simple_select("posts", "COUNT(pid) AS newposts", "dateline>'".$mybb->user['lastvisit']."' $unviewwhere");
	$newposts = $db->fetch_field($query, "newposts");
	if($newposts)
	{ // if there aren't any new posts, there is no point in wasting two more queries
		$query = $db->simple_select("threads", "COUNT(tid) AS newthreads", "dateline>'".$mybb->user['lastvisit']."' $unviewwhere");
		$newthreads = $db->fetch_field($query, "newthreads");
		$query = $db->simple_select("threads", "COUNT(tid) AS newann", "dateline>'".$mybb->user['lastvisit']."' AND fid IN (".$mybb->settings['portal_announcementsfid'].") $unviewwhere");
		$newann = $db->fetch_field($query, "newann");
		if(!$newthreads)
		{
			$newthreads = 0;
		}
		if(!$newann)
		{
			$newann = 0;
		}
	}
	else
	{
		$newposts = 0;
		$newthreads = 0;
		$newann = 0;
	}

	// Make the text
	if($newann == 1)
	{
		$lang->new_announcements = $lang->new_announcement;
	}
	else
	{
		$lang->new_announcements = $lang->sprintf($lang->new_announcements, $newann);
	}
	if($newthreads == 1)
	{
		$lang->new_threads = $lang->new_thread;
	}
	else
	{
		$lang->new_threads = $lang->sprintf($lang->new_threads, $newthreads);
	}
	if($newposts == 1)
	{
		$lang->new_posts = $lang->new_post;
	}
	else
	{
		$lang->new_posts = $lang->sprintf($lang->new_posts, $newposts);
	}
	
	if($mybb->user['avatar']){ $mybb->user['avatar'] = htmlspecialchars_uni($mybb->user['avatar']); }else{ $mybb->user['avatar'] = $mybb->settings['bburl']."/portal/images/user.png"; }
	$avatar_dimensions = explode("|", $mybb->user['avatardimensions']);
	
	if($avatar_dimensions[0] && $avatar_dimensions[1])
	{
		list($max_width, $max_height) = explode("x", my_strtolower("50x50"));
		if($avatar_dimensions[0] > $max_width || $avatar_dimensions[1] > $max_height)
		{
			require_once MYBB_ROOT."inc/functions_image.php";
			$scaled_dimensions = scale_image($avatar_dimensions[0], $avatar_dimensions[1], $max_width, $max_height);
			$ucp_avatar_width_height = "width=\"{$scaled_dimensions['width']}\" height=\"{$scaled_dimensions['height']}\"";
		}
		else
		{
			$ucp_avatar_width_height = "width=\"{$avatar_dimensions[0]}\" height=\"{$avatar_dimensions[1]}\"";	
		}
	}
	
	switch($db->type)
	{
		case "sqlite2":
		case "sqlite3":
		case "pgsql":
			$query = $db->simple_select("privatemessages", "COUNT(*) AS pms_total", "uid='".$mybb->user['uid']."'");
			$messages['pms_total'] = $db->fetch_field($query, "pms_total");
			
			$query = $db->simple_select("privatemessages", "COUNT(*) AS pms_unread", "uid='".$mybb->user['uid']."' AND CASE WHEN status = '0' AND folder = '0' THEN TRUE ELSE FALSE END");
			$messages['pms_unread'] = $db->fetch_field($query, "pms_unread");
			break;
		default:
			$query = $db->simple_select("privatemessages", "COUNT(*) AS pms_total, SUM(IF(status='0' AND folder='1','1','0')) AS pms_unread", "uid='".$mybb->user['uid']."'");
			$messages = $db->fetch_array($query);
	}
	
	// the SUM() thing returns "" instead of 0
	if($messages['pms_unread'] == "")
	{
		$messages['pms_unread'] = 0;
	}
	
	$lang->welcome = $lang->sprintf($lang->welcome, $mybb->user['username']);
	$welcometext = "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\" colspan=\"2\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong><a href=\"{$mybb->settings['bburl']}/usercp.php\">{$lang->usercp}</a></strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		<tr>
			<td class=\"trow1\" align=\"center\" valign=\"middle\" width=\"35%\"><img src=\"".$mybb->user['avatar']."\" alt=\"\" {$ucp_avatar_width_height} border=\"0\" /></td>
			<td class=\"trow2\" valign=\"top\"><span class=\"smalltext\">{$lang->welcome}<br />{$lang->member_welcome_lastvisit}<br /><em>{$lastvisit}</em></span>
		</td>
		</tr>
		<tr>
			<td class=\"trow1\" colspan=\"2\"><span class=\"smalltext\"><strong><a href=\"{$mybb->settings['bburl']}/private.php\">{$lang->private_messages}</a></strong></span><br /><span class=\"smalltext\"><strong>&raquo; </strong> {$messages['pms_unread']} {$lang->pms_unread}</span><br /><span class=\"smalltext\"><strong>&raquo; </strong> {$messages['pms_total']} {$lang->pms_total}</span></td>
		</tr>
		<tr>
			<td class=\"trow2\" colspan=\"2\"><span class=\"smalltext\"><strong>{$lang->since_last_activity}</strong></span><br /><span class=\"smalltext\"><strong>&raquo;</strong> {$lang->new_announcements}<br /><strong>&raquo;</strong> {$lang->new_threads}<br /><strong>&raquo;</strong> {$lang->new_posts}<br /><a href=\"{$mybb->settings['bburl']}/search.php?action=getnew\">{$lang->view_new}</a><br /><a href=\"{$mybb->settings['bburl']}/search.php?action=getdaily\">{$lang->view_todays}</a>
	</span></td>
		</tr>
		</tbody>
	</table>";

}
else
{
	$lang->guest_welcome_registration = $lang->sprintf($lang->guest_welcome_registration, $mybb->settings['bburl'] . '/member.php?action=register');
	$mybb->user['username'] = $lang->guest;
	$lang->welcome = $lang->sprintf($lang->welcome, $mybb->user['username']);
	$welcometext = "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong>{$lang->welcome}</strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		<tr>
			<td class=\"trow1\"><span class=\"smalltext\">{$lang->guest_welcome_registration}</span><br />
	<br />
	<form method=\"post\" action=\"{$portal_url}\"><input type=\"hidden\" name=\"action\" value=\"do_login\" />
	{$lang->username}<br />&nbsp;&nbsp;<input type=\"text\" class=\"textbox\" name=\"username\" value=\"\" /><br /><br />
	{$lang->password}<br />&nbsp;&nbsp;<input type=\"password\" class=\"textbox\" name=\"password\" value=\"\" /><br />
	<br /><input type=\"submit\" class=\"button\" name=\"loginsubmit\" value=\"{$lang->login}\" /></form></td>
		</tr>
		</tbody>
	</table>";
}
echo $welcometext;
?>