<?php
/***************************************************************
 * ProBlog - AdminCP Info / Statistics
 ***************************************************************/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$lang->load('problog_info');

$page->add_breadcrumb_item($lang->blog_info, "index.php?module=problog-info");
$plugins->run_hooks("admin_blog_info_begin");

$sub_tabs['blog_info'] = array(
	'title' => $lang->blog_info,
	'link' => "index.php?module=problog-info",
	'description' => $lang->blog_info_description
);

$sub_tabs['version_check'] = array(
	'title' => $lang->version_check,
	'link' => "index.php?module=problog-info&amp;action=version_check",
	'description' => $lang->version_check_description
);


if($mybb->input['action'] == "version_check")
{
	$plugins->run_hooks("admin_problog_version_check_start");

	$current_version = rawurlencode($problog->pversion_code);

	$updated_cache = array(
		"last_check" => TIME_NOW
	);

	// Use a placeholder or a generic update check URL
	$filedata = @file("https://raw.githubusercontent.com/MrUriosXD/ProBlog/main/version_check.xml");
	$contents = is_array($filedata) ? implode("", $filedata) : '';

	if(!$contents)
	{
		flash_message($lang->error_communication, 'error');
		admin_redirect("index.php?module=problog-info");
	}

	$plugins->run_hooks("admin_problog_version_check");

	$page->add_breadcrumb_item($lang->version_check, "index.php?module=problog-info&amp;action=version_check");
	$page->output_header($lang->version_check);
	$page->output_nav_tabs($sub_tabs, 'version_check');

	$contents = trim($contents);

	$parser = create_xml_parser($contents);
	$tree = $parser->get_tree();

	if(isset($tree['problog']))
	{
		$latest_code = (int)$tree['problog']['version_code']['value'];
		$latest_version = "<strong>".htmlspecialchars_uni($tree['problog']['latest_version']['value'])."</strong> (".$latest_code.")";
		if($latest_code > $problog->pversion_code)
		{
			$latest_version = "<span style=\"color: #C00;\">".$latest_version."</span>";
			$updated_cache['latest_version'] = $latest_version;
			$updated_cache['latest_version_code'] = $latest_code;
			$page->output_error("<p><em>{$lang->error_out_of_date}</em> {$lang->update_forum}</p>");
		}
		else
		{
			$latest_version = "<span style=\"color: green;\">".$latest_version."</span>";
			$page->output_success("<p><em>{$lang->success_up_to_date}</em></p>");
		}
	}
	else
	{
		$latest_version = "Unknown";
	}

	$table = new Table;
	$table->construct_header($lang->your_version);
	$table->construct_header($lang->latest_version);

	$table->construct_cell("<strong>".$problog->pversion."</strong> (".$problog->pversion_code.")");
	$table->construct_cell($latest_version);
	$table->construct_row();

	$table->output($lang->version_check);

	$table = new Table;
	$table->construct_header($lang->blog_latest_news_description);

	require_once MYBB_ROOT."inc/class_feedparser.php";
	$feed_parser = new FeedParser();
	// Using MyBB news as a fallback for Latest News from ProBlog project
	$feed_parser->parse_feed("https://blog.mybb.com/feed/");

	$updated_cache['news'] = array();

	if($feed_parser->error == '')
	{
		foreach($feed_parser->items as $item)
		{
			if(count($updated_cache['news']) < 3)
			{
				$updated_cache['news'][] = array(
					'title' => $item['title'],
					'description' => $item['description'],
					'link' => $item['link'],
					'author' => $item['author'],
					'dateline' => $item['date_timestamp'],
				);
			}

			$stamp = '';
			if($item['date_timestamp'])
			{
				$stamp = my_date('relative', (int)$item['date_timestamp']);
			}

			$link = htmlspecialchars_uni($item['link']);
			$title = htmlspecialchars_uni($item['title']);
			$description = htmlspecialchars_uni(strip_tags($item['description']));

			$table->construct_cell("<span style=\"font-size: 16px;\"><strong><a href=\"{$link}\" target=\"_blank\" rel=\"noopener\">{$title}</a></strong></span><strong><span style=\"float: right;\">{$stamp}</span></strong><br /><br />{$description}");
			$table->construct_row();
		}
	}
	else
	{
		$table->construct_cell($lang->no_announcements);
		$table->construct_row();
	}

	$cache->update("problog_update_check", $updated_cache);

	$table->output($lang->blog_latest_news);
	$page->output_footer();
}
elseif(!$mybb->input['action'])
{
	global $required_version;

	$plugins->run_hooks("admin_blog_info_start");
	$page->output_header($lang->blog_info);
	$page->output_nav_tabs($sub_tabs, 'blog_info');

	$required_version = $problog->required_version;
	$current_version = $mybb->version;
	$is_compatible = version_compare($current_version, $required_version, ">=");

	$compat_text = $is_compatible
		? "<span style='color: green;'>✅ MyBB {$current_version}</span>"
		: "<span style='color: red;'>❌ MyBB {$current_version} (minimum {$required_version})</span>";

    $today = strtotime("today");

    // Entries Stats
    $entries = array();
    $entries['total'] = (int)$db->fetch_field($db->simple_select("blog_posts", "COUNT(*) AS c", "enabled='1'"), "c");
    $entries['today'] = (int)$db->fetch_field($db->simple_select("blog_posts", "COUNT(*) AS c", "enabled='1' AND dateline >= {$today}"), "c");
    $entries['drafts'] = (int)$db->fetch_field($db->simple_select("blog_posts", "COUNT(*) AS c", "enabled='0'"), "c");
    $entries['rejected'] = (int)$db->fetch_field($db->simple_select("blog_posts", "COUNT(*) AS c", "archived='1'"), "c"); // Using archived as proxy for rejected for UI

    // Comments Stats
    $comments = array();
    $comments['total'] = (int)$db->fetch_field($db->simple_select("blog_comments", "COUNT(*) AS c"), "c");
    $comments['today'] = (int)$db->fetch_field($db->simple_select("blog_comments", "COUNT(*) AS c", "dateline >= {$today}"), "c");
    $comments['pending'] = (int)$db->fetch_field($db->simple_select("blog_reports", "COUNT(*) AS c", "is_read='0'"), "c"); // Reports as pending items
    $comments['rejected'] = (int)$db->fetch_field($db->simple_select("blog_reports", "COUNT(*) AS c", "is_read='1'"), "c");

    // Organization
    $categories_stats = array();
    $categories_stats['total'] = (int)$db->fetch_field($db->simple_select("blog_categories", "COUNT(*) AS c"), "c");

    // Tags total (approximate from distinct tags)
    $tags_query = $db->simple_select("blog_posts", "tags", "enabled='1'");
    $tags_list = array();
    while($row = $db->fetch_array($tags_query)) {
        foreach(explode(",", $row['tags']) as $t) if(trim($t)) $tags_list[trim($t)] = 1;
    }
    $tags_total = count($tags_list);

    // Views
    $views = array();
    $query = $db->simple_select("blog_posts", "SUM(views) AS total_views");
    $views['total'] = (int)$db->fetch_field($query, "total_views");
    $top_entry_query = $db->simple_select("blog_posts", "title", "enabled='1'", array("order_by" => "views", "order_dir" => "DESC", "limit" => 1));
    $views['top_entry'] = $db->fetch_field($top_entry_query, "title") ?: $lang->blog_na;

	// -------------------------
	// Blog Information
	// -------------------------
	$table = new Table;
	$table->construct_header("General", ['width' => '33%', 'colspan' => 2]);
	$table->construct_header("Blocks (Active / Inactive)", ['width' => '33%', 'colspan' => 2]);
	$table->construct_header("Detailed Info", ['width' => '33%', 'colspan' => 2]);

	// Get blocks info
	$query = $db->simple_select("blog_blocks", "COUNT(id) AS numblocks");
	$blocksnum = (int)$db->fetch_field($query, "numblocks");
	$query = $db->simple_select("blog_blocks", "COUNT(id) AS numblocks", "enabled='1'");
	$activeblocksnum = (int)$db->fetch_field($query, "numblocks");
	$passiveblocksnum = $blocksnum - $activeblocksnum;

	$query = $db->simple_select("blog_blocks", "COUNT(id) AS numblocks", "zone='0'");
	$leftblocksnum = (int)$db->fetch_field($query, "numblocks");
	$query = $db->simple_select("blog_blocks", "COUNT(id) AS numblocks", "zone='1'");
	$centerblocksnum = (int)$db->fetch_field($query, "numblocks");
	$rightblocksnum = $blocksnum - ($leftblocksnum + $centerblocksnum);

	$block_info = [
		'left' => [
			"{$lang->blog_version}:"     	 => $problog->pversion . " (" . $problog->pversion_code . ")",
			"Update Date:"	 	 => $problog->pupdate ?? "Unknown",
			"{$lang->blog_author}:"      	 => "MrUriosXD",
			"{$lang->blog_compatibility}:"	 => $compat_text

		],
		'center' => [
			"Status:" =>
				"Total: "					 . $blocksnum . "<br />" .
				"Active / Inactive: "	 . $activeblocksnum . " / " . $passiveblocksnum,

			"Position:" =>
				"Left: "			 		 . $leftblocksnum . "<br />" .
				"Center: "			 		 . $centerblocksnum . "<br />" .
				"Right: "			 		 . $rightblocksnum,
		],
		'right' => [
			// Entries
			"{$lang->blog_stats_entries}" =>
				"{$lang->blog_stats_published} "				 . ($entries['total']        ?? "{$lang->blog_na}") . "<br />" .
				"{$lang->blog_stats_today} "					 . ($entries['today']        ?? "{$lang->blog_na}") . "<br />" .
				"{$lang->blog_stats_drafts} "					 . ($entries['drafts']       ?? "{$lang->blog_na}") . "<br />" .
				"{$lang->blog_stats_rejected} "				 . ($entries['rejected']     ?? "{$lang->blog_na}"),

			// Comments
			"{$lang->blog_stats_comments}" =>
				"{$lang->blog_stats_total} "   				 . ($comments['total']       ?? "{$lang->blog_na}") . "<br />" .
				"{$lang->blog_stats_today} "					 . ($comments['today']       ?? "{$lang->blog_na}") . "<br />" .
				"{$lang->blog_stats_pending} "					 . ($comments['pending']     ?? "{$lang->blog_na}") . "<br />" .
				"{$lang->blog_stats_rejected} "				 . ($comments['rejected']    ?? "{$lang->blog_na}"),

			// Organization
			"{$lang->blog_stats_organization}" =>
				"{$lang->blog_stats_categories} "				 . ($categories_stats['total']     ?? "{$lang->blog_na}") . "<br />" .
				"{$lang->blog_stats_tags} "				     . ($tags_total           ?? "{$lang->blog_na}"),

			// Visits
			"{$lang->blog_stats_visits}" =>
				"{$lang->blog_stats_total} "					 . ($views['total']          ?? "{$lang->blog_na}") . "<br />" .
				"{$lang->blog_stats_top_entry} "				 . ($views['top_entry']     ?? "{$lang->blog_na}"),
		]
	];

	// Calculate maximum rows
	$max_rows = max(count($block_info['left']), count($block_info['center']), count($block_info['right']));

	// Separate keys and values
	$left_keys   = array_keys($block_info['left']);
	$left_values = array_values($block_info['left']);
	$center_keys   = array_keys($block_info['center']);
	$center_values = array_values($block_info['center']);
	$right_keys   = array_keys($block_info['right']);
	$right_values = array_values($block_info['right']);

	for ($i = 0; $i < $max_rows; $i++) {
		// Left
		$table->construct_cell("<strong>" . ($left_keys[$i] ?? '') . "</strong>", ['width' => '15%']);
		$table->construct_cell($left_values[$i] ?? '', ['width' => '15%']);

		// Center
		$table->construct_cell("<strong>" . ($center_keys[$i] ?? '') . "</strong>", ['width' => '15%']);
		$table->construct_cell($center_values[$i] ?? '', ['width' => '15%']);

		// Right
		$table->construct_cell("<strong>" . ($right_keys[$i] ?? '') . "</strong>", ['width' => '15%']);
		$table->construct_cell($right_values[$i] ?? '', ['width' => '15%']);

		$table->construct_row();
	}

	$table->output($lang->blog_statistics);

	$table = new Table;
	$table->construct_header("The latest news from ProBlog.");

	$update_check = $cache->read("problog_update_check");

	if(!empty($update_check['news']) && is_array($update_check['news']))
	{
		foreach($update_check['news'] as $news_item)
		{
			$posted = my_date('relative', (int)$news_item['dateline']);
			$link = htmlspecialchars_uni($news_item['link']);
			$title = htmlspecialchars_uni($news_item['title']);
			$description = htmlspecialchars_uni(strip_tags($news_item['description']));

			$table->construct_cell("<strong><a href=\"{$link}\" target=\"_blank\" rel=\"noopener\">{$title}</a></strong><br /><span class=\"smalltext\">{$posted}</span>");
			$table->construct_row();

			$table->construct_cell($description);
			$table->construct_row();
		}
	}
	else
	{
		$link = "index.php?module=problog-info&action=version_check";
		$no_announcements = $lang->sprintf($lang->no_announcements, $link);
		$table->construct_cell($no_announcements);
		$table->construct_row();
	}

	$table->output("Latest News from ProMyBB");

	$page->output_footer();
}
?>