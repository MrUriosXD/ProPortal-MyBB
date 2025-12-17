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

$timesearch = TIME_NOW - $mybb->settings['wolcutoff'];
$comma = '';
$guestcount = 0;
$membercount = 0;
$onlinemembers = '';
$query = $db->query("
	SELECT s.sid, s.ip, s.uid, s.time, s.location, u.username, u.invisible, u.usergroup, u.displaygroup
	FROM ".TABLE_PREFIX."sessions s
	LEFT JOIN ".TABLE_PREFIX."users u ON (s.uid=u.uid)
	WHERE s.time>'$timesearch'
	ORDER BY u.username ASC, s.time DESC
");
while($user = $db->fetch_array($query))
{

	// Create a key to test if this user is a search bot.
	$botkey = my_strtolower(str_replace("bot=", '', $user['sid']));
	
	if($user['uid'] == "0")
	{
		++$guestcount;
	}
	elseif(my_strpos($user['sid'], "bot=") !== false && $session->bots[$botkey])
	{
		// The user is a search bot.
		$onlinemembers .= $comma.format_name($session->bots[$botkey], $session->botgroup);
		$comma = ", ";
		++$botcount;
	}
	else
	{
		if($doneusers[$user['uid']] < $user['time'] || !$doneusers[$user['uid']])
		{
			++$membercount;
			
			$doneusers[$user['uid']] = $user['time'];
			
			// If the user is logged in anonymously, update the count for that.
			if($user['invisible'] == 1)
			{
				++$anoncount;
			}
			
			if($user['invisible'] == 1)
			{
				$invisiblemark = "*";
			}
			else
			{
				$invisiblemark = '';
			}
			
			if(($user['invisible'] == 1 && ($mybb->usergroup['canviewwolinvis'] == 1 || $user['uid'] == $mybb->user['uid'])) || $user['invisible'] != 1)
			{
				$user['username'] = format_name($user['username'], $user['usergroup'], $user['displaygroup']);
				$user['profilelink'] = get_profile_link($user['uid']);
				$onlinemembers .= "{$comma}<a href=\"{$mybb->settings['bburl']}/{$user['profilelink']}\">{$user['username']}</a>{$invisiblemark}";
				$comma = ", ";
			}
		}
	}
}

$onlinecount = $membercount + $guestcount + $botcount;

// If we can see invisible users add them to the count
if($mybb->usergroup['canviewwolinvis'] == 1)
{
	$onlinecount += $anoncount;
}

// If we can't see invisible users but the user is an invisible user incriment the count by one
if($mybb->usergroup['canviewwolinvis'] != 1 && $mybb->user['invisible'] == 1)
{
	++$onlinecount;
}

// Most users online
$mostonline = $cache->read("mostonline");
if($onlinecount > $mostonline['numusers'])
{
	$time = TIME_NOW;
	$mostonline['numusers'] = $onlinecount;
	$mostonline['time'] = $time;
	$cache->update("mostonline", $mostonline);
}
$recordcount = $mostonline['numusers'];
$recorddate = my_date($mybb->settings['dateformat'], $mostonline['time']);
$recordtime = my_date($mybb->settings['timeformat'], $mostonline['time']);

if($onlinecount == 1)
{
  $lang->online_users = $lang->online_user;
}
else
{
  $lang->online_users = $lang->sprintf($lang->online_users, $onlinecount);
}
$lang->online_counts = $lang->sprintf($lang->online_counts, $membercount, $guestcount);
echo "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong><a href=\"{$mybb->settings['bburl']}/online.php\">{$lang->online}: {$onlinecount}</a></strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		<tr>
			<td class=\"tcat\"><span class=\"smalltext\">{$lang->online_counts}</span></td>
		</tr>
		<tr>
			<td class=\"trow1\">{$onlinemembers}</td>
		</tr>
		</tbody>
	</table>";
?>