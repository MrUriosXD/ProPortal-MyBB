<?php
/***************************************************************
 * ProPortal
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/

$topposters = "";
$tpaltbg = alt_trow();
$query = $db->query("SELECT uid, username, postnum, avatar, avatardimensions, usergroup, displaygroup FROM ".TABLE_PREFIX."users ORDER by postnum DESC LIMIT 0, 10");
while($user = $db->fetch_array($query))
{
	$username = format_name($user['username'], $user['usergroup'], $user['displaygroup']);
	$profilelink = get_profile_link($user['uid']);
	if($user['postnum'] <= "1"){ $postnum = my_number_format($user['postnum'])." ".$lang->have_post; }else{ $postnum = my_number_format($user['postnum'])." ".$lang->have_posts; }
	
	if($user['avatar']){ $user['avatar'] = htmlspecialchars_uni($user['avatar']); }else{ $user['avatar'] = $mybb->settings['bburl']."/portal/images/user.png"; $user['avatardimensions'] = "40|40"; }
	$avatar_dimensions = explode("|", $user['avatardimensions']);
	
	if($avatar_dimensions[0] && $avatar_dimensions[1])
	{
		list($max_width, $max_height) = explode("x", my_strtolower("40x40"));
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
	
	$topposters .= "<tr><td class=\"{$tpaltbg}\" width=\"50\" align=\"center\"><img src=\"".$user['avatar']."\" alt=\"\" {$block_avatar_width_height} border=\"0\" /></td><td class=\"{$tpaltbg}\"><a href=\"{$mybb->settings['bburl']}/{$profilelink}\">{$username}</a><br />{$postnum}</td></tr>";
	$tpaltbg = alt_trow();
}

echo "<table border=\"0\" cellspacing=\"".$theme['borderwidth']."\" cellpadding=\"".$theme['tablespace']."\" class=\"tborder\">
		<tr>
			<td class=\"thead\" colspan=\"2\"><div class=\"expcolimage\"><img src=\"{$theme['imgdir']}/{$expcolimage}\" id=\"block_{$result_blocks['id']}_img\" class=\"expander\" alt=\"{$expaltext}\" title=\"{$expaltext}\" /></div>
<strong>{$lang->top_posters}</strong></td>
		</tr>
		<tbody style=\"{$expdisplay}\" id=\"block_{$result_blocks['id']}_e\">
		{$topposters}
		</tbody>
	</table>";
?>