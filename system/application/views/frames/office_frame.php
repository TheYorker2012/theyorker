<?php
// Must echo through PHP in case short tags is turned on
echo('<?xml version="1.0" encoding="UTF-8"?>');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?php echo(xml_escape($description)); ?>" />
	<meta name="keywords" content="<?php echo(xml_escape($keywords)); ?>" />

	<title><?php
		// FIXME: backwards compatibility, remove when all pages are shown with titles
		if(isset($head_title)) {
			echo(xml_escape($head_title));
		} else {
			echo('no pagename');
		}
	?> - The Yorker</title>

	<link rel="shortcut icon" href="/images/yorker.ico" />
	<link rel="alternate" type="application/rss+xml" title="The Yorker - Campus News" href="/news/rss" />
	<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" />
	<link href="/stylesheets/stylesheet.css" rel="stylesheet" type="text/css" />

	<!--<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" /> -->
	<!--[if lte IE 6]><link href="/stylesheets/new-ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->
	<link href="/stylesheets/office.css" rel="stylesheet" type="text/css" />

	<?php
	if (isset($extra_css)) {
		echo('<link href="'.$extra_css.'" rel="stylesheet" type="text/css" />'."\n");
	}
	?>

	<?php
		// Get common javascript
		include('top_script.php');
	?>
</head>

<body onload="onLoadHandler()" onunload="onUnloadHandler()">
	<div id="Header">
		<div id="HeaderMenu">
			<span style="color: #999999; font-size: 0.9em">
			<?php
			// Set by GenerateToplinks in mainframe_helper
			if (isset($toplinks)) {
				foreach ($toplinks as $link) {
					if (is_string($link)) {
						echo('<span style="color: #000000;">'.xml_escape($link).'</span> | ');
					} elseif (is_array($link)) {
						echo('<a href="'.xml_escape($link[1]).'">'.xml_escape($link[0]).'</a> | ');
					}
				}
			}
			?>
			<a href="/about/">about us</a> |
			<a href="/faq/">FAQs</a>
			</span>
		</div>

		<div id="TopBanner">
			<h1 id="TopBannerName">
				<a href="/"><img src="/images/prototype/header/header_Layer-1.gif" width="300" height="108" alt="The Yorker"/></a>
			</h1>
			<div id="TopBannerPictures">
				<a href="/office"><img src="/images/prototype/header/office_header.jpg" alt="Office" title="Office" /></a>
				<a href="/logout/office"><img src="/images/prototype/header/office_header2.jpg" alt="Logout of Office" title="Logout of Office" /></a>
			</div>
		</div>
	</div>

   	<div id="Page">
		<div id="NavigationColumn">
			<form id="searchbox_003080001858553066416:dyddjbcpdlc" action="http://www.google.com/search">
				<fieldset>
					<input type="hidden" name="cx" value="003080001858553066416:dyddjbcpdlc" />
					<input type="hidden" name="cof" value="FORID:0" />
				</fieldset>
				<fieldset id="SearchBox">
					<input name="q" type="text" size="40" value="Search for..." onfocus="inputFocus(this);" onblur="inputBlur(this);" />
				</fieldset>
			</form>
			<div id="NavigationMenu">
<?php
/**
 *	@param	Array of arrays (link title, URL, permission)
 */
function printMenu ($CI, $title, $links, $firstMenu = false) {
	$linkCount = count($links);
	for ($i = 0; $i < $linkCount; $i++) {
		if (!empty($links[$i][2]) && !$CI->permissions_model->hasUserPermission($links[$i][2])) {
			unset($links[$i]);
		}
	}
	if (count($links) == 0) return;
	echo('				<ul' . (($firstMenu) ? ' class="first"' : '') . '>'."\n");
	echo('					<li class="first">' . $title . '</li>'."\n");
	foreach ($links as $link) {
		echo('					<li><a href="' . $link[1] . '">' . $link[0] . '</a></li>'."\n");
	}
	echo('				</ul>'."\n");
}

printMenu($this, 'Office', array(
	array('Office Home', '/office', ''),
	array('Office Chat', '/office/irc', 'IRC_CHAT'),
	array('My Bylines', '/office/bylines', 'BYLINES_VIEW')
), true);

// Editor and Admins only
if (PermissionsSubset('editor', GetUserLevel())) {
	printMenu($this, 'Admin', array(
		array('Announcements', '/office/announcements', 'ANNOUNCEMENT_VIEW'),
		array('Permissions', '/admin/permissions', 'PERMISSIONS_VIEW'),
		array('Manage Team', '/office/manage/members', 'MANAGE'),
		array('Manage VIPs', '/office/vipmanager', 'VIPMANAGER_VIEW'),
		array('Content Schedule', '/office/news/contentschedule', 'ARTICLE_VIEW'),
		array('Change Live Article', '/office/news/scheduledlive', 'ARTICLE_VIEW'),
		array('Comment Moderation', '/office/moderator', 'COMMENT_MODERATE'),
		array('Page Properties', '/admin/pages', 'PAGES_VIEW'),
		array('Statistics', '/office/stats', 'STATS_VIEW'),
		array('Feedback', '/admin/feedback', 'FEEDBACK_VIEW'),
		array('Article Types', '/office/articletypes', 'ARTICLETYPES_VIEW'),
		array('Special Articles', '/office/specials', 'ARTICLE_VIEW'),
		array('Facebook Articles', '/office/ticker', 'ARTICLE_VIEW'),
		array('Advertising', '/office/advertising', 'ADVERTISING_VIEW'),
		array('Polls', '/office/polls', 'POLLS_VIEW')
	));
}

printMenu($this, 'Sections', array(
	array('Uni News', '/office/news/uninews', 'ARTICLE_VIEW'),
	array('Features', '/office/news/features', 'ARTICLE_VIEW'),
	array('Lifestyle', '/office/news/lifestyle', 'ARTICLE_VIEW'),
	array('Arts', '/office/news/arts', 'ARTICLE_VIEW'),
	array('Sport', '/office/news/sport', 'ARTICLE_VIEW'),
	array('Blogs', '/office/news/blogs', 'ARTICLE_VIEW'),
	array('Food', '/office/news/food', 'ARTICLE_VIEW'),
	array('Videocasts', '/office/news/videocasts', 'ARTICLE_VIEW'),
	array('News Comment', '/office/news/comment', 'ARTICLE_VIEW'),
	array('Podcasts', '/office/podcasts', 'ARTICLE_VIEW')
));

printMenu($this, 'Info + Reviews', array(
	array('Directory', '/office/prlist', ''),
	array('Food', '/office/reviewlist/foodreviews', ''),
	array('Drink', '/office/reviewlist/drinkreviews', ''),
	array('Review Tags', '/office/reviewtags', ''),
	array('Leagues', '/office/leagues', ''),
	array('PR System', '/office/pr/summary', ''),
	array('Campaigns', '/office/campaign', 'CAMPAIGN_VIEW'),
	array('Charities', '/office/charity', 'CHARITY_VIEW'),
	array('How Do I', '/office/howdoi', 'HOWDOI_VIEW'),
	array('Game Zone', '/office/games', 'GAMEZONE_VIEW')
));

printMenu($this, 'Photos', array(
	array('Photo Requests', '/office/photos', 'GALLERY_VIEW'),
	array('Gallery', '/office/gallery', 'GALLERY_VIEW'),
	array('Homepage Banners', '/office/banners', 'BANNERS_VIEW')
));

printMenu($this, 'Homepage', array(
	array('Quotes', '/office/quotes', 'QUOTES_VIEW'),
	array('Links', '/office/links', 'LINKS_VIEW'),
	array('Style Guide', '/office/guide', 'ARTICLE_VIEW')
));

?>
				<?php
				if (isset($extra_menu_buttons) && !empty($extra_menu_buttons)) {
					echo('<ul>');
					foreach ($extra_menu_buttons as $key => $button) {
						echo('<li'.(!$key ? ' class="first"':'').'>');
						if (is_string($button)) {
							echo($button);
						} else {
							echo('<a href="'.$button[1].'">'.$button[0].'</a>');
						}
						echo('</li>');
					}
					echo('</ul>');
				}
				?>
			</div>
		</div>

		<div id="MainBodyPane">
			<h1 id="PageTitle">
				<?php
				if(isset($body_title)) {
					echo htmlentities($body_title, ENT_QUOTES, 'utf-8')."\n";
				} else {
					echo 'no pagename'."\n";
				}
				if(isset($paged_edit_url) && NULL !== $paged_edit_url) {
					echo("<a href=\"$paged_edit_url\">[edit]</a>");
				}
				?>
			</h1>

<!-- BEGIN generated content -->
<?php
	// TODO: check this works properly

	// Navigation bar
	if (isset($content['navbars']) && is_array($content['navbars'])) {
		foreach ($content['navbars'] as $navbar) {
			$navbar->Load();
		}
	} elseif (isset($content['navbar'])) {
		$content['navbar']->Load();
	}

	// Display each message
	foreach ($messages as $message) {
		// Display the message
		$message->Load();
	}

	// Display the main content
	$content[0]->Load();
?>
<!-- END generated content -->

		</div>
	</div>


	<?php
		// Get common footer
		include('footer.php');
	?>

</body>
</html>