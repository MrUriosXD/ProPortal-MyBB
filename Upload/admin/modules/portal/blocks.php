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

$page->add_breadcrumb_item($lang->portal_block_management, "index.php?module=portal/blocks");

if($mybb->input['action'] == "add" || $mybb->input['action'] == "edit" || !$mybb->input['action'])
{
	if($mybb->input['action'] == "edit")
	{
		$sub_tabs['edit_block'] = array(
			'title' => $lang->portal_blocks_edit,
			'link' => "index.php?module=portal/blocks&amp;action=edit&amp;id=".$mybb->input['id'],
			'description' => $lang->portal_blocks_edit_description
		);
	} else {
		$sub_tabs['blocks'] = array(
			'title' => $lang->portal_block_management,
			'link' => "index.php?module=portal/blocks",
			'description' => $lang->portal_blocks_description
		);
		$sub_tabs['add_block'] = array(
			'title' => $lang->portal_blocks_add,
			'link' => "index.php?module=portal/blocks&amp;action=add",
			'description' => $lang->portal_blocks_add_description
		);
	}
}

$plugins->run_hooks("admin_portal_blocks_begin");

if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_portal_blocks_start");
	
	$page->output_header($lang->portal_block_management);

	$page->output_nav_tabs($sub_tabs, 'blocks');
	
	if(!empty($mybb->input['position']) && is_array($mybb->input['position']))
	{
		foreach($mybb->input['position'] as $update_id => $order)
		{
			$db->update_query("portal_blocks", array('position' => intval($order)), "id='".intval($update_id)."'");
		}
		
		$plugins->run_hooks("admin_portal_blocks_start_position_commit");
		
		// Log admin action
		log_admin_action();
	
		flash_message($lang->portal_success_block_position_updated, 'success');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$query = $db->simple_select("portal_blocks", "*");
	$blocks = $db->fetch_array($query);
	
	if(!$blocks)
	{
		$table = new Table;
		$table->construct_header($lang->portal_blocks, array('width' => '100%'));
		
		$table->construct_cell($lang->portal_blocks_noblock);
		$table->construct_row();
		
		$table->output($lang->portal_manage_blocks);
	} else {
		$form = new Form("index.php?module=portal/blocks", "post");
		
		$table = new Table;
		$table->construct_header($lang->portal_blocks_title, array('width' => '25%'));
		$table->construct_header($lang->portal_blocks_zone, array('width' => '10%'));
		$table->construct_header($lang->portal_blocks_position, array('width' => '10%'));
		$table->construct_header($lang->portal_blocks_custom_short, array('width' => '10%'));
		$table->construct_header($lang->portal_blocks_file, array('width' => '15%'));
		$table->construct_header($lang->portal_blocks_isenabled, array('width' => '10%'));
		$table->construct_header($lang->portal_blocks_visible_s, array('width' => '10%'));
		$table->construct_header($lang->portal_blocks_options, array('width' => '10%'));
		
		$query = $db->simple_select("portal_blocks", "*", "", array('order_by' => 'zone, position'));
		while($block = $db->fetch_array($query))
		{
			if($block['zone'] == "0"){ $zone = $lang->portal_blocks_zoneleft; }
			elseif($block['zone'] == "1"){ $zone = $lang->portal_blocks_zonecenter; }
			elseif($block['zone'] == "2"){ $zone = $lang->portal_blocks_zoneright; }
			
			if($block['custom'] == "0"){ $custom = $lang->portal_blocks_no; }
			elseif($block['custom'] == "1"){ $custom = $lang->portal_blocks_yes; }
			
			if($block['enabled'] == "0"){ $enabled = "<span style=\"color:red;\">".$lang->portal_blocks_no."</span>"; }
			elseif($block['enabled'] == "1"){ $enabled = "<span style=\"color:green;\">".$lang->portal_blocks_yes."</span>"; }
			
			if($block['file'] == "0"){ $file = $lang->portal_blocks_nofile; }
			else{ $file = $block['file']; }
			
			// Build visible groups popup menu
			if(!is_array($groupscache))
			{
				$groupscache = $cache->read("usergroups");
			}
			$groups = explode(",", $block['visible']);
			$popup = new PopupMenu("group_{$block['id']}", $lang->portal_blocks_seegroup);
			foreach($groups as $usergroups)
			{
				$usergroup = $groupscache[$usergroups];
				$popup->add_item($usergroup['title'], "");
			}
			$groups = $popup->fetch();
			
			// Build options popup menu
			$popup = new PopupMenu("block_{$block['id']}", $lang->portal_blocks_options);
			$popup->add_item($lang->portal_blocks_editblock, "index.php?module=portal/blocks&amp;action=edit&amp;id={$block['id']}");
			$popup->add_item($lang->portal_blocks_deleteblock, "index.php?module=portal/blocks&amp;action=delete&amp;id={$block['id']}");
			if($block['enabled'] == "0"){ $popup->add_item($lang->portal_blocks_enableblock, "index.php?module=portal/blocks&amp;action=enable&amp;id={$block['id']}"); }
			elseif($block['enabled'] == "1"){ $popup->add_item($lang->portal_blocks_disableblock, "index.php?module=portal/blocks&amp;action=disable&amp;id={$block['id']}"); }
			$controls = $popup->fetch();
			
			$table->construct_cell($block['title']);
			$table->construct_cell($zone);	
			$table->construct_cell("<input type=\"text\" name=\"position[".$block['id']."]\" value=\"".$block['position']."\" class=\"text_input align_center\" style=\"width: 80%; font-weight: bold;\" />", array('class' => 'align_center'));
			$table->construct_cell($custom);
			$table->construct_cell($file);
			$table->construct_cell($enabled);
			$table->construct_cell($groups, array('style' => 'text-align: center'));
			$table->construct_cell($controls, array('style' => 'text-align: center'));
			$table->construct_row();
		}
		
		$table->output($lang->portal_manage_blocks);
		
		$buttons[] = $form->generate_submit_button($lang->portal_blocks_updateposition);
		$form->output_submit_wrapper($buttons);
		$form->end();
	}
	
	$page->output_footer();
}

if($mybb->input['action'] == "add")
{
	$plugins->run_hooks("admin_portal_blocks_add");
	
	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->portal_blocks_no_title;
		}
		
		if(!$errors)
		{
			$visibles = array();
			foreach($mybb->input['visible'] as $visible)
			{
				$visibles[] = $visible;
			}
			$visible = implode(",", $visibles);
			
			$insert_array = array(
				"title" => $db->escape_string($mybb->input['title']),
				"zone" => intval($mybb->input['zone']),
				"position" => intval($mybb->input['position']),
				"custom" => intval($mybb->input['custom']),
				"file" => $db->escape_string($mybb->input['file']),
				"content" => $db->escape_string($mybb->input['content']),
				"enabled" => intval($mybb->input['enabled']),
				"visible" => $visible,
			);
			$db->insert_query("portal_blocks", $insert_array);
			
			$plugins->run_hooks("admin_portal_blocks_add_commit");
			
			// Log admin action
			log_admin_action($insert_array['title']);
			
			flash_message($lang->portal_success_block_added, 'success');
			admin_redirect("index.php?module=portal/blocks");
		}
	}
	
	$page->add_breadcrumb_item($lang->portal_blocks_add);
	$page->output_header($lang->portal_blocks_add);	
	$page->output_nav_tabs($sub_tabs, 'add_block');
	
	$form = new Form("index.php?module=portal/blocks&amp;action=add", "post");

	if($errors)
	{
		$page->output_inline_error($errors);
		$block_data = $mybb->input;
	}
	else
	{
		$block_data['title'] = "";
		$block_data['content'] = "";
		$block_data['zone'] = "0";
		$block_data['position'] = "";
		$block_data['custom'] = "";
		$block_data['file'] = "0";
		$block_data['enabled'] = 1;
		$block_data['visible'] = "";
	}

	$block_zone = array(
		'0' => $lang->portal_blocks_zoneleft,
		'1' => $lang->portal_blocks_zonecenter,
		'2' => $lang->portal_blocks_zoneright
	);
	
	$block_file = array();
	$block_file[] = $lang->portal_blocks_nofile;
	unset($filearray);
	$filearray = array();
	$handle = opendir(MYBB_ROOT.'portal/blocks/');
	while (false !== ($file = readdir($handle))) {
		if ($file != "." AND $file != ".." AND !preg_match("/html/i", $file)) {
			$file = str_replace("block_","",$file);
			$file = str_replace(".php","",$file);
			
			$filearray[] = $file;
		}
	}
	closedir($handle);
	@sort($filearray);
	@reset($filearray);
	foreach($filearray as $file) {
		$block_file[$file] = $file;
	}
	
	$query = $db->simple_select("usergroups", "gid, title");
	while($usergroup = $db->fetch_array($query))
	{
		$usergroups[$usergroup['gid']] = $usergroup['title'];
	}
	
	echo '
	<script type="text/javascript">
	function checksel()
	{
		var selObj = document.getElementById("file").selectedIndex;
		var selVal = document.getElementById("file")[selObj].value;
		if(selVal == "0")
		{
			document.getElementById("bcontent").disabled=false;
		}
		else
		{
			document.getElementById("bcontent").disabled=true;
			document.getElementById("bcontent").value="";
		}
	}
	window.onload = checksel;
	</script>
	';

	$form_container = new FormContainer($lang->portal_blocks_add);
	$form_container->output_row($lang->portal_blocks_title." <em>*</em>", "", $form->generate_text_box('title', $block_data['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->portal_blocks_zone, "", $form->generate_select_box('zone', $block_zone, $block_data['zone'], array('checked' => $block_data['zone'], 'id' => 'zone')), 'zone');
	$form_container->output_row($lang->portal_blocks_position, "", $form->generate_text_box('position', $block_data['position'], array('id' => 'position')), 'position');
	$form_container->output_row($lang->portal_blocks_custom, $lang->portal_blocks_custom_desc, $form->generate_radio_button('custom', '1', $lang->portal_blocks_yes)."<br />\n".$form->generate_radio_button('custom', '0', $lang->portal_blocks_no));
	$form_container->output_row($lang->portal_blocks_file, $lang->portal_blocks_file_desc, $form->generate_select_box('file', $block_file, $block_data['file'], array('checked' => $block_data['file'], 'id' => 'file" onChange="return checksel()')), 'file');
	$form_container->output_row($lang->portal_blocks_content, $lang->portal_blocks_content_desc, $form->generate_text_area('content', $block_data['content'], array('id' => 'bcontent', 'style' => 'width: 450px; height: 250px;')), 'bcontent');
	$form_container->output_row($lang->portal_blocks_enabled, "", $form->generate_radio_button('enabled', '1', $lang->portal_blocks_yes)."<br />\n".$form->generate_radio_button('enabled', '0', $lang->portal_blocks_no));
	$form_container->output_row($lang->portal_blocks_visible, $lang->portal_blocks_visible_desc, $form->generate_select_box('visible[]', $usergroups, $block_data['visible'], array('multiple' => 'multiple', 'id' => 'visible')), 'visible');
	
	$form_container->end();
	
	$buttons[] = $form->generate_submit_button($lang->portal_blocks_add);
	$form->output_submit_wrapper($buttons);
	$form->end();
	
	$page->output_footer();	
}

if($mybb->input['action'] == "edit")
{
	$plugins->run_hooks("admin_portal_blocks_edit");
	
	if(!$mybb->input['id'])
	{
		flash_message($lang->portal_blocks_invalidid, 'error');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$query = $db->simple_select("portal_blocks", "*", "id='{$mybb->input['id']}'");
	$block_data = $db->fetch_array($query);
	if(!$block_data)
	{
		flash_message($lang->portal_blocks_invalidid, 'error');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$id = intval($mybb->input['id']);
	
	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->portal_blocks_no_title;
		}
		
		if(!$errors)
		{
			$visibles = array();
			foreach($mybb->input['visible'] as $visible)
			{
				$visibles[] = $visible;
			}
			$visible = implode(",", $visibles);
			
			$update_array = array(
				"title" => $db->escape_string($mybb->input['title']),
				"zone" => intval($mybb->input['zone']),
				"position" => intval($mybb->input['position']),
				"custom" => intval($mybb->input['custom']),
				"file" => $db->escape_string($mybb->input['file']),
				"content" => $db->escape_string($mybb->input['content']),
				"enabled" => intval($mybb->input['enabled']),
				"visible" => $visible,
			);
			$db->update_query("portal_blocks", $update_array, "id='{$id}'");
			
			$plugins->run_hooks("admin_portal_blocks_edit_commit");
			
			// Log admin action
			log_admin_action($update_array['title']);
			
			flash_message($lang->portal_success_block_edited, 'success');
			admin_redirect("index.php?module=portal/blocks");
		}
	}
	
	$page->add_breadcrumb_item($lang->portal_blocks_edit);
	$page->output_header($lang->portal_blocks_edit);	
	$page->output_nav_tabs($sub_tabs, 'edit_block');
	
	$form = new Form("index.php?module=portal/blocks&amp;action=edit", "post");
	echo $form->generate_hidden_field("id", $id);

	if($errors)
	{
		$page->output_inline_error($errors);
		$block_data = $mybb->input;
	}

	$block_zone = array(
		'0' => $lang->portal_blocks_zoneleft,
		'1' => $lang->portal_blocks_zonecenter,
		'2' => $lang->portal_blocks_zoneright
	);
	
	$block_file = array();
	$block_file[] = $lang->portal_blocks_nofile;
	unset($filearray);
	$filearray = array();
	$handle = opendir(MYBB_ROOT.'portal/blocks/');
	while (false !== ($file = readdir($handle))) {
		if ($file != "." AND $file != ".." AND !preg_match("/html/i",$file) AND $file!="block_admin.php") {
			$file = str_replace("block_","",$file);
			$file = str_replace(".php","",$file);
			
			$filearray[] = $file;
		}
	}
	closedir($handle);
	@sort($filearray);
	@reset($filearray);
	foreach($filearray as $file) {
		$block_file[$file] = $file;
	}
	
	$blockcustom_yes = [];
	$blockcustom_no  = [];
	
	if($block_data['custom'] == "0")
	{
		$blockcustom_no['checked'] = true;
	}
	else
	{
		$blockcustom_yes['checked'] = true;
	}
	
	$blockenabled_yes = [];
	$blockenabled_no  = [];
	
	if($block_data['enabled'] == "0")
	{
		$blockenabled_no['checked'] = true;
	}
	else
	{
		$blockenabled_yes['checked'] = true;
	}
	
	$block_data['visible'] = explode(",", $block_data['visible']);
	
	$query = $db->simple_select("usergroups", "gid, title");
	while($usergroup = $db->fetch_array($query))
	{
		$usergroups[$usergroup['gid']] = $usergroup['title'];
	}
	
	echo '
	<script type="text/javascript">
	function checksel()
	{
		var selObj = document.getElementById("file").selectedIndex;
		var selVal = document.getElementById("file")[selObj].value;
		if(selVal == "0")
		{
			document.getElementById("bcontent").disabled=false;
		}
		else
		{
			document.getElementById("bcontent").disabled=true;
			document.getElementById("bcontent").value="";
		}
	}
	window.onload = checksel;
	</script>
	';

	$form_container = new FormContainer($lang->portal_blocks_edit);
	$form_container->output_row($lang->portal_blocks_title." <em>*</em>", "", $form->generate_text_box('title', $block_data['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->portal_blocks_zone, "", $form->generate_select_box('zone', $block_zone, $block_data['zone'], array('checked' => $block_data['zone'], 'id' => 'zone')), 'zone');
	$form_container->output_row($lang->portal_blocks_position, "", $form->generate_text_box('position', $block_data['position'], array('id' => 'position')), 'position');
	$form_container->output_row($lang->portal_blocks_custom, $lang->portal_blocks_custom_desc, $form->generate_radio_button('custom', '1', $lang->portal_blocks_yes, $blockcustom_yes)."<br />\n".$form->generate_radio_button('custom', '0', $lang->portal_blocks_no, $blockcustom_no));
	$form_container->output_row($lang->portal_blocks_file, $lang->portal_blocks_file_desc, $form->generate_select_box('file', $block_file, $block_data['file'], array('checked' => $block_data['file'], 'id' => 'file" onChange="return checksel()')), 'file');
	$form_container->output_row($lang->portal_blocks_content, $lang->portal_blocks_content_desc, $form->generate_text_area('content', $block_data['content'], array('id' => 'bcontent', 'style' => 'width: 450px; height: 250px;')), 'bcontent');
	$form_container->output_row($lang->portal_blocks_enabled, "", $form->generate_radio_button('enabled', '1', $lang->portal_blocks_yes, $blockenabled_yes)."<br />\n".$form->generate_radio_button('enabled', '0', $lang->portal_blocks_no, $blockenabled_no));
	$form_container->output_row($lang->portal_blocks_visible, $lang->portal_blocks_visible_desc, $form->generate_select_box('visible[]', $usergroups, $block_data['visible'], array('checked' => $block_data['visible'], 'multiple' => 'multiple', 'id' => 'visible')), 'visible');
	
	$form_container->end();
	
	$buttons[] = $form->generate_submit_button($lang->portal_blocks_edit);
	$form->output_submit_wrapper($buttons);
	$form->end();
	
	$page->output_footer();	
}

if($mybb->input['action'] == "delete")
{
	$plugins->run_hooks("admin_portal_blocks_delete");
	
	if(!$mybb->input['id'])
	{
		flash_message($lang->portal_blocks_invalidid, 'error');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$query = $db->simple_select("portal_blocks", "*", "id='{$mybb->input['id']}'");
	$block_data = $db->fetch_array($query);
	if(!$block_data)
	{
		flash_message($lang->portal_blocks_invalidid, 'error');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$id = intval($mybb->input['id']);
	
	$db->delete_query("portal_blocks", "id='{$id}'");
	$plugins->run_hooks("admin_portal_blocks_delete_commit");
	
	// Log admin action
	log_admin_action($id);
	
	flash_message($lang->portal_success_block_deleted, 'success');
	admin_redirect("index.php?module=portal/blocks");
}

if($mybb->input['action'] == "enable")
{
	$plugins->run_hooks("admin_portal_blocks_enable");
	
	if(!$mybb->input['id'])
	{
		flash_message($lang->portal_blocks_invalidid, 'error');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$query = $db->simple_select("portal_blocks", "*", "id='{$mybb->input['id']}'");
	$block_data = $db->fetch_array($query);
	if(!$block_data)
	{
		flash_message($lang->portal_blocks_invalidid, 'error');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$id = intval($mybb->input['id']);
	
	$update_array = array(
		"enabled" => '1'
	);
	$db->update_query("portal_blocks", $update_array, "id='{$id}'");
	
	$plugins->run_hooks("admin_portal_blocks_enable_commit");
	
	// Log admin action
	log_admin_action($id);
	
	flash_message($lang->portal_success_block_enabled, 'success');
	admin_redirect("index.php?module=portal/blocks");
}

if($mybb->input['action'] == "disable")
{
	$plugins->run_hooks("admin_portal_blocks_disable");
	
	if(!$mybb->input['id'])
	{
		flash_message($lang->portal_blocks_invalidid, 'error');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$query = $db->simple_select("portal_blocks", "*", "id='{$mybb->input['id']}'");
	$block_data = $db->fetch_array($query);
	if(!$block_data)
	{
		flash_message($lang->portal_blocks_invalidid, 'error');
		admin_redirect("index.php?module=portal/blocks");
	}
	
	$id = intval($mybb->input['id']);
	
	$update_array = array(
		"enabled" => '0'
	);
	$db->update_query("portal_blocks", $update_array, "id='{$id}'");
	
	$plugins->run_hooks("admin_portal_blocks_disable_commit");
	
	// Log admin action
	log_admin_action($id);
	
	flash_message($lang->portal_success_block_disabled, 'success');
	admin_redirect("index.php?module=portal/blocks");
}
?>