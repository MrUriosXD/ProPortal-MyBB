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

// Get forums user cannot view
$unviewable = get_unviewable_forums();
if($unviewable)
{
	$unviewwhere = " AND fid NOT IN ($unviewable)";
}

$threadlimit = 10;
$query = $db->query("
	SELECT t.*, t.subject AS threadsubject, u.username, u.usergroup, u.displaygroup, i.*, i.name AS iconname,
	t.dateline AS threaddate, t.lastpost AS threadlastpost
	FROM ".TABLE_PREFIX."threads t
	LEFT JOIN ".TABLE_PREFIX."icons i ON (i.iid=t.icon)
	LEFT JOIN ".TABLE_PREFIX."users u ON (t.lastposter=u.username)
	WHERE t.visible = '1'
	$unviewwhere
	GROUP BY t.tid
	ORDER BY threaddate DESC
	LIMIT 0, $threadlimit
");

while($threads = $db->fetch_array($query))
{

	if($threads['icon'] > 0)
	{
		$icon = "<img src=\"{$threads['path']}\" alt=\"{$threads['iconname']}\" title=\"{$threads['iconname']}\" />";
	}
	else
	{
		$icon = "&nbsp;";
	}

	if(strlen($threads['threadsubject']) > "40")
	{
		$threadsthreadsubject = my_substr($threads['threadsubject'],0,40)."...";
	}
	else
	{
		$threadsthreadsubject = $threads['threadsubject'];
	}

	if(strlen($threads['forumname']) > "20")
	{
		$threadsforumname = my_substr($threads['forumname'],0,20)."...";
	}
	else
	{
		$threadsforumname = $threads['forumname'];
	}

	$threadlink = get_thread_link($threads['tid']);
	$forumlink = get_forum_link($threads['fid']);
	$replies = my_number_format($threads['replies']);
	$views = my_number_format($threads['views']);
	$lastpostdate = my_date($mybb->settings['dateformat'], $threads['threadlastpost']);
	$lastposttime = my_date($mybb->settings['timeformat'], $threads['threadlastpost']);
	$lastposter = format_name($threads['username'], $threads['usergroup'], $threads['displaygroup']);
	$lastposter = build_profile_link($lastposter, $threads['lastposteruid']);

	$last_thread .= "<tr>
		<td class=\"trow1\" align=\"center\" height=\"24\">$icon</td>
		<td class=\"trow2\"><a href=\"$threadlink\" title=\"$threads[threadsubject]\">$threadsthreadsubject</a></td>
		<td class=\"trow1\" align=\"center\">$replies</td>
		<td class=\"trow2\" align=\"center\">$views</td>
		<td class=\"trow1\"><span class=\"smalltext\">$lastpostdate $lastposttime<br />by $lastposter</span></td>
</tr>";

}
if(!$last_thread){ $last_thread = "<tr><td class=\"trow1\" colspan=\"5\">{$lang->no_thread}</td></tr>"; }
echo "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\" colspan=\"6\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong>{$lang->latest_threads}</strong></td>
		</tr>
			<tr>
				<td class=\"tcat\" width=\"5%\" height=\"24\">&nbsp;</td>
				<td class=\"tcat\" width=\"50%\"><span class=\"smalltext\"><strong>{$lang->latest_threads_thread}</strong></span></td>
				<td class=\"tcat\" width=\"10%\" align=\"center\"><span class=\"smalltext\"><strong>{$lang->latest_threads_replies}</strong></span></td>
				<td class=\"tcat\" width=\"10%\" align=\"center\"><span class=\"smalltext\"><strong>{$lang->latest_threads_views}</strong></span></td>
				<td class=\"tcat\" width=\"25%\" align=\"center\"><span class=\"smalltext\"><strong>{$lang->latest_threads_lastpost}</strong></span></td>
			</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		{$last_thread}
		</tbody>
	</table>";
?>