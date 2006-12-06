	<div id='newsnav'>
		<ul id='newsnavlist'>
		<li><a href='/news/' id='current'><img src='/images/prototype/news/uk.png' alt='Campus News' title='Campus News' /> Campus News</a></li>
		<li><a href='/news/national/'><img src='/images/prototype/news/earth.png' alt='National News' title='National News' /> National News</a></li>
		<li><a href='/news/features/'><img src='/images/prototype/news/feature.gif' alt='Features' title='Features' /> Features</a></li>
		<li><a href='/news/lifestyle/'><img src='/images/prototype/news/feature.gif' alt='Lifestyle' title='Lifestyle' /> Lifestyle</a></li>
		<li class='right'><a href='/news/archive/'><img src='/images/prototype/news/archive.png' alt='Archive' title='Archive' /> Archive</a></li>
		</ul>
	</div>

    <?php
    /**
     * Preview #0 = top of the page
     * Preview #1 = bottom-left
     * Preview #2 = bottom-right
     */
    for ($preview_number = 0; $preview_number <= 2; $preview_number++)
    {
        echo '<div class=\'NewsPreview\' style=\'';
        
        /// the default heading size is 3 (i.e.: most headings in the loop will use <h3> for formatting)
        $heading_size = '3';
        
        /// different inline styles for each news preview
        switch ($preview_number){
            case 0:
                echo 'border-bottom: 1px solid #93969a;';
                $heading_size = '1';
                break;
            case 1:
                echo 'border-bottom: 1px solid #93969a; width: 47%; float: left;';
                break;
            case 2:
                echo 'width: 47%; float: right;';
                break;
        }
        
        echo '\'>';
        
        ?>
		<a href='/news/article/<?php echo $news_previews[$preview_number]['id']; ?>'><img src='<?php echo $news_previews[$preview_number]['image']; ?>' alt='<?php echo $news_previews[$preview_number]['image_description']; ?>' title='<?php echo $news_previews[$preview_number]['image_description']; ?>' /></a>
		<h<?php echo $heading_size; ?>><?php echo anchor('news/article/'.$news_previews[$preview_number]['id'], $news_previews[$preview_number]['headline']); ?></h1>
		<p class='Writer'>
			<a href='/directory/view/1'><?php echo $news_previews[$preview_number]['writer']; ?></a>
		</p>
		<p class='Date'><?php echo $news_previews[$preview_number]['date']; ?></p>
		<p class='More'><?php echo anchor('news/article/'.$news_previews[$preview_number]['id'], 'Read more...'); ?></p>
        <p>
            <?php echo $news_previews[$preview_number]['blurb']; ?>
		</p>
		<br style='clear: both;'/>
	</div>
	
    <?php } ?>
    
    <div class='NewsOther' style='width: 47%;'>
    <h3></h3>
    
    <?php
    foreach ($news_others as $other)
    {
        
        ?>
		<p>
		    <a href='/news/article/<?php echo $other['id']; ?>'><img src='<?php echo $other['image']; ?>' alt='<?php echo $other['image_description']; ?>' title='<?php echo $other['image_description']; ?>' /></a>
		    <p class='Headline'><a href='/news/article/<?php echo $other['id']; ?>'><?php echo $other['headline']; ?></a></p>
			<p class='Writer'><a href='/directory/view/1'><?php echo $other['writer']; ?></a></p>
			<p class='Date'><?php echo $other['date']; ?></p>
			<p class='More'><?php echo anchor('news/article/'.$other['id'], 'Read more...'); ?></p>
		</p>
	
    <?php } ?>

		<p class='More' style='text-align:right;'>
			<a href='/news/archive'>View News Archive...</a>
		</p>
	</div>
	<div class='Advert'><img src='/images/adverts/boxad.jpg' /></div>