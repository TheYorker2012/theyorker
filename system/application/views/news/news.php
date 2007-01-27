	<div style='width: 420px; float: left; border: 1px #93969A solid; border-right: 0; padding: 5px;'>
		<div class='ArticleColumn'>
			<a href='/news/article/<?php echo $main_article['id']; ?>'>
			<!--
			<img src='<?php echo $main_article['image']; ?>' alt='<?php echo $main_article['image_description']; ?>' title='<?php echo $main_article['image_description']; ?>' />
			-->
			</a>
			<h1><?php echo $main_article['heading']; ?></h1>
			<div class='byline'>
				<img src='/images/prototype/news/benest.png' alt='' title='' />
				<div class='text'>
					<div style='color: #000; font-size: 11px; padding-bottom: 10px;'><?php echo $main_article['date']; ?></div>
					&gt; Chris<span style='color: #000;'>Travis</span><br />
					<span style='color: #08c0ef; font-size: 10px;'>Read more articles by this reporter</span>
				</div>
				<div class='role'><span style='float: right;'>News Reporter</span>test@theyorker.co.uk</div>
			</div>

	        <p><?php echo $main_article['text']; ?></p>
			<br style='clear: both;' />
		</div>
	</div>
	<div style='width: 219px; float: right;'>
		<div style='border-left: 1px #93969A solid; padding-left: 5px; padding-bottom: 10px;'>
			<div style='background-color: #94979b; color: #fff; margin: 0; padding: 3px 3px 3px 5px; font-size: 12px; font-weight: bold;'>
				More Headlines
			</div>
		</div>
		<?php foreach ($news_previews as $preview)
		{ ?>
		<div class='NewsPreview' style='border-left: 1px #93969A solid; padding-left: 10px;'>
			<a href='/news/article/<?php echo $preview['id']; ?>'><img src='<?php echo $preview['image']; ?>' alt='<?php echo $preview['image_description']; ?>' title='<?php echo $preview['image_description']; ?>' /></a>
			<h3><?php echo anchor('news/article/'.$preview['id'], $preview['heading']); ?></h3>
			<p class='Writer'><a href='/directory/view/1'><?php echo $preview['writer']; ?></a></p>
			<p class='Date'><?php echo $preview['date']; ?></p>
			<p class='More'><?php echo anchor('news/article/'.$preview['id'], 'Read more...'); ?></p>
		    <p><?php echo $preview['blurb']; ?></p>
			<br style='clear: both;' />
		</div>
		<?php } ?>
		<div style='border-left: 1px #93969A solid; padding-left: 5px; padding-bottom: 10px;'>
			<div style='background-color: #94979b; color: #fff; margin: 0; padding: 3px 3px 3px 5px; font-size: 12px; font-weight: bold;'>
				Other News
			</div>
		</div>
	   	<?php foreach ($news_others as $other)
	   	{ ?>
		<div class='NewsOther' style='border-left: 1px #93969A solid; padding-left: 10px;'>
			<a href='/news/article/<?php echo $other['id']; ?>'><img src='<?php echo $other['image']; ?>' alt='<?php echo $other['image_description']; ?>' title='<?php echo $other['image_description']; ?>' /></a>
		    <p class='Headline'><a href='/news/article/<?php echo $other['id']; ?>'><?php echo $other['heading']; ?></a></p>
			<p class='Writer'><a href='/directory/view/1'><?php echo $other['writer']; ?></a></p>
			<p class='Date'><?php echo $other['date']; ?></p>
		</div>
   		<?php } ?>
   		<div style='clear: both; border-left: 1px #93969A solid; padding-left: 5px; padding-bottom: 10px; height: 200px'>

		</div>
		<div style='clear: both; border: 1px #93969A solid; border-left: 0; padding-left: 10px; width: 75%'>
			<?php
			foreach ($main_article['fact_boxes'] as $fact_box)
			{
    			echo '<h3 style=\'color: #ff6a00; text-align: left;\'>Fact Box</h3>'.$fact_box;
			}
			?>
		</div>
		<div style='clear: both; border-left: 1px #93969A solid; padding-left: 5px; padding-bottom: 10px; height: 100%'>

		</div>
	</div>
	<div id='clear' style='clear: both;'>&nbsp;</div>