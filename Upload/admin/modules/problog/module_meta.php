<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
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

function problog_meta()
{
	global $page, $lang, $plugins, $problog;

    $lang->load("problog_info");
    $lang->load("problog_posts");
    $lang->load("problog_categories");
    $lang->load("problog_blocks");
    $lang->load("problog_pages");
    $lang->load("problog_settings");
    $lang->load("problog_module_meta");

	$sub_menu = array();
	$sub_menu['10'] = array("id" => "info", "title" => $lang->problog_info, "link" => "index.php?module=problog-info");
	$sub_menu['15'] = array("id" => "posts", "title" => $lang->problog_posts, "link" => "index.php?module=problog-posts");
	$sub_menu['17'] = array("id" => "categories", "title" => $lang->problog_categories, "link" => "index.php?module=problog-categories");
	$sub_menu['20'] = array("id" => "blocks", "title" => $lang->problog_blocks, "link" => "index.php?module=problog-blocks");
	$sub_menu['30'] = array("id" => "pages", "title" => $lang->problog_pages, "link" => "index.php?module=problog-pages");
	$sub_menu['35'] = array("id" => "reports", "title" => $lang->problog_reports, "link" => "index.php?module=problog-reports");
	$sub_menu['40'] = array("id" => "settings", "title" => $lang->problog_settings, "link" => "index.php?module=problog-settings");

	$plugins->run_hooks("admin_problog_menu", $sub_menu);

	$page->add_menu_item($lang->problog_menu, "problog", "index.php?module=problog-info", 10, $sub_menu);

	return true;
}

function problog_action_handler($action)
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

	$plugins->run_hooks("admin_problog_action_handler", $actions);

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