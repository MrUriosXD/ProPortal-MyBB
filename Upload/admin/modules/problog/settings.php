<?php
/***************************************************************
 * ProBlog
 * Copyright \xa9 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$page->add_breadcrumb_item($lang->blog_settings, "index.php?module=problog/settings");

$sub_tabs['settings'] = array(
	'title' => $lang->blog_settings,
	'link' => "index.php?module=problog/settings",
	'description' => $lang->blog_settings_desc
);

$plugins->run_hooks("admin_blog_settings_begin");

if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_blog_settings_start");

	$page->output_header($lang->blog_settings);

	$page->output_nav_tabs($sub_tabs, 'settings');

	$query = $db->simple_select("blog_settings", "*");
	while($settings = $db->fetch_array($query))
	{
		$settings_data[$settings['name']] = $settings['value'];
	}

	$form = new Form("index.php?module=problog/settings&amp;action=update", "post");

	if($settings_data['blogcolumns'] == "left")
	{
		$annenabled_left['checked'] = true;
	}
	elseif($settings_data['blogcolumns'] == "right")
	{
		$annenabled_right['checked'] = true;
	}
	else
	{
		$annenabled_both['checked'] = true;
	}

	if($settings_data['showeditor'] == "0")
	{
		$showeditor_no['checked'] = true;
	}
	else
	{
		$showeditor_yes['checked'] = true;
	}

	$form_container = new FormContainer($lang->blog_settings);
	$form_container->output_row($lang->blog_settings_blogcolumns, $lang->blog_settings_blogcolumnsdesc, $form->generate_radio_button('upsetting[blogcolumns]', 'left', $lang->blog_settings_blogcolumns_left, $annenabled_left)."<br />\n".$form->generate_radio_button('upsetting[blogcolumns]', 'right', $lang->blog_settings_blogcolumns_right, $annenabled_right)."<br />\n".$form->generate_radio_button('upsetting[blogcolumns]', 'both', $lang->blog_settings_blogcolumns_both, $annenabled_both));
	$form_container->output_row($lang->blog_settings_annfid, $lang->blog_settings_annfiddesc, $form->generate_text_box('upsetting[announcementsfid]', $settings_data['announcementsfid'], array('id' => 'announcementsfid')), 'announcementsfid');
	$form_container->output_row($lang->blog_settings_annnum, $lang->blog_settings_annnumdesc, $form->generate_text_box('upsetting[numannouncements]', $settings_data['numannouncements'], array('id' => 'numannouncements')), 'numannouncements');
	$form_container->output_row($lang->blog_settings_messlen, $lang->blog_settings_messlendesc, $form->generate_text_box('upsetting[annmessagelength]', $settings_data['annmessagelength'], array('id' => 'annmessagelength')), 'annmessagelength');
	$form_container->output_row($lang->blog_settings_showeditor, $lang->blog_settings_showeditordesc, $form->generate_radio_button('upsetting[showeditor]', '1', $lang->blog_settings_yes, $showeditor_yes)."<br />\n".$form->generate_radio_button('upsetting[showeditor]', '0', $lang->blog_settings_no, $showeditor_no));

	$form_container->end();

	$form_container = new FormContainer($lang->blog_style_settings);
	$form_container->output_row($lang->blog_settings_leftcolwidth, $lang->blog_settings_leftcolwidthdesc, $form->generate_text_box('upsetting[leftcolwidth]', $settings_data['leftcolwidth'], array('id' => 'leftcolwidth')), 'leftcolwidth');
	$form_container->output_row($lang->blog_settings_rightcolwidth, $lang->blog_settings_rightcolwidthdesc, $form->generate_text_box('upsetting[rightcolwidth]', $settings_data['rightcolwidth'], array('id' => 'rightcolwidth')), 'rightcolwidth');
	$form_container->output_row($lang->blog_settings_hspace, $lang->blog_settings_hspacedesc, $form->generate_text_box('upsetting[horizontalspace]', $settings_data['horizontalspace'], array('id' => 'horizontalspace')), 'horizontalspace');
	$form_container->output_row($lang->blog_settings_vspace, $lang->blog_settings_vspacedesc, $form->generate_text_box('upsetting[verticalspace]', $settings_data['verticalspace'], array('id' => 'verticalspace')), 'verticalspace');

	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->blog_settings_update);
	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "update")
{
	$plugins->run_hooks("admin_blog_settings_update");

	if($mybb->request_method == "post")
	{
		if(is_array($mybb->input['upsetting']))
		{
			foreach($mybb->input['upsetting'] as $name => $value)
			{
				$value = $db->escape_string($value);
				$db->update_query("blog_settings", array('value' => $value), "name='".$db->escape_string($name)."'");
			}
		}

		$plugins->run_hooks("admin_blog_settings_update_commit");

		// Log admin action
		log_admin_action($lang->blog_settings_updated);

		flash_message($lang->blog_success_settings_edited, 'success');
		admin_redirect("index.php?module=problog/settings");
	}
}
?>