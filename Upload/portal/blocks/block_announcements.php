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
 
// Get latest news announcements
// First validate announcement fids:
$announcementsfids = explode(',', $proportal->settings['announcementsfid']);
if(is_array($announcementsfids))
{
	foreach($announcementsfids as $fid)
	{
		$fid_array[] = intval($fid);
	}
	$announcementsfids = implode(',', $fid_array);
}
// And get them!
$query = $db->simple_select("forums", "*", "fid IN (".$announcementsfids.")");
while($forumrow = $db->fetch_array($query))
{
    $forum[$forumrow['fid']] = $forumrow;
}

// Let's add pagination
$annnum = $db->fetch_field($db->simple_select('threads', 'COUNT(*) AS annnum', "fid IN (".$proportal->settings['announcementsfid'].") AND visible='1' AND closed NOT LIKE 'moved|%'"), 'annnum');
$pagenum = (int)($mybb->input['page'] ?? 1);
$totalpage = (int)ceil($annnum / (int)$proportal->settings['numannouncements']);
if($pagenum < 1 || !$pagenum || $pagenum > $totalpage){ $pagenum = 1; }
$multipage = multipage($annnum, $proportal->settings['numannouncements'], $pagenum, $mybb->settings['bburl'].'/portal.php');

$pids = '';
$tids = '';
$comma = '';
$query = $db->query("
	SELECT p.pid, p.message, p.tid
	FROM ".TABLE_PREFIX."posts p
	LEFT JOIN ".TABLE_PREFIX."threads t ON (t.tid=p.tid)
	WHERE t.fid IN (".$announcementsfids.") AND t.visible='1' AND t.closed NOT LIKE 'moved|%' AND t.firstpost=p.pid
	ORDER BY t.dateline DESC 
	LIMIT ".(($pagenum-1)*$proportal->settings['numannouncements']).", ".$proportal->settings['numannouncements']
);
while($getid = $db->fetch_array($query))
{
	$pids .= ",'{$getid['pid']}'";
	$tids .= ",'{$getid['tid']}'";
	$posts[$getid['tid']] = $getid;
}
$pids = "pid IN(0{$pids})";
// Now lets fetch all of the attachments for these posts
$query = $db->simple_select("attachments", "*", $pids);
while($attachment = $db->fetch_array($query))
{
	$attachcache[$attachment['pid']][$attachment['aid']] = $attachment;
}

if(is_array($forum))
{
	foreach($forum as $fid => $forumrow)
	{
		$forumpermissions[$fid] = forum_permissions($fid);
	}
}

$icon_cache = $cache->read("posticons");

$announcements = '';
$query = $db->query("
	SELECT t.*, t.username AS threadusername, u.username, u.avatar, u.avatardimensions
	FROM ".TABLE_PREFIX."threads t
	LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = t.uid)
	WHERE t.fid IN (".$announcementsfids.") AND t.tid IN (0{$tids}) AND t.visible='1' AND t.closed NOT LIKE 'moved|%'
	ORDER BY t.dateline DESC
	LIMIT 0, ".$proportal->settings['numannouncements']
);
while($announcement = $db->fetch_array($query))
{
	$announcement['message'] = $posts[$announcement['tid']]['message'];
	$announcement['pid'] = $posts[$announcement['tid']]['pid'];
	$announcement['threadlink'] = get_thread_link($announcement['tid']);
	
	if($announcement['uid'] == 0)
	{
		$profilelink = htmlspecialchars_uni($announcement['threadusername']);
	}
	else
	{
		$profilelink = build_profile_link($announcement['username'], $announcement['uid']);
	}
	
	if(!$announcement['username'])
	{
		$announcement['username'] = $announcement['threadusername'];
	}
	$announcement['subject'] = htmlspecialchars_uni($parser->parse_badwords($announcement['subject']));
	if($announcement['icon'] > 0 && $icon_cache[$announcement['icon']])
	{
		$icon = $icon_cache[$announcement['icon']];
		$icon = "<img src=\"{$icon['path']}\" alt=\"{$icon['name']}\" />";
	}
	else
	{
		$icon = "&nbsp;";
	}
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
		if (!stristr($announcement['avatar'], 'http://'))
		{
			$announcement['avatar'] = $mybb->settings['bburl'] . '/' . $announcement['avatar'];
		}		
		$avatar = "<img src=\"{$announcement['avatar']}\" alt=\"\" {$ann_avatar_width_height} />";
	}
	else
	{
		$avatar = '';
	}
	$anndate = my_date($mybb->settings['dateformat'], $announcement['dateline']);
	$anntime = my_date($mybb->settings['timeformat'], $announcement['dateline']);

	if($announcement['replies'])
	{
		$numcomments = "- <a href=\"{$mybb->settings['bburl']}/{$announcement['threadlink']}\"><strong>{$announcement['replies']}</strong> {$lang->replies}</a>";
	}
	else
	{
		$numcomments = "- {$lang->no_replies}";
		$lastcomment = '';
	}
	
	$views = "<strong>{$announcement['views']}</strong> {$lang->latest_threads_views}";
	
	$plugins->run_hooks("pro_portal_announcement");

	$parser_options = array(
		"allow_html" => $forum[$announcement['fid']]['allowhtml'],
		"allow_mycode" => $forum[$announcement['fid']]['allowmycode'],
		"allow_smilies" => $forum[$announcement['fid']]['allowsmilies'],
		"allow_imgcode" => $forum[$announcement['fid']]['allowimgcode'],
		"filter_badwords" => 1
	);
	if($announcement['smilieoff'] == 1)
	{
		$parser_options['allow_smilies'] = 0;
	}

	$message = $parser->parse_message($announcement['message'], $parser_options);
	// Cut the message and place a read more link if the related option is enabled
	if($proportal->settings['annmessagelength'] > "0")
	{
		$message = substr($message, 0, $proportal->settings['annmessagelength']);
		$message = $message."...<br /><a href=\"".$mybb->settings['bburl']."/".$announcement['threadlink']."\">".$lang->messagemore."</a>";
	}
	
	if(is_array($attachcache[$announcement['pid']]))
	{ // This post has 1 or more attachments
		$validationcount = 0;
		$id = $announcement['pid'];
		foreach($attachcache[$id] as $aid => $attachment)
		{
			if($attachment['visible'])
			{ // There is an attachment thats visible!
				$attachment['filename'] = htmlspecialchars_uni($attachment['filename']);
				$attachment['filesize'] = get_friendly_size($attachment['filesize']);
				$ext = get_extension($attachment['filename']);
				if($ext == "jpeg" || $ext == "gif" || $ext == "bmp" || $ext == "png" || $ext == "jpg")
				{
					$isimage = true;
				}
				else
				{
					$isimage = false;
				}
				$attachment['icon'] = get_attachment_icon($ext);
				// Support for [attachment=id] code
				if(stripos($message, "[attachment=".$attachment['aid']."]") !== false)
				{
					if($attachment['thumbnail'] != "SMALL" && $attachment['thumbnail'] != '')
					{ // We have a thumbnail to show (and its not the "SMALL" enough image
						eval("\$attbit = \"".$templates->get("postbit_attachments_thumbnails_thumbnail")."\";");
					}
					elseif($attachment['thumbnail'] == "SMALL" && $forumpermissions[$announcement['fid']]['candlattachments'] == 1)
					{
						// Image is small enough to show - no thumbnail
						eval("\$attbit = \"".$templates->get("postbit_attachments_images_image")."\";");
					}
					else
					{
						// Show standard link to attachment
						eval("\$attbit = \"".$templates->get("postbit_attachments_attachment")."\";");
					}
					$message = preg_replace("#\[attachment=".$attachment['aid']."]#si", $attbit, $message);
				}
				else
				{
					if($attachment['thumbnail'] != "SMALL" && $attachment['thumbnail'] != '')
					{ // We have a thumbnail to show
						eval("\$post['thumblist'] .= \"".$templates->get("postbit_attachments_thumbnails_thumbnail")."\";");
						if($tcount == 5)
						{
							$thumblist .= "<br />";
							$tcount = 0;
						}
						++$tcount;
					}
					elseif($attachment['thumbnail'] == "SMALL" && $forumpermissions[$announcement['fid']]['candlattachments'] == 1)
					{
						// Image is small enough to show - no thumbnail
						eval("\$post['imagelist'] .= \"".$templates->get("postbit_attachments_images_image")."\";");
					}
					else
					{
						eval("\$post['attachmentlist'] .= \"".$templates->get("postbit_attachments_attachment")."\";");
					}
				}
			}
			else
			{
				$validationcount++;
			}
		}
		if($post['thumblist'])
		{
			eval("\$post['attachedthumbs'] = \"".$templates->get("postbit_attachments_thumbnails")."\";");
		}
		if($post['imagelist'])
		{
			eval("\$post['attachedimages'] = \"".$templates->get("postbit_attachments_images")."\";");
		}
		if($post['attachmentlist'] || $post['thumblist'] || $post['imagelist'])
		{
			eval("\$post['attachments'] = \"".$templates->get("postbit_attachments")."\";");
		}
	}

	eval("\$announcements .= \"".$templates->get("pro_portal_announcement")."\";");
	unset($post);
}
if(!$announcements){ $announcements = "<table cellspacing=\"{$theme['borderwidth']}\" cellpadding=\"{$theme['tablespace']}\" class=\"tborder\">
<tr>
<td class=\"thead\"><strong>{$lang->no_announcement}</strong></td>
</tr>
<tr>
<td class=\"trow2\" valign=\"middle\">{$lang->no_announcement_desc}</td>
</tr>
</table>"; }
$multipage = str_replace('<div class="pagination">', '<div class="pagination" align="center">', $multipage);
echo $announcements.$multipage;
?>