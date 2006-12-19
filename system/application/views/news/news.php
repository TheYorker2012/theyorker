	<div id='navbar'>
		<ul>
		<li><a href='/news/' class='current'><img src='/images/prototype/news/uk.png' alt='Campus News' title='Campus News' /> Campus News</a></li>
		<li><a href='/news/national/'><img src='/images/prototype/news/earth.png' alt='National News' title='National News' /> National News</a></li>
		<li><a href='/news/features/'><img src='/images/prototype/news/feature.gif' alt='Features' title='Features' /> Features</a></li>
		<li><a href='/news/lifestyle/'><img src='/images/prototype/news/feature.gif' alt='Lifestyle' title='Lifestyle' /> Lifestyle</a></li>
		<li><a href='/news/archive/'><img src='/images/prototype/news/archive.png' alt='Archive' title='Archive' /> Archive</a></li>
		</ul>
	</div>
	<div class='clear'>&nbsp;</div>

	<div style='width:130px; float: left; text-align:center;'>
		<a href='/news'><img src='/images/prototype/news/campus_news.jpg' alt='Campus News' title='Campus News' /></a>
		<br /><br /><br />
		<a href='/news/rss'><img src='/images/prototype/news/icon_rss.jpg' alt='Subscribe to RSS Feed' title='Subscribe to RSS Feed' /></a>
		<br />
		<a href='/news/national'><img src='/images/prototype/news/icon_national.jpg' alt='National News' title='National News' /></a>
		<br />
		<a href='/listings'><img src='/images/prototype/news/icon_events.jpg' alt='Events' title='Events' /></a>
		<br />
		<a href='http://www.websudoku.com/'><img src='/images/prototype/news/icon_sudoku.jpg' alt='Play Sudoku' title='Play Sudoku' /></a>
		<br />
	</div>
	<div style='width:520px; float: left;'>

<?php
    /**
     * Preview #0 = top of the page
     * Preview #1 = bottom-left
     * Preview #2 = bottom-right
     */

	for ($preview_number = 0; $preview_number <= 2; $preview_number++) {
		echo '<div class=\'NewsPreview\' style=\'';
        /// the default heading size is 3 (i.e.: most headings in the loop will use <h3> for formatting)
        $heading_size = '3';
        /// different inline styles for each news preview
        switch ($preview_number){
            case 0:
                echo 'width:520px;';
                $heading_size = '1';
                break;
            case 1:
                echo 'width:255px; margin-right: 10px;';
                break;
            case 2:
                echo 'width:255px;';
                break;
		}
		echo '\'>';
		?>
			<a href='/news/article/<?php echo $news_previews[$preview_number]['id']; ?>'><img src='<?php echo $news_previews[$preview_number]['image']; ?>' alt='<?php echo $news_previews[$preview_number]['image_description']; ?>' title='<?php echo $news_previews[$preview_number]['image_description']; ?>' /></a>
			<h<?php echo $heading_size; ?>><?php echo anchor('news/article/'.$news_previews[$preview_number]['id'], $news_previews[$preview_number]['headline']); ?></h<?php echo $heading_size; ?>>
			<p class='Writer'><a href='/directory/view/1'><?php echo $news_previews[$preview_number]['writer']; ?></a></p>
			<p class='Date'><?php echo $news_previews[$preview_number]['date']; ?></p>
			<p class='More'><?php echo anchor('news/article/'.$news_previews[$preview_number]['id'], 'Read more...'); ?></p>
	        <p><?php echo $news_previews[$preview_number]['subtext']; ?></p>
			<br style='clear: both;' />
		</div>
<?php } ?>

		<div class='NewsOther' style='width:255px; margin-right: 10px;'>
	    	<?php for ($i = 0; $i < 3; $i++) { ?>
			<p>
				<a href='/news/article/<?php echo $news_others[$i]['id']; ?>'><img src='<?php echo $news_others[$i]['image']; ?>' alt='<?php echo $news_others[$i]['image_description']; ?>' title='<?php echo $news_others[$i]['image_description']; ?>' /></a>
			    <p class='Headline'><a href='/news/article/<?php echo $news_others[$i]['id']; ?>'><?php echo $news_others[$i]['headline']; ?></a></p>
				<p class='Writer'><a href='/directory/view/1'><?php echo $news_others[$i]['writer']; ?></a></p>
				<p class='Date'><?php echo $news_others[$i]['date']; ?></p>
			</p>
    		<?php } ?>
		</div>
		<div class='NewsOther' style='width:255px;'>
	    	<?php for ($i = 3; $i < 6; $i++) { ?>
			<p>
				<a href='/news/article/<?php echo $news_others[$i]['id']; ?>'><img src='<?php echo $news_others[$i]['image']; ?>' alt='<?php echo $news_others[$i]['image_description']; ?>' title='<?php echo $news_others[$i]['image_description']; ?>' /></a>
			    <p class='Headline'><a href='/news/article/<?php echo $news_others[$i]['id']; ?>'><?php echo $news_others[$i]['headline']; ?></a></p>
				<p class='Writer'><a href='/directory/view/1'><?php echo $news_others[$i]['writer']; ?></a></p>
				<p class='Date'><?php echo $news_others[$i]['date']; ?></p>
			</p>
    		<?php } ?>
		</div>
	</div>
	<div style='width:130px; float: left; text-align: center;'>
		<a href='' target='_blank'><img src='/images/adverts/3-120x600.gif' alt='Advert' title='Advert' /></a>
    </div>
	<div id='clear' style='clear: both;'>&nbsp;</div>