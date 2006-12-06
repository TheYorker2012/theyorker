<div id='minibox'>
    <div id='title'>Show</div>
    <a href='#'><b>All results</b> <i>(112)</i></a>
    <a href='#'>News <i>(41)</i></a>
    <a href='#'>Reviews <i>(31)</i></a>
    <a href='#'>Campaigns <i>(12)</i></a>
    <a href='#'>News <i>(28)</i></a>
    <div id='title'>Sort by</div>
    <a href='#'><b>Relevancy</b></a>
    <a href='#'>Date</a>
    <a href='#'>...</a>
</div>
<div id='searchresults'>
    <ol>
	<?php
	foreach($result as $item) { ?>
		<li>
			<a href="#"><?=$item->title?></a>
			<?=$item->description?>
		</li>
	<?php >} ?>