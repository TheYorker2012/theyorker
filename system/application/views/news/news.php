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
</div>

<?php $this->feedback_article_heading = $main_article['heading']; ?>

<div id="MainColumn">
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

		<div style="text-align: right">
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
	<?php
	// Comments if they're included
	if (isset($comments) && NULL !== $comments) {
		$comments->Load();
	}
	?>
</div>
