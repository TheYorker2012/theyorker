	<div class='RightToolbar'>
		<h4>More Headlines</h4>
		<?php foreach ($news_previews as $preview) { ?>
		<div class='NewsPreview'>
			<a href='/news/article/<?php echo $preview['id']; ?>'><img src='<?php echo $preview['image']; ?>' alt='<?php echo $preview['image_description']; ?>' title='<?php echo $preview['image_description']; ?>' /></a>
			<h3><?php echo anchor('news/article/'.$preview['id'], $preview['heading']); ?></h3>
			<p class='Writer'><a href='/directory/view/1'><?php echo $preview['writer']; ?></a></p>
			<p class='Date'><?php echo $preview['date']; ?></p>
			<p class='More'><?php echo anchor('news/article/'.$preview['id'], 'Read more...'); ?></p>
		    <p><?php echo $preview['blurb']; ?></p>
			<br style='clear: both;' />
		</div>
		<?php } ?>
		<h4>Other News</h4>
	   	<?php foreach ($news_others as $other) { ?>
		<div class='NewsOther'>
			<a href='/news/article/<?php echo $other['id']; ?>'><img src='<?php echo $other['image']; ?>' alt='<?php echo $other['image_description']; ?>' title='<?php echo $other['image_description']; ?>' /></a>
		    <p class='Headline'><a href='/news/article/<?php echo $other['id']; ?>'><?php echo $other['heading']; ?></a></p>
			<p class='Writer'><a href='/directory/view/1'><?php echo $other['writer']; ?></a></p>
			<p class='Date'><?php echo $other['date']; ?></p>
		</div>
   		<?php } ?>
		<div style='clear: both; text-align: center;'>
			<a href='/news/rss/'><img src='/images/prototype/news/feed.gif' alt='RSS Campus News Feed' title='RSS Uni News Feed' /> Uni News Feed</a>
		</div>
		<div style='padding-bottom: 150px;'>&nbsp;</div>
		<?php foreach ($main_article['fact_boxes'] as $fact_box) {
			echo '<div class=\'orange_box\'>';
   			echo '<h2>facts</h2>'.$fact_box;
			echo '</div>';
		} ?>
	</div>
	<div class='blue_box'>
		<img src='/images/prototype/news/benest.png' alt='Reporter' title='Reporter' style='float: right;' />
		<h2 style='margin-bottom: 5px;'>reported by...</h2>
		<span style='font-size: medium;'><b>Chris Travis</b></span><br />
		<?php echo $main_article['date']; ?><br />
		<span style='color: #ff6a00;'>Read more articles by this reporter</span>
	</div>
	<div class='grey_box'>
		<div class='ArticleColumn'>
			<h1><?php echo $main_article['heading']; ?></h1>
	        <p><?php echo $main_article['text']; ?></p>
			<br style='clear: both;' />
		</div>
	</div>
