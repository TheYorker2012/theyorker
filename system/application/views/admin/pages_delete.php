<?php if ($confirm) { ?>
<div class="RightToolbar">
	<?php if (isset($main_text)) { ?>
		<h4>What&#034;s this?</h4>
		<p><?php echo($main_text); ?></p>
	<?php } ?>
</div>
<div class="blue_box">
	<H2>Confirm page deletion</H2>
	<p><em>Are you sure you want to delete this page and its associated properties?</em></p>
	Codename: <?php echo(xml_escape($information['codename'])); ?><br />
	Title: <?php echo(xml_escape($information['head_title'])); ?><br />
	Description: <?php echo(xml_escape($information['description'])); ?><br />
	Keywords: <?php echo(xml_escape($information['keywords'])); ?><br />
	<br />
	<?php echo(count($information['properties'])); ?> Properties:<br />
	<?php
	foreach ($information['properties'] as $property) {
		?>
			&nbsp;&nbsp;Property: <?php echo(xml_escape($property['label'])); ?> (<?php echo(xml_escape($property['type'])); ?>)<br />
		<?php
	}
	?>
	<br />
	<form action="<?php echo $target; ?>" method="POST" class="form">
		<fieldset>
			<input type="hidden" name="confirm_delete" value="confirm" />
			<input type="submit" class="button" name="submit" value="Yes, Delete" />
		</fieldset>
	</form>
</div>
<?php } ?>
<a href="/admin/pages">Back to Pages Administration</a>