<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

$limit = (int)($problog->settings['latest_comments_num'] ?? 5);
$query = $db->query("
    SELECT c.*, u.username, p.title AS post_title
    FROM ".TABLE_PREFIX."blog_comments c
    LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = c.uid)
    LEFT JOIN ".TABLE_PREFIX."blog_posts p ON (p.pid = c.post_id)
    ORDER BY c.dateline DESC
    LIMIT {$limit}
");

echo "<ul style=\"list-style: none; margin: 0; padding: 0;\">";
while($comment = $db->fetch_array($query))
{
    $date = my_date($mybb->settings['dateformat'], $comment['dateline']);
    echo "<li style=\"margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 5px;\">";
    echo "<strong>".htmlspecialchars_uni($comment['username'])."</strong> on <a href=\"blog.php?action=view&id={$comment['post_id']}\">".htmlspecialchars_uni($comment['post_title'])."</a>";
    echo "<br /><span class=\"smalltext\">".my_substr(strip_tags($comment['content']), 0, 50)."...</span>";
    echo "</li>";
}
echo "</ul>";
?>