<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("<div style=\"border:1px solid #CC0000; padding:3px; margin:0; font-family:Tahoma; width:250px; font-size:12px;\"><strong>Error:</strong> This file cannot be viewed directly!</div>");
}

// Get latest blog posts
$annnum = $db->fetch_field($db->simple_select('blog_posts', 'COUNT(*) AS annnum', "enabled='1'"), 'annnum');
$pagenum = (int)($mybb->input['page'] ?? 1);
$perpage = (int)$problog->settings['numannouncements'];
if($perpage < 1) $perpage = 5;
$totalpage = (int)ceil($annnum / $perpage);
if($pagenum < 1 || !$pagenum || $pagenum > $totalpage){ $pagenum = 1; }
$multipage = multipage($annnum, $perpage, $pagenum, $mybb->settings['bburl'].'/blog.php');

$announcements = '';
$query = $db->query("
	SELECT p.*, u.username, u.avatar, u.avatardimensions
	FROM ".TABLE_PREFIX."blog_posts p
	LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = p.uid)
	WHERE p.enabled='1'
	ORDER BY p.dateline DESC
	LIMIT ".(($pagenum-1)*$perpage).", ".$perpage
);

while($announcement = $db->fetch_array($query))
{
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

	if($announcement['comments_count'])
	{
		$numcomments = "- <a href=\"{$announcement['threadlink']}#comments\"><strong>{$announcement['comments_count']}</strong> {$lang->replies}</a>";
	}
	else
	{
		$numcomments = "- {$lang->no_replies}";
	}

	$views = "<strong>{$announcement['views']}</strong> {$lang->latest_threads_views}";

	$plugins->run_hooks("pro_blog_announcement");

	$parser_options = array(
		"allow_html" => 0,
		"allow_mycode" => 1,
		"allow_smilies" => 1,
		"allow_imgcode" => 1,
		"filter_badwords" => 1
	);

	$message = $parser->parse_message($announcement['content'], $parser_options);

	if($problog->settings['annmessagelength'] > "0" && strlen(strip_tags($message)) > $problog->settings['annmessagelength'])
	{
		$message = my_substr(strip_tags($message), 0, $problog->settings['annmessagelength'])."...<br /><a href=\"".$announcement['threadlink']."\">".$lang->messagemore."</a>";
	}

    $icon = "&nbsp;"; // Simplified for now

	eval("\$announcements .= \"".$templates->get("pro_blog_announcement")."\";");
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