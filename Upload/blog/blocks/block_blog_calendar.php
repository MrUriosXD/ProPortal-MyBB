<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

// Minimalist Calendar implementation
$month = (int)($mybb->input['month'] ?? date('n'));
$year = (int)($mybb->input['year'] ?? date('Y'));

$first_day = mktime(0, 0, 0, $month, 1, $year);
$days_in_month = date('t', $first_day);
$day_of_week = date('w', $first_day);

$prev_month = $month - 1;
$prev_year = $year;
if($prev_month == 0) { $prev_month = 12; $prev_year--; }

$next_month = $month + 1;
$next_year = $year;
if($next_month == 13) { $next_month = 1; $next_year++; }

$month_name = date('F', $first_day);

echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" style=\"text-align: center;\">";
echo "<tr><th colspan=\"7\">{$month_name} {$year}</th></tr>";
echo "<tr><td>S</td><td>M</td><td>T</td><td>W</td><td>T</td><td>F</td><td>S</td></tr><tr>";

for($i = 0; $i < $day_of_week; $i++) echo "<td></td>";

for($day = 1; $day <= $days_in_month; $day++)
{
    if(($day + $day_of_week - 1) % 7 == 0 && $day != 1) echo "</tr><tr>";

    // Check if there are posts on this day
    $start_day = mktime(0, 0, 0, $month, $day, $year);
    $end_day = mktime(23, 59, 59, $month, $day, $year);
    $query = $db->simple_select("blog_posts", "COUNT(pid) AS pcount", "dateline >= {$start_day} AND dateline <= {$end_day} AND enabled='1'");
    $count = $db->fetch_field($query, "pcount");

    if($count > 0)
        echo "<td><a href=\"blog.php?action=archive&day={$day}&month={$month}&year={$year}\" style=\"font-weight: bold;\">{$day}</a></td>";
    else
        echo "<td>{$day}</td>";
}

echo "</tr></table>";
?>