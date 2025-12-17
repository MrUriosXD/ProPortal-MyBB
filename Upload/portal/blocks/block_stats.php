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

$stats = $cache->read("stats");
$stats['numthreads'] = my_number_format($stats['numthreads']);
$stats['numposts'] = my_number_format($stats['numposts']);
$stats['numusers'] = my_number_format($stats['numusers']);
if(!$stats['lastusername'])
{
	$newestmember = "<strong>" . $lang->no_one . "</strong>";
}
else
{
	$newestmember = build_profile_link($stats['lastusername'], $stats['lastuid']);
}
echo "<span class=\"smalltext\">
<strong>&raquo; </strong>$lang->num_members ".$stats['numusers']."<br />
<strong>&raquo; </strong>$lang->latest_member $newestmember<br />
<strong>&raquo; </strong>$lang->num_threads ".$stats['numthreads']."<br />
<strong>&raquo; </strong>$lang->num_posts ".$stats['numposts']."
<br /><br /><a href=\"".$mybb->settings['bburl']."/stats.php\">$lang->full_stats</a>
</span>";
?>