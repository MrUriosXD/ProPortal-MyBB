<?php
/***************************************************************
 * ProPortal
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com
 *
 * MyBB Installation Wizard originally written by MyBB Group
 * Website: http://www.mybboard.net
 ***************************************************************/

$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (1, 'portalcolumns', 'both');";
$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (2, 'announcementsfid', '2,3');";
$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (3, 'numannouncements', '1');";
$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (4, 'annmessagelength', '0');";
$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (5, 'showeditor', '1');";
$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (6, 'leftcolwidth', '205');";
$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (7, 'rightcolwidth', '205');";
$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (8, 'horizontalspace', '15');";
$inserts[] = "INSERT INTO `mybb_portal_settings` VALUES (9, 'verticalspace', '15');";

$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (1, 'UserCP', 0, 1, 1, 'usercp', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (2, 'Who''s Online', 0, 2, 1, 'whosonline', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (3, 'Statistics', 0, 3, 0, 'stats', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (4, 'Latest Users', 0, 4, 1, 'latestusers', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (5, 'Announcements', 1, 1, 1, 'announcements', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (6, 'Latest Threads', 1, 2, 1, 'latestthreads', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (7, 'Search', 2, 1, 0, 'search', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (8, 'Today''s Birthdays', 2, 2, 1, 'birthdays', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (9, 'Latest Posts', 2, 3, 1, 'latestposts', '', 1, '1,2,3,4,5,6,7');";
$inserts[] = "INSERT INTO `mybb_portal_blocks` VALUES (10, 'Top Posters', 2, 4, 1, 'topposters', '', 1, '1,2,3,4,5,6,7');";

?>