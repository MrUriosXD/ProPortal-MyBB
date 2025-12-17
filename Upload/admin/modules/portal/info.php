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

$page->add_breadcrumb_item($lang->portal_info, "index.php?module=portal/info");

$plugins->run_hooks("admin_portal_info_begin");

if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_portal_info_start");
	
	$page->output_header($lang->portal_info);
	
	$sub_tabs['portal_info'] = array(
		'title' => $lang->portal_info,
		'link' => "index.php?module=portal/info",
		'description' => $lang->portal_info_description
	);

	$page->output_nav_tabs($sub_tabs, 'portal_info');
	
	// Get the number of blocks
	$query = $db->simple_select("portal_blocks", "COUNT(id) AS numblocks");
	$blocksnum = my_number_format($db->fetch_field($query, "numblocks"));
	
	// Get the number of enabled blocks
	$query = $db->simple_select("portal_blocks", "COUNT(id) AS numblocks", "enabled='1'");
	$activeblocksnum = my_number_format($db->fetch_field($query, "numblocks"));
	
	// Get the number of disabled blocks
	$passiveblocksnum = $blocksnum - $activeblocksnum;
	
	// Get the number of left sided blocks
	$query = $db->simple_select("portal_blocks", "COUNT(id) AS numblocks", "zone='0'");
	$leftblocksnum = my_number_format($db->fetch_field($query, "numblocks"));
	
	// Get the number of centered blocks
	$query = $db->simple_select("portal_blocks", "COUNT(id) AS numblocks", "zone='1'");
	$centerblocksnum = my_number_format($db->fetch_field($query, "numblocks"));
	
	// Get the number of right sided blocks
	$rightblocksnum = $blocksnum - ($leftblocksnum + $centerblocksnum);
	
	$table = new Table;
	/*$table->construct_header($lang->portal_information, array('width' => '100%', 'colspan' => '4'));*/
	
	$table->construct_cell("<strong>{$lang->portal_version}</strong>", array('width' => '25%'));
	$table->construct_cell("1.0", array('width' => '25%'));	
	$table->construct_cell("<strong>{$lang->portal_publishdate}</strong>", array('width' => '25%'));
	$table->construct_cell("19.01.2010", array('width' => '25%'));
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->portal_blocksnum}</strong>", array('width' => '25%'));
	$table->construct_cell("{$blocksnum}", array('width' => '25%'));	
	$table->construct_cell("<strong>{$lang->portal_activeblocksnum}</strong>", array('width' => '25%'));
	$table->construct_cell("{$activeblocksnum}", array('width' => '25%'));
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->portal_passiveblocksnum}</strong>", array('width' => '25%'));
	$table->construct_cell("{$passiveblocksnum}", array('width' => '25%'));	
	$table->construct_cell("<strong>{$lang->portal_leftsidenum}</strong>", array('width' => '25%'));
	$table->construct_cell("{$leftblocksnum}", array('width' => '25%'));
	$table->construct_row();
	
	$table->construct_cell("<strong>{$lang->portal_centernum}</strong>", array('width' => '25%'));
	$table->construct_cell("{$centerblocksnum}", array('width' => '25%'));	
	$table->construct_cell("<strong>{$lang->portal_rightsidenum}</strong>", array('width' => '25%'));
	$table->construct_cell("{$rightblocksnum}", array('width' => '25%'));
	$table->construct_row();
	
	$table->output($lang->portal_information);
	
	$table = new Table;
	
	require_once MYBB_ROOT."inc/class_feedparser.php";
	$feed_parser = new FeedParser();
	$feed_parser->parse_feed("http://www.promybb.com/latest_news.php");
	
	if($feed_parser->error == '')
	{
		foreach($feed_parser->items as $item)
		{
			if($item['date_timestamp'])
			{
				$stamp = my_date($mybb->settings['dateformat'], $item['date_timestamp']).", ".my_date($mybb->settings['timeformat'], $item['date_timestamp']);
			}
			else
			{
				$stamp = '';
			}
			
			$content = $item['description'];
			
			$news .= "<p><span style=\"font-size: 16px;\"><strong>".$item['title']."</strong></span><br />{$stamp}<br /><br />{$content}<strong><br /><br /><a href=\"{$item['link']}\" target=\"_blank\">&raquo; {$lang->portal_read_more}</a></strong></p><hr />";
		}
	}
	
	$table->construct_cell("<div style='height: 200px; overflow: auto;'>{$news}</div>");
	$table->construct_row();
	
	$table->output($lang->portal_latest_news);
	
	$page->output_footer();
}

?>