<div class='WholeColumn'>
	<div class="NewsPreview">
		<a href='/news/article/1'><img src='/images/prototype/news/thumb4.jpg' alt='Soldier about to get run over by a tank' title='Soldier about to get run over by a tank' /></a>
		<h1><?php echo anchor($article_link, $article_title); ?></h1>
		<p class='Writer'>
			<?php echo "<a href='".$article_author_link."'>".$article_author."</a>"; ?>
		</p>
		<p class='Date'><?php echo $article_date ?></p>
		<p class='More'><?php echo anchor($article_link, 'Read more...'); ?></p>
	        <p>
			<?php echo $article_content; ?>
		</p>
	</div>
	<hr>
</div>
<div class="RightColumn">
	<div class='LifestylePuffer' style='background-color: #04669c;'>
		<a href='/reviews/leagues'>
		<img src='/images/prototype/news/puffer1.jpg' alt='Ashes' title='Ashes' />
	    <h3>Awesome Food</h3>
		<p>This guy is happy becuase he has visited our top ten best all time foods places</p>
		</a>
	</div>
	<div class='LifestylePuffer' style='background-color: #a38b69;'>
		<a href='/reviews/leagues'>
		<img src='/images/prototype/news/puffer2.jpg' alt='Cooking' title='Cooking' />
	    <h3>Desert</h3>
		<p>We've been all around York trying chocolate cakes, see what we have to say about them all!</p>
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
		<p>Want some more romance in your life? View our special hand picked list of the ten best places for romance.</p>
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
<div class="LeftColumn">
		Search our expansive list of food reviews<br /><br />
			<h3 style="display: inline;">Food Type</h3><br /><br />
<?php
	for ($restauranttype = 0; $restauranttype < count($type_array['name']); $restauranttype++)
		echo anchor($type_array['link'][$restauranttype],$type_array['name'][$restauranttype]).'<br />';
?>
			<h3 style="display: inline;">Price</h3><br /><br />

<?php
	for ($pricetype = 0; $pricetype < count($price_array['name']); $pricetype++)
		echo anchor($price_array['link'][$pricetype],$price_array['name'][$pricetype]).'<br />';;
?>
</div>


<div class="clear">&nbsp;</div>
