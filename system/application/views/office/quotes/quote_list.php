<div class='RightToolbar'>
	<h4>What's this?</h4>
	<div class="Entry">
		This page allows you to edit quotes <b>live</b>. Take care.<br />
		<br />
		Note that the quotes in the pool are listed in <b>chronological order</b> of when they will appear on the site. This list shows only the next few quotes that are in the queue.
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
	<td><?php echo $quote['quote_text']; ?></td>
	<td><?php echo $quote['quote_author']; ?></td>
	<td><?php echo (isset($quote['quote_last_displayed_timestamp']) ? $quote['quote_last_displayed_timestamp'] : 'In Pool'); ?></td>
	<td><a href="/office/quotes/edit/<?php echo $quote['quote_id']; ?>" title="Edit this quote">Edit</a></td>
	</tr>

	<?php } ?>
	</table>

</div>