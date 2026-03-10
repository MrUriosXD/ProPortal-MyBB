<?php
/***************************************************************
 * ProBlog
 * Copyright \xa9 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

require_once MYBB_ROOT."blog/inc/blog.class.php";
$problog = new ProBlog;
$lang->load("problog_posts");

function blog_meta()
{
	global $page, $lang, $plugins, $problog;

	$sub_menu = array();
	$sub_menu['10'] = array("id" => "info", "title" => $lang->blog_info, "link" => "index.php?module=problog/info");
	$sub_menu['15'] = array("id" => "posts", "title" => $lang->blog_posts_management, "link" => "index.php?module=problog/posts");
	$sub_menu['20'] = array("id" => "blocks", "title" => $lang->blog_block_management, "link" => "index.php?module=problog/blocks");
	$sub_menu['30'] = array("id" => "pages", "title" => $lang->blog_page_management, "link" => "index.php?module=problog/pages");
	$sub_menu['40'] = array("id" => "settings", "title" => $lang->blog_settings, "link" => "index.php?module=problog/settings");

	$plugins->run_hooks("admin_blog_menu", $sub_menu);

	$page->add_menu_item($lang->blog, "problog", "index.php?module=problog", 10, $sub_menu);

	return true;
}

function blog_action_handler($action)
{
	global $page, $lang, $plugins, $problog;

	$page->active_module = "problog";

	$actions = array(
		'settings' => array('active' => 'settings', 'file' => 'settings.php'),
		'blocks' => array('active' => 'blocks', 'file' => 'blocks.php'),
		'pages' => array('active' => 'pages', 'file' => 'pages.php'),
		'posts' => array('active' => 'posts', 'file' => 'posts.php'),
		'info' => array('active' => 'info', 'file' => 'info.php')
	);

	$plugins->run_hooks("admin_blog_action_handler", $actions);

	if(isset($actions[$action]))
	{
		$page->active_action = $actions[$action]['active'];
		return $actions[$action]['file'];
	}
	else
	{
		$page->active_action = "info";
		return "info.php";
	}
}

?>