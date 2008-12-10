<?php
// Must echo through PHP in case short tags is turned on
echo('<?xml version="1.0" encoding="UTF-8"?>');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
<?php if (isset($head_title)) { ?>
	<meta name="title" content="<?php echo(xml_escape($head_title)); ?> - The Yorker" />
<?php }
if (isset($main_image)) { ?>
	<link rel="image_src" href="http://<?php echo($_SERVER['SERVER_NAME'].$main_image); ?>" />
<?php }
/* Valid values for medium_type are "audio", "image", "video", "news", "blog" and "mult" */
if (isset($medium_type)) { ?>
	<meta name="medium" content="<?php echo($medium_type); ?>" />
<?php } ?>
	<meta name="description" content="<?php echo(xml_escape($description)); ?>" />
	<meta name="keywords" content="<?php echo(xml_escape($keywords)); ?>" />
	<meta name="verify-v1" content="slrlMuizkqTRTqt5W2zF1EZ6nMrwx+/qRmNDJ7xt2m8=" />

	<title><?php
		// FIXME: backwards compatibility, remove when all pages are shown with titles
		if(isset($head_title)) {
			echo(xml_escape($head_title));
		} else {
			echo('no pagename');
		}
	?> - The Yorker</title>

	<link rel="shortcut icon" href="/images/yorker.ico" />
	<link rel="alternate" type="application/rss+xml" title="The Yorker - Campus News" href="/feeds/news" />


	<link href="/stylesheets/v2.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 6]><link href="/stylesheets/new-ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->

	<?php
		// Get common javascript
		include('top_script.php');
	?>

</head>

<body onload="onLoadHandler()" onunload="onUnloadHandler()">
	<div id="Header">
		<div id="HeaderItems">
			<div id="HeaderMenu">
				<?php
				// Set by GenerateToplinks in mainframe_helper
				if (isset($toplinks)) {
					foreach ($toplinks as $link) {
						if (is_string($link)) {
							echo(xml_escape($link). ' | ');
						} elseif (is_array($link)) {
							echo('<a href="' . xml_escape($link[1]) . '">' . xml_escape($link[0]) . '</a> | ');
						}
					}
				}
				?>
				<a href="/account/">my account</a>
			</div>
			<div id="HeaderTime">
				16:44
			</div>
		</div>
		<h1 id="Logo"><a href="/"><img src="/images/version2/frame/logo.png" alt="The Yorker" /></a></h1>
	</div>

	<div id="Bar">
		<div id="BarTicker">
			latest news: <a href="http://www.theyorker.co.uk/news/uninews/2414">More students to miss out on grants</a>
		</div>
	</div>

	<div id="Navigation">
		<ul id="Tabs">
			<li class="current"><a href="/home">home</a></li>
			<li><a href="/news/uninews">news</a></li>
			<li><a href="/sport">sport</a></li>
			<li><a href="/arts">arts</a></li>
			<li><a href="/lifestyle">lifestyle</a></li>
			<li><a href="/home">stuff</a></li>

			<li class="link"><a href="http://www.yusu.org/"><img src="/image/link/248" width="20" height="20" alt="YUSU" title="YUSU" /></a></li>
			<li class="link"><a href="http://www.facebook.com/"><img src="/image/link/246" width="20" height="20" alt="Facebook" title="Facebook" /></a></li>
			<li class="link"><a href="http://news.bbc.co.uk/"><img src="/image/link/245" width="20" height="20" alt="BBC News" title="BBC News" /></a></li>
			<li class="link"><a href="http://en.wikipedia.org/"><img src="/image/link/244" width="20" height="20" alt="Wikipedia" title="Wikipedia" /></a></li>
			<li class="link"><a href="http://gmail.google.com/"><img src="/image/link/250" width="20" height="20" alt="Gmail" title="Gmail" /></a></li>
		</ul>
	</div>

	<div id="Page">
		<div id="MainBodyPane">
		
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

			<div class="clear"></div>
		</div>
	</div>

<?php /*
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
					<li class="first"><a href="/">My Home</a></li>
					<li><a href="/calendar/">My Calendar</a></li>
					<li><a href="/directory/">Directory</a></li>
				</ul>
				<ul>
					<li class="first"><a href="/news/uninews">Uni News</a></li>
					<li><a href="/sport/">Sport</a></li>
					<li><a href="/news/comment/">News Comment</a></li>
					<li><a href="/news/features/">Features</a></li>
					<li><a href="/lifestyle/">Lifestyle</a></li>
					<li><a href="/arts/">Arts</a></li>
					<li><a href="/blogs/">Blogs</a></li>
					<li><a href="/food/">Food</a></li>
					<li><a href="/reviews/drink">Drink</a></li>
					<li><a href="/news/videocasts">Videocasts</a></li>
					<li><a href="/campaign/">Campaigns</a></li>
					<li><a href="/news/archive/">Archive</a></li>
					<li><a href="/feeds/">Feeds</a></li>
				</ul>
				<ul>
					<li class="first"><a href="/charity/">Our Charity</a></li>
					<li><a href="/howdoi/">How Do I</a></li>
					<li><a href="http://yorkipedia.theyorker.co.uk">Yorkipedia</a></li>
					<li><a href="/games/">Game Zone</a></li>
				</ul>
				<?php
				if (isset($extra_menu_buttons) && !empty($extra_menu_buttons)) {
					echo('<ul>');
					foreach ($extra_menu_buttons as $key => $button) {
						echo('<li'.(!$key ? ' class="first"':'').'>');
						if (is_string($button)) {
							echo(xml_escape($button));
						} else {
							echo('<a href="'.$button[1].'">'.xml_escape($button[0]).'</a>');
						}
						echo('</li>');
					}
					echo('</ul>');
				}
				?>
			</div>

<?php
	if (isset($advert) && !empty($advert['image_id']) && !empty($advert['url'])) {
		echo('			<a href="'.xml_escape($advert['url']).'" target="_blank"><img src="/image/advert/'.$advert['image_id'].'" width="120" height="600" style="margin-top: 40px;" alt="'.xml_escape($advert['alt']).'" title="'.xml_escape($advert['alt']).'" /></a>'."\n");
	} elseif ($this->config->item('enable_adsense')) {
		$this->load->view('frames/adsense');
	}
?>
		</div>

		<div id="MainBodyPane">
			<h1 id="PageTitle">
				<?php
				if(isset($body_title)) {
					echo xml_escape($body_title)."\n";
				} else {
					echo 'no pagename'."\n";
				}
				if(isset($paged_edit_url) && NULL !== $paged_edit_url) {
					echo('<a href="'.$paged_edit_url.'">[edit]</a>');
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
*/ ?>


	<?php
		// Get common footer
		include('footer.php');
	?>

</body>
</html>
