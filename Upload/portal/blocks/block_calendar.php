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

require_once MYBB_ROOT."inc/functions_calendar.php";
$lang->load("calendar");
$monthnames = array(
	"offset",
	$lang->alt_month_1,
	$lang->alt_month_2,
	$lang->alt_month_3,
	$lang->alt_month_4,
	$lang->alt_month_5,
	$lang->alt_month_6,
	$lang->alt_month_7,
	$lang->alt_month_8,
	$lang->alt_month_9,
	$lang->alt_month_10,
	$lang->alt_month_11,
	$lang->alt_month_12
);
$year = my_date("Y");
$month = my_date("n");
$calendarquery = $db->simple_select("calendars", "*", "", array('order_by' => 'disporder', 'limit' => 1));
$mycalendar = $db->fetch_array($calendarquery);

$next_month = get_next_month($month, $year);
$prev_month = get_prev_month($month, $year);

$prev_link = get_calendar_link($mycalendar['cid'], $prev_month['year'], $prev_month['month']);
$next_link = get_calendar_link($mycalendar['cid'], $next_month['year'], $next_month['month']);

// Start constructing the calendar

$weekdays = fetch_weekday_structure($mycalendar['startofweek']);

$month_start_weekday = gmdate("w", gmmktime(0, 0, 0, $month, $mycalendar['startofweek']+1, $year));

// This is if we have days in the previous month to show
if($month_start_weekday != $weekdays[0] || $mycalendar['startofweek'] != 0)
{
	$day = gmdate("t", gmmktime(0, 0, 0, $prev_month['month'], 1, $prev_month['year']));
	$day -= array_search(($month_start_weekday), $weekdays);
	$day += $mycalendar['startofweek']+1;
	$calendar_month = $prev_month['month'];
	$calendar_year = $prev_month['year'];
}
else
{
	$day = $mycalendar['startofweek']+1;
	$calendar_month = $month;
	$calendar_year = $year;
}

$prev_month_days = gmdate("t", gmmktime(0, 0, 0, $prev_month['month'], 1, $prev_month['year']));

// So now we fetch events for this month (nb, cache events for past month, current month and next month for mini calendars too)
$start_timestamp = gmmktime(0, 0, 0, $prev_month['month'], $day, $prev_month['year']);
$num_days = gmdate("t", gmmktime(0, 0, 0, $next_month['month'], 1, $next_month['year']));
$end_timestamp = gmmktime(23, 59, 59, $next_month['month'], $num_days, $next_month['year']);

$num_days = gmdate("t", gmmktime(0, 0, 0, $month, 1, $year));

$events_cache = get_events($mycalendar['cid'], $start_timestamp, $end_timestamp);
$minicalendar = build_mini_calendar($mycalendar, $month, $year, $events_cache);
$search = array(" style=\"width: 180px;\">","<td class=\"thead\" colspan=\"8\">","<tbody>", "<td class=\"thead\" colspan=\"8\">", "<td class=\"tcat\">&nbsp;</td>");
$replace = array(">","<td class=\"thead\" colspan=\"8\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div>","<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">", "<td class=\"thead\" colspan=\"7\">", "");
$minicalendar = str_replace($search, $replace, $minicalendar);
$minicalendar = preg_replace("/<td class=\"tcat\" align=\"center\" width=\"1\">(.*)&raquo;<\/a><\/td>/", "", $minicalendar);
echo $minicalendar;
?>