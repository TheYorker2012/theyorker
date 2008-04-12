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


	<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" />
	<!--[if lte IE 6]><link href="/stylesheets/new-ie6fix.css" rel="stylesheet" type="text/css" /><![endif]-->
	<link href="/stylesheets/home_new.css" rel="stylesheet" type="text/css" />
	
	<?php
		// Get common javascript
		include('top_script.php');
	?>

</head>

<body onload="onLoadHandler()" onunload="onUnloadHandler()">
	<div id="Header">
		<div id="TopBanner">
			<h1 id="TopBannerName">
				<a href="/"><img src="/images/prototype/new_home/header.jpg" alt="The Yorker" /></a>
			</h1>
		</div>
		<div id="HeaderMenu">
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
			<a href="<?php echo($this->config->item('static_web_address')); ?>/pdf/advertisewithus.pdf">advertise with us</a> |
			<a href="/account/">my account</a> |
			<a href="/about/">about us</a> |
			<a href="/pages/join_our_team/">join us</a> |
			<a href="/faq/">FAQs</a>
		</div>
		<div id="TopBannerPictures">
			<div id="bannertop1" onclick="document.getElementById('bannertop2').style.display='block';document.getElementById('bannertop1').style.display='none';">
				<img src="/images/prototype/new_home/man1.png" alt="News" />
				<img src="/images/prototype/new_home/man2.png" alt="Calendar" />
				<img src="/images/prototype/new_home/man3.png" alt="Reviews" />
			</div>
			<div id="bannertop2" style="display:none"  onclick="document.getElementById('bannertop3').style.display='block';document.getElementById('bannertop2').style.display='none';">
				<img src="/images/prototype/new_home/banner_ad.jpg" alt="Banner Advert" style="margin-top:15px" />
			</div>
			<div id="bannertop3" style="display:none"  onclick="document.getElementById('bannertop4').style.display='block';document.getElementById('bannertop3').style.display='none';document.getElementById('picoftheday').style.display='none';">
				<img src="/images/prototype/new_home/banner_ad2.jpg" alt="Banner Advert" style="margin-top:15px" />
			</div>
			<div id="bannertop4" style="display:none"  onclick="document.getElementById('bannertop1').style.display='block';document.getElementById('bannertop4').style.display='none';document.getElementById('picoftheday').style.display='block';">
				<img src="/images/prototype/new_home/new_homepage_banner.png" alt="Banner Advert" />
			</div>
		</div>
		<div style="clear:both"></div>
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
					<li class="first"><a href="/">My Home</a></li>
					<li><a href="/calendar/">My Calendar</a></li>
					<li><a href="/directory/">Directory</a></li>
				</ul>
				<ul>
					<li class="first"><a href="/news/uninews">Uni News</a></li>
					<li><a href="/sport/">Sport</a></li>
					<li><a href="/news/comment/">News Comment</a></li>
					<?php /*
					<li><a href="/news/national/">UK &amp; World News</a></li>
					*/ ?>
					<li><a href="/news/features/">Features</a></li>
					<li><a href="/lifestyle/">Lifestyle</a></li>
					<li><a href="/arts/">Arts</a></li>
					<li><a href="/blogs/">Blogs</a></li>
					<li><a href="/reviews/food">Food</a></li>
					<li><a href="/reviews/drink">Drink</a></li>
					<?php /*
					<li><a href="/reviews/culture">Culture</a></li>
					*/ ?>
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
				<?php /*
				<ul>
					<li class="first"><a href="/viparea/">Enter VIP Area</a></li>
					<li><a href="/office/">Enter Office</a></li>
				</ul>
				*/ ?>
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


	<?php
		// Get common footer
		include('footer.php');
	?>

</body>
</html>
