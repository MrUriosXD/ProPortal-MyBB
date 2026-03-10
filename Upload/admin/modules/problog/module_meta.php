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

    $lang->load("problog_info");
    $lang->load("problog_posts");
    $lang->load("problog_categories");
    $lang->load("problog_blocks");
    $lang->load("problog_pages");
    $lang->load("problog_settings");

	$sub_menu = array();
	$sub_menu['10'] = array("id" => "info", "title" => $lang->blog_info, "link" => "index.php?module=problog/info");
	$sub_menu['15'] = array("id" => "posts", "title" => $lang->blog_posts_management, "link" => "index.php?module=problog/posts");
	$sub_menu['17'] = array("id" => "categories", "title" => $lang->blog_categories_management, "link" => "index.php?module=problog/categories");
	$sub_menu['20'] = array("id" => "blocks", "title" => $lang->blog_block_management, "link" => "index.php?module=problog/blocks");
	$sub_menu['30'] = array("id" => "pages", "title" => $lang->blog_page_management, "link" => "index.php?module=problog/pages");
	$sub_menu['35'] = array("id" => "reports", "title" => $lang->blog_reports_management, "link" => "index.php?module=problog/reports");
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
		'categories' => array('active' => 'categories', 'file' => 'categories.php'),
		'reports' => array('active' => 'reports', 'file' => 'reports.php'),
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