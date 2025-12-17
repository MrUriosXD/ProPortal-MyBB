<?php
if (!defined("IN_PORTAL")) {
	die("<div style=\"border:1px solid #CC0000; padding:3px; margin:0; font-family:Tahoma; width:250px; font-size:12px;\"><strong>Error:</strong> This file cannot be viewed directly!</div>");
}

// Get forums user cannot view
$unviewable = get_unviewable_forums();
if($unviewable)
{
	$unviewwhere = " AND fid NOT IN ($unviewable)";
}

$altbg = alt_trow();
$threadlist = '';
$showlimit = 5;
$query = $db->query("
	SELECT p.*, u.username
	FROM ".TABLE_PREFIX."posts p
	LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid=p.uid)
	WHERE p.visible='1' $unviewwhere
	ORDER BY p.dateline DESC 
	LIMIT 0, ".$showlimit
);
while($thread = $db->fetch_array($query))
{
	$lastpostdate = my_date($mybb->settings['dateformat'], $thread['dateline']);
	$lastposttime = my_date($mybb->settings['timeformat'], $thread['dateline']);
	// Don't link to guest's profiles (they have no profile).
	if($thread['uid'] == 0)
	{
		$lastposterlink = $thread['username'];
	}
	else
	{
		$lastposterlink = build_profile_link($thread['username'], $thread['uid']);
	}
	if(my_strlen($thread['subject']) > 20)
	{
		$thread['subject'] = my_substr($thread['subject'], 0, 20) . "...";
	}
	$thread['subject'] = htmlspecialchars_uni($parser->parse_badwords($thread['subject']));
	$thread['threadlink'] = get_post_link($thread['pid'],$thread['tid']);
	$threadlist .= "<tr>
<td class=\"$altbg\">
<strong><a href=\"".$mybb->settings['bburl']."/".$thread['threadlink']."#pid".$thread['pid']."\">$thread[subject]</a></strong>
<span class=\"smalltext\"><br />
$lang->posted_by <em>$lastposterlink</em><br />
$lastpostdate $lastposttime
</span>
</td>
</tr>";
	$altbg = alt_trow();
}
if($threadlist)
{ 
	// Show the table only if there are threads
	$latestposts = "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong>Latest Posts</strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		{$threadlist}
		</tbody>
	</table>";
}
else
{
	$latestposts = "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong>Latest Posts</strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		<tr>
		<td class=\"$altbg\">No Post</td>
		</tr>
		</tbody>
	</table>";
}

echo $latestposts;
?>