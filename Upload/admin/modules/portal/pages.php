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

require_once MYBB_ROOT."portal/inc/portal.class.php";
$proportal = new ProPortal;

// Construct portal settings
$options = array(
	"order_by" => "id",
	"order_dir" => "ASC"
);
$query = $db->simple_select("portal_settings", "*", "", $options);
while($setting = $db->fetch_array($query))
{
	$setting['value'] = str_replace("\"", "\\\"", $setting['value']);
	$settings[$setting['name']] = $setting['value'];
}
$proportal->settings = &$settings;

$page->add_breadcrumb_item($lang->portal_page_management, "index.php?module=portal/pages");

if($mybb->input['action'] == "add" || $mybb->input['action'] == "edit" || !$mybb->input['action'])
{
	if($mybb->input['action'] == "edit")
	{
		$sub_tabs['edit_page'] = array(
			'title' => $lang->portal_pages_edit,
			'link' => "index.php?module=portal/pages&amp;action=edit&amp;id=".$mybb->input['id'],
			'description' => $lang->portal_pages_edit_description
		);
	} else {
		$sub_tabs['pages'] = array(
			'title' => $lang->portal_pages_management,
			'link' => "index.php?module=portal/pages",
			'description' => $lang->portal_pages_description
		);
		$sub_tabs['add_page'] = array(
			'title' => $lang->portal_pages_add,
			'link' => "index.php?module=portal/pages&amp;action=add",
			'description' => $lang->portal_pages_add_description
		);
	}
}

$plugins->run_hooks("admin_portal_pages_begin");

if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_portal_pages_start");
	
	$page->output_header($lang->portal_page_management);

	$page->output_nav_tabs($sub_tabs, 'pages');
	
	$query = $db->simple_select("portal_pages", "*");
	$pages = $db->fetch_array($query);
	
	if(!$pages)
	{
		$table = new Table;
		$table->construct_header($lang->portal_pages, array('width' => '100%'));
		
		$table->construct_cell($lang->portal_pages_nopage);
		$table->construct_row();
		
		$table->output($lang->portal_manage_pages);
	} else {
		$table = new Table;
		$table->construct_header($lang->portal_pages_title, array('width' => '35%'));
		$table->construct_header($lang->portal_pages_name, array('width' => '20%'));
		$table->construct_header($lang->portal_pages_isenabled, array('width' => '15%'));
		$table->construct_header($lang->portal_pages_visible_s, array('width' => '15%'));
		$table->construct_header($lang->portal_pages_options, array('width' => '15%'));
		
		$query = $db->simple_select("portal_pages", "*", "", array('order_by' => 'id'));
		while($mypage = $db->fetch_array($query))
		{
			if($mypage['enabled'] == "0"){ $enabled = "<span style=\"color:red;\">".$lang->portal_pages_no."</span>"; }
			elseif($mypage['enabled'] == "1"){ $enabled = "<span style=\"color:green;\">".$lang->portal_pages_yes."</span>"; }
			
			// Build visible groups popup menu
			if(!is_array($groupscache))
			{
				$groupscache = $cache->read("usergroups");
			}
			$groups = explode(",", $mypage['visible']);
			$popup = new PopupMenu("group_{$mypage['id']}", $lang->portal_pages_seegroup);
			foreach($groups as $usergroups)
			{
				$usergroup = $groupscache[$usergroups];
				$popup->add_item($usergroup['title'], "");
			}
			$groups = $popup->fetch();
			
			// Build options popup menu
			$popup = new PopupMenu("block_{$mypage['id']}", $lang->portal_pages_options);
			$popup->add_item($lang->portal_pages_editpage, "index.php?module=portal/pages&amp;action=edit&amp;id={$mypage['id']}");
			$popup->add_item($lang->portal_pages_deletepage, "index.php?module=portal/pages&amp;action=delete&amp;id={$mypage['id']}");
			if($mypage['enabled'] == "0"){ $popup->add_item($lang->portal_pages_enablepage, "index.php?module=portal/pages&amp;action=enable&amp;id={$mypage['id']}"); }
			elseif($mypage['enabled'] == "1"){ $popup->add_item($lang->portal_pages_disablepage, "index.php?module=portal/pages&amp;action=disable&amp;id={$mypage['id']}"); }
			$popup->add_item($lang->portal_pages_viewpage, "{$mybb->settings['bburl']}/portal.php?pages={$mypage['name']}\" target=\"_blank");
			$controls = $popup->fetch();
			
			$table->construct_cell($mypage['title']);
			$table->construct_cell($mypage['name']);	
			$table->construct_cell($enabled);
			$table->construct_cell($groups, array('style' => 'text-align: center'));
			$table->construct_cell($controls, array('style' => 'text-align: center'));
			$table->construct_row();
		}
		
		$table->output($lang->portal_manage_pages);
	}
	
	$page->output_footer();
}

if($mybb->input['action'] == "add")
{
	$plugins->run_hooks("admin_portal_pages_add");
	
	if($mybb->request_method == "post")
	{
		$checkquery = $db->simple_select('portal_pages','*','name LIKE "'.$db->escape_string(trim($mybb->input['name'])).'"');
		$check = $db->fetch_array($checkquery);
		
		if($check)
		{
			$errors[] = $lang->portal_pages_check;
		}
		
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->portal_pages_no_title;
		}
		
		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->portal_pages_no_name;
		}
		
		if(!trim($mybb->input['content']))
		{
			$errors[] = $lang->portal_pages_no_content;
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
				"name" => $db->escape_string(trim($mybb->input['name'])),
				"content" => $db->escape_string(nl2br($mybb->input['content'])),
				"enabled" => intval($mybb->input['enabled']),
				"visible" => $visible,
			);
			$db->insert_query("portal_pages", $insert_array);
			
			$plugins->run_hooks("admin_portal_pages_add_commit");
			
			// Log admin action
			log_admin_action($insert_array['title']);
			
			flash_message($lang->portal_success_page_added, 'success');
			admin_redirect("index.php?module=portal/pages");
		}
	}
	
	$page->add_breadcrumb_item($lang->portal_pages_add);
	$page->output_header($lang->portal_pages_add);	
	$page->output_nav_tabs($sub_tabs, 'add_page');
	
	$form = new Form("index.php?module=portal/pages&amp;action=add", "post");

	if($errors)
	{
		$page->output_inline_error($errors);
		$page_data = $mybb->input;
	}
	else
	{
		$page_data['title'] = "";
		$page_data['content'] = "";
		$page_data['name'] = "";
		$page_data['enabled'] = 1;
		$page_data['visible'] = "";
	}
	
	if($page_data['enabled'] == "0")
	{
		$pageenabled_no['checked'] = true;
	}
	else
	{
		$pageenabled_yes['checked'] = true;
	}
	
	$query = $db->simple_select("usergroups", "gid, title");
	while($usergroup = $db->fetch_array($query))
	{
		$usergroups[$usergroup['gid']] = $usergroup['title'];
	}
	
	if($proportal->settings['showeditor'] == "1")
	{
		echo'<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>';
	}

	$form_container = new FormContainer($lang->portal_pages_add);
	$form_container->output_row($lang->portal_pages_title." <em>*</em>", "", $form->generate_text_box('title', $page_data['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->portal_pages_name." <em>*</em>", $lang->portal_pages_name_desc, $form->generate_text_box('name', $page_data['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->portal_pages_content." <em>*</em>", "", $form->generate_text_area('content', $page_data['content'], array('id' => 'pcontent', 'style' => 'width: 650px; height: 350px;')), 'pcontent');
	$form_container->output_row($lang->portal_pages_enabled, "", $form->generate_radio_button('enabled', '1', $lang->portal_pages_yes, $pageenabled_yes)."<br />\n".$form->generate_radio_button('enabled', '0', $lang->portal_pages_no, $pageenabled_no));
	$form_container->output_row($lang->portal_pages_visible, $lang->portal_pages_visible_desc, $form->generate_select_box('visible[]', $usergroups, $page_data['visible'], array('multiple' => 'multiple', 'id' => 'visible')), 'visible');
	
	$form_container->end();
	
	$buttons[] = $form->generate_submit_button($lang->portal_pages_add);
	$form->output_submit_wrapper($buttons);
	$form->end();
	
	$page->output_footer();	
}

if($mybb->input['action'] == "edit")
{
	$plugins->run_hooks("admin_portal_pages_edit");
	
	if(!$mybb->input['id'])
	{
		flash_message($lang->portal_pages_invalidid, 'error');
		admin_redirect("index.php?module=portal/pages");
	}
	
	$query = $db->simple_select("portal_pages", "*", "id='{$mybb->input['id']}'");
	$page_data = $db->fetch_array($query);
	if(!$page_data)
	{
		flash_message($lang->portal_pages_invalidid, 'error');
		admin_redirect("index.php?module=portal/pages");
	}
	
	$id = intval($mybb->input['id']);
	
	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->portal_pages_no_title;
		}
		
		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->portal_pages_no_name;
		}
		
		if(!trim($mybb->input['content']))
		{
			$errors[] = $lang->portal_pages_no_content;
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
				"name" => $db->escape_string($mybb->input['name']),
				"content" => $db->escape_string(nl2br($mybb->input['content'])),
				"enabled" => intval($mybb->input['enabled']),
				"visible" => $visible,
			);
			$db->update_query("portal_pages", $update_array, "id='{$id}'");
			
			$plugins->run_hooks("admin_portal_pages_edit_commit");
			
			// Log admin action
			log_admin_action($update_array['title']);
			
			flash_message($lang->portal_success_page_edited, 'success');
			admin_redirect("index.php?module=portal/pages");
		}
	}
	
	$page->add_breadcrumb_item($lang->portal_pages_edit);
	$page->output_header($lang->portal_pages_edit);	
	$page->output_nav_tabs($sub_tabs, 'edit_page');
	
	$form = new Form("index.php?module=portal/pages&amp;action=edit", "post");
	echo $form->generate_hidden_field("id", $id);

	if($errors)
	{
		$page->output_inline_error($errors);
		$page_data = $mybb->input;
	}
	
	if($page_data['enabled'] == "0")
	{
		$pageenabled_no['checked'] = true;
	}
	else
	{
		$pageenabled_yes['checked'] = true;
	}
	
	$page_data['visible'] = explode(",", $page_data['visible']);
	
	$query = $db->simple_select("usergroups", "gid, title");
	while($usergroup = $db->fetch_array($query))
	{
		$usergroups[$usergroup['gid']] = $usergroup['title'];
	}
	
	if($proportal->settings['showeditor'] == "1")
	{
		echo'<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>';
	}

	$form_container = new FormContainer($lang->portal_pages_edit);
	$form_container->output_row($lang->portal_pages_title." <em>*</em>", "", $form->generate_text_box('title', $page_data['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->portal_pages_name." <em>*</em>", $lang->portal_pages_name_desc, $form->generate_text_box('name', $page_data['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->portal_pages_content." <em>*</em>", "", $form->generate_text_area('content', $page_data['content'], array('id' => 'pcontent', 'style' => 'width: 650px; height: 350px;')), 'pcontent');
	$form_container->output_row($lang->portal_pages_enabled, "", $form->generate_radio_button('enabled', '1', $lang->portal_pages_yes, $pageenabled_yes)."<br />\n".$form->generate_radio_button('enabled', '0', $lang->portal_pages_no, $pageenabled_no));
	$form_container->output_row($lang->portal_pages_visible, $lang->portal_pages_visible_desc, $form->generate_select_box('visible[]', $usergroups, $page_data['visible'], array('checked' => $block_data['visible'], 'multiple' => 'multiple', 'id' => 'visible')), 'visible');
	
	$form_container->end();
	
	$buttons[] = $form->generate_submit_button($lang->portal_pages_edit);
	$form->output_submit_wrapper($buttons);
	$form->end();
	
	$page->output_footer();	
}

if($mybb->input['action'] == "delete")
{
	$plugins->run_hooks("admin_portal_pages_delete");
	
	if(!$mybb->input['id'])
	{
		flash_message($lang->portal_pages_invalidid, 'error');
		admin_redirect("index.php?module=portal/pages");
	}
	
	$query = $db->simple_select("portal_pages", "*", "id='{$mybb->input['id']}'");
	$page_data = $db->fetch_array($query);
	if(!$page_data)
	{
		flash_message($lang->portal_pages_invalidid, 'error');
		admin_redirect("index.php?module=portal/pages");
	}
	
	$id = intval($mybb->input['id']);
	
	$db->delete_query("portal_pages", "id='{$id}'");
	$plugins->run_hooks("admin_portal_pages_delete_commit");
	
	// Log admin action
	log_admin_action($id);
	
	flash_message($lang->portal_success_page_deleted, 'success');
	admin_redirect("index.php?module=portal/pages");
}

if($mybb->input['action'] == "enable")
{
	$plugins->run_hooks("admin_portal_pages_enable");
	
	if(!$mybb->input['id'])
	{
		flash_message($lang->portal_pages_invalidid, 'error');
		admin_redirect("index.php?module=portal/pages");
	}
	
	$query = $db->simple_select("portal_pages", "*", "id='{$mybb->input['id']}'");
	$page_data = $db->fetch_array($query);
	if(!$page_data)
	{
		flash_message($lang->portal_pages_invalidid, 'error');
		admin_redirect("index.php?module=portal/pages");
	}
	
	$id = intval($mybb->input['id']);
	
	$update_array = array(
		"enabled" => '1'
	);
	$db->update_query("portal_pages", $update_array, "id='{$id}'");
	
	$plugins->run_hooks("admin_portal_pages_enable_commit");
	
	// Log admin action
	log_admin_action($id);
	
	flash_message($lang->portal_success_page_enabled, 'success');
	admin_redirect("index.php?module=portal/pages");
}

if($mybb->input['action'] == "disable")
{
	$plugins->run_hooks("admin_portal_pages_disable");
	
	if(!$mybb->input['id'])
	{
		flash_message($lang->portal_pages_invalidid, 'error');
		admin_redirect("index.php?module=portal/pages");
	}
	
	$query = $db->simple_select("portal_pages", "*", "id='{$mybb->input['id']}'");
	$page_data = $db->fetch_array($query);
	if(!$page_data)
	{
		flash_message($lang->portal_pages_invalidid, 'error');
		admin_redirect("index.php?module=portal/pages");
	}
	
	$id = intval($mybb->input['id']);
	
	$update_array = array(
		"enabled" => '0'
	);
	$db->update_query("portal_pages", $update_array, "id='{$id}'");
	
	$plugins->run_hooks("admin_portal_pages_disable_commit");
	
	// Log admin action
	log_admin_action($id);
	
	flash_message($lang->portal_success_page_disabled, 'success');
	admin_redirect("index.php?module=portal/pages");
}
?>