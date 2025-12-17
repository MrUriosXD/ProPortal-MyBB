<?php
/***************************************************************
 * ProPortal
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/
 
class ProPortal {
	
	/**
	 * Get results as an array.
	 *
	 * @param string Incoming query
	 */
	function get_list($query)
	{
		global $db;
	
		$result = array();
		$query = $db->query($query);
		while ($row = $db->fetch_array($query)) {
			$result[] = $row;
		}
		$db->free_result($query);
		return $result;
	}
	
	/**
	 * Build the module url.
	 *
	 * @param string Name of the module
	 */
	function module_url($module)
	{
		$link = str_replace("{module}", $module, MODULE_URL);
		return htmlspecialchars_uni($link);
	}
	
	/**
	 * Build the page url.
	 *
	 * @param string Name of the page
	 */
	function page_url($page)
	{
		$link = str_replace("{page}", $page, PAGE_URL);
		return htmlspecialchars_uni($link);
	}
	
	/**
	 * Replaces page title with the specified input.
	 *
	 * @param string Page content
	 * @param string Page title
	 */
	function page_title($content, $title)
	{
		global $mybb;
		
		$portal = preg_replace("/<title>(.*)<\/title>/", "<title>{$title} - {$mybb->settings['bbname']}</title>", $content);
		return $portal;
	}
}
?>