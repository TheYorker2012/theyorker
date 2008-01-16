<?php if ($confirm) { ?>
<div class="RightColumn">
	<?php if (isset($main_text)) { ?>
		<h2 class="first">What's this?</h2>
		<p><?php echo $main_text; ?></p>
	<?php } ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2 class="first">Confirm page deletion</h2>
		<p><b>Are you sure you want to delete this page and its associated properties?</b></p>
		Codename: <?php echo $information['codename']; ?><br />
		Title: <?php echo $information['head_title']; ?><br />
		Description: <?php echo $information['description']; ?><br />
		Keywords: <?php echo $information['keywords']; ?><br />
		<br />
		<?php echo count($information['properties']); ?> Properties:<br />
		<?php
		foreach ($information['properties'] as $property) {
			?>
				&nbsp;&nbsp;Property: <?php echo $property['label']; ?> (<?php echo $property['type']; ?>)<br />
			<?php
		}
		?>
		<br />
		<form name="delete_confirm_form" action="<?php echo $target; ?>" method="post" class="form">
			<fieldset>
				<input type="hidden" name="confirm_delete" value="confirm" />
				<input type="submit" class="button" name="submit" value="Yes, Delete" />
			</fieldset>
		</form>
	</div>
	<a href="/admin/pages">Back to Pages Administration</a>
</div>
<?php } ?>