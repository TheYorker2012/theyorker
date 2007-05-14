	<div class="Byline">
<?php
foreach ($reporters as $reporter) {
	echo('		');
	echo('<img src="'.$reporter['photo'].'" alt="'.$reporter['name'].'" title="'.$reporter['name'].'" />'."\n");
}

foreach ($reporters as $reporter) {
	echo('		<span class="Name">'.$reporter['name'].'</span><br />'."\n");
}

echo('		<div class="Date">'.$article_date.'</div>'."\n");

?>
	</div>
