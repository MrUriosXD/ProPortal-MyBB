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

$tables[] = "CREATE TABLE `mybb_portal_blocks` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `zone` smallint(1) NOT NULL default '0',
  `position` smallint(2) NOT NULL default '0',
  `custom` smallint(1) NOT NULL default '0',
  `file` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `enabled` smallint(1) NOT NULL default '0',
  `visible` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE `mybb_portal_pages` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `enabled` smallint(1) NOT NULL default '1',
  `visible` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE `mybb_portal_settings` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

?>