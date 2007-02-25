	<div class='RightToolbar'>
		<h4><?php echo $latest_heading; ?></h4>

		<?php if (isset($puffers)) { ?>

			<?php echo '<div class=\'LifestylePuffer\'>';
			foreach ($puffers as $puffer) {
				echo '<a href=\'/news/' . $puffer['codename'] . '\'>';
				echo '<img src=\'' . $puffer['image'] . '\' alt=\'' . $puffer['image_title'] . '\' title=\'' . $puffer['image_title'] . '\' />';
				echo '</a>';
			}
			echo '</div>'; ?>
			<h4><?php echo $other_heading; ?></h4>

		<?php } ?>

		<?php foreach ($news_previews as $preview) { ?>
		<div class='NewsPreview'>
			<a href='/news/<?php echo $preview['article_type']; ?>/<?php echo $preview['id']; ?>'><img src='<?php echo $preview['image']; ?>' alt='<?php echo $preview['image_description']; ?>' title='<?php echo $preview['image_description']; ?>' /></a>
			<h3><?php echo anchor('/news/' .$preview['article_type'].'/'.$preview['id'], $preview['heading']); ?></h3>
			<?php foreach ($preview['authors'] as $reporter) { ?>
			<p class='Writer'><a href='/contact'><?php echo $reporter['name']; ?></a></p>
			<?php } ?>
			<p class='Date'><?php echo $preview['date']; ?></p>
			<p class='More'><?php echo anchor('/news/'.$preview['article_type'].'/'.$preview['id'], 'Read more...'); ?></p>
		    <p><?php echo $preview['blurb']; ?></p>
			<br style='clear: both;' />
		</div>
		<?php } ?>

		<h4><?php echo $other_heading; ?></h4>
	   	<?php foreach ($news_others as $other) { ?>
		<div class='NewsOther'>
			<a href='/news/<?php echo $other['article_type']; ?>/<?php echo $other['id']; ?>'><img src='<?php echo $other['image']; ?>' alt='<?php echo $other['image_description']; ?>' title='<?php echo $other['image_description']; ?>' /></a>
		    <p class='Headline'><a href='/news/<?php echo $other['article_type']; ?>/<?php echo $other['id']; ?>'><?php echo $other['heading']; ?></a></p>
			<?php foreach ($other['authors'] as $reporter) { ?>
			<p class='Writer'><a href='/contact'><?php echo $reporter['name']; ?></a></p>
			<?php } ?>
			<p class='Date'><?php echo $other['date']; ?></p>
		</div>
   		<?php } ?>
		<div class='clear'></div>
		<?php if (count($main_article['related_articles']) > 0) { ?>
		<h4><?php echo $related_heading; ?></h4>
			<?php foreach ($main_article['related_articles'] as $related) { ?>
			<div class='NewsOther'>
				<a href='/news/<?php echo $article_type; ?>/<?php echo $related['id']; ?>'><img src='<?php echo $related['image']; ?>' alt='<?php echo $related['image_description']; ?>' title='<?php echo $related['image_description']; ?>' /></a>
			    <p class='Headline'><a href='/news/<?php echo $article_type; ?>/<?php echo $related['id']; ?>'><?php echo $related['heading']; ?></a></p>
				<?php foreach ($related['authors'] as $reporter) { ?>
				<p class='Writer'><a href='/contact'><?php echo $reporter['name']; ?></a></p>
				<?php } ?>
				<p class='Date'><?php echo $related['date']; ?></p>
			</div>
			<?php } ?>
		<?php } ?>
		<?php if ($article_type == 'uninews') { ?>
		<div style='clear: both; text-align: center;'>
			<a href='/news/rss/'><img src='/images/prototype/news/feed.gif' alt='<?php echo $rss_feed_title; ?>' title='<?php echo $rss_feed_title; ?>' /> <?php echo $rss_feed_title; ?></a>
		</div>
		<?php } ?>
		<?php foreach ($main_article['fact_boxes'] as $fact_box) { ?>
		<div style='padding-bottom: 150px;'>&nbsp;</div>
		<div class='orange_box'>
			<h2><?php echo $fact_box['title']; ?></h2>
			<?php echo $fact_box['wikitext']; ?>
		</div>
		<?php } ?>
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
			<p class='form'><button class="button" onclick="window.location='/office/news/article/<?php echo $main_article['id']; ?>';">GO BACK TO NEWS OFFICE</button></p>
		<?php } ?>
		<br style='clear: both;' />
	</div>
	<?php if (count($main_article['links']) > 0) { ?>
		<div class='blue_box'>
			<h2><?php echo $links_heading; ?></h2>
			<ul>
			<?php foreach ($main_article['links'] as $link) {
				echo '<li><a href=\'' . $link['url'] . '\'>' . $link['name'] . '</a></li>';
			} ?>
			</ul>
		</div>
	<?php } ?>