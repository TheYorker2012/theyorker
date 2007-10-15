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
	echo('				'.$article['heading']."\n");
	echo('			</a>'."\n");
	echo('		</h3>'."\n");
	echo('		<div class="Date">'.$article['date'].'</div>'."\n");
	echo('		<div class="Author">'."\n");
	foreach($article['authors'] as $reporter)
		echo('			<a href="/contact">'.$reporter['name'].'</a>'."\n");
	echo('		</div>'."\n");
	if (!array_key_exists('blurb', $article)) {
		echo('		</div>'."\n");
	}
	if (array_key_exists('blurb', $article) && $article['blurb'] != '') {
		echo('		<p>'.$article['blurb'].'</p>'."\n");
	}
	echo('	</div>'."\n");
}
?>

<div id="RightColumn">
<?php
$first_header = ' class="first"';

// Puffers / Blogs Heading
if (((isset($blogs)) && (count($blogs) > 0)) || ((isset($puffers)) && (count($puffers) > 0))) {
	echo('	<h2' . $first_header . '>' . $puffer_heading . '</h2>'."\n");
	$first_header = '';
}

// Blogs
if (isset($blogs)) {
	foreach ($blogs as $blog) {
		echo '<div class=\'Puffer\'>';
		echo '<a href=\'/news/' . $blog['codename'] . '\'>';
		echo '<img src=\'' . $blog['image'] . '\' alt=\'' . $blog['image_title'] . '\' title=\'' . $blog['image_title'] . '\' style="float:right;" />';
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
				echo '<div class=\'Puffer\'>';
				echo '<a href=\'/news/' . $puffer['codename'] . '\'>';
				echo '<img src=\'' . $puffer['image'] . '\' alt=\'' . $puffer['image_title'] . '\' title=\'' . $puffer['image_title'] . '\' />';
				echo '</a></div>';
			}else{
				echo '<div class=\'Puffer\'>';
				echo '<a href=\'/news/' . $puffer['codename'] . '\'>';
				echo $puffer['name'];
				echo '</a></div>';
			}
		}
}

// Latest Articles Heading
if (count($news_previews) > 0) {
	echo('	<h2' . $first_header . '>' . $latest_heading . '</h2>'."\n");
}

// News Previews
foreach($news_previews as $preview)
	printarticlelink($preview);

// More Articles Heading
if (count($news_others) > 0)
	echo('	<h2>'.$other_heading.'</h2>'."\n");

// Other News
foreach ($news_others as $other)
	printarticlelink($other);

// Related Articles
if (count($main_article['related_articles']) > 0)
	echo('	<h2>'.$related_heading.'</h2>'."\n");

foreach ($main_article['related_articles'] as $related)
	printarticlelink($related);

?>
</div>

<?php $this->feedback_article_heading = $main_article['heading']; ?>

<div id="MainColumn">
	<div class="BlueBox">
		<h2 class="Headline"><?php echo $main_article['heading']; ?></h2>
		<?php if(isset($main_article['primary_photo_xhtml'])) { ?>
			<div style="float:right;margin-top:0;line-height:95%;width:180px;">
				<?php echo($main_article['primary_photo_xhtml']); ?><br />
				<?php echo($main_article['primary_photo_caption']); ?>
			</div>
		<?php } ?>
		<div class="Date"><?php echo($main_article['date']); ?></div>
		<div class="Author">
<?php foreach($main_article['authors'] as $reporter) { ?>
			<a href="/contact"><?php echo($reporter['name']); ?></a><br />
<?php } ?>
		</div>
<?php if ($main_article['subtext'] != '') { ?>
		<div class="SubText"><?php echo($main_article['subtext']); ?></div>
<?php } ?>

        <?php echo($main_article['text']); ?>

		<div style="text-align: right">
			<div>
				<a href="http://www.facebook.com/share.php?u=http://<?php echo($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>" target="_blank" class="fb_share_button" onclick="return fbs_click()">Share</a>
			</div>
		</div>

		<?php if (isset($office_preview)) { ?>
			<p class='form'><button class="button" onclick="window.location='/office/news/<?php echo $main_article['id']; ?>';">GO BACK TO NEWS OFFICE</button></p>
		<?php } ?>
	</div>
	<?php if (count($main_article['links']) > 0) { ?>
	<div class="BlueBox">
		<h2><?php echo $links_heading; ?></h2>
		<ul>
		<?php foreach ($main_article['links'] as $link) {
			echo '<li><a href=\'' . $link['url'] . '\'>' . $link['name'] . '</a></li>';
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
