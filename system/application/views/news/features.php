	<div id='newsnav'>
		<ul id='newsnavlist'>
		<li><a href='/news/'><img src='/images/prototype/news/uk.png' alt='Campus News' title='Campus News' /> Campus News</a></li>
		<li><a href='/news/national/'><img src='/images/prototype/news/earth.png' alt='National News' title='National News' /> National News</a></li>
		<li><a href='/news/features/' id='current'><img src='/images/prototype/news/feature.gif' alt='Features' title='Features' /> Features</a></li>
		<li><a href='/news/lifestyle/'><img src='/images/prototype/news/feature.gif' alt='Lifestyle' title='Lifestyle' /> Lifestyle</a></li>
		<li><a href='/news/archive/'><img src='/images/prototype/news/archive.png' alt='Archive' title='Archive' /> Archive</a></li>
		</ul>
	</div>
	<div class='clear'>&nbsp;</div>

	<div class='NewsPreview' style='width:380px; margin-right: 10px; margin-left: 5px;'>
		<a href='/news/article/<?php echo $main_article['id']; ?>'><img src='<?php echo $main_article['image']; ?>' alt='<?php echo $main_article['image_description']; ?>' title='<?php echo $main_article['image_description']; ?>' /></a>
		<h1><?php echo anchor('news/article/'.$main_article['id'], $main_article['heading']); ?></h1>
		<p class='Writer'><a href='/directory/view/1'><?php echo $main_article['writer']; ?></a></p>
		<p class='Date'><?php echo $main_article['date']; ?></p>
		<p class='More'><?php echo anchor('news/article/'.$main_article['id'], 'Read more...'); ?></p>
        <?php echo $main_article['text']; ?>
		<br style='clear: both;' />
	</div>
	
	<div style='width:255px; float: left;'>

    <?php
	foreach ($news_previews as $preview)
	{ ?>
		<div class='NewsPreview' style='width:255px;'>
			<a href='/news/article/<?php echo $preview['id']; ?>'><img src='<?php echo $preview['image']; ?>' alt='<?php echo $preview['image_description']; ?>' title='<?php echo $preview['image_description']; ?>' /></a>
			<h<?php echo $heading_size; ?>><?php echo anchor('news/article/'.$preview['id'], $preview['heading']); ?></h<?php echo $heading_size; ?>>
			<p class='Writer'><a href='/directory/view/1'><?php echo $preview['writer']; ?></a></p>
			<p class='Date'><?php echo $preview['date']; ?></p>
			<p class='More'><?php echo anchor('news/article/'.$preview['id'], 'Read more...'); ?></p>
	        <p><?php echo $preview['subtext']; ?></p>
			<br style='clear: both;' />
		</div>
    <?php } ?>

		<div class='NewsOther' style='width:255px;'>
	    	<?php
	    	foreach ($news_others as $other)
	    	{ ?>
			<p>
				<a href='/news/article/<?php echo $other['id']; ?>'><img src='<?php echo $other['image']; ?>' alt='<?php echo $other['image_description']; ?>' title='<?php echo $other['image_description']; ?>' /></a>
			    <p class='Headline'><a href='/news/article/<?php echo $other['id']; ?>'><?php echo $other['headline']; ?></a></p>
				<p class='Writer'><a href='/directory/view/1'><?php echo $other['writer']; ?></a></p>
				<p class='Date'><?php echo $other['date']; ?></p>
			</p>
    		<?php } ?>
		</div>
	</div>
	<div style='width:130px; float: left; text-align: center;'>
		<a href='' target='_blank'><img src='/images/adverts/3-120x600.gif' alt='Advert' title='Advert' /></a>
    </div>
	<div id='clear' style='clear: both;'>&nbsp;</div>