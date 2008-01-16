<?php
// Must echo through PHP in case short tags is turned on
echo('<?xml version="1.0" encoding="UTF-8"?>');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?php echo htmlspecialchars($description); ?>" />
	<meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>" />

	<title>The Yorker - <?php
		// FIXME: backwards compatibility, remove when all pages are shown with titles
		if(isset($head_title)) {
			echo htmlspecialchars($head_title, ENT_QUOTES, 'utf-8');
		} else {
			echo 'no pagename';
		}
	?></title>

	<link rel="shortcut icon" href="/images/yorker.ico" />
	<link rel="alternate" type="application/rss+xml" title="The Yorker - Campus News" href="/news/rss" />

	<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" />
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
						echo '<span style="color: #000000;">'.$link.'</span> | ';
					} elseif (is_array($link)) {
						echo '<a href="'.$link[1].'">'.$link[0].'</a> | ';
					}
				}
			}
			?>
			<a href="/about/">about us</a> |
			<a href="/contact/">contact us</a> |
			<a href="/faq/">FAQs</a>
			</span>
		</div>

		<div id="TopBanner">
			<h1 id="TopBannerName">
				<a href="/"><img src="/images/prototype/header/header_Layer-1.gif" width="300" height="108" alt="The Yorker"/></a>
			</h1>
			<div id="TopBannerPictures">
				<a href="/office">
					<img src="/images/prototype/new_home/office_header.jpg" alt="Office" />
				</a>
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
				<!-- Nasty "first" class used as IE6 doesn't have :first-child -->
				<ul class="first">
					<li class="first">Office</li>
					<li><a href="/office/">Office Home</a></li>
					<li><a href="/office/irc/">Office Chat</a></li>
					<li><a href="/office/bylines/">My Bylines</a></li>
				</ul>
<?php
	//editor and admins only
	if (PermissionsSubset('editor', GetUserLevel())){
?>
				<ul>
					<li class="first">Admin</li>
					<li><a href="/office/manage/members/">Manage Team</a></li>
					<li><a href="/office/vipmanager/">Manage VIPs</a></li>
					<li><a href="/office/news/contentschedule/">Content Schedule</a></li>
					<li><a href="/office/news/scheduledlive/">Change Live Article</a></li>
					<li><a href="/office/moderator/">Comment Moderation</a></li>
					<li><a href="/admin/pages/">Page Properties</a></li>
					<li><a href="/office/articletypes/">Article Types</a></li>
					<li><a href="/office/specials/">Special Articles</a></li>
					<li><a href="/office/ticker/">Facebook Articles</a></li>
					<li><a href="/office/advertising/">Advertising</a></li>
				</ul>
<?php
	}
?>
				<ul>
					<li class="first">Sections</li>
					<li><a href="/office/news/uninews/">Uni News</a></li>
					<li><a href="/office/news/features/">Features</a></li>
					<li><a href="/office/news/lifestyle/">Lifestyle</a></li>
					<li><a href="/office/news/arts/">Arts</a></li>
					<li><a href="/office/news/sport/">Sport</a></li>
					<li><a href="/office/news/blogs/">Blogs</a></li>
					<li><a href="/office/news/videocasts/">Videocasts</a></li>
					<li><a href="/office/news/comment/">News Comment</a></li>
					<li><a href="/office/podcasts/">Podcasts</a></li>
				</ul>
				<ul>
					<li class="first">Info + Reviews</li>
					<li><a href="/office/prlist/">Directory</a></li>
					<li><a href="/office/reviewlist/food/">Food</a></li>
					<li><a href="/office/reviewlist/drink/">Drink</a></li>
					<li><a href="/office/reviewtags/">Review Tags</a></li>
					<li><a href="/office/leagues/">Leagues</a></li>
					<li><a href="/office/pr/summary/">PR System</a></li>
					<li><a href="/office/campaign/">Campaigns</a></li>
					<li><a href="/office/charity/">Charities</a></li>
					<li><a href="/office/howdoi/">How Do I</a></li>
					<li><a href="/office/games/">Game Zone</a></li>
					<li><a href="http://yorkipedia.theyorker.co.uk">Yorkipedia</a></li>
					<!--<li><a href="/office/packages/">Packages</a></li>-->
				</ul>
				<ul>
					<li class="first">Photos</li>
					<li><a href="/office/photos/">Photo Requests</a></li>
					<li><a href="/office/gallery/">Gallery</a></li>
					<li><a href="/office/banners/">Homepage Banners</a></li>
				</ul>
				<ul>
					<li class="first">Homepage</li>
					<li><a href="/office/quotes/">Quote Moderation</a></li>
					<li><a href="/office/guide/">Style Guide</a></li>
					<li><a href="/office/links/">Links</a></li>
				</ul>
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