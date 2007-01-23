<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<title>The Yorker - <?php if(isset($title)) { echo $title; } else { echo 'no pagename'; } //FIXME backwards compatibility, remove when all pages are shown with titles?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel='shortcut icon' href='/images/yorker.ico' />
<link rel='alternate' type='application/rss+xml' title='The Yorker - Campus News' href='/news/rss' />
<link href="/stylesheets/general.css" rel="stylesheet" type="text/css" />
<link href="/stylesheets/stylesheet.css" rel="stylesheet" type="text/css" />
<!-- BEGIN 'head' tag items from controlling script -->
<?php echo @$extra_head; ?>
<!-- END 'head' tag items from controlling script -->
</head>

<body onLoad="preloader(); if(typeof onLoad == 'function') onLoad();">
<a name="top"></a>
<script src="/javascript/jumpto.js" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript">

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


</script>

<div style="width: 100%;" align="center">
<div style="width: 780px; text-align: left; background-color: #fff;">
	<div style="height: 22px; text-align: right;" class="HeaderMenu">
		<a class="HeaderLinks" href="/login/">log in</a> |
		<a class="HeaderLinks" href="/about/">about us</a> |
		<a class="HeaderLinks" href="/contact/">contact us</a> |
		<a class="HeaderLinks" href="/faq/">FAQs</a>
	</div>
	<div style="background-image:url(/images/prototype/header/homepage_bk.gif); background-repeat:repeat-x; height:108; float: left;">
		<div style="float: left;">
			<a href="/home/">
				<img src="/images/prototype/header/header_Layer-1.gif" width="275" height="108" alt="" border="0" />
			</a>
		</div>
		<div style="float: right;">
			<a href="/news/" onMouseOut="document.img01.src='/images/prototype/header/header_Layer-4.gif';" onMouseOver="document.img01.src='/images/prototype/header/header2_Layer-4.gif';">
				<img name="img01" src="/images/prototype/header/header_Layer-4.gif" width="107" height="108" alt="News" border="0" />
			</a>
			&nbsp;
			<a href="/calendar/" onMouseOut="document.img02.src='/images/prototype/header/header_Layer-3.gif';" onMouseOver="document.img02.src='/images/prototype/header/header2_Layer-3.gif';">
				<img name="img02" src="/images/prototype/header/header_Layer-3.gif" width="107" height="108" alt="Calendar" border="0" />
			</a>
			&nbsp;
			<a href="/reviews/" onMouseOut="document.img03.src='/images/prototype/header/header_Layer-2.gif';" onMouseOver="document.img03.src='/images/prototype/header/header2_Layer-2.gif';">
				<img name="img03" src="/images/prototype/header/header_Layer-2.gif" width="108" height="108" alt="Reviews" border="0" />
			</a>
			&nbsp;
		</div>
	</div>
	<div style="background-color: #fff;">
		<form name='site_search' action='/search/layout' method='post' style='display:inline; '>
		<input type="text" style="float: left; width: 130px; font-size: 10px; border: solid 1px #20c1f0; color: #20c1f0; padding: 2px 0px 2px 2px; margin: 5px; margin-left: 0px;" 
value=" &gt; Search" onFocus="if (this.value==this.defaultValue) this.value=''" />
		</form>
		<div style="float: right; width: 630px; margin-bottom: 8px; background-color: #20c1f0; padding: 3px 0px 3px 5px; color: #fff; font-size: medium; font-weight: bold; height: 18px; " >
				<div style="float: right"><a href="javascript:history.go(-1)"><img src="/images/prototype/header/backarrow.gif" alt="Back" /></a>&nbsp;</div>
				<?php if(isset($title)) { echo $title; } else { echo 'no pagename'; } ?>
		</div>
	</div>
	<br style="clear: both;" />
	<div style="float: left; width: 140px; margin-right: 5px; background-color: #fff;">
		<div class='navigationmenu_item'>
			<a href='/'>Home</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/calendar/'>Calendar</a>
		</div>
		<div class='navigationmenu_item_noborder'>
			<a href='/directory/'>Directory</a>
		</div>
		<hr/>
		<div class='navigationmenu_item'>
			<a href='/news/'>Uni News</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/news/national/'>UK & World News</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/news/features/'>Features</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/news/lifestyle/'>Lifestyle</a>
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
		<hr/>
		<div class='navigationmenu_item'>
			<a href='/charity/'>Our Charity</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='/howdoi/'>How Do I</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='http://yorkipedia.theyorker.co.uk'>Yorkipedia</a>
		</div>
		<div class='navigationmenu_item'>
			<a href='#'>Games Zone</a>
		</div>
		<div style='padding: 20px 0px 0px 0px;' align='center'>
			<img src='/images/adverts/3-120x600.gif' />
		</div>
	</div>
	<div style="float: right; width: 630px; margin-left: 5px; background-color: #fff;">
	<?php
		// Display each message
		foreach ($messages as $message) {
			// Display the message
			$message->Load();
		}
		// Display the main content
		$content[0]->Load();
	?>
	
		<div class='clear'>&nbsp;</div>

		<div id="feedbackdiv" style="width: 100%; display: none;">
	
		<form name='feedback_form' id='feedback_form' action='<?php echo site_url('feedback/'); ?>' method='post' class='form'>
			<fieldset>
				<legend>Feedback</legend>
				<label for='a_authorname'>Your Name:</label>
				<input type='text' name='a_authorname' id='a_authorname' value='' />
				<input type='hidden' name='a_pagetitle' id='a_pagetitle' value='<?php if(isset($title)) { echo str_replace("'", "", $title); } ?>' />
				<br />
				<label for='a_authoremail'>Your E-mail:</label>
				<input type='text' name='a_authoremail' id='a_authorname' value='' />
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
</div>
<br style="clear: both;" />

<div style="width: 100%; text-align: center;" id="feedbackshowdiv">
	<a href="#" onclick="document.getElementById('feedbackdiv').style.display = 'block'; document.getElementById('feedbackshowdiv').style.display = 'none'; return false;"><span style="color:#ff6a00; font-weight: bold;">Please give Feedback about this page</span></a>
</div>

<br /><br />

<div style="text-align: center; width: 100%;">
<small>Page rendered in {elapsed_time} seconds</small>
</div>

</body>
</html>
