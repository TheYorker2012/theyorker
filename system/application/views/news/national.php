
	<div style='width:130px; float: left; text-align:center;'>
		<a href='/news/national'><img src='/images/prototype/news/national_news.jpg' alt='National News' title='National News' /></a>
		<br /><br /><br />
		<a href='/news'><img src='/images/prototype/news/icon_campus.jpg' alt='Campus News' title='Campus News' /></a>
		<br />
		<a href='/calendar'><img src='/images/prototype/news/icon_events.jpg' alt='Events' title='Events' /></a>
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
			<a href='<?php echo $news_previews[$preview_number]['link']; ?>' target='_blank'><img src='<?php echo $news_previews[$preview_number]['image']; ?>' alt='<?php echo $news_previews[$preview_number]['image_description']; ?>' title='<?php echo $news_previews[$preview_number]['image_description']; ?>' /></a>
			<h<?php echo $heading_size; ?>><?php echo anchor($news_previews[$preview_number]['link'], $news_previews[$preview_number]['headline']); ?></h<?php echo $heading_size; ?>>
			<p class='Writer'><a href=''><?php echo $news_previews[$preview_number]['writer']; ?></a></p>
			<p class='Date'><?php echo $news_previews[$preview_number]['date']; ?></p>
			<p class='More'><?php echo anchor($news_previews[$preview_number]['link'], 'Read more...'); ?></p>
	        <p><?php echo $news_previews[$preview_number]['subtext']; ?></p>
			<br style='clear: both;' />
		</div>
<?php } ?>

		<div class='NewsOther' style='width:255px; margin-right: 10px;'>
			<h3>UK</h3>
			<ul>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Ex-spy death inquiry stepped up</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Olympics audio surveillance row</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Missing boy search scaled down</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>No copyright extension for songs</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Two die after city museum 'fall'</a></li>
	 		</ul>
		</div>
		<div class='NewsOther' style='width:255px;'>
			<h3>Science / Nature</h3>
			<ul>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Ban on 'brutal' fishing blocked</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Wheat's lost gene helps nutrition</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Help urged for Ivory Coast waste</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Smart homes a reality in S Korea</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>What is polonium-210?</a>
			</ul>
		</div>
		<div class='NewsOther' style='width:255px; margin-right: 10px;'>
			<h3>Technology</h3>
			<ul>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Sony recalls cameras over glitch</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Problems hit Xbox video service</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>M&amp;S tops High Street web ranking</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Ban on MP3 transmitters is lifted</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Plastic paper to 'cut' emissions</a>
			</ul>
		</div>
		<div class='NewsOther' style='width:255px;'>
			<h3>Entertainment</h3>
			<ul>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Take That claim number one single</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Diana charity concert 'next year'</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>No copyright extension for songs</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Richards 'shattered' by outburst</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Eton Road out of The X Factor</a></li>
			</ul>
		</div>
		<div class='NewsOther' style='width:255px; margin-right: 10px;'>
			<h3>Education</h3>
			<ul>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Schools: to fail or not to fail?</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Exam system hit by record appeals</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Soap characters 'damage ambition'</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>School sacks woman after veil row</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>More fun fewer facts, pupils say</a></li>
			</ul>
		</div>
		<div class='NewsOther' style='width:255px;'>
			<h3>Education</h3>
			<ul>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Schools: to fail or not to fail?</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Exam system hit by record appeals</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>Soap characters 'damage ambition'</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>School sacks woman after veil row</a></li>
			<li><a href='<?php echo site_url('news/national/'); ?>'>More fun fewer facts, pupils say</a></li>
			</ul>
		</div>
	</div>
	<div style='width:130px; float: left; text-align: center;'>
		<a href='' target='_blank'><img src='/images/adverts/3-120x600.gif' alt='Advert' title='Advert' /></a>
    </div>
	<div id='clear' style='clear: both;'>&nbsp;</div>