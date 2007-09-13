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
			echo $head_title;
		} else {
			echo 'no pagename';
		}
	?></title>

	<link rel="shortcut icon" href="/images/yorker.ico" />
	<link rel="alternate" type="application/rss+xml" title="The Yorker - Campus News" href="/news/rss" />


	<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 6]><link href="/stylesheets/new-ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->

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
			<div id="TopBannerText">
				<p>
					<?php echo $vipinfo['name']; ?><br />
					<?php echo $vipinfo['organisation']; ?>
				</p>
			</div>
		</div>
	</div>

	<div id="Page">
		<div id="NavigationColumn">
			<form method="post" id="site_search" action="/search">
				<input type="text" name="site-search" id="site-search" onblur="search_onBlur()" onfocus="search_onFocus()" onkeyup="search_onKeyUp()" title="search the Site" value="search" autocomplete="off" />
			</form>
			<div id="ajax-results" onMouseOver="if (isMouseLeaveOrEnter(event, this)) {active=true;}" onMouseOut="if (isMouseLeaveOrEnter(event, this)) {search_Close(false); active=false;}">
				<p class="note"><a href="javascript:search_noShow(0)">toggle</a></a></p>
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
			<div id="NavigationMenu">
				<!-- Nasty "first" class used as IE6 doesn't have :first-child -->
				<ul class="first">
					<li class="first"><a href="<?php echo site_url('viparea'); ?>">VIP Area Home</a></li>
				</ul>
				<ul>
					<li class="first"><a href="<?php echo vip_url('directory/information'); ?>">Directory Entry</a></li>
					<li><a href="<?php echo vip_url('calendar'); ?>">Manage Events</a></li>
					<li><a href="<?php echo vip_url('notices'); ?>">Manage Notices</a></li>
					<li><a href="<?php echo vip_url('members'); ?>">Manage Members</a></li>
				</ul>
				<ul>
					<li class="first"><a href="<?php echo vip_url('account'); ?>">Settings</a></li>
					<li><a href="<?php echo vip_url('advertising'); ?>" onclick="alert('Coming soon...'); return false;">Advertising</a></li>
					<li><a href="<?php echo vip_url('contactpr'); ?>">Contact PR Rep</a></li>
				</ul>
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
