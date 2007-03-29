<?php
function printsmallnewsbox($article){
	echo('	<div class="NewsBox SmallNewsBox">'."\n");
	echo('          <a class="NewsImg" href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('                  <img src="'.$article['photo_url'].'" alt="'.$article['photo_title'].'" title="'.$article['photo_title'].'" />'."\n");
	echo('          </a>'."\n");
	echo('		<h3 class="SmallNewsHeading"><a href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('			'.$article['heading']."\n");
	echo('		</a></h3>'."\n");
	echo('		<div class="Date">'.$article['date'].'</div>'."\n");
//	echo('		<p class="More">'.$article['blurb'].'</p>'."\n");
	echo('	</div>'."\n");
}
?>

<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
		<span class="RightColumnAction"><a href=".">Customise</a></span>
		<a href="."><img src="/images/prototype/homepage/links.jpg" alt="Sample Links" title="Sample Links" /></a>
	</div>
	
	<h2>Today's Events</h2>
	<div class="Entry">
		<span class="RightColumnAction"><a href=".">Calendar</a></span>
		<ul>
			<li>To Do</li>
		</ul>
	</div>

	<h2>To Do</h2>
	<div class="Entry">
		<span class="RightColumnAction"><a href=".">New To Do</a></span>
		<ul>
			<li>To Do</li>
		</ul>
	</div>

	<h2>2 Day Forecast</h2>
	<div class="Entry">
		<?php echo($weather_forecast);?>
	</div>

	<h2>Quote of the Day</h2>
	<div class="Entry">
		"If you're going to kill a man it costs nothing to be polite to him" - <b>Winston Churchill</b>
	</div>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<img src="/images/prototype/homepage/rowing.jpg" alt="Rowing at York" title="Rowing at York" width="100%" />
	</div>

	<div class="BlueBox">
		<h2><?php echo "latest uni news"?></h2>
		<div class="NewsBox">
			<?php
				echo('	<a class="NewsImg" href="/news/'.$primary_article['article_type'].'/'.$primary_article['id'].'">'."\n");
				echo('		<img src="'.$primary_article['photo_url'].'" alt="'.$primary_article['photo_title'].'" title="'.$primary_article['photo_title'].'" />'."\n");
				echo('	</a>'."\n");
			?>
			<h3><?php echo('<a href="/news/'.$primary_article['article_type'].'/'.$primary_article['id'].'">'.$primary_article['heading'].'</a>') ?></h3>
			<div class="Date" > <?php echo($primary_article['date']) ?></div>
			<p class="More"> <?php echo($primary_article['blurb']) ?></p>
		</div>
		<?php printsmallnewsbox($secondary_article) ?>
		<?php printsmallnewsbox($tertiary_article) ?>
	</div>
</div>
