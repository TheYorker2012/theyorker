<?php
// Must echo through PHP in case short tags is turned on
echo('<?xml version="1.0" encoding="UTF-8"?>');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?php echo $description; ?>" />
	<meta name="keywords" content="<?php echo $keywords; ?>" />

	<title>The Yorker - <?php 
		// FIXME: backwards compatibility, remove when all pages are shown with titles
		if(isset($title)) { 
			echo $title; 
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
	
	<!-- BEGIN Multiple event handlers code -->
	<script type="text/javascript">
	//<![CDATA[

	// An array containing functors for all function to be run on page load
	var onLoadFunctions = new Array();

	// An array containing functors for all function to be run on page unload
	var onUnloadFunctions = new Array();

	// The function which is run on page load ensuring all functors are run
	function onLoadHandler() {
		for (i = 0; i < onLoadFunctions.length; i++) {
			onLoadFunctions[i]();
		}
	}
	// The function which is run on page unload ensuring all functors are run
	function onUnloadHandler() {
		for (i = 0; i < onUnloadFunctions.length; i++) {
			onUnloadFunctions[i]();
		}
	}

	//]]>
	</script>
	<!-- END Multiple event handlers code -->

	<!-- BEGIN 'head' tag items from controlling script -->
	<?php if (isset($extra_head)) { echo($extra_head."\n"); }; ?>
	<!-- END 'head' tag items from controlling script -->

	<?php
	include('maps.php');
	?>
	
	<!-- BEGIN search box code -->
	<script type="text/javascript">
	//<![CDATA[

	function inputFocus(element) {
		if (element.value == element.defaultValue) {
			element.value = '';
		}
	}

	function inputBlur(element) {
		if (element.value =='') {
			element.value = element.defaultValue;
		}
	}

	//]]>
	</script>
	<!-- END search box code -->

	<!-- BEGIN feedback form code -->
	<script type="text/javascript">
	//<![CDATA[

	function showFeedback() {
		var showFeedbackObj = document.getElementById('ShowFeedback');
		var feedbackObj = document.getElementById('FeedbackForm');
		showFeedbackObj.style.display = 'none';
		feedbackObj.style.display = 'block';

		return false;
	}

	function hideFeedback() {
		var showFeedbackObj = document.getElementById('ShowFeedback');
		var feedbackObj = document.getElementById('FeedbackForm');
		showFeedbackObj.style.display = 'block';
		feedbackObj.style.display = 'none';

		return false;
	}
	
	onLoadFunctions.push(hideFeedback);

	//]]>
	</script>
	<!-- END feedback form code -->

</head>

<body onload="onLoadHandler()" onunload="onUnloadHandler()">
	<div id="Header">
		<div id="HeaderMenu">
			<?php
			// Set by GenerateToplinks in mainframe_helper
			if (isset($toplinks)) {
				foreach ($toplinks as $link) {
					if (is_string($link)) {
						echo $link.' | ';
					} elseif (is_array($link)) {
						echo '<a href="'.$link[1].'">'.$link[0].'</a> | ';
					}
				}
			}
			?>
			<a href="/about/">about us</a> |
			<a href="/contact/">contact us</a> |
			<a href="/faq/">FAQs</a>
		</div>

		<div id="TopBanner">
			<h1 id="TopBannerName">
				<a href="/home/"><img src="/images/prototype/header/header_Layer-1.gif" width="275" height="108" alt="The Yorker"/></a>
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
			<form id="site_search" action="/search/layout" method="post">
				<fieldset id="SearchBox">
					<input type="text" value="Search for..." onfocus="inputFocus(this);" onblur="inputBlur(this);" />
				</fieldset>
			</form>
			<div id="NavigationMenu">
				<!-- Nasty "first" class used as IE6 doesn't have :first-child -->
				<ul class="first">
					<li class="first"><a href="/">Home</a></li>
					<li><a href="/calendar/">Calendar</a></li>
					<li><a href="/directory/">Directory</a></li>
				</ul>
				<ul>
					<li class="first"><a href="/news/uninews">Uni News</a></li>
					<li><a href="/news/national/">UK &amp; World News</a></li>
					<li><a href="/news/features/">Features</a></li>
					<li><a href="/news/lifestyle/">Lifestyle</a></li>
					<li><a href="/news/arts/">Arts</a></li>
					<li><a href="/news/sport/">Sport</a></li>
					<li><a href="/reviews/food">Food</a></li>
					<li><a href="/reviews/drink">Drink</a></li>
					<li><a href="/reviews/culture">Culture</a></li>
					<li><a href="/campaign/">Campaigns</a></li>
					<li><a href="/news/archive/">News Archive</a></li>
				</ul>
				<ul>
					<li class="first"><a href="/charity/">Our Charity</a></li>
					<li><a href="/howdoi/">How Do I</a></li>
					<li><a href="http://yorkipedia.theyorker.co.uk">Yorkipedia</a></li>
					<li><a href="/games/">Games Zone</a></li>
				</ul>
				<ul>
					<li class="first"><a href="/viparea/">Enter VIP Area</a></li>
					<li><a href="/office/">Enter Office</a></li>
				</ul>
			</div>
		</div>

		<div id="MainBodyPane">
			<h1 id="PageTitle">
				<?php if(isset($title)) { echo $title."\n"; } else { echo 'no pagename'."\n"; } ?>
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

	<div id="Footer">
		<a id="ShowFeedback" href="#FeedbackForm" onclick="showFeedback();">Please give feedback about this page</a>
		<div id="FeedbackForm">
			<form id="feedback_form" action="<?php echo site_url('feedback/'); ?>" method="post" class="form">
				<fieldset>
					<h4>Feedback</h4>
					<!-- <br /> tags necessary for correct rendering in text based browsers -->
					<label for="a_authorname">Your Name: </label>
						<input type="text" name="a_authorname" id="a_authorname" value="" /><br />
					<label for="a_authoremail">Your E-mail: </label>
						<input type="text" name="a_authoremail" id="a_authoremail" value="" /><br />
					<label for="a_rating">Your Rating: </label>
						<select name="a_rating" id="a_rating" size="1">
							<option value="" selected="selected">&nbsp;</option>
							<option value="1">What's this for?</option>
							<option value="2">Good idea - but what does it do?</option>
							<option value="3">Useful.. I guess.</option>
							<option value="4">Great idea, and easy to use!</option>
							<option value="5">Amazing!!</option>
						</select><br />
					<label for="a_feedbacktext">Your Comments: </label>
						<textarea name="a_feedbacktext" id="a_feedbacktext" rows="6" cols="40" ></textarea>
					<input type="hidden" name="a_pagetitle" id="a_pagetitle" value="<?php if(isset($title)) { echo str_replace("'", "", $title); } ?>" />
					<input type="hidden" name="r_redirecturl" id="r_redirecturl" value='<?php echo $_SERVER['REQUEST_URI']; ?>' />
				</fieldset>
				<fieldset>
					<input class="button" type="submit" name="r_submit" id="r_submit" value="Submit" />
					<input class="button" type="reset" name="r_cancel" id="r_cancel" value="Cancel" onclick="hideFeedback();"/>
				</fieldset>
			</form>
		</div>
		<small>
			Copyright 2007 The Yorker.  Use of this Web site constitutes acceptance of the The Yorker 
			<a href='/policy/#user_agreement'>User Agreement</a> and 
			<a href='/policy/#privacy_policy'>Privacy Policy</a>.  
			 Page rendered in {elapsed_time} seconds.  
		</small>
	</div>
</body>
</html>
