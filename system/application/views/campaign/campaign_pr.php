<div id="RightColumn">
	<h2 class="first"><?php echo $sections['sidebar_links']['title']; ?></h2>
	<div class="Entry">
		<a href="/campaign"><?php echo $sections['sidebar_links']['text']; ?></a>
	</div>
</div>

<div id="MainColumn">

<?php
	echo('	<div class="BlueBox">'."\n");
	echo('		<h2>'.$sections['progress_reports']['title'].'</h2>');
	foreach ($sections['progress_reports']['entries'] as $pr_entry) {
		echo('			');
		echo('<h3>'.$pr_entry['date'].'</h3>'."\n");
		echo('			');
		echo($pr_entry['text']);
	}
	echo('	</div>'."\n");
?>

</div>