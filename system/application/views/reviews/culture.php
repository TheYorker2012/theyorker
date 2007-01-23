<div class="WholeColumn">
	<div class='NewsPreview'>
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
		<h3>COLLEGE LEAGUES</h3>Current Positions
		<ol>
			<li>Halifax
			<li>Derwent
			<li>Langwith
		</ol>
	</div>
	<div class="LeftColumn">
		Click on the links below to get a list of all places that fits the category.<br />
		<div>
			<h3 style="display: inline;">Price</h3><br /><br />
<?php
	for ($pricetype = 0; $pricetype < count($price_array['name']); $pricetype++)
		echo anchor($price_array['link'][$pricetype],$price_array['name'][$pricetype]).'<br />';
?>
		</div><br />
		<div>
			<h3 style="display: inline;">Location</h3><br /><br />
<?php
	for ($locationno = 0; $locationno < count($location_array['name']); $locationno++)
		echo anchor($location_array['link'][$locationno],$location_array['name'][$locationno]).'<br />';
?>
		</div><br />
		<hr>
		<h3>BAR CRAWLS</h3>
		We also have a list of bar crawls which you can browse through<br /><br />
			<a href="/reviews/barcrawl">Bob Bastards Bar Craw</a><br />
			Sids Death Line<br />
			Garys Green Mile<br />
	</div>
