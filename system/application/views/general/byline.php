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

foreach ($reporters as $id => $reporter) {
	echo('		');
	echo('<a href="/archive/reporter/'.$id.'">Read more articles by '.$reporter['name'].'</a>');
}
?>
	</div>
