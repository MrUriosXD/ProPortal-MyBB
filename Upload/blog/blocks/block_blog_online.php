<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

$timesearch = TIME_NOW - 900; // 15 minutes
$query = $db->query("
	SELECT DISTINCT u.uid, u.username, u.usergroup, u.displaygroup
	FROM ".TABLE_PREFIX."sessions s
	LEFT JOIN ".TABLE_PREFIX."users u ON (s.uid = u.uid)
	WHERE s.location LIKE '%blog.php%' AND s.time > {$timesearch}
	ORDER BY u.username ASC
");

$online_count = 0;
$guest_count = 0;
$users = array();

while($user = $db->fetch_array($query))
{
	if($user['uid'] > 0)
	{
		$users[] = format_name($user['username'], $user['usergroup'], $user['displaygroup']);
		$online_count++;
	}
	else
	{
		$guest_count++;
	}
}

echo "<span class=\"smalltext\">";
echo "<strong>Online Users:</strong> ".($online_count + $guest_count)."<br />";
echo "<strong>Members:</strong> {$online_count}<br />";
echo "<strong>Guests:</strong> {$guest_count}<br /><br />";
if(!empty($users)) echo implode(", ", $users);
echo "</span>";
?>