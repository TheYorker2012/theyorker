<div id="RightColumn">
	<?php if (isset($main_text)) { ?>
		<h2 class="first">What&#034;s this?</h2>
		<div class="Entry">
			<?php echo($main_text); ?>
		</div>
	<?php } ?>
</div>
<div id="MainColumn">
<?php if ($confirm) { ?>
	<div class='BlueBox'>
		<h2>Confirm page deletion</h2>
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
</div>
