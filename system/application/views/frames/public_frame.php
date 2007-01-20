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
		<input type="text" style="float: left; width: 130px; font-size: 10px; border: solid 1px #20c1f0; color: #20c1f0; padding: 2px 0px 2px 2px; margin: 5px; margin-left: 0px; " value=" &gt; Search" onFocus="if (this.value==this.defaultValue) this.value=''" />
		</form>
		<div style="float: right; width: 630px; margin-bottom: 8px; background-color: #20c1f0; padding: 3px 0px 3px 5px; color: #fff; font-size: large; " >
				<div style="float: right"><a href="javascript:history.go(-1)"><img src="/images/prototype/header/backarrow.gif" alt="Back" /></a>&nbsp;</div>
				<?php if(isset($title)) { echo $title; } else { echo 'no pagename'; } ?>
		</div>
	</div>
	<br style="clear: both;" />
	<div style="float: left; width: 140px; margin-right: 5px; background-color: #fff;">
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/'>Home</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/calendar/'>Calendar</a>
		</div>
		<div style="color: #aaa; font-size: small;  padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/directory/'>Directory</a>
		</div>
		<hr/>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/news/'>Uni News</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/news/national/'>UK & World News</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/news/features/'>Features</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/news/lifestyle/'>Lifestyle</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/reviews/food'>Food</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/reviews/drink'>Drink</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/reviews/culture'>Culture</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/campaign/'>Campaigns</a>
		</div>
		<div style="color: #aaa; font-size: small; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/news/archive/'>News Archive</a>
		</div>
		<hr/>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/charity/'>Our Charity</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='/howdoi/'>How Do I</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='http://yorkipedia.theyorker.co.uk'>Yorkipedia</a>
		</div>
		<div style="color: #aaa; font-size: small; border-bottom: 1px solid #ccc; padding: 0px 0px 3px 4px; margin: 0px 0px 3px 0px;">
			<a href='#'>Games Zone</a>
		</div>
	</div>
	<div style="float: right; width: 630px; margin-left: 5px; background-color: #fff;">
	<?php $content[0]->Load(); ?>
	</div>
</div>
</div>
<br style="clear: both;" />
<div style="text-align: center; width: 100%;">
<small>Page rendered in {elapsed_time} seconds</small>
</div>
</body>
</html>
