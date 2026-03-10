<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com
 *
 * MyBB Installation Wizard originally written by MyBB Group
 * Website: http://www.mybboard.net
 ***************************************************************/

$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (1, 'blogcolumns', 'both');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (2, 'announcementsfid', '2,3');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (3, 'numannouncements', '5');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (4, 'annmessagelength', '500');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (5, 'showeditor', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (6, 'leftcolwidth', '205');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (7, 'rightcolwidth', '205');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (8, 'horizontalspace', '15');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (9, 'verticalspace', '15');";

// New settings for MyBlog features
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (10, 'enable_likes', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (11, 'enable_reports', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (12, 'enable_tagcloud', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (13, 'posts_per_page', '10');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (14, 'comments_per_page', '10');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (15, 'closed_bgcolor', '#fff0f0');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (16, 'highlight_closed', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (17, 'search_highlight_color', '#ffffa0');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (18, 'latest_comments_num', '5');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (19, 'enable_quickreply', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (20, 'can_comment_groups', '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (21, 'can_like_groups', '2,3,4,6');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (22, 'can_post_groups', '3,4,6');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (23, 'can_report_groups', '2,3,4,6');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (24, 'moderator_groups', '4,6');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (25, 'rss_num_items', '10');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (26, 'date_format', 'relative');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (27, 'avatar_size', '35x35');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (28, 'enable_calendar', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (29, 'enable_stats', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (30, 'enable_online', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (31, 'archive_limit', '20');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (32, 'highlight_keywords', '1');";
$inserts[] = "INSERT INTO `mybb_blog_settings` VALUES (33, 'enable_rss', '1');";

// Updated blocks with MyBlog specific ones
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (1, 'UserCP', 0, 1, 1, 'usercp', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (2, 'Who''s Online', 0, 2, 1, 'blog_online', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (3, 'Blog Statistics', 0, 3, 0, 'blog_stats', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (4, 'Blog Categories', 0, 4, 1, 'blog_categories', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (5, 'Latest Posts', 1, 1, 1, 'announcements', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (6, 'Blog Search', 2, 1, 0, 'blog_search', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (7, 'Tag Cloud', 2, 2, 1, 'blog_tags', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (8, 'Blog Calendar', 2, 3, 1, 'blog_calendar', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_blog_blocks` VALUES (9, 'Latest Comments', 2, 4, 1, 'blog_latestcomments', '', 1, '1,2,3,4,5,6,7');";

$inserts[] = "INSERT INTO `mybb_blog_categories` (`cid`, `name`, `description`) VALUES (1, 'General', 'Default blog category');";
$inserts[] = "INSERT INTO `mybb_blog_posts` (`pid`, `title`, `description`, `content`, `tags`, `uid`, `cid`, `dateline`, `enabled`) VALUES (1, 'Welcome to MyBlog', 'This is a sample blog post.', 'Welcome to your new blog integrated with MyBB. You can edit or delete this post in Admin CP.', 'welcome, mybb, blog', 1, 1, ".time().", 1);";

?>