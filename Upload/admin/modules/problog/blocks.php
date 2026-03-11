<?php
/***************************************************************
 * ProBlog - AdminCP Block Management
 ***************************************************************/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$lang->load("problog_blocks");

$page->add_breadcrumb_item($lang->blog_blocks_management, "index.php?module=problog-blocks");

$sub_tabs['blocks'] = array(
	'title' => $lang->blog_blocks_management,
	'link' => "index.php?module=problog-blocks",
	'description' => $lang->blog_blocks_management_desc
);

$sub_tabs['add_block'] = array(
	'title' => $lang->blog_blocks_add,
	'link' => "index.php?module=problog-blocks&amp;action=add",
	'description' => $lang->blog_blocks_add_desc
);

if($mybb->input['action'] == "edit")
{
	$sub_tabs['edit_block'] = array(
		'title' => $lang->blog_blocks_edit,
		'link' => "index.php?module=problog-blocks&amp;action=edit&amp;id=".(int)$mybb->input['id'],
		'description' => $lang->blog_blocks_edit_desc
	);
}

$plugins->run_hooks("admin_blog_blocks_begin");

if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_blog_blocks_start");

	$page->output_header($lang->blog_blocks_management);

	$page->output_nav_tabs($sub_tabs, 'blocks');

	$table = new Table;
	$table->construct_header($lang->blog_blocks_title);
	$table->construct_header($lang->blog_blocks_zone, array("class" => "align_center", "width" => "10%"));
	$table->construct_header($lang->blog_blocks_position, array("class" => "align_center", "width" => "10%"));
	$table->construct_header($lang->blog_blocks_enabled, array("class" => "align_center", "width" => "10%"));
	$table->construct_header($lang->options, array("class" => "align_center", "width" => "150"));

	$query = $db->simple_select("blog_blocks", "*", "", array("order_by" => "zone, position", "order_dir" => "ASC"));
	while($block = $db->fetch_array($query))
	{
		if($block['zone'] == "0") { $zone = $lang->blog_blocks_zoneleft; }
		elseif($block['zone'] == "1") { $zone = $lang->blog_blocks_zonecenter; }
		else { $zone = $lang->blog_blocks_zoneright; }

		if($block['enabled'] == "1")
		{
			$enabled = "<img src='styles/default/images/icons/bullet_green.png' title='Enabled' alt='Active' />";
		}
		else
		{
			$enabled = "<img src='styles/default/images/icons/bullet_red.png' title='Disabled' alt='Inactive' />";
		}

		$table->construct_cell(htmlspecialchars_uni($block['title']));
		$table->construct_cell($zone, array("class" => "align_center"));
		$table->construct_cell($block['position'], array("class" => "align_center"));
		$table->construct_cell($enabled, array("class" => "align_center"));

		$popup = new PopupMenu("block_{$block['id']}", $lang->options);
		$popup->add_item($lang->blog_blocks_edit, "index.php?module=problog-blocks&amp;action=edit&amp;id={$block['id']}");
		$popup->add_item($lang->blog_blocks_delete, "index.php?module=problog-blocks&amp;action=delete&amp;id={$block['id']}&amp;my_post_key={$mybb->post_code}", "return confirm('{$lang->blog_blocks_confirm_delete}')");
		if($block['enabled'] == "0"){ $popup->add_item($lang->blog_blocks_enable, "index.php?module=problog-blocks&amp;action=enable&amp;id={$block['id']}&amp;my_post_key={$mybb->post_code}"); }
		else { $popup->add_item($lang->blog_blocks_disable, "index.php?module=problog-blocks&amp;action=disable&amp;id={$block['id']}&amp;my_post_key={$mybb->post_code}"); }

		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		$table->construct_row();
	}

	if($table->num_rows() == 0)
	{
		$table->construct_cell($lang->blog_blocks_no_blocks, array("colspan" => 5));
		$table->construct_row();
	}

	$table->output($lang->blog_blocks_management);

	$page->output_footer();
}

if($mybb->input['action'] == "add")
{
	$plugins->run_hooks("admin_blog_blocks_add");

	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->blog_blocks_no_title;
		}

		if(!$errors)
		{
			$visibles = array();
			foreach($mybb->input['visible'] as $visible)
			{
				$visibles[] = $visible;
			}
			$visible = implode(",", $visibles);

			$new_block = array(
				"title" => $db->escape_string($mybb->input['title']),
				"zone" => (int)$mybb->input['zone'],
				"position" => (int)$mybb->input['position'],
				"custom" => (int)$mybb->input['custom'],
				"file" => $db->escape_string($mybb->input['file']),
				"content" => $db->escape_string($mybb->input['content']),
				"enabled" => (int)$mybb->input['enabled'],
				"visible" => $visible,
			);
			$db->insert_query("blog_blocks", $new_block);

			$plugins->run_hooks("admin_blog_blocks_add_commit");

			// Log admin action
			log_admin_action($new_block['title']);

			flash_message($lang->blog_success_block_added, 'success');
			admin_redirect("index.php?module=problog-blocks");
		}
	}

	$page->add_breadcrumb_item($lang->blog_blocks_add);
	$page->output_header($lang->blog_blocks_add);
	$page->output_nav_tabs($sub_tabs, 'add_block');

	$form = new Form("index.php?module=problog-blocks&amp;action=add", "post");

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$block_zone = array(
		'0' => $lang->blog_blocks_zoneleft,
		'1' => $lang->blog_blocks_zonecenter,
		'2' => $lang->blog_blocks_zoneright
	);

	$block_file = array();
	$block_file['0'] = $lang->blog_blocks_nofile;
	$handle = opendir(MYBB_ROOT.'blog/blocks/');
	while (false !== ($file = readdir($handle))) {
		if ($file != "." AND $file != ".." AND !preg_match("/html/i",$file) AND $file!="block_admin.php" AND strpos($file, 'block_') === 0) {
			$file = str_replace("block_","",$file);
			$file = str_replace(".php","",$file);
			$block_file[$file] = $file;
		}
	}
	closedir($handle);
	ksort($block_file);

	$query = $db->simple_select("usergroups", "gid, title");
	while($usergroup = $db->fetch_array($query))
	{
		$usergroups[$usergroup['gid']] = $usergroup['title'];
	}

	$form_container = new FormContainer($lang->blog_blocks_add);
	$form_container->output_row($lang->blog_blocks_title." <em>*</em>", "", $form->generate_text_box('title', $mybb->input['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->blog_blocks_zone, "", $form->generate_select_box('zone', $block_zone, $mybb->input['zone'], array('id' => 'zone')), 'zone');
	$form_container->output_row($lang->blog_blocks_position, "", $form->generate_text_box('position', $mybb->input['position'], array('id' => 'position')), 'position');
	$form_container->output_row($lang->blog_blocks_custom, $lang->blog_blocks_custom_desc, $form->generate_yes_no_radio('custom', $mybb->input['custom'] ?? 0));
	$form_container->output_row($lang->blog_blocks_file, $lang->blog_blocks_file_desc, $form->generate_select_box('file', $block_file, $mybb->input['file'], array('id' => 'file')), 'file');
	$form_container->output_row($lang->blog_blocks_content, $lang->blog_blocks_content_desc, $form->generate_text_area('content', $mybb->input['content'], array('id' => 'bcontent', 'style' => 'width: 450px; height: 250px;')), 'bcontent');
	$form_container->output_row($lang->blog_blocks_enabled, "", $form->generate_yes_no_radio('enabled', $mybb->input['enabled'] ?? 1));
	$form_container->output_row($lang->blog_blocks_visible, $lang->blog_blocks_visible_desc, $form->generate_select_box('visible[]', $usergroups, $mybb->input['visible'], array('multiple' => 'multiple', 'id' => 'visible')), 'visible');

	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->save);
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "edit")
{
	$plugins->run_hooks("admin_blog_blocks_edit");

	$query = $db->simple_select("blog_blocks", "*", "id='".(int)$mybb->input['id']."'");
	$block_data = $db->fetch_array($query);
	if(!$block_data)
	{
		flash_message($lang->blog_blocks_invalidid, 'error');
		admin_redirect("index.php?module=problog-blocks");
	}

	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['title']))
		{
			$errors[] = $lang->blog_blocks_no_title;
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
				"zone" => (int)$mybb->input['zone'],
				"position" => (int)$mybb->input['position'],
				"custom" => (int)$mybb->input['custom'],
				"file" => $db->escape_string($mybb->input['file']),
				"content" => $db->escape_string($mybb->input['content']),
				"enabled" => (int)$mybb->input['enabled'],
				"visible" => $visible,
			);
			$db->update_query("blog_blocks", $update_array, "id='".(int)$mybb->input['id']."'");

			$plugins->run_hooks("admin_blog_blocks_edit_commit");

			log_admin_action($update_array['title']);

			flash_message($lang->blog_success_block_edited, 'success');
			admin_redirect("index.php?module=problog-blocks");
		}
	}

	$page->add_breadcrumb_item($lang->blog_blocks_edit);
	$page->output_header($lang->blog_blocks_edit);
	$page->output_nav_tabs($sub_tabs, 'edit_block');

	$form = new Form("index.php?module=problog-blocks&amp;action=edit", "post");
	echo $form->generate_hidden_field("id", $block_data['id']);

	if($errors)
	{
		$page->output_inline_error($errors);
	}

	$block_zone = array(
		'0' => $lang->blog_blocks_zoneleft,
		'1' => $lang->blog_blocks_zonecenter,
		'2' => $lang->blog_blocks_zoneright
	);

	$block_file = array();
	$block_file['0'] = $lang->blog_blocks_nofile;
	$handle = opendir(MYBB_ROOT.'blog/blocks/');
	while (false !== ($file = readdir($handle))) {
		if ($file != "." AND $file != ".." AND !preg_match("/html/i",$file) AND $file!="block_admin.php" AND strpos($file, 'block_') === 0) {
			$file = str_replace("block_","",$file);
			$file = str_replace(".php","",$file);
			$block_file[$file] = $file;
		}
	}
	closedir($handle);
	ksort($block_file);

	$query = $db->simple_select("usergroups", "gid, title");
	while($usergroup = $db->fetch_array($query))
	{
		$usergroups[$usergroup['gid']] = $usergroup['title'];
	}

	$form_container = new FormContainer($lang->blog_blocks_edit);
	$form_container->output_row($lang->blog_blocks_title." <em>*</em>", "", $form->generate_text_box('title', $block_data['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->blog_blocks_zone, "", $form->generate_select_box('zone', $block_zone, $block_data['zone'], array('id' => 'zone')), 'zone');
	$form_container->output_row($lang->blog_blocks_position, "", $form->generate_text_box('position', $block_data['position'], array('id' => 'position')), 'position');
	$form_container->output_row($lang->blog_blocks_custom, $lang->blog_blocks_custom_desc, $form->generate_yes_no_radio('custom', $block_data['custom']));
	$form_container->output_row($lang->blog_blocks_file, $lang->blog_blocks_file_desc, $form->generate_select_box('file', $block_file, $block_data['file'], array('id' => 'file')), 'file');
	$form_container->output_row($lang->blog_blocks_content, $lang->blog_blocks_content_desc, $form->generate_text_area('content', $block_data['content'], array('id' => 'bcontent', 'style' => 'width: 450px; height: 250px;')), 'bcontent');
	$form_container->output_row($lang->blog_blocks_enabled, "", $form->generate_yes_no_radio('enabled', $block_data['enabled']));
	$form_container->output_row($lang->blog_blocks_visible, $lang->blog_blocks_visible_desc, $form->generate_select_box('visible[]', $usergroups, explode(",", $block_data['visible']), array('multiple' => 'multiple', 'id' => 'visible')), 'visible');

	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->save);
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "delete")
{
	verify_post_check($mybb->input['my_post_key']);
	$db->delete_query("blog_blocks", "id='".(int)$mybb->input['id']."'");
	flash_message($lang->blog_success_block_deleted, 'success');
	admin_redirect("index.php?module=problog-blocks");
}

if($mybb->input['action'] == "enable")
{
    verify_post_check($mybb->input['my_post_key']);
	$db->update_query("blog_blocks", array("enabled" => '1'), "id='".(int)$mybb->input['id']."'");
	flash_message($lang->blog_success_block_enabled, 'success');
	admin_redirect("index.php?module=problog-blocks");
}

if($mybb->input['action'] == "disable")
{
    verify_post_check($mybb->input['my_post_key']);
	$db->update_query("blog_blocks", array("enabled" => '0'), "id='".(int)$mybb->input['id']."'");
	flash_message($lang->blog_success_block_disabled, 'success');
	admin_redirect("index.php?module=problog-blocks");
}
?>