<?php
function printarticlelink($article) {
	echo('	<div class="Entry">'."\n");
	echo('		<a href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('			'.$article['photo_xhtml']."\n");
	echo('		</a>'."\n");
	if (!array_key_exists('blurb', $article)) {
		echo('		<div class="ArticleEntry">'."\n");
	}
	echo('		<h3 class="Headline">'."\n");
	echo('			<a href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('				'.xml_escape($article['heading'])."\n");
	echo('			</a>'."\n");
	echo('		</h3>'."\n");
	echo('		<div class="Date">'.$article['date'].'</div>'."\n");
	echo('		<div class="Author">'."\n");
	foreach($article['authors'] as $reporter)
		echo('			<a href="/news/archive/reporter/'.$reporter['id'].'/">'.xml_escape($reporter['name']).'</a>'."\n");
	echo('		</div>'."\n");
	if (!array_key_exists('blurb', $article)) {
		echo('		</div>'."\n");
	}
	if (array_key_exists('blurb', $article) && $article['blurb'] != '') {
		echo('		<p>'.xml_escape($article['blurb']).'</p>'."\n");
	}
	echo('	</div>'."\n");
}
?>

<div id="RightColumn">
<?php
$first_header = ' class="first"';

// Puffers / Blogs Heading
if (((isset($blogs)) && (count($blogs) > 0)) || ((isset($puffers)) && (count($puffers) > 0))) {
	echo('	<h2' . $first_header . '>' . xml_escape($puffer_heading) . '</h2>'."\n");
	$first_header = '';
}

// Blogs
if (isset($blogs)) {
	foreach ($blogs as $blog) {
		echo '<div class="Puffer">';
		echo '<a href="/news/' . $blog['codename'] . '">';
		echo '<img src="' . xml_escape($blog['image']) . '" alt="' . xml_escape($blog['image_title']) . '" title="' . xml_escape($blog['image_title']) . '" style="float:right;" />';
		echo $blog['name'];
		echo '</a><br />';
		echo $blog['blurb'];
		echo '<br style="clear:both" /></div>';
	}
}

// Puffers
if (isset($puffers)) {
		foreach ($puffers as $puffer) {
			if(!empty($puffer['image_title'])){
				echo '<div class="Puffer">';
				echo '<a href="/news/' . $puffer['codename'] . '">';
				echo '<img src="' . xml_escape($puffer['image']) . '" alt="' . xml_escape($puffer['image_title']) . '" title="' . xml_escape($puffer['image_title']) . '" />';
				echo '</a></div>';
			}else{
				echo '<div class="Puffer">';
				echo '<a href="/news/' . $puffer['codename'] . '">';
				echo xml_escape($puffer['name']);
				echo '</a></div>';
			}
		}
}

// Latest Articles Heading
if (count($news_previews) > 0) {
	echo('	<h2' . $first_header . '>' . xml_escape($latest_heading) . '</h2>'."\n");
}

// News Previews
foreach($news_previews as $preview)
	printarticlelink($preview);

// More Articles Heading
if (count($news_others) > 0)
	echo('	<h2>'.xml_escape($other_heading).'</h2>'."\n");

// Other News
foreach ($news_others as $other)
	printarticlelink($other);

// Related Articles
if (count($main_article['related_articles']) > 0)
	echo('	<h2>'.xml_escape($related_heading).'</h2>'."\n");

foreach ($main_article['related_articles'] as $related)
	printarticlelink($related);

?>
	<h2>RSS Feeds</h2>
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

<?php $this->feedback_article_heading = $main_article['heading']; ?>

<div style="float:right; margin-right:10px; width:170px;">
	<img src="/images/prototype/directory/members/no_image.png" alt="Chris Travis" style="float:right" />
	<div style="padding:0.4em; background-color:#FF6B01; color:#fff; font-weight:bold;">
		Chris Travis
	</div>
	Other articles written by this reporter:
	<div style="clear:right">
		<ul>
			<li><a href="">Week in Pictures: International Week</a></li>
			<li><a href="">Film Preview: Wings of Desire</a></li>
			<li><a href="">Lake Bled: Slovenias action adventure destination</a></li>
			<li><a href="">Lycra at the ready: Ride of the Roses is coming</a></li>
			<li><a href="">SMACK MY GIMP UP!</a></li>
		</ul>
	</div>

	<img src="/images/prototype/directory/members/no_image.png" alt="Richard Ingle" style="float:right" />
	<div style="padding:0.4em; background-color:#FF6B01; color:#fff; font-weight:bold;">
		Richard Ingle
	</div>
	Other articles written by this reporter:
	<div style="clear:right">
		<ul>
			<li><a href="">Week in Pictures: International Week</a></li>
			<li><a href="">Film Preview: Wings of Desire</a></li>
			<li><a href="">Lake Bled: Slovenias action adventure destination</a></li>
			<li><a href="">Lycra at the ready: Ride of the Roses is coming</a></li>
			<li><a href="">SMACK MY GIMP UP!</a></li>
		</ul>
	</div>
	
	<div style="padding:0.4em; background-color:#20C1F0; color:#fff; font-weight:bold;">
		More of Today's News
	</div>
	<div style="clear:left;font-weight:bold;padding-top:5px;">
		<img src="/photos/small/1067" alt="Article Image" style="float:left" />
		Taylor drops out of race
	</div>
	<div style="clear:left;font-weight:bold;padding-top:5px;">
		<img src="/photos/small/1066" alt="Article Image" style="float:left" />
		Ride of the Roses
	</div>
   	<div style="clear:left;font-weight:bold;padding-top:5px;">
		<img src="/photos/small/1068" alt="Article Image" style="float:left" />
		Bridge Closures
	</div>
</div>

<div id="MainColumn" style="margin-right:420px;">
	<div class="BlueBox">
		<h2 class="Headline"><?php echo(xml_escape($main_article['heading'])); ?></h2>
		<?php if(isset($main_article['primary_photo_xhtml'])) { ?>
			<div style="float:right;margin-top:0;line-height:95%;width:180px;">
				<?php echo($main_article['primary_photo_xhtml']); ?><br />
				<?php echo(xml_escape($main_article['primary_photo_caption'])); ?>
			</div>
		<?php } ?>
		<div class="Date"><?php echo($main_article['date']); ?></div>
		<div class="Author">
<?php foreach($main_article['authors'] as $reporter) { ?>
			<a href="/news/archive/reporter/<?php echo($reporter['id']); ?>/"><?php echo(xml_escape($reporter['name'])); ?></a><br />
<?php } ?>
		</div>
<?php if ($main_article['subtext'] != '') { ?>
		<div class="SubText"><?php echo(xml_escape($main_article['subtext'])); ?></div>
<?php } ?>

        <?php echo($main_article['text']); ?>

		<div class="share_article">
<?php if ($article_type == 'podcasts') { ?>
			<span style="float:left">
				<a href="itpc://<?php echo($this->config->item('podcast_rss_feed')); ?>">
					<img src="/images/prototype/news/itunes_subscribe.gif" alt="Subscribe to The Yorker Artscast via iTunes" title="Subscribe to The Yorker Artscast via iTunes" />
				</a>
			</span>
<?php } ?>
<?php if (!isset($main_article['placeholder']) || !$main_article['placeholder']) { ?>
			<a href="http://www.facebook.com/share.php?u=http://<?php echo($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>" target="_blank" class="fb_share_button" onclick="return fbs_click()">Share</a>
<?php } ?>
		</div>

		<?php if (isset($office_preview)) { ?>
			<p class='form'><button class="button" onclick="window.location='/office/news/<?php echo $main_article['id']; ?>';">GO BACK TO NEWS OFFICE</button></p>
		<?php } ?>
	</div>
	
	<?php if (isset($main_article['article_poll'])) { ?>
		<?php if ($main_article['article_poll']['info']['is_competition']) { ?>
			<form id="competition" action="<?php echo($PHP_SELF); ?>" method="post">
				<div class="BlueBox">
					<h2>Competition</h2>
					<?php echo($main_article['article_poll']['info']['question']); ?>

					<?php if ($main_article['article_poll']['message'] != '') { ?>
						<p><?php echo($main_article['article_poll']['message']); ?></p>
					<?php } else { ?>
						<fieldset>
							<div>
								<label for="comp_answer">Answer:</label>
								<select name="comp_answer" id="comp_answer" size="1">
									<option selected="selected">&nbsp;</option>
									<?php foreach ($main_article['article_poll']['options'] as $option) { ?>
										<option value="<?php echo($option['id']); ?>"><?php echo($option['name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</fieldset>
						<p>Competition closes on <?php echo(date('l, jS F Y', $main_article['article_poll']['info']['finish_time'])); ?> at <?php echo(date('H:i', $main_article['article_poll']['info']['finish_time'])); ?></p>
						<h2>Contact Details</h2>
						<p>
							<b>Name</b>: <?php echo(xml_escape($main_article['article_poll']['user']['user_firstname'] . ' ' . $main_article['article_poll']['user']['user_surname'])); ?><br />
							<b>E-Mail</b>: <?php echo($main_article['article_poll']['user']['user_email']); ?><br />
							<a href="/account/personal">Click here</a> to change your contact details.
						</p>
						<fieldset>
							<input type="submit" value="Enter Competition" class="button" />
						</fieldset>
					<?php } ?>
				</div>
			</form>
		<?php } else { ?>
			<div id="poll" class="BlueBox">
				<h2>Poll</h2>
			</div>
		<?php } ?>
	<?php } ?>

	<?php if (count($main_article['links']) > 0) { ?>
	<div class="BlueBox">
		<h2><?php echo(xml_escape($links_heading)); ?></h2>
		<ul>
		<?php foreach ($main_article['links'] as $link) {
			echo '<li><a href="' . xml_escape($link['url']) . '">' . xml_escape($link['name']) . '</a></li>';
		} ?>
		</ul>
	</div>
	<?php } ?>
	
	<div style="float:left;width:438px;margin-bottom:0.5em;">
		<div style="width:144px;height:150px;float:left;text-align:center;background:url('/images/prototype/new_home/smallnews1.jpg');position:relative;border:1px #20C1F0 solid;">
			<div style="background-color:#20C1F0;opacity:0.7;filter:alpha(opacity = 70);bottom:0;left:0;position:absolute;width:100%;">
				<a href="/news/uninews/1062" style="color:#000;font-weight:bold;opacity:1;filter:alpha(opacity = 100);">
    				Vision editor: "If stays same we may not survive"
				</a>
			</div>
		</div>
		<div style="width:144px;height:150px;float:left;text-align:center;background:url('/images/prototype/new_home/smallnews2.jpg');position:relative;border:1px #20C1F0 solid;">
			<div style="background-color:#20C1F0;opacity:0.7;filter:alpha(opacity = 70);bottom:0;left:0;position:absolute;width:100%;">
				<a href="/news/uninews/1066" style="color:#000;font-weight:bold;opacity:1;filter:alpha(opacity = 100);">
					DNA match jails student's attacker
				</a>
			</div>
		</div>
		<div style="width:144px;height:150px;float:left;text-align:center;background:url('/images/prototype/new_home/smallnews3.jpg');position:relative;border:1px #20C1F0 solid;">
			<div style="background-color:#20C1F0;opacity:0.7;filter:alpha(opacity = 70);bottom:0;left:0;position:absolute;width:100%;">
				<a href="/news/uninews/1079" style="color:#000;font-weight:bold;opacity:1;filter:alpha(opacity = 100);">
					Tree planted to remember lost student
				</a>
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>

	<?php
	// Comments if they're included
	if (isset($comments) && NULL !== $comments) {
		$comments->Load();
	}
	?>
</div>
