<form action="/office/liveblog/scores" method="post">
	<table>
		<tr>
		</tr>
		<?php foreach ($allevents as $event) { ?>
		<tr>
			<td><?php echo($event['event_sport']); ?></td>
			<td><?php echo($event['event_name']); ?></td>
			<td><?php echo($event['event_venue']); ?></td>
			<td><?php echo($event['event_points']); ?></td>
			<td><input type="text" name="yscore<?php echo($event['event_id']); ?>" value="<?php echo(isset($valid[$event['event_york_score']]) ? $valid[$event['event_york_score']] : $event['event_york_score']); ?>" size="3" /></td>
			<td><input type="text" name="lscore<?php echo($event['event_id']); ?>" value="<?php echo(isset($valid[$event['event_lancaster_score']]) ? $valid[$event['event_lancaster_score']] : $event['event_lancaster_score']); ?>" size="3" /></td>
			<td><input type="submit" name="updatescore" value="<?php echo($event['event_id']); ?>" /></td>
		</tr>
		<?php } ?>
	</table>
</form>
