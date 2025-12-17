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

echo "<div style=\"text-align:center;\"><form method=\"post\" action=\"{$mybb->settings['bburl']}/search.php\">
<input type=\"hidden\" name=\"action\" value=\"do_search\" />
<input type=\"hidden\" name=\"postthread\" value=\"1\" />
<input type=\"hidden\" name=\"forums\" value=\"all\" />
<input type=\"hidden\" name=\"showresults\" value=\"threads\" />
<input type=\"text\" class=\"textbox\" name=\"keywords\" value=\"\" />
{$gobutton}
</form>
<span class=\"smalltext\">
(<a href=\"{$mybb->settings['bburl']}/search.php\">{$lang->advanced_search}</a>)
</span></div>";
?>