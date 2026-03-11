<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

function display_categories_tree($parent_id = 0)
{
    global $db, $problog;
    $query = $db->query("
        SELECT c.*, COUNT(p.pid) AS post_count
        FROM ".TABLE_PREFIX."blog_categories c
        LEFT JOIN ".TABLE_PREFIX."blog_posts p ON (p.cid = c.cid AND p.enabled='1')
        WHERE c.parent_id = '{$parent_id}'
        GROUP BY c.cid
        ORDER BY c.name ASC
    ");

    if($db->num_rows($query) > 0)
    {
        echo "<ul style=\"list-style: none; margin: 0; padding: 0 ".($parent_id ? "15" : "0")."px;\">";
        while($cat = $db->fetch_array($query))
        {
            echo "<li style=\"margin-bottom: 5px; border-bottom: 1px dotted #ccc; padding-bottom: 3px;\">";
            echo "<a href=\"#\" onclick=\"var e = $(this).next().next(); if(e.is(':visible')){ e.hide(); $(this).text('[+]'); } else { e.show(); $(this).text('[-]'); } return false;\" style=\"text-decoration: none; font-family: monospace; font-weight: bold; margin-right: 5px;\">[+]</a> ";
            echo "<a href=\"blog.php?action=search&cid={$cat['cid']}\">".htmlspecialchars_uni($cat['name'])."</a> <span class=\"smalltext\" style=\"color: #777;\">({$cat['post_count']})</span>";

            echo "<div style=\"display: none;\">";
            // Subcategories
            display_categories_tree($cat['cid']);

            // Post titles
            $query2 = $db->simple_select("blog_posts", "pid, title", "cid='{$cat['cid']}' AND enabled='1'", array("limit" => 5, "order_by" => "dateline", "order_dir" => "DESC"));
            echo "<ul style=\"margin-left: 20px; list-style: square; padding-top: 5px;\">";
            while($post = $db->fetch_array($query2))
            {
                echo "<li><a href=\"blog.php?action=view&id={$post['pid']}\" class=\"smalltext\">".htmlspecialchars_uni($post['title'])."</a></li>";
            }
            echo "</ul></div>";
            echo "</li>";
        }
        echo "</ul>";
    }
}

display_categories_tree(0);
?>