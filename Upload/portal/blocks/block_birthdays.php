<?php
/***************************************************************
 * ProPortal
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/
 
if (!defined("IN_PORTAL")) {
	die("<div style=\"border:1px solid #CC0000; padding:3px; margin:0; font-family:Tahoma; width:250px; font-size:12px;\"><strong>Error:</strong> This file cannot be viewed directly!</div>");
}

// First, see what day this is.
$bdaycount = 0; $bdayhidden = 0;
$bdaytime = TIME_NOW;
$bdaydate = my_date("j-n", $bdaytime, '', 0);
$year = my_date("Y", $bdaytime, '', 0);

$bdaycache = $cache->read("birthdays");

if(!is_array($bdaycache))
{
	$cache->update_birthdays();
	$bdaycache = $cache->read("birthdays");
}

$hiddencount = $bdaycache[$bdaydate]['hiddencount'];
$today_bdays = $bdaycache[$bdaydate]['users'];

$comma = '';
if(!empty($today_bdays))
{
	foreach($today_bdays as $bdayuser)
	{
		$bday = explode("-", $bdayuser['birthday']);
		if($year > $bday['2'] && $bday['2'] != '')
		{
			$age = " (".($year - $bday['2']).")";
		}
		else
		{
			$age = '';
		}
		if(!$altbg){ $altbg = "trow2"; }
		$bdayuser['username'] = format_name($bdayuser['username'], $bdayuser['usergroup'], $bdayuser['displaygroup']);
		$bdayuser['profilelink'] = build_profile_link($bdayuser['username'], $bdayuser['uid']);
		$bdays .= "<tr><td class=\"$altbg\"><span style=\"float:right;\">".$age."</span>".$bdayuser['profilelink']."</td></tr>";
		/*eval("\$bdays .= \"".$templates->get("index_birthdays_birthday", 1, 0)."\";");*/
		++$bdaycount;
		$comma = ", ";
		$altbg = alt_trow();
	}
}

if($hiddencount > 0)
{
	$bdays .= "<tr><td class=\"$altbg\">{$hiddencount} {$lang->birthdayhidden}</td></tr>";
}

// If there are one or more birthdays, show them.
if($bdaycount > 0 || $hiddencount > 0)
{
	$birthdays = "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong>{$lang->todays_birthdays}</strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		{$bdays}
		</tbody>
	</table>";
} else {
	$birthdays = "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong>{$lang->todays_birthdays}</strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		<tr>
			<td class=\"trow1\">{$lang->nobirthday}</td>
		</tr>
		</tbody>
	</table>";
}

echo $birthdays;
?>