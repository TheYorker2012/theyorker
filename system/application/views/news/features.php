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

	<?php $heading_size = '1'; $preview_number = 0; ?>
	<div class='NewsPreview' style='width:380px; margin-right: 10px; margin-left: 5px;'>
		<a href='/news/article/<?php echo $news_previews[$preview_number]['id']; ?>'><img src='<?php echo $news_previews[$preview_number]['image']; ?>' alt='<?php echo $news_previews[$preview_number]['image_description']; ?>' title='<?php echo $news_previews[$preview_number]['image_description']; ?>' /></a>
		<h<?php echo $heading_size; ?>><?php echo anchor('news/article/'.$news_previews[$preview_number]['id'], $news_previews[$preview_number]['headline']); ?></h<?php echo $heading_size; ?>>
		<p class='Writer'><a href='/directory/view/1'><?php echo $news_previews[$preview_number]['writer']; ?></a></p>
		<p class='Date'><?php echo $news_previews[$preview_number]['date']; ?></p>
		<p class='More'><?php echo anchor('news/article/'.$news_previews[$preview_number]['id'], 'Read more...'); ?></p>
        <p>
			Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi accumsan sem nec ante. Sed adipiscing volutpat arcu. In egestas leo volutpat lorem condimentum tempor. Fusce nibh. Sed pulvinar, lacus vitae sollicitudin pellentesque, tellus ante adipiscing enim, vitae ultricies libero ante nec turpis. Vestibulum eget mauris. Mauris id justo sed nisi iaculis ultrices. Morbi fringilla adipiscing mi. Curabitur tortor neque, gravida eget, eleifend nec, vulputate nec, nibh. Quisque interdum urna eu metus blandit facilisis. Etiam vehicula quam sit amet turpis. Donec porta metus. Donec pede neque, sagittis vel, cursus ac, aliquet vitae, nulla. Nulla euismod nonummy velit. Mauris nec purus eget neque iaculis pretium. Vivamus gravida. Ut vel tellus eget nisl tempus tincidunt. Phasellus egestas ultrices sapien.
		</p><br />
		<p>
			Cras eget nunc. Suspendisse potenti. Cras pulvinar mauris et risus. Nulla ullamcorper massa sit amet metus. Etiam vestibulum, nunc ac auctor sollicitudin, nunc mi consectetuer dolor, eu varius mauris quam sed risus. Praesent consequat, diam non elementum lobortis, augue quam eleifend est, ut sagittis quam nulla id neque. Nunc eu dui. Vivamus mi nulla, tempor quis, cursus luctus, aliquam eu, massa. Aliquam ut nibh. Quisque fermentum vulputate magna. Proin ultricies. Nulla feugiat nisi ac sem. Fusce pulvinar. Mauris vel nunc id ante elementum malesuada. Ut sollicitudin massa. Integer pede. Nullam sit amet nulla. Pellentesque interdum fringilla mi.
		</p><br />
		<p>
			Fusce consequat sem id neque cursus iaculis. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Sed nunc nisi, congue in, commodo vel, luctus quis, sapien. Ut iaculis tortor quis turpis. Pellentesque tincidunt lorem sed felis. Vivamus sagittis. Morbi orci. Pellentesque metus. Phasellus metus. Pellentesque nunc leo, mollis quis, tempus eu, tempor et, dui. Duis iaculis dui nec enim. Maecenas semper turpis in odio.
		</p>
		<br style='clear: both;' />
	</div>
	<div style='width:255px; float: left;'>

<?php
    /**
     * Preview #0 = top of the page
     * Preview #1 = bottom-left
     * Preview #2 = bottom-right
     */

	for ($preview_number = 1; $preview_number <= 2; $preview_number++) {
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
                echo 'width:255px;';
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

		<div class='NewsOther' style='width:255px;'>
	    	<?php for ($i = 0; $i < 6; $i++) { ?>
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