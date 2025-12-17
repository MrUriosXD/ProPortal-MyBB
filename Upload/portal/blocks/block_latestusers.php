<?php
/***************************************************************
 * ProPortal
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/
 
$lualtbg = alt_trow();
$query = $db->query("SELECT uid, username, regdate, avatar, avatardimensions, usergroup, displaygroup FROM ".TABLE_PREFIX."users ORDER by regdate DESC LIMIT 0, 10");
while($user = $db->fetch_array($query))
{
	$username = format_name($user['username'], $user['usergroup'], $user['displaygroup']);
	$profilelink = get_profile_link($user['uid']);
	$regdate = my_date($mybb->settings['dateformat'], $user['regdate']);
	$regtime = my_date($mybb->settings['timeformat'], $user['regdate']);
	
	if($user['avatar']){ $user['avatar'] = htmlspecialchars_uni($user['avatar']); }else{ $user['avatar'] = $mybb->settings['bburl']."/portal/images/user.png"; $user['avatardimensions'] = "40|40"; }
	$avatar_dimensions = explode("|", $user['avatardimensions']);
	
	if($avatar_dimensions[0] && $avatar_dimensions[1])
	{
		list($max_width, $max_height) = explode("x", my_strtolower("24x24"));
		if($avatar_dimensions[0] > $max_width || $avatar_dimensions[1] > $max_height)
		{
			require_once MYBB_ROOT."inc/functions_image.php";
			$scaled_dimensions = scale_image($avatar_dimensions[0], $avatar_dimensions[1], $max_width, $max_height);
			$block_avatar_width_height = "width=\"{$scaled_dimensions['width']}\" height=\"{$scaled_dimensions['height']}\"";
		}
		else
		{
			$block_avatar_width_height = "width=\"{$avatar_dimensions[0]}\" height=\"{$avatar_dimensions[1]}\"";	
		}
	}
	
	$latestusers .= "<tr><td class=\"{$lualtbg}\" width=\"30\" align=\"center\"><img src=\"".$user['avatar']."\" alt=\"\" {$block_avatar_width_height} border=\"0\" /></td><td class=\"{$lualtbg}\" style=\"font-size:11px;\"><a href=\"{$mybb->settings['bburl']}/{$profilelink}\">{$username}</a><br />{$regdate} {$regtime}</td></tr>";
	$lualtbg = alt_trow();
}
echo "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\" colspan=\"2\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div><strong>{$lang->latest_users}</strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		{$latestusers}
		</tbody>
	</table>";
?>