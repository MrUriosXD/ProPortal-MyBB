<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

$blog_search_form = "
<form method=\"get\" action=\"blog.php\">
	<input type=\"hidden\" name=\"action\" value=\"search\" />
	<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"100%\">
		<tr>
			<td><input type=\"text\" class=\"textbox\" name=\"keywords\" style=\"width: 100%; box-sizing: border-box;\" placeholder=\"Search the blog...\" /></td>
		</tr>
        <tr>
			<td align=\"right\"><input type=\"submit\" class=\"button\" value=\"{$lang->search}\" /></td>
		</tr>
	</table>
</form>
";

echo $blog_search_form;
?>