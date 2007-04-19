<?php
function printsmallnewsbox($article){
	echo('	'."\n");
	echo('		'."\n");
	echo('		<li><h3 class="Headline"><a href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('			'.$article['heading']."\n");
	echo('		</a></h3></li>'."\n");
	echo('		'."\n");
	echo('	'."\n");
}
?>

<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
		<!--<a href="."><img src="/images/prototype/homepage/links.jpg" alt="Sample Links" title="Sample Links" /></a>-->
		<p>TODO</p>
		<a class="RightColumnAction"  href=".">Customise</a>
	</div>

	<h2>Today's Events</h2>
	<div class="Entry">
		<ul>
			<li>To Do</li>
		</ul>
		<a class="RightColumnAction"  href=".">Calendar</a>
	</div>

	<h2>To Do</h2>
	<div class="Entry">
		<ul>
			<li>To Do</li>
		</ul>
		<a class="RightColumnAction"  href=".">New To Do</a>
	</div>

	<h2>2 Day Forecast</h2>
	<div class="Entry">
		<?php echo($weather_forecast);?>
	</div>

	<h2>Quote of the Day</h2>
	<div class="Entry">
		"<?php echo $quote->quote_text;?>" - <b><?php echo $quote->quote_author;?></b>
	</div>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php
			echo($banner);
		?>
	</div>

	<div class="BlueBox">
		<h2><?php echo('latest uni news')?></h2>
		<div class="NewsBox">
			<?php
				echo('	<a class="NewsImg" href="/news/'.$primary_article['article_type'].'/'.$primary_article['id'].'">'."\n");
				echo('		'.$primary_article['photo_xhtml']."\n");
				echo('	</a>'."\n");
			?>
			<h3 class="Headline"><?php echo('<a href="/news/'.$primary_article['article_type'].'/'.$primary_article['id'].'">'.$primary_article['heading'].'</a>') ?></h3>
			<div class="Date" > <?php echo($primary_article['date']) ?></div>
			<p class="More"> <?php echo($primary_article['blurb']) ?></p>
		</div>
		<div class="NewsBox">
		<ul>
		<?php printsmallnewsbox($secondary_article) ?>
		<?php printsmallnewsbox($tertiary_article) ?>
		</ul>
		</div>
	</div>

	<div class="BlueBox">
		<h2><?php echo('features') ?></h2>
	</div>
</div>
