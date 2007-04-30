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
		if(isset($head_title)) { 
			echo $head_title; 
		} else { 
			echo 'no pagename'; 
		} 
	?></title>

	<link rel="shortcut icon" href="/images/yorker.ico" />
	<link rel="alternate" type="application/rss+xml" title="The Yorker - Campus News" href="/news/rss" />
	<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" />
	<link href="/stylesheets/general.css" rel="stylesheet" type="text/css" />
	<link href="/stylesheets/stylesheet.css" rel="stylesheet" type="text/css" />

	<!--<link href="/stylesheets/new.css" rel="stylesheet" type="text/css" /> -->
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
	
	//onLoadFunctions.push(hideFeedback);

	//]]>
	</script>
	<!-- END feedback form code -->

</head>

<body onload="onLoadHandler()" onunload="onUnloadHandler()">

<div style="width: 100%;" align="center">
<div style="width: 780px; text-align: left; background-color: #fff;">
	<div style="height: 22px; text-align: right;" class="HeaderMenu">
		<?php
		// Set by GenerateToplinks in mainframe_helper
		if (isset($toplinks)) {
			foreach ($toplinks as $link) {
				if (is_string($link)) {
					echo $link.' | ';
				} elseif (is_array($link)) {
					echo '<a class="HeaderLinks" href="'.$link[1].'">'.$link[0].'</a> | ';
				}
			}
		}
		?>
		<a class="HeaderLinks" href="/about/">about us</a> |
		<a class="HeaderLinks" href="/contact/">contact us</a> |
		<a class="HeaderLinks" href="/faq/">FAQs</a>
	</div>
	<div style="width: 780px; background-image:url(/images/prototype/header/homepage_bk.gif); background-repeat:repeat-x; height:108; float: left;">
		<div style="float: left;">
			<a href="/home/">
				<img src="/images/prototype/header/header_Layer-1.gif" width="275" height="108" alt="" border="0" />
			</a>
		</div>
		<a href="/logout/office">
		<div style="float: right; width: 100px; overflow: hidden; color: #FFFFFF; text-align: center; position: relative; top: 37px; height: 40px;">
			<span style="font-size: 18px; font-weight:bold; ">Leave<br />Office</span>
		</div>
		</a>
		<div style="float: right; width: 400px; overflow: hidden; color: #FFFFFF; text-align: center; position: relative; top: 37px; height: 40px;">
			<span style="font-size: 40px; font-weight:bold; ">Office</span>
		</div>
	</div>
	<div style="background-color: #fff;">
		<form name='site_search' action='/search/layout' method='post' style='display:inline; '>
		<div style='float: left; width: 120px; font-size: 10px; border: solid 1px #20c1f0; padding: 2px; margin: 0px; margin-left: 0px;'>
			<img src='/images/prototype/header/search.png' alt='Search' title='Search' style='float: left; padding-top: 1px;' />
			<input type="text" style="float: right; color: #20c1f0; font-size: 12px; width: 100px; border: 0; margin: 2px 0; padding: 0;" value="Search for..." onFocus="if (this.value==this.defaultValue) this.value=''" onBlur="if (this.value=='') this.value=this.defaultValue" />
		</div>
		</form>
		<div style="float: right; width: 645px; margin-bottom: 0px; background-color: #20c1f0; padding: 3px 0px 3px 5px; color: #fff; font-size: medium; font-weight: bold; height: 18px; " >
				<?php if(isset($body_title)) { echo $body_title; } else { echo 'no pagename'; } ?>
		</div>
	</div>
	<br style="clear: both;" />
	<div style="float: left; width: 120px; margin-top: 8px; margin-right: 5px; background-color: #fff;">
		<div class='officenavigation_title'>
			Office
		</div>
		<div class='officenavigation_item'>
			<a href='/office/'>Office Home</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/logout/office'>Leave Office</a>
		</div>

		<div class='officenavigation_title'>
			Calendar
		</div>

		<div class='officenavigation_item'>
			<a href='/office/packages/'>Packages</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/directory/'>Directory</a>
		</div>

		<div class='officenavigation_title'>
			Sections
		</div>

		<div class='officenavigation_item'>
			<a href='/office/news/uninews/'>Uni News</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/national/'>UK &amp; World News</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/features/'>Features</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/lifestyle/'>Lifestyle</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/arts/'>Arts</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/sport/'>Sport</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/food/'>Food</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/drink/'>Drink</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/culture/'>Culture</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/news/archive/'>Manage Writer</a>
		</div>

		<div class='officenavigation_title'>
			Photographers
		</div>

		<div class='officenavigation_item'>
			<a href='/office/photos/'>Photos</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/gallery/'>Gallery</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/photographers'>Manage Photographers</a>
		</div>

		<div class='officenavigation_title'>
			Reviews
		</div>

		<div class='officenavigation_item'>
			<a href='/office/reviewlist/food'>Food</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/reviewlist/drink'>Drink</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/reviewlist/culture'>Culture</a>
		</div>

		<div class='officenavigation_title'>
			Other Features
		</div>
		<div class='officenavigation_item'>
			<a href='/office/campaign/'>Campaigns</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/charity/'>Our Charity</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/howdoi/'>How Do I</a>
		</div>
		<div class='officenavigation_item'>
			<a href='/office/games/'>Games Zone</a>
		</div>
	
	</div>
	<div style="float: right; width: 650px; padding: 0px; margin-top: 0px; margin-bottom: 0px; margin-left: 5px; background-color: #fff;">
	<?php
		// Navigation bar
		if (isset($content['navbar']))
			$content['navbar']->Load();

		// Display each message
		foreach ($messages as $message) {
			// Display the message
			$message->Load();
		}
	?>

	</div>
	<div style="float: right; width: 650px; padding: 0px; margin-top: 8px; margin-bottom: 0px; margin-left: 5px; background-color: #fff;">
		<?php
			// Display the main content
			$content[0]->Load();
		?>
	</div>
</div>

	<div style="float: right; width: 650px; margin-top: 8px; margin-left: 5px; background-color: #fff;">
		<div class='clear'>&nbsp;</div>

		<div id="feedbackdiv" style="width: 100%; display: none;">

		<form name='feedback_form' id='feedback_form' action='<?php echo site_url('feedback/'); ?>' method='post' class='form'>
			<fieldset>
				<legend>Feedback</legend>
				<label for='a_authorname'>Your Name:</label>
				<input type='text' name='a_authorname' id='a_authorname' value='' />
				<input type='hidden' name='a_pagetitle' id='a_pagetitle' value='<?php if(isset($head_title)) { echo str_replace("'", "", $head_title); } ?>' />
				<input type='hidden' name='r_redirecturl' id='r_redirecturl' value='<?php echo $_SERVER['REQUEST_URI']; ?>' />
				<br />
				<label for='a_authoremail'>Your E-mail:</label>
				<input type='text' name='a_authoremail' id='a_authoremail' value='' />
				<br />
				<label for='a_rating'>Your Rating:</label>
				<select name='a_rating' id='a_rating' size='1'>
					<option value='' selected='selected'></option>
					<option value='1'>What's this for?</option>
					<option value='2'>Good idea - but what does it do?</option>
					<option value='3'>Useful.. I guess.</option>
					<option value='4'>Great idea, and easy to use!</option>
					<option value='5'>Amazing!!</option>
				</select>
				<br />
				<label for='a_feedbacktext'>Your Comments:</label>
				<textarea name="a_feedbacktext" id="a_feedbacktext" rows="6" cols="40"></textarea>
				<br />
			</fieldset>
			<fieldset>
				<label for='r_submit'></label>
				<input type='submit' name='r_submit' id='r_submit' value='Submit' class='button' />
				<input type='reset' name='r_cancel' id='r_cancel' value='Cancel' class='button' onClick="document.getElementById('feedbackshowdiv').style.display = 'block'; document.getElementById('feedbackdiv').style.display = 'none';"/>
				<br />
			</fieldset>
		</form>

		</div>

	</div>


</div>
<br style="clear: both;" />

<div align='center'>
<div style="width: 780px; text-align: center;" id="feedbackshowdiv">
	<a href="#" onclick="document.getElementById('feedbackdiv').style.display = 'block'; document.getElementById('feedbackshowdiv').style.display = 'none'; return false;"><span style="color:#ff6a00; font-weight: bold;">Please give Feedback about this page</span></a>
</div>

<br />
<br />

<div style="text-align: center; width: 780px;">
	<small>Copyright  2007 The Yorker. Use of this Web site constitutes acceptance of the The Yorker <a href='/policy/#user_agreement'>User Agreement</a> and <a href='/policy/#privacy_policy'>Privacy Policy</a>. Page rendered in {elapsed_time} seconds</small>
</div>
</div>
</body>
</html>
