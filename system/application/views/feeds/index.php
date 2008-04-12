<?php
function printFeed ($feeds) {
	foreach ($feeds as $feed) {
		echo('<li><a href="/feeds/' . $feed[1] . '">' . $feed[0] . '</a>');
		if (count($feed[2]) > 0) {
			echo('<ul style="list-style-image: url(\'/images/prototype/news/feed.gif\');">');
			printFeed($feed[2]);
			echo('</ul>');
		}
		echo('</li>');
	}
}
?>

<div class="BlueBox">
	<h2><?php echo(xml_escape($whatis_title)); ?></h2>
	<?php echo($whatis_content); ?>
</div>

<div class="BlueBox">
	<h2><?php echo(xml_escape($feeds_title)); ?></h2>

	<ul style="list-style-image: url('/images/prototype/news/feed.gif');">
		<?php printFeed($feeds); ?>
	</ul>
</div>