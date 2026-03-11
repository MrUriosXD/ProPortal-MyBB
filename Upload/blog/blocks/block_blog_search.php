<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 ***************************************************************/

if (!defined("IN_BLOG")) {
	die("Direct initialization of this file is not allowed.");
}

$blog_search_form = "
<script type='text/javascript'>
function doAjaxSearch(v) {
    if(v.length < 3) { $('#ajax_results').hide(); return; }
    $.get('blog.php', {action: 'search', keywords: v, ajax: 1}, function(d) {
        $('#ajax_results').html(d).show();
    });
}
</script>
<form method=\"get\" action=\"blog.php\">
	<input type=\"hidden\" name=\"action\" value=\"search\" />
	<table border=\"0\" cellspacing=\"0\" cellpadding=\"2\" width=\"100%\">
		<tr>
			<td>
                <input type=\"text\" class=\"textbox\" name=\"keywords\" id=\"blog_search_input\" onkeyup=\"doAjaxSearch(this.value)\" style=\"width: 100%; box-sizing: border-box;\" placeholder=\"Search the blog...\" autocomplete='off' />
                <div id='ajax_results' style='display: none; position: absolute; background: #fff; border: 1px solid #ccc; width: 180px; z-index: 100; max-height: 200px; overflow-y: auto; padding: 5px;'></div>
            </td>
		</tr>
        <tr>
			<td align=\"right\"><input type=\"submit\" class=\"button\" value=\"{$lang->search}\" /></td>
		</tr>
	</table>
</form>
";

echo $blog_search_form;
?>