<div class="BlueBox">
	<h2>announcements</h2>

	<div>
		<a href="/office/announcements/add">
			<img src="/images/version2/office/button_add.png" alt="Add New Notification" />
		</a>
	</div>

	<table>
		<tr>
			<th>Subject</th>
			<th>Recipients</th>
			<th>Poster</th>
			<th>Date</th>
		</tr>
<?php foreach ($announcements as $item) { ?>
		<tr<?php if ($item->deleted) echo(' style="color:red"'); ?>>
			<td><a href="<?php echo($item->id); ?>"><?php echo(xml_escape($item->subject)); ?></a></td>
			<td><?php echo($item->sent_to); ?></td>
			<td><?php echo(xml_escape($item->user_name)); ?></td>
			<td><?php echo(date('d/m/y H:i', $item->time)); ?></td>
		</tr>
<?php } ?>
	</table>
</div>