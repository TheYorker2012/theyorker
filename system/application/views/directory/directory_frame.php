<?php
	// Navigation bar
	$content['navbar']->Load();
?>
<h2><?php echo $organisation['name']; ?></h2>
<?php
	// Page content
	$content[0]->Load();
?>
<div class="clear">&nbsp;</div>
<a href='/directory/'>Back to the directory</a>