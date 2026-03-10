<?php
/***************************************************************
 * ProBlog
 * Copyright © 2010 ProMyBB, All Rights Reserved
 *
 * Website: http://www.promybb.com/
 * License: http://creativecommons.org/licenses/by-nc-sa/3.0/
 ***************************************************************/

define("IN_MYBB", 1);
define("IN_BLOG", 1);
define('THIS_SCRIPT', 'blog.php');

$leftblocks = $centerblocks = $rightblocks = "";

// set the path to your forums directory here (without trailing slash)
$forumdir = "./";

// end editing

$change_dir = "./";

if(!@chdir($forumdir) && !empty($forumdir))
{
	if(@is_dir($forumdir))
	{
		$change_dir = $forumdir;
	}
	else
	{
		die("\$forumdir is invalid!");
	}
}

$templatelist = "pro_blog,pro_blog_left,pro_blog_page,pro_blog_right,pro_blog_block,pro_blog_announcement,pro_blog_comment_bit,pro_blog_quick_reply,pro_blog_report,pro_blog_search,pro_blog_archive,pro_blog_archive_bit,calendar_mini,calendar_mini_weekrow,calendar_mini_weekrow_day,calendar_mini_weekdayheader, multipage, multipage_nextpage, multipage_page, multipage_page_current";

require_once $change_dir."/global.php";
require_once MYBB_ROOT."inc/functions_post.php";
require_once MYBB_ROOT."inc/functions_user.php";
require_once MYBB_ROOT."inc/class_parser.php";
$parser = new postParser;
require_once MYBB_ROOT."blog/inc/blog.class.php";
$problog = new ProBlog;

// Load blog language phrases
$lang->load("pro_blog");

// Check is blog installed
if(!$db->table_exists("blog_blocks") || !$db->table_exists("blog_pages") || !$db->table_exists("blog_settings"))
{
	error($lang->is_not_installed);
}

// Fetch the current URL
$blog_url = get_current_location();

add_breadcrumb($lang->nav_blog, "blog.php");

$plugins->run_hooks("pro_blog_start");

// Construct blog settings
$options = array(
	"order_by" => "id",
	"order_dir" => "ASC"
);
$query = $db->simple_select("blog_settings", "*", "", $options);
while($setting = $db->fetch_array($query))
{
	$setting['value'] = str_replace("\"", "\\\"", $setting['value']);
	$settings[$setting['name']] = $setting['value'];
}

$problog->settings = &$settings;

// Permissions
$mod_groups = explode(",", $problog->settings['moderator_groups']);
$is_mod = in_array($mybb->user['usergroup'], $mod_groups) || $mybb->usergroup['cancp'] == 1;

// Handle Like Action (AJAX)
if($mybb->input['action'] == "like" && $mybb->user['uid'] > 0)
{
    $can_like = explode(",", $problog->settings['can_like_groups']);
    if(!in_array($mybb->user['usergroup'], $can_like)) { echo "Permission Denied"; exit; }

	$pid = (int)$mybb->input['pid'];
	$query = $db->simple_select("blog_likes", "lid", "pid='{$pid}' AND uid='{$mybb->user['uid']}'");
	if(!$db->fetch_field($query, "lid"))
	{
		$db->insert_query("blog_likes", array("pid" => $pid, "uid" => $mybb->user['uid'], "dateline" => TIME_NOW));
		$db->write_query("UPDATE ".TABLE_PREFIX."blog_posts SET likes = likes + 1 WHERE pid = '{$pid}'");
	}
	$query = $db->simple_select("blog_posts", "likes", "pid='{$pid}'");
	echo $db->fetch_field($query, "likes");
	exit;
}

// Moderator Actions
if($is_mod)
{
    if($mybb->input['action'] == "mod_close")
    {
        verify_post_check($mybb->input['my_post_key']);
        $pid = (int)$mybb->input['id'];
        $db->update_query("blog_posts", array("closed" => 1), "pid='{$pid}'");
        redirect("blog.php?action=view&id={$pid}", "Post closed.");
    }
    if($mybb->input['action'] == "mod_open")
    {
        verify_post_check($mybb->input['my_post_key']);
        $pid = (int)$mybb->input['id'];
        $db->update_query("blog_posts", array("closed" => 0), "pid='{$pid}'");
        redirect("blog.php?action=view&id={$pid}", "Post opened.");
    }
    if($mybb->input['action'] == "mod_archive")
    {
        verify_post_check($mybb->input['my_post_key']);
        $pid = (int)$mybb->input['id'];
        $db->update_query("blog_posts", array("archived" => 1), "pid='{$pid}'");
        redirect("blog.php?action=view&id={$pid}", "Post archived.");
    }
    if($mybb->input['action'] == "mod_unarchive")
    {
        verify_post_check($mybb->input['my_post_key']);
        $pid = (int)$mybb->input['id'];
        $db->update_query("blog_posts", array("archived" => 0), "pid='{$pid}'");
        redirect("blog.php?action=view&id={$pid}", "Post restored from archive.");
    }
    if($mybb->input['action'] == "mod_delete_post")
    {
        verify_post_check($mybb->input['my_post_key']);
        $pid = (int)$mybb->input['id'];
        $db->delete_query("blog_posts", "pid='{$pid}'");
        $db->delete_query("blog_comments", "post_id='{$pid}'");
        $db->delete_query("blog_likes", "pid='{$pid}'");
        redirect("blog.php", "Post deleted.");
    }
    if($mybb->input['action'] == "mod_delete_comment")
    {
        verify_post_check($mybb->input['my_post_key']);
        $cid = (int)$mybb->input['id'];
        $query = $db->simple_select("blog_comments", "post_id", "cid='{$cid}'");
        $pid = $db->fetch_field($query, "post_id");
        $db->delete_query("blog_comments", "cid='{$cid}'");
        if($pid) $db->write_query("UPDATE ".TABLE_PREFIX."blog_posts SET comments_count = comments_count - 1 WHERE pid = '{$pid}'");
        redirect("blog.php?action=view&id={$pid}", "Comment deleted.");
    }
}

// Handle RSS
if($mybb->input['action'] == "rss" && $problog->settings['enable_rss'])
{
	header("Content-Type: application/xml; charset=utf-8");
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	echo "<rss version=\"2.0\">\n<channel>\n";
	echo "<title>".htmlspecialchars($mybb->settings['bbname'])." Blog</title>\n";
	echo "<link>".$mybb->settings['bburl']."/blog.php</link>\n";
	echo "<description>Latest blog posts</description>\n";

	$query = $db->simple_select("blog_posts", "*", "enabled='1'", array("order_by" => "dateline", "order_dir" => "DESC", "limit" => (int)$problog->settings['rss_num_items']));
	while($post = $db->fetch_array($query))
	{
		echo "<item>\n";
		echo "<title>".htmlspecialchars($post['title'])."</title>\n";
		echo "<link>".$mybb->settings['bburl']."/blog.php?action=view&amp;id=".$post['pid']."</link>\n";
		echo "<description>".htmlspecialchars(my_substr(strip_tags($post['content']), 0, 500))."</description>\n";
		echo "<pubDate>".date('r', $post['dateline'])."</pubDate>\n";
		echo "</item>\n";
	}
	echo "</channel>\n</rss>";
	exit;
}

// Define blog-related seo urls
if($mybb->settings['seourls'] == "yes" || ($mybb->settings['seourls'] == "auto" && !empty($_SERVER['SEO_SUPPORT']) == 1))
{
	define('PAGE_URL', "page-{page}.html");
}
else
{
	define('PAGE_URL', "blog.php?pages={page}");
}

// Choosing blog template by column setting
if($problog->settings['blogcolumns'] == "left"){ $blogtemplate = "pro_blog_left"; }
elseif($problog->settings['blogcolumns'] == "right"){ $blogtemplate = "pro_blog_right"; }
else{ $blogtemplate = "pro_blog"; }

// Logic to load blocks
function load_blocks($zone)
{
    global $db, $mybb, $problog, $templates, $lang, $collapsed, $theme;
    $blocks = "";
    if ($fetch_blocks = $problog->get_list("SELECT * FROM ".TABLE_PREFIX."blog_blocks WHERE zone='{$zone}' AND enabled='1' AND visible REGEXP '\\\b".$mybb->user['usergroup']."\\\b' ORDER BY position")) {
		foreach ($fetch_blocks as $result_blocks) {
			$title = $result_blocks['title'];
			$file = $result_blocks['file'];
			$content = $result_blocks['content'];

			$expdisplay = '';
			$collapsed_name = "block_{$result_blocks['id']}_c";
			if(isset($collapsed[$collapsed_name]) && $collapsed[$collapsed_name] == "display: show;")
			{
				$expcolimage = "collapse_collapsed.png";
				$expdisplay = "display: none;";
				$expaltext = "[+]";
			}
			else
			{
				$expcolimage = "collapse.png";
				$expaltext = "[-]";
			}

			if($file != "0"){
				if (file_exists(MYBB_ROOT."blog/blocks/block_$file.php")) {
					ob_start();
					include_once(MYBB_ROOT."blog/blocks/block_$file.php");
					$content .= ob_get_contents();
					ob_end_clean();
				} else {
					$content = $lang->block_file_missing;
				}
			}

			if($result_blocks['custom'] == "0"){
				eval("\$blocks .= \"".$templates->get("pro_blog_block")."\";");
			} else {
				$blocks .= "<div style=\"padding-bottom:".$problog->settings['horizontalspace']."px;\">".$content."</div>";
			}
		}
	}
    return $blocks;
}

if($problog->settings['blogcolumns'] == "left" || $problog->settings['blogcolumns'] == "both")
    $leftblocks = load_blocks(0);

if($problog->settings['blogcolumns'] == "right" || $problog->settings['blogcolumns'] == "both")
    $rightblocks = load_blocks(2);

// Center content logic
$action = $mybb->input['action'] ?? '';

if($action == "search")
{
    $keywords = $db->escape_string($mybb->input['keywords']);
    $tag = $db->escape_string($mybb->input['tag']);
    $cid = (int)$mybb->input['cid'];

    $where = "enabled='1'";
    if($keywords) $where .= " AND (title LIKE '%{$keywords}%' OR content LIKE '%{$keywords}%')";
    if($tag) $where .= " AND tags LIKE '%{$tag}%'";
    if($cid) $where .= " AND cid='{$cid}'";

    $query = $db->simple_select("blog_posts", "*", $where, array("order_by" => "dateline", "order_dir" => "DESC"));
    $search_results = "";
    while($post = $db->fetch_array($query))
    {
        $post['title'] = htmlspecialchars_uni($post['title']);
        if($keywords && $problog->settings['highlight_keywords'])
        {
            $post['title'] = str_replace($keywords, "<mark style='background:{$problog->settings['search_highlight_color']}'>{$keywords}</mark>", $post['title']);
        }
        $search_results .= "<div><h3><a href='blog.php?action=view&id={$post['pid']}'>{$post['title']}</a></h3></div>";
    }
    if(!$search_results) $search_results = $lang->no_results;

    eval("\$centerblocks = \"".$templates->get("pro_blog_search")."\";");
}
elseif($action == "view")
{
    $id = (int)$mybb->input['id'];
	$query = $db->query("
		SELECT p.*, u.username, u.avatar, u.avatardimensions
		FROM ".TABLE_PREFIX."blog_posts p
		LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = p.uid)
		WHERE p.pid='{$id}' AND p.enabled='1'
	");
	$announcement = $db->fetch_array($query);
	if($announcement)
	{
		$db->update_query("blog_posts", array("views" => $announcement['views'] + 1), "pid='{$id}'");

		$announcement['threadlink'] = "blog.php?action=view&id=".$announcement['pid'];
		$profilelink = ($announcement['uid'] == 0) ? $lang->guest : build_profile_link($announcement['username'], $announcement['uid']);
		$announcement['subject'] = htmlspecialchars_uni($parser->parse_badwords($announcement['title']));

        // Avatar
		if($announcement['avatar'] != '')
		{
			$avatar_dimensions = explode("|", $announcement['avatardimensions']);
            list($max_width, $max_height) = explode("x", my_strtolower($problog->settings['avatar_size'] ?? "35x35"));
            require_once MYBB_ROOT."inc/functions_image.php";
            $scaled_dimensions = scale_image($avatar_dimensions[0] ?? 0, $avatar_dimensions[1] ?? 0, $max_width, $max_height);
            $ann_avatar_width_height = "width=\"{$scaled_dimensions['width']}\" height=\"{$scaled_dimensions['height']}\"";
			if (!stristr($announcement['avatar'], 'http://') && !stristr($announcement['avatar'], 'https://'))
				$announcement['avatar'] = $mybb->settings['bburl'] . '/' . $announcement['avatar'];
			$avatar = "<img src=\"".htmlspecialchars_uni($announcement['avatar'])."\" alt=\"\" {$ann_avatar_width_height} />";
		}
		else $avatar = '';

		$anndate = my_date($mybb->settings['dateformat'], $announcement['dateline']);
		$anntime = my_date($mybb->settings['timeformat'], $announcement['dateline']);
		$numcomments = "- {$announcement['comments_count']} {$lang->replies}";
		$views = "<strong>{$announcement['views']}</strong> {$lang->latest_threads_views}";
        $likes = "<strong>{$announcement['likes']}</strong> Likes";

		$parser_options = array("allow_html" => 0, "allow_mycode" => 1, "allow_smilies" => 1, "allow_imgcode" => 1, "filter_badwords" => 1);
		$message = $parser->parse_message($announcement['content'], $parser_options);
		$icon = "&nbsp;";

        // Moderator Options
        $mod_options = "";
        if($is_mod)
        {
            $mod_options .= "<a href='blog.php?action=mod_delete_post&id={$id}&my_post_key={$mybb->post_code}' onclick='return confirm(\"Are you sure?\")'>Delete</a> | ";
            if($announcement['closed']) $mod_options .= "<a href='blog.php?action=mod_open&id={$id}&my_post_key={$mybb->post_code}'>Open</a> | ";
            else $mod_options .= "<a href='blog.php?action=mod_close&id={$id}&my_post_key={$mybb->post_code}'>Close</a> | ";
            if($announcement['archived']) $mod_options .= "<a href='blog.php?action=mod_unarchive&id={$id}&my_post_key={$mybb->post_code}'>Unarchive</a>";
            else $mod_options .= "<a href='blog.php?action=mod_archive&id={$id}&my_post_key={$mybb->post_code}'>Archive</a>";
        }

        // Like button logic
        $like_btn = "";
        $can_like = explode(",", $problog->settings['can_like_groups']);
        if(in_array($mybb->user['usergroup'], $can_like))
            $like_btn = "<button onclick=\"$.post('blog.php', {action: 'like', pid: '{$id}'}, function(d){ $('#likes_count').text(d); });\">Like</button>";

        // Report button
        $report_btn = "";
        $can_report = explode(",", $problog->settings['can_report_groups']);
        if(in_array($mybb->user['usergroup'], $can_report))
			$report_btn = "<a href='blog.php?action=report&type=post&id={$id}'>Report</a>";

        // Closed style
        $closed_style = ($announcement['closed'] && $problog->settings['highlight_closed']) ? "background: {$problog->settings['closed_bgcolor']};" : "";

		add_breadcrumb($announcement['subject']);
		eval("\$centerblocks = \"".$templates->get("pro_blog_announcement")."\";");

		// Fetch comments
		$comment_list = '';
		$query = $db->query("
			SELECT c.*, u.username, u.avatar, u.avatardimensions
			FROM ".TABLE_PREFIX."blog_comments c
			LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = c.uid)
			WHERE c.post_id = '{$id}'
			ORDER BY c.dateline ASC
		");
		while($comment = $db->fetch_array($query))
		{
			$comment['date'] = my_date($mybb->settings['dateformat'], $comment['dateline'])." ".my_date($mybb->settings['timeformat'], $comment['dateline']);
			$comment['content'] = $parser->parse_message($comment['content'], $parser_options);

            $comment_mod_options = "";
            if($is_mod) $comment_mod_options = "<a href='blog.php?action=mod_delete_comment&id={$comment['cid']}&my_post_key={$mybb->post_code}' onclick='return confirm(\"Are you sure?\")'>Delete</a>";

			eval("\$comment_list .= \"".$templates->get("pro_blog_comment_bit")."\";");
		}

		if(!$comment_list) $comment_list = "No comments yet.";
		$centerblocks .= "<div id='comments' style='margin-top:20px;'><h2>Comments</h2>{$comment_list}</div>";

		// Quick reply
        $can_comment = explode(",", $problog->settings['can_comment_groups']);
		if(in_array($mybb->user['usergroup'], $can_comment) && $announcement['closed'] == 0)
			eval("\$centerblocks .= \"".$templates->get("pro_blog_quick_reply")."\";");

		eval("\$blog = \"".$templates->get($blogtemplate)."\";");
		$blog = $problog->page_title($blog, $announcement['subject']);
		output_page($blog);
        exit;
	}
}
elseif($action == "archive")
{
    $page = (int)$mybb->input['page'];
    if($page < 1) $page = 1;
    $per_page = (int)$problog->settings['archive_limit'];
    $start = ($page - 1) * $per_page;

    $query = $db->simple_select("blog_posts", "COUNT(*) AS count", "enabled='1'");
    $count = $db->fetch_field($query, "count");

    $multipage = multipage($count, $per_page, $page, "blog.php?action=archive");

    $archive_bits = "";
    $query = $db->simple_select("blog_posts", "*", "enabled='1'", array("order_by" => "dateline", "order_dir" => "DESC", "limit_start" => $start, "limit" => $per_page));
    while($post = $db->fetch_array($query))
    {
        $post['date'] = my_date($mybb->settings['dateformat'], $post['dateline']);
        eval("\$archive_bits .= \"".$templates->get("pro_blog_archive_bit")."\";");
    }

    add_breadcrumb("Archive");
    eval("\$centerblocks = \"".$templates->get("pro_blog_archive")."\";");
}
elseif($action == "report")
{
    $id = (int)$mybb->input['id'];
	$type = $db->escape_string($mybb->input['type']);
	if($mybb->user['uid'] == 0) error_no_permission();
	eval("\$centerblocks = \"".$templates->get("pro_blog_report")."\";");
}
elseif($action == "do_report")
{
    verify_post_check($mybb->input['my_post_key']);
	$id = (int)$mybb->input['id'];
	$type = $db->escape_string($mybb->input['type']);
	$reason = $db->escape_string($mybb->input['reason']);
	if($mybb->user['uid'] > 0 && !empty($reason))
	{
		$db->insert_query("blog_reports", array("id" => $id, "type" => $type, "uid" => $mybb->user['uid'], "reason" => $reason, "dateline" => TIME_NOW));
	}
	redirect("blog.php", "Thank you for your report.");
}
elseif($action == "do_comment")
{
    verify_post_check($mybb->input['my_post_key']);
	$pid = (int)$mybb->input['pid'];
	$content = $db->escape_string($mybb->input['message']);
    $can_comment = explode(",", $problog->settings['can_comment_groups']);
	if(in_array($mybb->user['usergroup'], $can_comment) && !empty($content))
	{
		$db->insert_query("blog_comments", array("post_id" => $pid, "uid" => $mybb->user['uid'], "content" => $content, "dateline" => TIME_NOW, "ipaddress" => my_inet_pton(get_ip())));
		$db->write_query("UPDATE ".TABLE_PREFIX."blog_posts SET comments_count = comments_count + 1 WHERE pid = '{$pid}'");
	}
	redirect("blog.php?action=view&id={$pid}#comments");
}
else
{
    // Index - Latest Posts with pagination
    $page = (int)$mybb->input['page'];
    if($page < 1) $page = 1;
    $per_page = (int)$problog->settings['posts_per_page'];
    $start = ($page - 1) * $per_page;

    $query = $db->simple_select("blog_posts", "COUNT(*) AS count", "enabled='1' AND archived='0'");
    $count = $db->fetch_field($query, "count");
    $multipage = multipage($count, $per_page, $page, "blog.php");

    $query = $db->query("
        SELECT p.*, u.username, u.avatar, u.avatardimensions
        FROM ".TABLE_PREFIX."blog_posts p
        LEFT JOIN ".TABLE_PREFIX."users u ON (u.uid = p.uid)
        WHERE p.enabled='1' AND p.archived='0'
        ORDER BY p.dateline DESC
        LIMIT {$start}, {$per_page}
    ");

    while($announcement = $db->fetch_array($query))
    {
        $announcement['threadlink'] = "blog.php?action=view&id=".$announcement['pid'];
		$profilelink = ($announcement['uid'] == 0) ? $lang->guest : build_profile_link($announcement['username'], $announcement['uid']);
		$announcement['subject'] = htmlspecialchars_uni($parser->parse_badwords($announcement['title']));

        // Avatar logic (truncated for brevity in index)
        $avatar = "";
        if($announcement['avatar'] != '')
        {
            if (!stristr($announcement['avatar'], 'http://') && !stristr($announcement['avatar'], 'https://'))
				$announcement['avatar'] = $mybb->settings['bburl'] . '/' . $announcement['avatar'];
            $avatar = "<img src=\"".htmlspecialchars_uni($announcement['avatar'])."\" alt=\"\" width=\"35\" height=\"35\" />";
        }

		$anndate = my_date($mybb->settings['dateformat'], $announcement['dateline']);
		$anntime = my_date($mybb->settings['timeformat'], $announcement['dateline']);
		$numcomments = "- {$announcement['comments_count']} {$lang->replies}";
		$views = "<strong>{$announcement['views']}</strong> {$lang->latest_threads_views}";
        $likes = "<strong>{$announcement['likes']}</strong> Likes";

		$parser_options = array("allow_html" => 0, "allow_mycode" => 1, "allow_smilies" => 1, "allow_imgcode" => 1, "filter_badwords" => 1);
        $content = $announcement['description'] ?: $announcement['content'];
		$message = $parser->parse_message(my_substr($content, 0, $problog->settings['annmessagelength']), $parser_options);
		$icon = "&nbsp;";

        $mod_options = $report_btn = $like_btn = ""; // Simplified for index
        $closed_style = ($announcement['closed'] && $problog->settings['highlight_closed']) ? "background: {$problog->settings['closed_bgcolor']};" : "";

		eval("\$centerblocks .= \"".$templates->get("pro_blog_announcement")."\";");
    }
    $centerblocks .= $multipage;
}

eval("\$blog = \"".$templates->get($blogtemplate)."\";");
output_page($blog);
?>