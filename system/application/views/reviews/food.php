<div class='RightToolbar'>
	<h4>Leagues</h4>
	<div class='Entry'>
		<div class='LifestylePuffer' style='background-color: #04669c;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer1.jpg' alt='Ashes' title='Ashes' />
			<h3>Awesome Food</h3>
			<p>This guy is happy becuase he has visited our top ten foods places</p>
			</a>
		</div>
			<div class='LifestylePuffer' style='background-color: #a38b69;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer2.jpg' alt='Cooking' title='Cooking' />
			<h3>Desert</h3>
			<p>We've been all around York trying chocolate cakes...</p>
			</a>
		</div>
		<div class='LifestylePuffer' style='background-color: #000000;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer3.jpg' alt='Workout' title='Workout' />
			<h3>Lbs of Meat!</h3>
			<p>Want an all you can eat? Be sure to head on over to our ten best all you can eats.</p>
			</a>
		</div>
		<div class='LifestylePuffer' style='background-color: #ef7f94;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer4.jpg' alt='Love' title='Love' />
			<h3>Romance</h3>
			<p>Want some more romance in your life? Eat some lard!</p>
			</a>
		</div>
		<div class='LifestylePuffer' style='background-color: #000000;'>
			<a href='/reviews/leagues'>
			<img src='/images/prototype/news/puffer3.jpg' alt='Workout' title='Workout' />
			<h3>Beefcake</h3>
			<p>I tried this once and became a lard. Lots and lots of lard</p>
			</a>
		</div>
	</div>
</div>
<div class='grey_box'>
	<h2>browse by</h2>
	<span class="black"><?php echo $main_blurb; ?></span><br /><br />

<?php
//As far as I can tell we are going to show the first 2 columns only on this page
//Hence a for loop is probaility not worth it...

echo '<div class="half_left"><h3 style="display: inline;">';
echo $table_data['tag_group_names'][0];
echo '</h3><br /><br />';

foreach($table_data[$table_data['tag_group_names'][0]] as $tag)
{
	echo anchor('reviews/table/star/'.$table_data['tag_group_names'][0].'/'.$tag, $tag).'<br />';
}

echo'</div>';

echo '<div class="half_right"><h3 style="display: inline;">';
echo $table_data['tag_group_names'][1];
echo '</h3><br /><br />';

foreach($table_data[$table_data['tag_group_names'][1]] as $tag)
{
	echo anchor('reviews/table/star/'.$table_data['tag_group_names'][1].'/'.$tag, $tag).'<br />';
}

echo'</div>';

?>

</div>
<div class='blue_box'>
		<h2>featured article</h2>
		<?php echo '<a href="'.$article_link.'">'; ?><img style="float: right;" src='/images/prototype/news/thumb4.jpg' alt='Soldier about to get run over by a tank' title='Soldier about to get run over by a tank' /></a>
		<h3><?php echo anchor($article_link, $article_title); ?></h3>
		<span style='font-size: medium;'><b><?php echo "<a href='".$article_author_link."'>".$article_author."</a>"; ?></b></span><br />
		<?php echo $article_date ?><br />
		<span class="orange"><?php echo anchor($article_link, 'Read more...', array('class' => 'orange')); ?></span>
	        <p>
			<?php echo $article_content; ?>
		</p>
</div>
