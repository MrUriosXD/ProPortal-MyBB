<?php
/***************************************************************
 * ProBlog - AdminCP Reports Management
 ***************************************************************/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$lang->load('problog_reports');

$page->add_breadcrumb_item($lang->blog_reports_management, "index.php?module=problog-reports");

if($mybb->input['action'] == "delete")
{
	$db->delete_query("blog_reports", "rid='".(int)$mybb->input['rid']."'");
	flash_message($lang->blog_reports_success_deleted, 'success');
	admin_redirect("index.php?module=problog-reports");
}

if(!$mybb->input['action'])
{
	$page->output_header($lang->blog_reports_management);

	$table = new Table;
	$table->construct_header($lang->blog_reports_reason);
	$table->construct_header($lang->blog_reports_item, array("class" => "align_center", "width" => "150"));
	$table->construct_header($lang->blog_reports_reporter, array("class" => "align_center", "width" => "150"));
	$table->construct_header($lang->options, array("class" => "align_center", "width" => "100"));

	$query = $db->query("
		SELECT r.*, u.username
		FROM ".TABLE_PREFIX."blog_reports r
		LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = r.uid)
		ORDER BY r.dateline DESC
	");
	while($report = $db->fetch_array($query))
	{
		$table->construct_cell(htmlspecialchars_uni($report['reason']));
		$table->construct_cell("{$report['type']} #{$report['id']}", array("class" => "align_center"));
		$table->construct_cell(htmlspecialchars_uni($report['username']), array("class" => "align_center"));

		$popup = new PopupMenu("report_{$report['rid']}", $lang->options);
		$popup->add_item($lang->delete, "index.php?module=problog-reports&amp;action=delete&amp;rid={$report['rid']}&amp;my_post_key={$mybb->post_code}");
		$table->construct_cell($popup->fetch(), array("class" => "align_center"));
		$table->construct_row();
	}

	if($table->num_rows() == 0)
	{
		$table->construct_cell($lang->blog_reports_no_reports, array("colspan" => 4));
		$table->construct_row();
	}

	$table->output($lang->blog_reports_management);
	$page->output_footer();
}
?>