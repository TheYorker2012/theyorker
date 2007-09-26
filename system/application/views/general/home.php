<?php
//Note this can all be got from the Homepage_boxes library.
function get_link_ref($article,$prefix){
	return 'href="/'.$prefix.'/'.$article['article_type'].'/'.$article['id'].'"';
};

function print_small_story($class, $article, $prefix) {
	echo('	<div class="'.$class.' NewsBox">'."\n");
	echo('		<a class="NewsImgSmall" '.get_link_ref($article,$prefix).'>'."\n");
	echo('			'.$article['photo_xhtml']."\n").'';
	echo('		</a>'."\n");
	echo('		<p class="More">'."\n");
	echo('			<a '.get_link_ref($article,$prefix).'>'."\n");
	echo('				'.$article['heading']."\n");
	echo('			</a>'."\n");
	echo('		</p>'."\n");
	echo('	</div>');
}

function print_box($articles,$heading,$prefix){
	if (count($articles) != 0) {
		echo('  <h2>'.$heading.'</h2>'."\n");
		echo('  <div class="NewsBox">'."\n");
		echo('          <a class="NewsImg"'.get_link_ref($articles[0],$prefix).'>'."\n");
		echo('                  '.$articles[0]['photo_xhtml']."\n").'';
		echo('          </a>'."\n");
		echo('          <h3 class="Headline"><a '.get_link_ref($articles[0],$prefix).'>'.$articles[0]['heading'].'</a></h3>'."\n");
		echo('          <div class="Date">'.$articles[0]['date'].'</div>'."\n");
		echo('		<p class="More">'.$articles[0]['blurb'].'</p>'."\n");
		echo('	</div>'."\n");
		if (count($articles) > 1) {
			echo('	<div class="LineContainer"></div>'."\n");
			print_small_story('LeftNewsBox', $articles[1], $prefix);
			if (count($articles) > 2)
				print_small_story('RightNewsBox', $articles[2], $prefix);
		}
	}

};

function print_middle_box($title,$article_array){
	echo('  <h4>'.$title.'</h4>'."\n");
	if (count($article_array) > 0) {
		echo('  <ul class="TitleList">'."\n");
		foreach ($article_array as $article) {
			echo('          <li><a href="/news/'.$article['article_type'].'/'.$article['id'].'" >'."\n");
			echo('                  '.$article['heading']."\n");
			echo('          </a></li>'."\n");
		}
		echo('  </ul>'."\n");
	}
};
?>

<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
<?php 	if ($link->num_rows() > 0)
	{
	foreach($link->result() as $picture){
		echo('	<a href="'.$picture->link_url.'">'.$this->image->getImage($picture->link_image_id, 'link', array('title' => $picture->link_name, 'alt' => $picture->link_name)).'</a>'."\n");
		}
	} else {
		echo('	<a href="http://theyorker.co.uk">You have no links :(</a>'."\n");
	}
?>
		<a class="RightColumnAction"  href="/account/links">Customise</a>
	</div>

	<h2>Search the Web</h2>
	<div class="Entry">
		<form method="get" action="http://www.google.co.uk/search">
			<input type="hidden" name="ie" value="UTF-8" />
			<input type="hidden" name="oe" value="UTF-8" />
			<a href="http://www.google.co.uk/">
				<img src="http://www.google.co.uk/logos/Logo_40wht.gif" alt="Google" />
			</a>
			<fieldset class="inline">
				<input type="text" name="q" value="" />
				<input type="submit" class="button" name="btnG" value="Search" />
			</fieldset>
		</form>
	</div>

	<h2>My Webmail </h2>
	<div class="Entry">
		<a class="MailLogo" href="https://webmail.york.ac.uk">
			<img src="/images/prototype/news/test/webmail_large.jpg" alt="Webmail Logo" />
		</a>
		<p class="MailText">
			<a href="https://webmail.york.ac.uk/">E-mail</a>
		</p>
	</div>

	<!--
	<h2>Upcoming Events</h2>
	<div class="Entry">
		<?php $events->Load(); ?>
	</div>

	<h2>To Do</h2>
	<div class="Entry">
		<?php $todo->Load(); ?>
	</div>
	-->

	<h2>York Weather</h2>
	<div class="Entry">
		<?php echo($weather_forecast);?>
	</div>

	<h2>Quote of the Day</h2>
	<div class="Entry">
		"<?php echo $quote->quote_text;?>" - <b><?php echo $quote->quote_author;?></b>
	</div>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php echo($banner) ?>
	</div>

	<div class="BlueBox">
		<?php print_box($articles['uninews'],'latest news','news') ?>
	</div>

	<div class="BlueBox">
		<h2><?php echo('and today...')?></h2>
		<div class="LeftNewsBox NewsBox">
			<?php print_middle_box('IN FEATURES',$articles['features']) ?>
		</div>
		<div class="RightNewsBox NewsBox">
			<?php print_middle_box('IN ARTS',$articles['arts']) ?>
		</div>
	</div>

	<div class="BlueBox">
		<?php print_box($articles['sport'],'latest sport','news') ?>
	</div>
	
	<div class="BlueBox">
		<h2>breaker</h2>
	</div>
	
	<div class="BlueBox">
		<h2>latest news</h2>
		<div class="NewsBox">
			<a class="NewsImg" href="#">
				<img src="http://www.theyorker.co.uk/photos/medium/25" class="left">
			</a>
			<h3 class="Headline"><a href="#">Yorker back from 8th October</a></h3>
			<div class="Date">
				Thursday, 28th June 2007
			</div>
			<p class="More">
				The yorker is undergoing development over the summer ready for its Fresher's Week relaunch, and so will not provide new content.
			</p>
		</div>
		<div class="LineContainer">
		</div>
		<div class="LeftNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/34" class="left">
			</a>
			<p class="More">
				<a href="#">Yorker Review of the Year</a>
			</p>
		</div>
		<div class="RightNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/343" class="left">
			</a>
			<p class="More">
				<a href="#">We've got flood on our hands</a>
			</p>
		</div>
	</div>
	
	<div class="BlueBox">
		<h2>latest features</h2>
		<div class="NewsBox">
			<a class="NewsImg" href="#">
				<img src="http://www.theyorker.co.uk/photos/medium/337" class="left">
			</a>
			<h3 class="Headline"><a href="#">Into Tentation: Tent State Sussex, June 2007</a></h3>
			<div class="Date">
				Wednesday, 27th June 2007
			</div>
			<p class="More">
				Tent State University is a movement that involves universities from the US and UK at which students.
			</p>
		</div>
	</div>
	
	<div class="BlueBox">
		<h2>latest arts</h2>
		<div class="NewsBox">
			<a class="NewsImg" href="#">
				<img src="http://www.theyorker.co.uk/photos/medium/5" class="left">
			</a>
			<h3 class="Headline"><a href="#">Arts Week 7: Live Music and Revolutionary Politics</a></h3>
			<div class="Date">
				Monday, 4th June 2007
			</div>
			<p class="More">
				The very best of arts-based activities in week 7.
			</p>
		</div>
		<div class="LineContainer">
		</div>
		<div class="LeftNewsBox NewsBox">
			<h4>MORE ARTS FEATURES</h4>
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/4" class="left">
			</a>
			<p class="More">
				<a href="#">Artsweek6: Live York street music</a>
			</p>
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/3" class="left">
			</a>
			<p class="More">
				<a href="#">Artsweek</a>
			</p>
		</div>
		<div class="RightNewsBox NewsBox">
			<h4>REVIEWS</h4>
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/9" class="left">
			</a>
			<p class="More">
				<a href="#">Review: The Tempest</a>
			</p>
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/318" class="left">
			</a>
			<p class="More">
				<a href="#">Live: Jack Penate at The Cockpit 17/6/07</a>
			</p>
		</div>
	</div>
	
	<div class="BlueBox">
		<h2>latest sports</h2>
		<div class="NewsBox">
			<a class="NewsImg" href="#">
				<img src="http://www.theyorker.co.uk/photos/medium/326" class="left">
			</a>
			<h3 class="Headline"><a href="#">End of Season Review</a></h3>
			<div class="Date">
				Wednesday, 11th July 2007
			</div>
			<p class="More">
				The Yorker speaks to outgoing AU Vice-President Nick Hassey, and brings you all the awards for this athletic season.
			</p>
		</div>
		<div class="LineContainer">
		</div>
		<div class="LeftNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/332" class="left">
			</a>
			<p class="More">
				<a href="#">G*Stars enter history as 6-a-side champions</a>
			</p>
		</div>
		<div class="RightNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/334" class="left">
			</a>
			<p class="More">
				<a href="#">Veterans Claim Coveted Netball Prize</a>
			</p>
		</div>
	</div>
	
	<div class="BlueBox PufferBox">
		<a class="PufferImg" href="#">
			<img src="http://www.theyorker.co.uk/photos/small/332" class="left">
		</a>
		<h2>feature</h2>
		<p class="More">
			<a href="#">We trial 18 uni sports in the desert...</a>
		</p>
	</div>
	
	<div class="BlueBox">
		<h2>yorker student videocasts</h2>
		<div class="NewsBox">
			<div style="text-align:center;">
				<script type="text/javascript">
				<!--
				// Version check based upon the values entered above in "Globals"
				var hasReqestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

				// Check to see if the version meets the requirements for playback
				if (hasReqestedVersion) {
					// if we've detected an acceptable version
					// embed the Flash Content SWF when all tests are passed
					AC_FL_RunContent(
								"src", "http://www.youtube.com/v/LX3JJ_tPvwA",
								"width", "340",
								"height", "280",
								"align", "center",
								"id", "movie",
								"quality", "high",
								"bgcolor", "#FFFFFF",
								"name", "movie",
								"allowScriptAccess","sameDomain",
								"type", "application/x-shockwave-flash",
								"codebase", "http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab",
								"pluginspage", "http://www.adobe.com/go/getflashplayer"
					);
				} else {  // flash is too old or we can't detect the plugin
					var alternateContent = '<div style="width: 340px; height: 280px; border: 1px solid #999999;"><br />'
					+ "<b>YouTube Video Clip</b><br /><br /> "
					+ "This content requires the Adobe Flash Player 9. "
					+ "<a href=http://www.adobe.com/go/getflash/>Get Flash</a>"
					+ "</div>";
					document.write(alternateContent);  // insert non-flash content
				}
				// -->
				</script>
				<noscript>
					<div style="width: 340px; height: 280px; border: 1px solid #999999;"><br />
						<b>YouTube Video Clip</b><br /><br />
					  	This content requires the Adobe Flash Player 9 and a browser with JavaScript enabled.
					  	<a href="http://www.adobe.com/go/getflash/">Get Flash</a>
				  	</div>
				</noscript>
			</div>
			<div class="VideoCastBox">
				<h4>LATEST VIDEOCASTS</h4>
				<ul class="TitleList">
					<li><a href="#">Videocast 1</a></li>
					<li><a href="#">Videocast 2</a></li>
					<li><a href="#">Videocast 3</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
