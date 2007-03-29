<?php
function printsmallnewsbox($article){
	echo('	<div class="NewsBox SmallNewsBox">'."\n");
	echo('          <a class="NewsImg" href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('                  <img src="'.$article['photo_url'].'" alt="'.$article['photo_title'].'" title="'.$article['photo_title'].'" />'."\n");
	echo('          </a>'."\n");
	echo('		<h3><a href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('			'.$article['heading']."\n");
	echo('		</a></h3>'."\n");
	echo('		<p class="Writer">'."\n");
	foreach ($article['authors'] as $author){
		echo($author['name'].' ');
	}
	echo('		</p>'."\n");
	echo('		<div class="Date">'.$article['date'].'</div>'."\n");
	echo('		<p class="More">'.$article['blurb'].'</p>'."\n");
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
			<li>9:30am &gt; Lecture &gt; Alcuin College</li>
			<li>10:30am &gt; Badminton &gt; Sports Centre</li>
			<li>2:30pm &gt; Lecture &gt; P/X/001</li>
			<li>6.00pm &gt; The Shuttle Launch &gt; Sports Centre, Main Hall</li>
			<li>7.30pm &gt; The Pirates of Penzance &gt; Central Hall</li>
		</ul>
	</div>

	<h2>To Do</h2>
	<div class="Entry">
		<span class="RightColumnAction"><a href=".">New To Do</a></span>
		<ul>
			<li>Oil Bike Chain!</li>
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

<!--<div id="HomeBanner">
	<img src="/images/prototype/homepage/rowing.jpg" alt="Rowing at York" title="Rowing at York" />
</div>-->

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo "latest uni news"?></h2>
		<div class="NewsBox">
			<?php
				echo('	<a class="NewsImg" href="/news/'.$primary_article['article_type'].'/'.$primary_article['id'].'">'."\n");
				echo('		<img src="'.$primary_article['photo_url'].'" alt="'.$primary_article['photo_title'].'" title="'.$primary_article['photo_title'].'" />'."\n");
				echo('	</a>'."\n");
			?>
			<h3><?php echo('<a href="/news/'.$primary_article['article_type'].'/'.$primary_article['id'].'">'.$primary_article['heading'].'</a>') ?></h3>
			<p class="Writer">
			<?php
				foreach ($primary_article['authors'] as $author){
					echo($author['name'].' ');
				}
			?>
			</p>
			<span>
			<div class="Date"> <?php echo($primary_article['date']) ?></div>
			<p class="More"> <?php echo($primary_article['blurb']) ?></p>
			</span>
		</div>
		<hr/>
		<?php printsmallnewsbox($tertiary_article) ?>
		<?php printsmallnewsbox($secondary_article) ?>
	</div>
</div>
