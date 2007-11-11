<?php
foreach ($textblocks as $textblock) {
?>
<div class="BlueBox">
<?php
	if ($textblock['image'] != null) {
		echo('	<div style="float: right">'."\n");
		echo('		'.$textblock['image']."\n");
		echo('	</div>'."\n");
	}
	echo('	'.$textblock['blurb']."\n");
	echo('</div>'."\n");
}
?>
