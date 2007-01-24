	<div style='width: 380px; float: left; border: 1px #93969A solid; border-right: 0; padding: 5px;'>
		<div class='ArticleColumn'>
			<a href='/news/article/<?php echo $main_article['id']; ?>'>
			<!--
			<img src='<?php echo $main_article['image']; ?>' alt='<?php echo $main_article['image_description']; ?>' title='<?php echo $main_article['image_description']; ?>' />
			-->
			</a>
			<h1><?php echo $main_article['headline']; ?></h1>
			<div class='byline'>
				<img src='/images/prototype/news/benest.png' alt='' title='' />
				<div class='text'>
					<div style='color: #000; font-size: 11px; padding-bottom: 10px;'><?php echo $main_article['date']; ?></div>
					&gt; Chris<span style='color: #000;'>Travis</span><br />
					<span style='color: #08c0ef; font-size: 10px;'>Read more articles by this reporter</span>
				</div>
				<div class='role'><span style='float: right;'>News Reporter</span>test@theyorker.co.uk</div>
			</div>

	        <p><?php echo $main_article['body']; ?></p>
			<br style='clear: both;' />
		</div>
	</div>
	<div style='width: 239px; float: right;'>
		<div style='border-left: 1px #93969A solid; padding-left: 5px; padding-bottom: 10px;'>
			<div style='background-color: #94979b; color: #fff; margin: 0; padding: 3px 3px 3px 5px; font-size: 12px; font-weight: bold;'>
				More Headlines
			</div>
		</div>
		<?php for ($preview_number = 1; $preview_number <= 2; $preview_number++) { ?>
		<div class='NewsPreview' style='border-left: 1px #93969A solid; padding-left: 10px;'>
			<a href='/news/article/<?php echo $news_previews[$preview_number]['id']; ?>'><img src='<?php echo $news_previews[$preview_number]['image']; ?>' alt='<?php echo $news_previews[$preview_number]['image_description']; ?>' title='<?php echo $news_previews[$preview_number]['image_description']; ?>' /></a>
			<h3><?php echo anchor('news/article/'.$news_previews[$preview_number]['id'], $news_previews[$preview_number]['headline']); ?></h3>
			<p class='Writer'><a href='/directory/view/1'><?php echo $news_previews[$preview_number]['writer']; ?></a></p>
			<p class='Date'><?php echo $news_previews[$preview_number]['date']; ?></p>
			<p class='More'><?php echo anchor('news/article/'.$news_previews[$preview_number]['id'], 'Read more...'); ?></p>
		    <p><?php echo $news_previews[$preview_number]['subtext']; ?></p>
			<br style='clear: both;' />
		</div>
		<?php } ?>
		<div style='border-left: 1px #93969A solid; padding-left: 5px; padding-bottom: 10px;'>
			<div style='background-color: #94979b; color: #fff; margin: 0; padding: 3px 3px 3px 5px; font-size: 12px; font-weight: bold;'>
				Other News
			</div>
		</div>
	   	<?php for ($i = 0; $i < 6; $i++) { ?>
		<div class='NewsOther' style='border-left: 1px #93969A solid; padding-left: 10px;'>
			<a href='/news/article/<?php echo $news_others[$i]['id']; ?>'><img src='<?php echo $news_others[$i]['image']; ?>' alt='<?php echo $news_others[$i]['image_description']; ?>' title='<?php echo $news_others[$i]['image_description']; ?>' /></a>
		    <p class='Headline'><a href='/news/article/<?php echo $news_others[$i]['id']; ?>'><?php echo $news_others[$i]['headline']; ?></a></p>
			<p class='Writer'><a href='/directory/view/1'><?php echo $news_others[$i]['writer']; ?></a></p>
			<p class='Date'><?php echo $news_others[$i]['date']; ?></p>
		</div>
   		<?php } ?>
		<div style='clear: both; border: 1px #93969A solid; border-left: 0; padding-left: 10px;'>
			<h3 style='color: #ff6a00; text-align: left;'><?php echo $main_article['factbox_title']; ?></h3>
			<?php echo $main_article['factbox_contents']; ?>
		</div>
	</div>
	<div id='clear' style='clear: both;'>&nbsp;</div>