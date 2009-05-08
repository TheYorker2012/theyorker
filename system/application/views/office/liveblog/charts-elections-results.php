<?php
$boxes = array('a','d','g','h','j','l','v','w','nc','votes');
$candidate_count = -1;
?>

<form action="/office/liveblog/charts2/<?php echo($position->id); ?>" method="post">
<div class="BlueBox">
	<h2><?php echo($position->role); ?></h2>
	<p><a href="/office/liveblog/charts">GO BACK</a></p>

	<table>
<?php for ($r = 1; $r <= count($candidates[0]['rounds']); $r++) { ?>
		<tr>
			<th>ROUND #<?php echo($r); ?></th>
	<?php foreach ($boxes as $b) { ?>
			<th style="text-align:center"><?php echo($b); ?></th>
	<?php } ?>
		</tr>
	<?php foreach ($candidates as $c) { ?>
		<tr>
			<td><?php echo($c['name']); ?></td>
		<?php foreach ($boxes as $b) { ?>
			<td><input type="text" name="c-<?php echo($c['id'].'-'.$r.'-'.$b); ?>" size="1" value="<?php echo($c['rounds'][$r][$b]); ?>" /></td>
		<?php } ?>
		</tr>
	<?php } ?>
<?php } ?>
		<tr>
			<th>ROUND #<?php echo(count($candidates[0]['rounds']) + 1); ?></th>
<?php foreach ($boxes as $b) { ?>
			<th style="text-align:center"><?php echo($b); ?></th>
<?php } ?>			
		</tr>
<?php foreach ($candidates as $c) { $candidate_count++; $round_count = -1; ?>
		<tr>
			<td><?php echo($c['name']); ?></td>
	<?php foreach ($boxes as $b) { $round_count++; ?>
			<td><input type="text" name="c-<?php echo($c['id'].'-'.(count($candidates[0]['rounds']) + 1).'-'.$b); ?>" size="1" tabindex="<?php echo($candidate_count + (count($candidates) * $round_count) + 5); ?>" /></td>
	<?php } ?>
		</tr>
<?php } ?>
	</table>
	<div>
		<input type="submit" name="set-data" value="Save" class="button" />
	</div>
	<div style="clear:both"></div>
</div>

<div class="BlueBox" id="chart1">
	<div style="float:right">
		<input type="text" name="chart1-height" value="<?php echo($_POST['chart1-height']); ?>" size="1" />
		<input type="submit" name="update-chart" value="Update" />
		<br />
		<input type="submit" name="insert-chart1" value="Insert into Article" />
	</div>
	<?php foreach ($chart1  as $c) { ?>
		<p><img src="<?php echo($c); ?>" /><br />
		<input type="text" value="<?php echo($c); ?>" size="80" /><br /></p>
	<?php } ?>
</div>
</form>
