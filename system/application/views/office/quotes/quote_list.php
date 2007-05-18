<div class='RightToolbar'>
	<h4>What's this?</h4>
	<div class="Entry">
		This page allows you to edit quotes <b>live</b>. Take care.
	</div>
</div>

<div class='blue_box'>
	<h2>quotes</h2>
	<table>
	<tr>
	<th>Quote</th>
	<th>Author</th>
	<th>Schedule</th>
	<th></th>
	</tr>

	<?php foreach($quotes as $quote) { ?>

	<tr>
	<td><?php echo htmlentities($quote['quote_text']); ?></td>
	<td><?php echo htmlentities($quote['quote_author']); ?></td>
	<td><?php echo (isset($quote['quote_last_displayed_timestamp']) ? $quote['quote_last_displayed_timestamp'] : 'Unscheduled'); ?></td>
	<td><a href="/office/quotes/edit/<?php echo $quote['quote_id']; ?>" title="Edit this quote">Edit</a></td>
	</tr>

	<? } ?>
	</table>

</div>