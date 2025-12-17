<?php
/***************************************************************
 * ProPortal
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

function portal_meta()
{
	global $page, $lang, $plugins;
	
	$sub_menu = array();
	$sub_menu['10'] = array("id" => "info", "title" => $lang->portal_info, "link" => "index.php?module=portal/info");
	$sub_menu['20'] = array("id" => "blocks", "title" => $lang->portal_block_management, "link" => "index.php?module=portal/blocks");
	$sub_menu['30'] = array("id" => "pages", "title" => $lang->portal_page_management, "link" => "index.php?module=portal/pages");
	$sub_menu['40'] = array("id" => "settings", "title" => $lang->portal_settings, "link" => "index.php?module=portal/settings");
	
	$plugins->run_hooks_by_ref("admin_portal_menu", $sub_menu);

	$page->add_menu_item($lang->portal, "portal", "index.php?module=portal", 10, $sub_menu);

	return true;
}

function portal_action_handler($action)
{
	global $page, $lang, $plugins;
	
	$page->active_module = "portal";
	
	$actions = array(
		'settings' => array('active' => 'settings', 'file' => 'settings.php'),
		'blocks' => array('active' => 'blocks', 'file' => 'blocks.php'),
		'pages' => array('active' => 'pages', 'file' => 'pages.php'),
		'info' => array('active' => 'info', 'file' => 'info.php')
	);
	
	$plugins->run_hooks_by_ref("admin_portal_action_handler", $actions);
	
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