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
	<meta name="verify-v1" content="5poz9wzYQRZavDYfeR105NoeDMr2URjQ0DFD4uH+MsY=" />

	<title><?php
		// FIXME: backwards compatibility, remove when all pages are shown with titles
		if(isset($head_title)) {
			echo $head_title;
		} else {
			echo 'no pagename';
		}
	?> - The Yorker</title>

	<link rel="shortcut icon" href="/images/yorker.ico" />
	<link rel="alternate" type="application/rss+xml" title="The Yorker - Campus News" href="/news/rss" />


	<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 6]><link href="/stylesheets/new-ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->
	<link href="/stylesheets/search.css" rel="stylesheet" type="text/css" />

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
			<a href="/account/">my account</a> |
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
				<img src="/images/prototype/header/header_Layer-4.gif" alt="News" />
				<img src="/images/prototype/header/header_Layer-3.gif" alt="Calendar" />
				<img src="/images/prototype/header/header_Layer-2.gif" alt="Reviews" />
			</div>
		</div>
	</div>

	<div id="Page">
		<div id="NavigationColumn">
			<form method="post" id="site_search" action="/search">
				<input type="text" name="site-search" id="site-search" onblur="search_onBlur()" onfocus="search_onFocus()" onkeyup="search_onKeyUp()" title="search the Site" value="search" autocomplete="off" />
				<div id="ajax-results" onMouseOver="if (isMouseLeaveOrEnter(event, this)) {active=true;}" onMouseOut="if (isMouseLeaveOrEnter(event, this)) {search_Close(false); active=false;}">
					<p class="note"><a href="javascript:search_noShow(0)">toggle</a></p>
					<h1>Articles</h1>
					<ul id="ajax-articles">
						<li>No results found</li>
					</ul>
					<p class="note"><a href="javascript:search_noShow(1)">toggle</a></p>
					<h1>Directory</h1>
					<ul id="ajax-dir">
						<li>No results found</li>
					</ul>
					<p class="note"><a href="javascript:search_noShow(2)">toggle</a></p>
					<h1>Events</h1>
					<ul id="ajax-events">
						<li>No results found</li>
					</ul>
					<p class="note"><a href="javascript:search_noShow(3)">toggle</a></p>
					<h1>Yorkipedia</h1>
					<p class="extraLogin">Login to see your own events here</p>
					<ul id="ajax-york">
						<li>No results found</li>
					</ul>
				</div>
			</form>
			<div id="NavigationMenu">
				<!-- Nasty "first" class used as IE6 doesn't have :first-child -->
				<ul class="first">
					<li class="first"><a href="/">My Home</a></li>
					<li><a href="/calendar/">My Calendar</a></li>
					<li><a href="/directory/">Directory</a></li>
				</ul>
				<ul>
					<li class="first"><a href="/news/uninews">Uni News</a></li>
					<li><a href="/news/comment/">News Comment</a></li>
					<!--<li><a href="/news/national/">UK &amp; World News</a></li>-->
					<li><a href="/news/features/">Features</a></li>
					<li><a href="/lifestyle/">Lifestyle</a></li>
					<li><a href="/news/arts/">Arts</a></li>
					<li><a href="/sport/">Sport</a></li>
					<li><a href="/news/blogs/">Blogs</a></li>
					<li><a href="/reviews/food">Food</a></li>
					<li><a href="/reviews/drink">Drink</a></li>
					<!--<li><a href="/reviews/culture">Culture</a></li>-->
					<li><a href="/campaign/">Campaigns</a></li>
					<li><a href="/news/archive/">News Archive</a></li>
				</ul>
				<ul>
					<li class="first"><a href="/charity/">Our Charity</a></li>
					<li><a href="/howdoi/">How Do I</a></li>
					<li><a href="http://yorkipedia.theyorker.co.uk">Yorkipedia</a></li>
					<!--<li><a href="/games/">Games Zone</a></li>-->
				</ul>
				<!--
				<ul>
					<li class="first"><a href="/viparea/">Enter VIP Area</a></li>
					<li><a href="/office/">Enter Office</a></li>
				</ul>
				-->
				<?php
				if (isset($extra_menu_buttons)) {
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

			<a href="http://www.derwentcollege.co.uk"><img src="/images/adverts/bigd_webad.jpg" width="120" height="600" style="margin-top: 80px;" alt="Big D" /></a>
		</div>

		<div id="MainBodyPane">
			<h1 id="PageTitle">
				<?php if(isset($body_title)) { echo $body_title."\n"; } else { echo 'no pagename'."\n"; } ?>
			</h1>

<!-- BEGIN generated content -->
<?php
				// TODO: check this works properly

				// Navigation bar
				if (isset($content['navbar']))
					$content['navbar']->Load();

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
