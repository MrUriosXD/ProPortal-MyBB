<?php
/***************************************************************
 * ProBlog - AdminCP Posts Management
 ***************************************************************/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$lang->load('problog_posts');

$page->add_breadcrumb_item($lang->blog_posts_management, "index.php?module=problog/posts");

if($mybb->input['action'] == "add" || $mybb->input['action'] == "edit")
{
	if($mybb->input['action'] == "edit")
	{
		$query = $db->simple_select("blog_posts", "*", "pid='".(int)$mybb->input['pid']."'");
		$post = $db->fetch_array($query);
		$page->add_breadcrumb_item($lang->blog_posts_edit);
		$page->output_header($lang->blog_posts_edit);
	}
	else
	{
		$page->add_breadcrumb_item($lang->blog_posts_add);
		$page->output_header($lang->blog_posts_add);
	}

	$form = new Form("index.php?module=problog/posts&amp;action=do_add", "post");
	if($mybb->input['action'] == "edit")
	{
		echo $form->generate_hidden_field("pid", $post['pid']);
		echo $form->generate_hidden_field("edit", 1);
	}

	$form_container = new FormContainer($mybb->input['action'] == "edit" ? $lang->blog_posts_edit : $lang->blog_posts_add);
	$form_container->output_row($lang->blog_posts_title, $lang->blog_posts_title_desc, $form->generate_text_box('title', $post['title'], array('id' => 'title')), 'title');
	$form_container->output_row($lang->blog_posts_content, $lang->blog_posts_content_desc, $form->generate_text_area('content', $post['content'], array('id' => 'content')), 'content');
	$form_container->output_row($lang->blog_posts_enabled, $lang->blog_posts_enabled_desc, $form->generate_yes_no_radio('enabled', $post['enabled'] ?? 1));

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
		admin_redirect("index.php?module=problog/posts");
	}

	$post_data = array(
		"title" => $db->escape_string($mybb->input['title']),
		"content" => $db->escape_string($mybb->input['content']),
		"enabled" => (int)$mybb->input['enabled'],
	);

	if($mybb->input['edit'])
	{
		$db->update_query("blog_posts", $post_data, "pid='".(int)$mybb->input['pid']."'");
		flash_message($lang->blog_posts_success_edited, 'success');
	}
	else
	{
		$post_data['uid'] = $mybb->user['uid'];
		$post_data['dateline'] = TIME_NOW;
		$db->insert_query("blog_posts", $post_data);
		flash_message($lang->blog_posts_success_added, 'success');
	}

	admin_redirect("index.php?module=problog/posts");
}

if($mybb->input['action'] == "delete")
{
	$db->delete_query("blog_posts", "pid='".(int)$mybb->input['pid']."'");
	flash_message($lang->blog_posts_success_deleted, 'success');
	admin_redirect("index.php?module=problog/posts");
}

if(!$mybb->input['action'])
{
	$page->output_header($lang->blog_posts_management);

	$sub_tabs['posts'] = array(
		'title' => $lang->blog_posts_management,
		'link' => "index.php?module=problog/posts",
		'description' => $lang->blog_posts_management_desc
	);
	$sub_tabs['add_post'] = array(
		'title' => $lang->blog_posts_add,
		'link' => "index.php?module=problog/posts&amp;action=add",
	);

	$page->output_nav_tabs($sub_tabs, 'posts');

	$table = new Table;
	$table->construct_header($lang->blog_posts_title);
	$table->construct_header($lang->blog_posts_date, array("class" => "align_center", "width" => "150"));
	$table->construct_header($lang->options, array("class" => "align_center", "width" => "150"));

	$query = $db->simple_select("blog_posts", "*", "", array("order_by" => "dateline", "order_dir" => "DESC"));
	while($post = $db->fetch_array($query))
	{
		$post['title'] = htmlspecialchars_uni($post['title']);
		$post['date'] = my_date($mybb->settings['dateformat'], $post['dateline']).", ".my_date($mybb->settings['timeformat'], $post['dateline']);

		$table->construct_cell($post['title']);
		$table->construct_cell($post['date'], array("class" => "align_center"));

		$popup = new PopupMenu("post_{$post['pid']}", $lang->options);
		$popup->add_item($lang->edit, "index.php?module=problog/posts&amp;action=edit&amp;pid={$post['pid']}");
		$popup->add_item($lang->delete, "index.php?module=problog/posts&amp;action=delete&amp;pid={$post['pid']}&amp;my_post_key={$mybb->post_code}", "return confirm('{$lang->blog_posts_confirm_delete}')");
		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		$table->construct_row();
	}

	if($table->num_rows() == 0)
	{
		$table->construct_cell($lang->blog_posts_no_posts, array("colspan" => 3));
		$table->construct_row();
	}

	$table->output($lang->blog_posts_management);
	$page->output_footer();
}
?>