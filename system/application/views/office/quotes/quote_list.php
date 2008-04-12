<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class='BlueBox'>
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
		<td><?php echo(xml_escape($quote['quote_text'])); ?></td>
		<td><?php echo(xml_escape($quote['quote_author'])); ?></td>
		<td><?php echo(isset($quote['quote_last_displayed_timestamp']) ? $quote['quote_last_displayed_timestamp'] : 'In Pool'); ?></td>
		<td><a href="/office/quotes/edit/<?php echo($quote['quote_id']); ?>" title="Edit this quote">Edit</a></td>
		</tr>

		<?php } ?>
		</table>

	</div>
</div>