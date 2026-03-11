<?php
/***************************************************************
 * ProBlog - AdminCP Category Management
 ***************************************************************/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$lang->load('problog_categories');

$page->add_breadcrumb_item($lang->blog_categories_management, "index.php?module=problog-categories");

if($mybb->input['action'] == "add" || $mybb->input['action'] == "edit")
{
	if($mybb->input['action'] == "edit")
	{
		$query = $db->simple_select("blog_categories", "*", "cid='".(int)$mybb->input['cid']."'");
		$category = $db->fetch_array($query);
		$page->add_breadcrumb_item($lang->blog_categories_edit);
		$page->output_header($lang->blog_categories_edit);
	}
	else
	{
		$page->add_breadcrumb_item($lang->blog_categories_add);
		$page->output_header($lang->blog_categories_add);
	}

	$form = new Form("index.php?module=problog-categories&amp;action=do_add", "post");
	if($mybb->input['action'] == "edit")
	{
		echo $form->generate_hidden_field("cid", $category['cid']);
		echo $form->generate_hidden_field("edit", 1);
	}

	$form_container = new FormContainer($mybb->input['action'] == "edit" ? $lang->blog_categories_edit : $lang->blog_categories_add);
	$form_container->output_row($lang->blog_categories_name, $lang->blog_categories_name_desc, $form->generate_text_box('name', $category['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->blog_categories_description, $lang->blog_categories_description_desc, $form->generate_text_area('description', $category['description'], array('id' => 'description')), 'description');

	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->save);
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "do_add")
{
	if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=problog-categories");
	}

	$category_data = array(
		"name" => $db->escape_string($mybb->input['name']),
		"description" => $db->escape_string($mybb->input['description']),
	);

	if($mybb->input['edit'])
	{
		$db->update_query("blog_categories", $category_data, "cid='".(int)$mybb->input['cid']."'");
		flash_message($lang->blog_categories_success_edited, 'success');
	}
	else
	{
		$db->insert_query("blog_categories", $category_data);
		flash_message($lang->blog_categories_success_added, 'success');
	}

	admin_redirect("index.php?module=problog-categories");
}

if($mybb->input['action'] == "delete")
{
	if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=problog-categories");
	}

	$db->delete_query("blog_categories", "cid='".(int)$mybb->input['cid']."'");
	flash_message($lang->blog_categories_success_deleted, 'success');
	admin_redirect("index.php?module=problog-categories");
}

if(!$mybb->input['action'])
{
	$page->output_header($lang->blog_categories_management);

	$sub_tabs['categories'] = array(
		'title' => $lang->blog_categories_management,
		'link' => "index.php?module=problog-categories",
		'description' => $lang->blog_categories_management_desc
	);
	$sub_tabs['add_category'] = array(
		'title' => $lang->blog_categories_add,
		'link' => "index.php?module=problog-categories&amp;action=add",
	);

	$page->output_nav_tabs($sub_tabs, 'categories');

	$table = new Table;
	$table->construct_header($lang->blog_categories_name);
	$table->construct_header($lang->options, array("class" => "align_center", "width" => "150"));

	$query = $db->simple_select("blog_categories", "*", "", array("order_by" => "name", "order_dir" => "ASC"));
	while($category = $db->fetch_array($query))
	{
		$table->construct_cell(htmlspecialchars_uni($category['name']));

		$popup = new PopupMenu("category_{$category['cid']}", $lang->options);
		$popup->add_item($lang->edit, "index.php?module=problog-categories&amp;action=edit&amp;cid={$category['cid']}");
		$popup->add_item($lang->delete, "index.php?module=problog-categories&amp;action=delete&amp;cid={$category['cid']}&amp;my_post_key={$mybb->post_code}", "return confirm('{$lang->blog_categories_confirm_delete}')");
		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		$table->construct_row();
	}

	if($table->num_rows() == 0)
	{
		$table->construct_cell($lang->blog_categories_no_categories, array("colspan" => 2));
		$table->construct_row();
	}

	$table->output($lang->blog_categories_management);
	$page->output_footer();
}
?>