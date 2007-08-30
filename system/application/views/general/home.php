<?php
function get_link_ref($article,$prefix){
	return 'href="/'.$prefix.'/'.$article['article_type'].'/'.$article['id'].'"';
};

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
		if (count($articles) > 1) {
			echo('          <ul class="TitleList">'."\n");
			echo('                  <li><a '.get_link_ref($articles[1],$prefix).'>'.$articles[1]['heading'].'</a></li>'."\n");
			if (count($articles) > 2)
				echo('                  <li><a '.get_link_ref($articles[2],$prefix).'>'.$articles[2]['heading'].'</a></li>'."\n");
			echo('          </ul>'."\n");
		}
		echo('  </div>'."\n");

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
	<!-- Search Google -->
	<form method="get" action="http://www.google.co.uk/search">
		<div class="Entry">
			<input type="hidden" name="ie" value="UTF-8" />
			<input type="hidden" name="oe" value="UTF-8" />
			<a href="http://www.google.co.uk/">
				<img src="http://www.google.co.uk/logos/Logo_40wht.gif" alt="Google" />
			</a>
			<input type="text" name="q" size="16" maxlength="255" value="" />
			<input type="submit" name="btnG" value="Search" />
		</div>
	</form>
	<!-- Search Google -->

	<h2>My Webmail </h2>
	<a class="MailLogo" href="https://webmail.york.ac.uk">
		<img src="/images/prototype/news/test/webmail_large.jpg" alt="Webmail Logo" />
	</a>
	<div class="Entry">
			<p class="MailText">
				<a href="https://webmail.york.ac.uk/">E-mail</a>
			</p>
	</div>

	<h2>Upcoming Events</h2>
	<div class="Entry">
		<?php $events->Load(); ?>
	</div>

	<h2>To Do</h2>
	<div class="Entry">
		<?php $todo->Load(); ?>
	</div>

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
				<img src="http://www.theyorker.co.uk/photos/medium/25">
			</a>
			<h3 class="Headline"><a href="#">Yorker back from 8th October</a></h3>
			<div class="Date">
				Thursday, 28th June 2007
			</div>
			<p class="More">
				The yorker is undergoing development over the summer ready for its Fresher's Week relaunch, and so will not provide new content.
			</p>
		</div>
		<div class="LeftNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/34">
			</a>
			<h4 class="Headline">
				<a href="#">Yorker Review of the Year</a>
			</h4>
		</div>
		<div class="RightNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/343">
			</a>
			<h4 class="Headline">
				<a href="#">We've got flood on our hands</a>
			</h4>
		</div>
	</div>
	
	<div class="BlueBox">
		<h2>latest features</h2>
		<div class="NewsBox">
			<a class="NewsImg" href="#">
				<img src="/photos/medium/2">
			</a>
			<h3 class="Headline"><a href="#">A Heading</a></h3>
			<div class="Date">
				Tuesday, 26th June 2007
			</div>
			<p class="More">
				Two miners who escaped from a collapsed pit in Beijing ate coal and drank urine to survive, Chinese media has reported.
			</p>
		</div>
	</div>
	
	<div class="BlueBox">
		<h2>latest arts</h2>
		<div class="NewsBox">
			<a class="NewsImg" href="#">
				<img src="/photos/medium/164">
			</a>
			<h3 class="Headline"><a href="#">A Heading</a></h3>
			<div class="Date">
				Tuesday, 26th June 2007
			</div>
			<p class="More">
				Two miners who escaped from a collapsed pit in Beijing ate coal and drank urine to survive, Chinese media has reported.
			</p>
		</div>
		<div class="LeftNewsBox NewsBox">
			<h4>MORE ARTS FEATURES</h4>
			<a class="NewsImgSmall" href="#">
				<img src="/photos/small/164">
			</a>
			<p class="More">
				<a href="#">A Mini Heading</a>
			</p>
		</div>
		<div class="RightNewsBox NewsBox">
			<h4>REVIEWS</h4>
			<a class="NewsImgSmall" href="#">
				<img src="/photos/small/2">
			</a>
			<p class="More">
				<a href="#">A Second Mini Heading</a>
			</p>
		</div>
		<div class="LeftNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="/photos/small/164">
			</a>
			<p class="More">
				<a href="#">A Third Mini Heading</a>
			</p>
		</div>
		<div class="RightNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="/photos/small/2">
			</a>
			<p class="More">
				<a href="#">A Fourth Mini Heading</a>
			</p>
		</div>
	</div>
	
	<div class="BlueBox">
		<h2>latest sports</h2>
		<div class="NewsBox">
			<a class="NewsImg" href="#">
				<img src="http://www.theyorker.co.uk/photos/medium/326">
			</a>
			<h3 class="Headline"><a href="#">End of Season Review</a></h3>
			<div class="Date">
				Wednesday, 11th July 2007
			</div>
			<p class="More">
				The Yorker speaks to outgoing AU Vice-President Nick Hassey, and brings you all the awards for this athletic season.
			</p>
		</div>
		<div class="LeftNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/332">
			</a>
			<h4 class="Headline">
				<a href="#">G*Stars enter history as 6-a-side champions</a>
			</h4>
		</div>
		<div class="RightNewsBox NewsBox">
			<a class="NewsImgSmall" href="#">
				<img src="http://www.theyorker.co.uk/photos/small/334">
			</a>
			<h4 class="Headline">
				<a href="#">Veterans Claim Coveted Netball Prize</a>
			</h4>
		</div>
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
	
	<!--
	<div class="GreyBar">
		<h2>lifestyle</h2>
	</div>-->
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
