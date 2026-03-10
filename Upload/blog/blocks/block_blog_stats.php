<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

$total_posts = $db->fetch_field($db->simple_select("blog_posts", "COUNT(*) AS c", "enabled='1'"), "c");
$total_comments = $db->fetch_field($db->simple_select("blog_comments", "COUNT(*) AS c"), "c");

$latest_post = $db->fetch_array($db->simple_select("blog_posts", "pid, title", "enabled='1'", array("order_by" => "dateline", "order_dir" => "DESC", "limit" => 1)));
$top_viewed = $db->fetch_array($db->simple_select("blog_posts", "pid, title", "enabled='1'", array("order_by" => "views", "order_dir" => "DESC", "limit" => 1)));
$top_liked = $db->fetch_array($db->simple_select("blog_posts", "pid, title", "enabled='1'", array("order_by" => "likes", "order_dir" => "DESC", "limit" => 1)));
$top_commented = $db->fetch_array($db->simple_select("blog_posts", "pid, title", "enabled='1'", array("order_by" => "comments_count", "order_dir" => "DESC", "limit" => 1)));

echo "<span class=\"smalltext\">";
echo "<strong>Total Posts:</strong> {$total_posts}<br />";
echo "<strong>Total Comments:</strong> {$total_comments}<br /><br />";

if($latest_post) echo "<strong>Latest:</strong> <a href=\"blog.php?action=view&id={$latest_post['pid']}\">".htmlspecialchars_uni($latest_post['title'])."</a><br />";
if($top_viewed) echo "<strong>Most Viewed:</strong> <a href=\"blog.php?action=view&id={$top_viewed['pid']}\">".htmlspecialchars_uni($top_viewed['title'])."</a><br />";
if($top_liked) echo "<strong>Most Liked:</strong> <a href=\"blog.php?action=view&id={$top_liked['pid']}\">".htmlspecialchars_uni($top_liked['title'])."</a><br />";
if($top_commented) echo "<strong>Most Discussed:</strong> <a href=\"blog.php?action=view&id={$top_commented['pid']}\">".htmlspecialchars_uni($top_commented['title'])."</a><br />";

echo "</span>";
?>