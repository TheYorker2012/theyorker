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
?>

<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
<?php if ($link->num_rows() > 0) { foreach($link->result() as $picture) {?>
		<a href="<?=$picture->link_url?>"><?=imageLocTag($picture->link_image_id, 'link', false, $picture->link_name)?></a>
<?php } } else { ?>
	<a href="http://theyorker.co.uk">You have no links :(</a>
<?php }?>
		<a class="RightColumnAction"  href="/account/links">Customise</a>
	</div>

	<h2>My Webmail</h2>
	<div class="Entry">
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
		<h2><?php echo('features') ?></h2>
		<ul style="list-style-position: outside; padding-left: 17px;">
		<li><a href="" style="font-weight: bold; color: #000000;" >Student in bad attack on cat</a></li>
		<li><a href="" style="font-weight: bold; color: #000000;">Why I think ducks are bad</a></li>
		</ul>

	</div>
	<div class="BlueBox" style="float: left; width:48%; height:120px; margin-left: 9px;">
		<h2><?php echo('arts') ?></h2>

		<ul style="list-style-position: outside; padding-left: 17px;">
		<li><a href="" style="font-weight: bold; color: #000000;">Green books are better for you</a></li>
		<li><a href="" style="font-weight: bold; color: #000000;">What the matrix never told you</a></li>
		</ul>
	</div>

	<div class="BlueBox">
		<h2><?php echo('latest sport')?></h2>
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
</div>
