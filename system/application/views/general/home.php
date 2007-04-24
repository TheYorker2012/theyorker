<?php
function printsmallnewsbox($article){
	echo('	<div class="NewsBox SmallNewsBox">'."\n");
	echo('		<a class="NewsImg" href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('			'.$article['photo_xhtml']."\n").'';
	echo('		</a>'."\n");
	echo('		<h3 class="Headline"><small><a href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
	echo('			'.$article['heading']."\n");
	echo('		</a></small></h3>'."\n");
	echo('		<div style="clear: both" class="Date">'.$article['date'].'</div>'."\n");
	echo('	</div>'."\n");
}

function printmiddlebox($title,$article_array){
		echo('  <h2>'.$title.'</h2>'."\n");
		echo('  <ul class="TitleList">'."\n");
		foreach ($article_array as $article) {
			echo('          <li><a class="TitleHeading" href="/news/'.$article['article_type'].'/'.$article['id'].'" >'."\n");
			echo('			'.$article['heading']."\n");
			echo('		</a></li>'."\n");
		}
		echo('  </ul>'."\n");
	}
?>

<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
<?php if ($link->num_rows() > 0) { foreach($link->result() as $picture) {?>
		<a href="<?=$picture->link_url?>"><?=imageLocTag($picture->link_image_id, 'link', false, $picture->link_name, null, $picture->image_file_extension)?></a>
<?php } } else { ?>
	<a href="http://theyorker.co.uk">You have no links :(</a>
<?php }?>
		<a class="RightColumnAction"  href="/account/links">Customise</a>
	</div>

	<h2>My Webmail</h2>
	<div class="Entry">
		<table width="100%"><tr><td><a href="https://webmail.york.ac.uk/"><img src="/images/prototype/news/test/webmail_large.jpg"></a></td><td><a href="https://webmail.york.ac.uk/">0 Unread E-mails</a></td></tr></table>
		<table width="100%"><tr><td><a href="https://webmail.york.ac.uk/"><img src="/images/prototype/news/test/webmail_large.jpg"></a></td><td><a href="https://webmail1.york.ac.uk/">0 Unread E-mails</a></td></tr></table>
	</div>

	<h2>Today's Events</h2>
	<div class="Entry">
		<?php $events->Load(); ?>
		<a class="RightColumnAction"  href="/calendar/">Calendar</a>
	</div>

	<h2>To Do</h2>
	<div class="Entry">
		<?php $todo->Load(); ?>
		<a class="RightColumnAction"  href=".">New To Do</a>
	</div>

	<h2>York Weather</h2>
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
		<h2><?php echo('latest news')?></h2>
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
		<?php printsmallnewsbox($secondary_article) ?>
		<?php printsmallnewsbox($tertiary_article) ?>
	</div>

	<div class="BlueBox" style="float: left; width:48%; height:120px;">
		<?php 
			printmiddlebox('features',$features);
		?>
	</div>
	<div class="BlueBox" style="float: left; width:48%; height:120px; margin-left: 9px;">
		<?php
			printmiddlebox('arts',$arts);
		?>
	</div>

	<div class="BlueBox">
		<h2><?php echo('latest sport')?></h2>
		<div class="NewsBox">
			<?php
				echo('	<a class="NewsImg" href="/news/'.$primary_sports['article_type'].'/'.$primary_sports['id'].'">'."\n");
				echo('		'.$primary_sports['photo_xhtml']."\n");
				echo('	</a>'."\n");
			?>
			<h3 class="Headline"><?php echo('<a href="/news/'.$primary_sports['article_type'].'/'.$primary_sports['id'].'">'.$primary_sports['heading'].'</a>') ?></h3>
			<div class="Date" > <?php echo($primary_sports['date']) ?></div>
			<p class="More"> <?php echo($primary_sports['blurb']) ?></p>
		</div>
		<?php printsmallnewsbox($secondary_sports) ?>
		<?php printsmallnewsbox($tertiary_sports) ?>
	</div>
</div>
