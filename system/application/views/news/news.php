<?php
function printarticlelink($article, $blurb = false) {
	echo('	<div class="Entry">'."\n");
	echo('		<a href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('			<img src="'.$article['photo_url'].'" alt="'.$article['photo_title'].'" title="'.$article['photo_title'].'" />'."\n");
	echo('			<h5>'.$article['heading'].'</h5>'."\n");
	echo('		</a>'."\n");
	foreach($article['authors'] as $reporter)
		echo('		<p><a href="/contact">'.$reporter['name'].'</a></p>'."\n");
	echo('		<p>'.$article['date'].'</p>'."\n");
	echo('		'.anchor('/news/'.$article['article_type'].'/'.$article['id'], 'Read more...')."\n");
	if ($blurb)
		echo('		<p>'.$article['blurb'].'</p>'."\n");
	echo('	</div>'."\n");
}
?>

<div id="RightColumn">
	<h4 class="First"><?php echo($latest_heading); ?></h4>
<?php
foreach($news_previews as $preview)
	printarticlelink($preview, true);
?>
	
	<h4><?php echo($other_heading); ?></h4>
<?php
foreach ($news_others as $other)
	printarticlelink($other, false);

if (count($main_article['related_articles']) > 0)
	echo('	<h4>'.$related_heading.'</h4>'."\n");

foreach ($main_article['related_articles'] as $related)
	printarticlelink($related, false);

foreach($main_article['fact_boxes'] as $fact_box) {
	echo('	<h4>'.$fact_box['title'].'</h4>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		'.$fact_box['wikitext']."\n");
	echo('	</div>'."\n");
}
?>
</div>




	<div class='grey_box ArticleColumn'>
		<h1><?php echo $main_article['heading']; ?></h1>
		<?php if ($main_article['subheading'] != '') { ?>
			<h2><?php echo $main_article['subheading']; ?></h2>
		<?php } ?>
		<?php if ($main_article['subtext'] != '') { ?>
		<div class='intro'><?php echo $main_article['subtext']; ?></div>
		<?php } ?>

		<?php $this->byline->load(); ?>

        <p><?php echo $main_article['text']; ?></p>

		<?php if (isset($office_preview)) { ?>
			<p class='form'><button class="button" onclick="window.location='/office/news/<?php echo $main_article['id']; ?>';">GO BACK TO NEWS OFFICE</button></p>
		<?php } ?>
		<br style='clear: both;' />
	</div>
	<?php if (count($main_article['links']) > 0) { ?>
		<div class='blue_box'>
			<h2><?php echo $links_heading; ?></h2>
			<ul>
			<?php foreach ($main_article['links'] as $link) {
				echo '<li><a href=\'' . $link['url'] . '\' target=\'_blank\'>' . $link['name'] . '</a></li>';
			} ?>
			</ul>
		</div>
	<?php } ?>
