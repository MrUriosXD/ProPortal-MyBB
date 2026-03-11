<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/

$tables[] = "CREATE TABLE `mybb_blog_blocks` (
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

$tables[] = "CREATE TABLE `mybb_blog_pages` (
  `id` int(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `enabled` smallint(1) NOT NULL default '1',
  `visible` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE `mybb_blog_settings` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE `mybb_blog_posts` (
  `pid` int(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `uid` int(10) NOT NULL default '0',
  `cid` int(10) NOT NULL default '0',
  `dateline` bigint(30) NOT NULL default '0',
  `views` int(10) NOT NULL default '0',
  `likes` int(10) NOT NULL default '0',
  `comments_count` int(10) NOT NULL default '0',
  `closed` smallint(1) NOT NULL default '0',
  `archived` smallint(1) NOT NULL default '0',
  `featured` smallint(1) NOT NULL default '0',
  `image` varchar(255) NOT NULL default '',
  `enabled` smallint(1) NOT NULL default '1',
  `ipaddress` varbinary(16) NOT NULL default '',
  PRIMARY KEY  (`pid`)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE `mybb_blog_categories` (
  `cid` int(10) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE `mybb_blog_comments` (
  `cid` int(10) NOT NULL auto_increment,
  `post_id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `content` text NOT NULL,
  `dateline` bigint(30) NOT NULL default '0',
  `ipaddress` varbinary(16) NOT NULL default '',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE `mybb_blog_likes` (
  `lid` int(10) NOT NULL auto_increment,
  `pid` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `dateline` bigint(30) NOT NULL default '0',
  PRIMARY KEY (`lid`)
) ENGINE=MyISAM;";

$tables[] = "CREATE TABLE `mybb_blog_reports` (
  `rid` int(10) NOT NULL auto_increment,
  `id` int(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `reason` text NOT NULL,
  `dateline` bigint(30) NOT NULL default '0',
  `is_read` smallint(1) NOT NULL default '0',
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM;";

?>