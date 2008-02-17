	<div class="Byline">
<?php
foreach ($reporters as $reporter) {
	if($reporter['photo']) echo($reporter['photo']);
}

foreach ($reporters as $reporter) {
	echo('		<span class="Name">'.xml_escape($reporter['name']).'</span><br />'."\n");
}

echo('		<div class="Date">'.$article_date.'</div>'."\n");

foreach ($reporters as $id => $reporter) {
	echo('		');
}
?>
	</div>
