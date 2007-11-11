<div id="RightColumn">
	<h2 class="first"><?php echo($sections['sidebar_about']['title']); ?></h2>
	<div class="Entry">
		<h3><?php echo($sections['sidebar_about']['subtitle']); ?></h3>
		<?php echo($sections['charity']['target_text']); ?>
	</div>

	<h2><?php echo($sections['sidebar_help']['title']); ?></h2>
	<div class="Entry">
		<?php echo($sections['sidebar_help']['text']); ?>
	</div>

<?php
if (count($sections['article']['related_articles']) > 0) {
?>
	<h2><?php echo($sections['sidebar_related']['title']); ?></h2>
	<div class="Entry">
		<ul>
<?php
	foreach ($sections['article']['related_articles'] as $related_articles) {
		echo('			');
		echo('<li><a href="/news/uninews/'.$related_articles['id'].'">'.$related_articles['heading'].'</a></li>'."\n");
	}
?>
		</ul>
	</div>
<?php
}
?>

<?php
if (count($sections['article']['links']) > 0) {
?>
	<h2><?php echo($sections['sidebar_external']['title']); ?></h2>
	<div class="Entry">
		<ul>
<?php
	foreach ($sections['article']['links'] as $link) {
		echo('			');
		echo('<li><a href="'.$link['url'].'">'.$link['name'].'</a></li>'."\n");
	}
?>
		</ul>
	</div>
<?php
}
?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($sections['article']['heading']); ?></h2>
		<?php echo($sections['article']['text']); ?>
	</div>

	<div class="BlueBox">
		<h2><?php echo($sections['funding']['title']); ?></h2>
		<div class="Entry">
			<?php echo($sections['funding']['text']); ?>
		</div>
	</div>

<?php
if (isset($sections['progress_reports']['entries'])) {
	echo('	<div class="BlueBox">'."\n");
	echo('		<h2>'.$sections['progress_reports']['title'].'</h2>'."\n");
	foreach ($sections['progress_reports']['entries'] as $pr_entry) {
		echo('		<h3>'.$pr_entry['date'].'</h3>'."\n");
		echo($pr_entry['text']."\n");
	}
	if ($sections['progress_reports']['totalcount'] > 3)
		echo('<p><a href="/charity/preports/">There are older reports click here to view all progress reports.</a></p>'."\n");
	echo '</div>';
}
?>

</div>
