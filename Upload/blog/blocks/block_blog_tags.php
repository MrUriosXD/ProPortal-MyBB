<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

$query = $db->simple_select("blog_posts", "tags", "enabled='1'");
$all_tags = array();
while($row = $db->fetch_array($query))
{
    $tags = explode(",", $row['tags']);
    foreach($tags as $tag)
    {
        $tag = trim($tag);
        if($tag) $all_tags[$tag] = ($all_tags[$tag] ?? 0) + 1;
    }
}

if(!empty($all_tags))
{
    echo "<div id=\"tag_cloud\" style=\"text-align: center;\">";
    foreach($all_tags as $tag => $count)
    {
        $size = 10 + (min($count, 10) * 2);
        echo "<a href=\"blog.php?action=search&tag=".urlencode($tag)."\" style=\"font-size: {$size}px; margin: 5px; display: inline-block;\">".htmlspecialchars_uni($tag)."</a> ";
    }
    echo "</div>";
}
else
{
    echo "No tags found.";
}
?>