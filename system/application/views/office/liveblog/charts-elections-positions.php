<div class="BlueBox">
	<h2>Election Positions</h2>

	<?php foreach ($positions as $pos) { ?>
		<p><a href="/office/liveblog/charts2/<?php echo($pos->id); ?>"><?php echo($pos->role); ?></a> (<?php echo($pos->candidate_count); ?> candidates)</p>
	<?php } ?>
</div>
