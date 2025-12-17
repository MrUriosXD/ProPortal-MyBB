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

$page->add_breadcrumb_item($lang->portal_settings, "index.php?module=portal/settings");

$sub_tabs['settings'] = array(
	'title' => $lang->portal_settings,
	'link' => "index.php?module=portal/settings",
	'description' => $lang->portal_settings_desc
);

$plugins->run_hooks("admin_portal_settings_begin");

if(!$mybb->input['action'])
{
	$plugins->run_hooks("admin_portal_settings_start");
	
	$page->output_header($lang->portal_settings);

	$page->output_nav_tabs($sub_tabs, 'settings');
	
	$query = $db->simple_select("portal_settings", "*");
	while($settings = $db->fetch_array($query))
	{
		$settings_data[$settings['name']] = $settings['value'];
	}
	
	$form = new Form("index.php?module=portal/settings&amp;action=update", "post");
	
	if($settings_data['portalcolumns'] == "left")
	{
		$annenabled_left['checked'] = true;
	}
	elseif($settings_data['portalcolumns'] == "right")
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

	$form_container = new FormContainer($lang->portal_settings);
	$form_container->output_row($lang->portal_settings_portalcolumns, $lang->portal_settings_portalcolumnsdesc, $form->generate_radio_button('upsetting[portalcolumns]', 'left', $lang->portal_settings_portalcolumns_left, $annenabled_left)."<br />\n".$form->generate_radio_button('upsetting[portalcolumns]', 'right', $lang->portal_settings_portalcolumns_right, $annenabled_right)."<br />\n".$form->generate_radio_button('upsetting[portalcolumns]', 'both', $lang->portal_settings_portalcolumns_both, $annenabled_both));
	$form_container->output_row($lang->portal_settings_annfid, $lang->portal_settings_annfiddesc, $form->generate_text_box('upsetting[announcementsfid]', $settings_data['announcementsfid'], array('id' => 'announcementsfid')), 'announcementsfid');
	$form_container->output_row($lang->portal_settings_annnum, $lang->portal_settings_annnumdesc, $form->generate_text_box('upsetting[numannouncements]', $settings_data['numannouncements'], array('id' => 'numannouncements')), 'numannouncements');
	$form_container->output_row($lang->portal_settings_messlen, $lang->portal_settings_messlendesc, $form->generate_text_box('upsetting[annmessagelength]', $settings_data['annmessagelength'], array('id' => 'annmessagelength')), 'annmessagelength');
	$form_container->output_row($lang->portal_settings_showeditor, $lang->portal_settings_showeditordesc, $form->generate_radio_button('upsetting[showeditor]', '1', $lang->portal_settings_yes, $showeditor_yes)."<br />\n".$form->generate_radio_button('upsetting[showeditor]', '0', $lang->portal_settings_no, $showeditor_no));
	
	$form_container->end();
	
	$form_container = new FormContainer($lang->portal_style_settings);
	$form_container->output_row($lang->portal_settings_leftcolwidth, $lang->portal_settings_leftcolwidthdesc, $form->generate_text_box('upsetting[leftcolwidth]', $settings_data['leftcolwidth'], array('id' => 'leftcolwidth')), 'leftcolwidth');
	$form_container->output_row($lang->portal_settings_rightcolwidth, $lang->portal_settings_rightcolwidthdesc, $form->generate_text_box('upsetting[rightcolwidth]', $settings_data['rightcolwidth'], array('id' => 'rightcolwidth')), 'rightcolwidth');
	$form_container->output_row($lang->portal_settings_hspace, $lang->portal_settings_hspacedesc, $form->generate_text_box('upsetting[horizontalspace]', $settings_data['horizontalspace'], array('id' => 'horizontalspace')), 'horizontalspace');
	$form_container->output_row($lang->portal_settings_vspace, $lang->portal_settings_vspacedesc, $form->generate_text_box('upsetting[verticalspace]', $settings_data['verticalspace'], array('id' => 'verticalspace')), 'verticalspace');
	
	$form_container->end();
	
	$buttons[] = $form->generate_submit_button($lang->portal_settings_update);
	$form->output_submit_wrapper($buttons);
	$form->end();
	
	$page->output_footer();	
}

if($mybb->input['action'] == "update")
{
	$plugins->run_hooks("admin_portal_settings_update");
	
	if($mybb->request_method == "post")
	{
		if(is_array($mybb->input['upsetting']))
		{
			foreach($mybb->input['upsetting'] as $name => $value)
			{
				$value = $db->escape_string($value);
				$db->update_query("portal_settings", array('value' => $value), "name='".$db->escape_string($name)."'");
			}
		}
		
		$plugins->run_hooks("admin_portal_settings_update_commit");
		
		// Log admin action
		log_admin_action($lang->portal_settings_updated);
		
		flash_message($lang->portal_success_settings_edited, 'success');
		admin_redirect("index.php?module=portal/settings");
	}
}
?>