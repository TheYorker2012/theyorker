<?php
// Must echo through PHP in case short tags is turned on
echo('<?xml version="1.0" encoding="UTF-8"?>');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>The Yorker - <?php if(isset($title)) { echo $title; } else { echo 'no pagename'; } //FIXME backwards compatibility, remove when all pages are shown with titles?></title>
<meta name="description" content="<?php echo $description; ?>" />
<meta name="keywords" content="<?php echo $keywords; ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel='shortcut icon' href='/images/yorker.ico' />
<link rel='alternate' type='application/rss+xml' title='The Yorker - Campus News' href='/news/rss' />
<link href="/stylesheets/general.css" rel="stylesheet" type="text/css" />
<link href="/stylesheets/stylesheet.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
// An array containing functors for all function to be run on page load
var onLoadFunctions = new Array();

// An array containing functors for all function to be run on page unload
var onUnloadFunctions = new Array();

// The function which is run on page load ensuring all functors are run
function onLoadHandler() {
	// foreach loop does not work for some reason....
	for (i = 0; i < onLoadFunctions.length; i++) {
		onLoadFunctions[i]();
	}
}
// The function which is run on page unload ensuring all functors are run
function onUnloadHandler() {
	// foreach loop does not work for some reason....
	for (i = 0; i < onUnloadFunctions.length; i++) {
		onUnloadFunctions[i]();
	}
}
</script>

<!-- BEGIN 'head' tag items from controlling script -->
<?php echo @$extra_head; ?>
<!-- END 'head' tag items from controlling script -->

<?php 
if (isset($maps)) {
// The google maps API key will need to be changed whenever we change server
// There is a google account to do this:
//   username - theyorkermaps
//   password - same as the database
?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA4LuflJA4VPgM8D-gyba8yBQpSg5-_eQ-9kxEpRcRNaP_SBL1ahQ985h-Do2Gm1Tle5pYiLO7kiWF8Q" type="text/javascript"></script>
<script type="text/javascript">
function loadMaps() {
	// define a sctucture to store map settings
	function OMap(element, lat, lng) {
		this.element = element;
		this.lat = lat;
		this.lng = lng;
	}

	var map;
	var maps = new Array();
	<?php
	// Write code to put each the maps defined in PHP into a Javascript array
	foreach ($maps as $map) {
		echo 'maps.push(new OMap("'.$map['element'].'", '.$map['lat'].', '.$map['lng'].'));';
	}
	?>

	// For each map, update the page to actually show the map
	for (i = 0; i < maps.length; i++) {
		var mapobj = new GMap2(document.getElementById(maps[i].element));
		mapobj.setCenter(new GLatLng(maps[i].lat, maps[i].lng), 13);
		mapobj.enableDoubleClickZoom();
		mapobj.enableContinuousZoom();
	}
}

onLoadFunctions.push(loadMaps);
onUnloadFunctions.push(GUnload);
</script>
<?php
}
?>

<script src="/javascript/jumpto.js" type="text/javascript"></script>

<script type="text/javascript">
function preloader()
{
     // counter
     var i = 0;

     // create object
     imageObj = new Image();

     // set image list
     images = new Array();
     images[0]="/images/prototype/header/header2_Layer-4.gif";
     images[1]="/images/prototype/header/header2_Layer-3.gif";
     images[2]="/images/prototype/header/header2_Layer-2.gif";

     // start preloading
     for(i=0; i<=3; i++)
     {
     	imageObj.src=images[i];
     }
}

// Add the preloader to the functions to be run on page load
onLoadFunctions.push(preloader);
</script>

</head>

<body onLoad="onLoadHandler()" onUnload="onUnloadHandler()">
<a name="top"></a>


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
		<div style="float: right;">
			<span onMouseOut="document.img01.src='/images/prototype/header/header_Layer-4.gif';" onMouseOver="document.img01.src='/images/prototype/header/header2_Layer-4.gif';">
				<img name="img01" src="/images/prototype/header/header_Layer-4.gif" width="107" height="108" alt="News" border="0" />
			</span>
			&nbsp;
			<span onMouseOut="document.img02.src='/images/prototype/header/header_Layer-3.gif';" onMouseOver="document.img02.src='/images/prototype/header/header2_Layer-3.gif';">
				<img name="img02" src="/images/prototype/header/header_Layer-3.gif" width="107" height="108" alt="Calendar" border="0" />
			</span>
			&nbsp;
			<span onMouseOut="document.img03.src='/images/prototype/header/header_Layer-2.gif';" onMouseOver="document.img03.src='/images/prototype/header/header2_Layer-2.gif';">
				<img name="img03" src="/images/prototype/header/header_Layer-2.gif" width="108" height="108" alt="Reviews" border="0" />
			</span>
			&nbsp;
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
				<?php if(isset($title)) { echo $title; } else { echo 'no pagename'; } ?>
		</div>
	</div>
	<br style="clear: both;" />
	<div style="float: left; width: 120px; margin-top: 8px; margin-right: 5px; background-color: #fff;">
		<div class='navigationmenu_item'>
			<a href='/'>Home</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/calendar/'>Calendar</a>
		</div>
		<div class='navigationmenu_item_noborder'>
			<a href='/directory/'>Directory</a>
		</div>

		<div class='navigationmenu_item'>
			<a href='/news/'>Uni News</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/national/'>UK &amp; World News</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/features/'>Features</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/lifestyle/'>Lifestyle</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/reviews/food'>Food</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/reviews/drink'>Drink</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/reviews/culture'>Culture</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/campaign/'>Campaigns</a>
		</div>
		<div class='navigationmenu_item_noborder'>
			<a href='/news/archive/'>News Archive</a>
		</div>

		<div class='navigationmenu_item'>
			<a href='/charity/'>Our Charity</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/howdoi/'>How Do I</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='http://yorkipedia.theyorker.co.uk'>Yorkipedia</a>
		</div>
		<div class='navigationmenu_item_noborder'>
			<a href='/games/'>Games Zone</a>
		</div>

		<div class='navigationmenu_item'>
			<a href='/viparea/'>Enter VIP Area</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/office/'>Enter Office</a>
		</div>

		<div style='padding: 20px 0px 0px 0px;' align='center'>
			<img src='/images/adverts/3-120x600.gif' alt='Ad' title='Ad' />
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

</div>
<br style="clear: both;" />

<div align='center'>
	<div style="width: 780px; margin-top: 8px; margin-left: 5px;">
		<div id="feedbackdiv" style="width: 100%; display: none;">
			<form name='feedback_form' id='feedback_form' action='<?php echo site_url('feedback/'); ?>' method='post' class='form'>
				<fieldset>
					<h4>Feedback</h4>
					<label for='a_authorname'>Your Name:</label>
					<input type='text' name='a_authorname' id='a_authorname' value='' />
					<input type='hidden' name='a_pagetitle' id='a_pagetitle' value='<?php if(isset($title)) { echo str_replace("'", "", $title); } ?>' />
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
					<input type='submit' name='r_submit' id='r_submit' value='Submit' class='button' />
					<input type='reset' name='r_cancel' id='r_cancel' value='Cancel' class='button' onClick="document.getElementById('feedbackshowdiv').style.display = 'block'; document.getElementById('feedbackdiv').style.display = 'none';"/>
					<br />
				</fieldset>
			</form>
		</div>
	</div>
	<div style="width: 780px; text-align: center;" id="feedbackshowdiv">
		<a href="#" onclick="document.getElementById('feedbackdiv').style.display = 'block'; document.getElementById('feedbackshowdiv').style.display = 'none'; return false;"><span style="color:#ff6a00; font-weight: bold;">Please give Feedback about this page</span></a>
	</div>
	<br /><br />
	<div style="text-align: center; width: 780px;">
		<small>Copyright  2007 The Yorker. Use of this Web site constitutes acceptance of the The Yorker <a href='/policy/#user_agreement'>User Agreement</a> and <a href='/policy/#privacy_policy'>Privacy Policy</a>. Page rendered in {elapsed_time} seconds</small>
	</div>
</div>
</body>
</html>
