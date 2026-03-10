<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

$query = $db->query("
    SELECT c.*, COUNT(p.pid) AS post_count
    FROM ".TABLE_PREFIX."blog_categories c
    LEFT JOIN ".TABLE_PREFIX."blog_posts p ON (p.cid = c.cid AND p.enabled='1')
    GROUP BY c.cid
    ORDER BY c.name ASC
");

echo "<ul style=\"list-style: none; margin: 0; padding: 0;\">";
while($cat = $db->fetch_array($query))
{
    echo "<li style=\"margin-bottom: 5px;\">";
    echo "<a href=\"#\" onclick=\"$(this).next().toggle(); return false;\" style=\"text-decoration: none;\">[+]</a> ";
    echo "<a href=\"blog.php?action=search&cid={$cat['cid']}\">".htmlspecialchars_uni($cat['name'])."</a> ({$cat['post_count']})";

    // Submenu with post titles
    $query2 = $db->simple_select("blog_posts", "pid, title", "cid='{$cat['cid']}' AND enabled='1'", array("limit" => 5, "order_by" => "dateline", "order_dir" => "DESC"));
    echo "<ul style=\"display: none; margin-left: 20px; list-style: circle;\">";
    while($post = $db->fetch_array($query2))
    {
        echo "<li><a href=\"blog.php?action=view&id={$post['pid']}\" class=\"smalltext\">".htmlspecialchars_uni($post['title'])."</a></li>";
    }
    echo "</ul>";
    echo "</li>";
}
echo "</ul>";
?>