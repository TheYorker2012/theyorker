<style type="text/css">
div#bb_count span {
	border: 1px #fff solid;
	padding: 0;
	margin-left: 3px;
}
div#bb_count span.selected {
	background-color: #fff;
}
div#bb_count span a {
	padding: 0 5px;
	color: #fff;
}
div#bb_count span.selected a {
	color: #000;
}
</style>

<script type="text/javascript">
var current_story = 0;
var story_timer;
var stories = new Array();
stories[0] = new Array();
stories[0][0] = '/photos/billboard/1053';
stories[0][1] = 'News in Brief: FTR price-rise';
stories[0][2] = 'A new reduced-fare trial will begin on the 1st of June after students failed to respond to the first promotion.';
stories[0][3] = '/news/uninews/1868';

stories[1] = new Array();
stories[1][0] = '/photos/billboard/106';
stories[1][1] = 'Rapist arrested after 11 years';
stories[1][2] = 'Eleven years after his attempted rape of a University of York student, Steven Sellars was arrested and pleaded guilty to the offence.';
stories[1][3] = '/news/uninews/1883';

stories[2] = new Array();
stories[2][0] = '/photos/billboard/1051';
stories[2][1] = 'My Crisis Christmas: Volunteering with the homeless';
stories[2][2] = 'On passing a homeless person in the street, guilt is a feeling common to many, including myself.';
stories[2][3] = '/news/features/1049';

function changeBillboard (new_story) {
	clearTimeout(story_timer);
	document.getElementById('bb_count' + current_story).className = '';
	if ((new_story == undefined) || (new_story == null)) {
		current_story = current_story + 1;
	} else {
		current_story = new_story;
	}
	if ((current_story >= stories.length) || (current_story < 0))
		current_story = 0;
	document.getElementById('bb_img').src = stories[current_story][0];
	document.getElementById('bb_heading').innerHTML = stories[current_story][1];
	//document.getElementById('bb_text').innerHTML = '<b>' + stories[current_story][1] + '</b><br />' + stories[current_story][2];
	document.getElementById('bb_text').innerHTML = stories[current_story][2];
	document.getElementById('bb_link').href = stories[current_story][3];
	document.getElementById('bb_count' + current_story).className = 'selected';
	story_timer = setTimeout(function(){ changeBillboard(); }, 10000);
}
story_timer = setTimeout(function(){ changeBillboard(); }, 10000);
</script>
<!--<div style="float:left;width:500px;height:200px;text-align:center;position:relative;margin-bottom:0.5em;" onmouseover="document.getElementById('bb_count').style.display='block';"  onmouseout="document.getElementById('bb_count').style.display='none';">-->
<div style="float:left;width:500px;height:200px;text-align:center;position:relative;margin-bottom:0.5em;">
	<div id="bb_heading" style="top:0;left:0;position:absolute;background-color:#20C1F0;color:#fff;font-size:12pt;padding:3px 5px;font-weight: bold;z-index:5;">
		News in Brief: FTR price-rise
	</div>
	<div id="bb_bg" style="width:120px;height:200px;top:0;right:0;position:absolute;background-color:#fff;opacity:0.4;">
	</div>
	<div id="bb_text" style="width:110px;bottom:0;right:0;position:absolute;padding:3px 5px;">
		A new reduced-fare trial will begin on the 1st of June after students failed to respond to the first promotion.
	</div>
	<!--
	<div id="bb_text" style="width:110px;height:200px;top:0;right:0;position:absolute;padding:3px 5px;">
		<b>News in Brief: FTR price-rise</b>
		<br />
		A new reduced-fare trial will begin on the 1st of June after students failed to respond to the first promotion.
	</div>
	-->
	<a id="bb_link" href="#">
		<img id="bb_img" src="/photos/billboard/1053" />
	</a>
	<div id="bb_count" style="bottom:0;left:0;position:absolute;/*display:none;*/">
		<span id="bb_count0" class="selected"><a href="javascript:changeBillboard(0);">1</a></span>
		<span id="bb_count1"><a href="javascript:changeBillboard(1);">2</a></span>
		<span id="bb_count2"><a href="javascript:changeBillboard(2);">3</a></span>
	</div>
</div>

<div style="float:left;width:200px;height:210px;">
	<input type="button" value="switch" onclick="document.getElementById('bb_bg').style.background = '#000';document.getElementById('bb_text').style.color = '#fff';" />
	<input type="button" value="switch" onclick="document.getElementById('bb_bg').style.background = '#fff';document.getElementById('bb_text').style.color = '#000';" />
	<input type="button" value="switch" onclick="document.getElementById('bb_bg').style.background = '#ff6a00';document.getElementById('bb_text').style.color = '#fff';" />
	<input type="button" value="switch" onclick="document.getElementById('bb_bg').style.background = '#ff6a00';document.getElementById('bb_text').style.color = '#000';" />
	<input type="button" value="switch" onclick="document.getElementById('bb_bg').style.background = '#000';document.getElementById('bb_text').style.color = '#ff6a00';" />
	<input type="button" value="switch" onclick="document.getElementById('bb_bg').style.background = '#fff';document.getElementById('bb_text').style.color = '#ff6a00';" />
</div>

<style type="text/css">
div.pl_container {
	float: left;
	width: 280px;
	margin-right: 7px;
	margin-bottom: 0.5em;
}
div.pl_container_last {
	margin-right: 0;
}
div.pl_container div.pl_header {
	/*background-color: #20c1f0;*/
	background-color: #999;
	color: #fff;
	font-weight: bold;
	text-align: left;
	font-size: 12pt;
	padding: 3px 5px;
}
div.pl_container div.pl_list {
	height: 100px;
	overflow: hidden;
}
div.pl_container div.pl_list img {
	float: left;
}
div.pl_container div.pl_list div {
	border-bottom: 1px #888 solid;
	margin-left: 120px;
	padding: 0 5px;
	height: 33px;
	overflow: hidden;
	background-color: #efefef;
}
div.pl_container div.pl_list div a {
	color: #000;
}
div.pl_container div.pl_list div a:hover {
	text-decoration: none;
}
div.pl_container div.pl_list div.selected {
	background-color: #ccc;
}
</style>

<script type="text/javascript">
function changePreview (box, option, article_id, article_type, photo_id, photo_title) {
	document.getElementById('pl_img_link' + box).href = '/news/' + article_type + '/' + article_id;
	document.getElementById('pl_img' + box).src = '/photos/preview/' + photo_id;
	document.getElementById('pl_img' + box).alt = photo_title;
	document.getElementById('pl_img' + box).title = photo_title;
	var x = 0;
	var ele = document.getElementById('pl_option' + box + '_' + x);
	while ((ele != undefined) && (ele != null)) {
		if (x == option) {
			ele.className = 'selected';
		} else {
			ele.className = '';
		}
		x = x + 1;
		var ele = document.getElementById('pl_option' + box + '_' + x);
	}
}
</script>

<?php
$preview_box_count = 0;
foreach($articles as $type => $info) {
	$preview_box_count++;
	if (($preview_box_count % 3) == 0) {
		$preview_class = ' pl_container_last';
	} else {
		$preview_class = '';
	}
?>
<div class="pl_container<?php echo($preview_class); ?>">
	<div class="pl_header">latest <?php echo(xml_escape($type)); ?></div>
	<div class="pl_list">
		<a id="pl_img_link<?php echo($preview_box_count); ?>" href="/news/<?php echo(xml_escape($info[0]['article_type'])); ?>/<?php echo($info[0]['id']); ?>">
			<img id="pl_img<?php echo($preview_box_count); ?>" src="/photos/preview/<?php echo($info[0]['photo_id']); ?>" alt="<?php echo(xml_escape($info[0]['photo_title'])); ?>" title="<?php echo(xml_escape($info[0]['photo_title'])); ?>" />
		</a>
		<div id="pl_option<?php echo($preview_box_count); ?>_0" class="selected" onmouseover="changePreview(<?php echo($preview_box_count); ?>, 0, <?php echo($info[0]['id']); ?>, '<?php echo(xml_escape($info[0]['article_type'])); ?>', <?php echo($info[0]['photo_id']); ?>, '<?php echo(xml_escape(addslashes($info[0]['photo_title']))); ?>');">
			<a href="/news/<?php echo(xml_escape($info[0]['article_type'])); ?>/<?php echo($info[0]['id']); ?>">
				<?php echo(xml_escape($info[0]['heading'])); ?>
			</a>
		</div>
		<?php for ($i = 1; $i < count($info); $i++) { ?>
			<div id="pl_option<?php echo($preview_box_count); ?>_<?php echo($i); ?>" onmouseover="changePreview(<?php echo($preview_box_count); ?>, <?php echo($i); ?>, <?php echo($info[$i]['id']); ?>, '<?php echo(xml_escape($info[$i]['article_type'])); ?>', <?php echo($info[$i]['photo_id']); ?>, '<?php echo(xml_escape(addslashes($info[$i]['photo_title']))); ?>');">
				<a href="/news/<?php echo(xml_escape($info[$i]['article_type'])); ?>/<?php echo($info[$i]['id']); ?>">
					<?php echo(xml_escape($info[$i]['heading'])); ?>
				</a>
			</div>
		<?php } ?>
	</div>
</div>
<?php } ?>

<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
<?php 	if ($link->num_rows() > 0)
	{
		/// @todo FIXME data from database should be processed in the model
		foreach($link->result() as $picture){
			echo('	<a href="'.xml_escape($picture->link_url).'" target="_blank">'.
				$this->image->getImage( // getImage does the escaping
					$picture->link_image_id, 'link',
					array(
						'title'	=> $picture->link_name,
						'alt'	=> $picture->link_name,
					)
				).
				'</a>'."\n"
			);
		}
	} else {
// 		<a href="/account/links">You have no links</a>
	}
?>
		<a class="RightColumnAction"  href="/account/links">Customise</a>
	</div>
	
<?php
	if (null !== $poll_vote_box)
	{
		$poll_vote_box->Load();
	}
?>

	<h2>Search the Web</h2>
	<div class="Entry">
		<form method="get" action="http://www.google.co.uk/search" target="_blank">
			<fieldset class="inline">
				<input type="hidden" name="ie" value="UTF-8" />
				<input type="hidden" name="oe" value="UTF-8" />
				<a href="http://www.google.co.uk/" target="_blank">
					<img src="http://www.google.co.uk/logos/Logo_40wht.gif" alt="Google" />
				</a>
				<input type="text" name="q" value="" />
				<input type="submit" class="button" name="btnG" value="Search" target="_blank" />
			</fieldset>
		</form>
	</div>

	<h2>My Webmail </h2>
	<div class="Entry">
		<a class="MailLogo" href="https://webmail.york.ac.uk/" target="_blank">
			<img src="/images/prototype/news/test/webmail_large.jpg" alt="Webmail Logo" />
		</a>
		<p class="MailText">
			<a href="https://webmail.york.ac.uk/" target="_blank">E-mail</a>
		</p>
	</div>

	<h2>Upcoming Events</h2>
	<div class="Entry">
		<?php $events->Load(); ?>
	</div>
	<?php /*
	<h2>To Do</h2>
	<div class="Entry">
		<?php $todo->Load(); ?>
	</div>
	*/ ?>

<?php
if ($weather_forecast != null) {
?>
	<h2>York Weather</h2>
	<div class="Entry">
		<?php echo($weather_forecast);?>
	</div>

	<h2>Quote of the Day</h2>
	<div class="Entry">
		"<?php echo(xml_escape($quote->quote_text));?>" - <b><?php echo(xml_escape($quote->quote_author));?></b>
	</div>
<?php
}
?>
</div>

<div id="MainColumn">

	<div id="picoftheday" style="float:left;width:614px;margin-bottom:0.5em;text-align:center;">
		<img src="/images/prototype/new_home/picoftheday.jpg" alt="Central Hall by Chris Travis" title="Central Hall by Chris Travis" />
	</div>

	<div style="float:left;width:614px;margin-bottom:0.5em;">
		<div style="width:458px;height:150px;float:left;text-align:center;margin-right:10px;position:relative;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;position:absolute;top:0;left:0;">
				LATEST UNI NEWS
			</div>
			<div style="font-size:11pt;padding:3px 5px;background-color:#FE6A00;color:#fff;font-weight:bold;position:absolute;bottom:0;right:0;">
				News in Brief: Ftr price-rise
			</div>
			<a href="/news/uninews/1063">
				<img src="/images/prototype/new_home/news1.jpg" alt="" />
			</a>
		</div>
		<div style="width:146px;float:left;">
			<!--<h3 style="padding:0;margin:0;" class="Headline"><a href="/news/uninews/1063">News in Brief: Ftr price-rise</a></h3>-->
			<div style="padding:0;margin:0;" class="Date">Saturday, 12th January 2008</div>
			<p style="padding-top:0;margin-top:0;" class="More">First York have raised prices on the number 4 route from the University to York city centre to &pound;1.80 for a single and &pound;2.90 for a return journey.</p>
		</div>
		<div style="clear:both;"></div>
	</div>

   	<div style="float:left;width:614px;margin-bottom:0.5em;">
		<div style="width:302px;float:left;text-align:center;margin-right:6px;position:relative;border:1px #20C1F0 solid;">
			<embed type="application/x-shockwave-flash" src="/flash/mediaplayer.swf" id="mp1" name="mp1" quality="high" allowscriptaccess="samedomain" allowfullscreen="true" flashvars="height=248&amp;width=302&amp;file=http://static.theyorker.co.uk/media/videocasts/08_york90_spr07.flv&amp;id=http://static.theyorker.co.uk/media/videocasts/08_york90_spr07.flv&amp;callback=analytics&amp;backcolor=0xFF6A00&amp;frontcolor=0xFFFFFF&amp;lightcolor=0x000000&amp;screencolor=0xFFFFFF&amp;logo=/images/prototype/news/video_overlay_icon.png&amp;overstretch=true&amp;showdownload=true" height="248" width="302">
		</div>
		<div style="width:302px;float:left;text-align:center;position:relative;border:1px #20C1F0 solid;">
			<img src="/images/prototype/new_home/roses2.jpg" alt="Roses" title="Roses" />
			<div style="font-size:14pt">
				<span style="color:#3B8538">184</span> vs <span style="color:#EC1B2E">12</span>
			</div>
			<table border="0" style="width:100%" cellpadding="0" cellspacing="0">
			<tr><td style="border-top:1px #20C1F0 solid">Badminton Mens 1sts:</td><td style="border-top:1px #20C1F0 solid"><b>York 5</b> vs 2 Lancaster</td></tr>
			<tr><td style="border-top:1px #20C1F0 solid">Football Mens 2nds:</td><td style="border-top:1px #20C1F0 solid"><b>York 3</b> vs 0 Lancaster</td></tr>
			<tr><td style="border-top:1px #20C1F0 solid">Netball Mixed 1sts:</td><td style="border-top:1px #20C1F0 solid">York 1 vs <b>2 Lancaster</b></td></tr>
			<tr><td style="border-top:1px #20C1F0 solid">Futsal 1sts:</td><td style="border-top:1px #20C1F0 solid"><b>York 2</b> vs 1 Lancaster</td></tr>
			<tr><td style="border-top:1px #20C1F0 solid">Rugby Mens 3rds:</td><td style="border-top:1px #20C1F0 solid">York 1 vs <b>3 Lancaster</b></td></tr>
			</table>
		</div>
	</div>

	<div style="float:left;width:614px;margin-bottom:0.5em;">
		<div style="width:458px;height:150px;float:left;text-align:center;margin-right:10px;position:relative;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;position:absolute;top:0;left:0;">
				LATEST FEATURE
			</div>
			<div style="font-size:11pt;padding:3px 5px;background-color:#FE6A00;color:#fff;font-weight:bold;position:absolute;bottom:0;right:0;">
				My Crisis Christmas :  Volunteering with the homeless
			</div>
			<a href="/news/features/1049">
				<img src="/images/prototype/new_home/news2.jpg" alt="" />
			</a>
		</div>
		<div style="width:146px;float:left;">
			<!--<h3 style="padding:0;margin:0;" class="Headline"><a href="/news/uninews/1049">Volunteering with the homeless</a></h3>-->
			<div style="padding:0;margin:0;" class="Date">Wednesday, 9th January 2008</div>
			<p style="padding-top:0;margin-top:0;" class="More">This Christmas I decided to do something positive for the homeless and for myself - by trading in the traditional festivities to volunteer for Crisis, a charity for homeless people.</p>
		</div>
		<div style="clear:both;"></div>
	</div>

	<div style="float:left;width:614px;margin-bottom:0.5em;">
		<div style="width:302px;float:left;text-align:center;margin-right:6px;position:relative;border:1px #20C1F0 solid;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;position:absolute;top:0;left:0;">
				LATEST ARTS
			</div>
			<a href="/news/artsweek/1096">
				<img src="/images/prototype/new_home/halfnews1.jpg" alt="" />
			</a>
			<h3 style="padding:0;margin:0;" class="Headline"><a href="/news/artsweek/1096">Artsweek 2: Ballads, Greece, Paintings and Gene Kelly.</a></h3>
			<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
			<p style="padding-top:0;margin-top:0;" class="More">Artsweek presents everything you need to get out and about in the world of the arts in week 2.</p>
		</div>
		<div style="width:302px;float:left;text-align:center;position:relative;border:1px #20C1F0 solid;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;position:absolute;top:0;left:0;">
				LATEST SPORTS
			</div>
			<a href="/news/football/1069">
				<img src="/images/prototype/new_home/halfnews2.jpg" alt="" />
			</a>
			<h3 style="padding:0;margin:0;" class="Headline"><a href="/news/football/1069">Alcuin 1sts win college football title</a></h3>
			<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
			<p style="padding-top:0;margin-top:0;" class="More">A strong Alcuin side were crowned college first team champions last term, albeit in circumstances that left no one happy.</p>
		</div>
		<div style="clear:both;"></div>
	</div>

	<div style="float:left;width:614px;margin-bottom:0.5em;">
		<div style="width:144px;height:150px;float:left;text-align:center;background:url('/images/prototype/new_home/smallnews1.jpg');position:relative;border:1px #20C1F0 solid;margin-right:10px;">
			<div style="background-color:#20C1F0;opacity:0.7;filter:alpha(opacity = 70);bottom:0;left:0;position:absolute;width:100%;">
				<a href="/news/uninews/1062" style="color:#000;font-weight:bold;opacity:1;filter:alpha(opacity = 100);">
    				Vision editor: "If stays same we may not survive"
				</a>
			</div>
		</div>
		<div style="width:144px;height:150px;float:left;text-align:center;background:url('/images/prototype/new_home/smallnews2.jpg');position:relative;border:1px #20C1F0 solid;margin-right:10px;">
			<div style="background-color:#20C1F0;opacity:0.7;filter:alpha(opacity = 70);bottom:0;left:0;position:absolute;width:100%;">
				<a href="/news/uninews/1066" style="color:#000;font-weight:bold;opacity:1;filter:alpha(opacity = 100);">
					DNA match jails student's attacker
				</a>
			</div>
		</div>
		<div style="width:144px;height:150px;float:left;text-align:center;background:url('/images/prototype/new_home/smallnews3.jpg');position:relative;border:1px #20C1F0 solid;margin-right:10px;">
			<div style="background-color:#20C1F0;opacity:0.7;filter:alpha(opacity = 70);bottom:0;left:0;position:absolute;width:100%;">
				<a href="/news/uninews/1079" style="color:#000;font-weight:bold;opacity:1;filter:alpha(opacity = 100);">
					Tree planted to remember lost student
				</a>
			</div>
		</div>
		<div style="width:144px;height:150px;float:left;text-align:center;background:url('/images/prototype/new_home/smallnews4.jpg');position:relative;border:1px #20C1F0 solid;">
			<div style="background-color:#20C1F0;opacity:0.7;filter:alpha(opacity = 70);bottom:0;left:0;position:absolute;width:100%;">
				<a href="/news/uninews/1054" style="color:#000;font-weight:bold;opacity:1;filter:alpha(opacity = 100);">
					YUSU say no: campus lottery faces the guillotine
				</a>
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>

	<div style="float:left;width:614px;margin-bottom:0.5em;">
		<div style="width:302px;float:left;margin-right:6px;border:1px #20C1F0 solid;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;margin-bottom:5px;">
				LATEST LIFESTYLE
			</div>
			<div>
				<a href="/news/getaway/1089">
					<img src="/photos/small/1064" alt="" title="" style="float:left;margin:0 5px 5px 5px;" />
					Oxford: dreaming spires, amongst other things
				</a>
				<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
				<div style="clear:both;"></div>
			</div>
			<div>
				<a href="/news/freshchef/1082">
					<img src="/photos/small/1063" alt="" title="" style="float:left;margin:0 5px 5px 5px;" />
					A penny saved...
				</a>
				<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
				<div style="clear:both;"></div>
			</div>
			<div>
				<a href="/news/lifesaver/1053">
					<img src="/photos/small/1046" alt="" title="" style="float:left;margin:0 5px 5px 5px;" />
					Keep fit the WAG way
				</a>
				<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
				<div style="clear:both;"></div>
			</div>
			<div>
				<a href="/news/thelook/1060">
					<img src="/photos/small/1048" alt="" title="" style="float:left;margin:0 5px 5px 5px;" />
					Fashion resolutions for 2008
				</a>
				<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
				<div style="clear:both;"></div>
			</div>
		</div>
		<div style="width:302px;float:left;border:1px #20C1F0 solid;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;margin-bottom:5px;">
				LATEST VIDEOCASTS
			</div>
			<div>
				<a href="/news/videocasts/999">
					<img src="/photos/small/1032" alt="" title="" style="float:left;margin:0 5px 5px 5px;" />
					Talking shop with Ramsey Street's Harold Bishop
				</a>
				<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
				<div style="clear:both;"></div>
			</div>
			<div>
				<a href="/news/videocasts/924">
					<img src="/photos/small/937" alt="" title="" style="float:left;margin:0 5px 5px 5px;" />
					Student scam victim speaks out
				</a>
				<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
				<div style="clear:both;"></div>
			</div>
			<div>
				<a href="/news/videocasts/858">
					<img src="/photos/small/810" alt="" title="" style="float:left;margin:0 5px 5px 5px;" />
					NUS convince students to stay
				</a>
				<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
				<div style="clear:both;"></div>
			</div>
			<div>
				<a href="/news/videocasts/574">
					<img src="/photos/small/544" alt="" title="" style="float:left;margin:0 5px 5px 5px;" />
					Videocasts: Coming Soon
				</a>
				<div style="padding:0;margin:0;" class="Date">Monday, 14th January 2008</div>
				<div style="clear:both;"></div>
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>

	<div style="float:left;width:614px;margin-bottom:0.5em;">
		<div style="width:302px;height:100px;float:left;text-align:center;margin-right:10px;position:relative;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;position:absolute;top:0;left:0;">
				FOOD REVIEW: AKBARS
			</div>
			<div style="font-size:11pt;padding:3px 5px;background-color:#FE6A00;color:#fff;font-weight:bold;position:absolute;bottom:0;right:0;">
				"Best Indian in Yorkshire"
			</div>
			<a href="/reviews/food/akbars">
				<img src="/images/prototype/new_home/halfpuffer1.jpg" alt="" />
			</a>
		</div>
		<div style="width:302px;height:100px;float:left;text-align:center;position:relative;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;position:absolute;top:0;left:0;">
				LATEST BLOG
			</div>
			<div style="font-size:11pt;padding:3px 5px;background-color:#FE6A00;color:#fff;font-weight:bold;position:absolute;bottom:0;right:0;">
				Screw democracy - the Raid must go on
			</div>
			<a href="/news/campusnews/1072">
				<img src="/images/prototype/new_home/halfpuffer2.jpg" alt="" />
			</a>
		</div>
		<div style="clear:both;"></div>
	</div>

	<div style="float:left;width:614px;margin-bottom:0.5em;">
		<div style="width:458px;float:left;margin-right:6px;position:relative;border:1px #20C1F0 solid;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;position:absolute;top:0;left:0;">
				LATEST COMMENTS
			</div>
			<img src="/images/prototype/new_home/comments.jpg" alt="" />
			<ul class="comments" style="margin:0 5px;">
				<?php $latest_comments->Load(); ?>
			</ul>
		</div>
		<div style="width:146px;float:left;position:relative;border:1px #20C1F0 solid;">
			<div style="font-size:13pt;padding:3px 5px;background-color:#20C1F0;color:#fff;font-weight:bold;position:absolute;top:0;left:0;">
				RSS FEEDS
			</div>
			<a href="/news/rss/">
				<img src="/images/prototype/new_home/feeds.jpg" alt="" />
			</a>
			<ul style="margin:0 5px;">
				<li style="list-style-image: url('/images/prototype/new_home/feed.gif');"><a href="/news/rss/">All Articles</a></li>
				<li style="list-style-image: url('/images/prototype/new_home/feed.gif');"><a href="/news/rss/">Uni News</a></li>
				<li style="list-style-image: url('/images/prototype/new_home/feed.gif');"><a href="/news/rss/">Features</a></li>
				<li style="list-style-image: url('/images/prototype/new_home/feed.gif');"><a href="/news/rss/">Arts</a></li>
				<li style="list-style-image: url('/images/prototype/new_home/feed.gif');"><a href="/news/rss/">Sport</a></li>
				<li style="list-style-image: url('/images/prototype/new_home/feed.gif');"><a href="/news/rss/">Lifestyle</a></li>
				<li style="list-style-image: url('/images/prototype/new_home/feed.gif');"><a href="/news/rss/">Comments</a></li>
			</ul>
		</div>
		<div style="clear:both;"></div>
	</div>

	<br style="clear:both" /><br style="clear:both" /><br style="clear:both" /><br style="clear:both" />
	<br style="clear:both" /><br style="clear:both" /><br style="clear:both" /><br style="clear:both" />
	<br style="clear:both" /><br style="clear:both" /><br style="clear:both" /><br style="clear:both" />
	<br style="clear:both" /><br style="clear:both" /><br style="clear:both" /><br style="clear:both" />

<!--
	<div id="HomeBanner">
		<?php
		$this->homepage_boxes->print_homepage_banner($banner);
		?>
	</div>
-->

	<?php 
		$this->homepage_boxes->print_box_with_picture_list($articles['uninews'],'latest news','news');
		if($special['lifestyle']['show']) { $this->homepage_boxes->print_specials_box($special['lifestyle']['title'],$special['lifestyle']['data']); }
		$this->homepage_boxes->print_box_with_picture_list($articles['arts'],'latest arts','news');
		$this->homepage_boxes->print_box_with_picture_list($articles['sport'],'latest sport','news');
		if($special['blogs']['show']) { $this->homepage_boxes->print_specials_box($special['blogs']['title'],$special['blogs']['data']); }
		$this->homepage_boxes->print_box_with_picture_list($articles['features'],'latest features','news');
		$this->homepage_boxes->print_box_with_picture_list($articles['videocasts'],'latest videocasts','news');
	?>
</div>
