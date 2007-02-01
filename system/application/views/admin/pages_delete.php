<?php if ($confirm) { ?>
	<p><em>Are you sure you want to delete this page and its associated properties?</em></p>
	Codename: <?php echo $information['codename']; ?><br />
	Title: <?php echo $information['title']; ?><br />
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
	<form name='delete_confirm_form' action='<?php echo $target; ?>' method='POST' class='form'>
		<fieldset>
			<input type='submit' class='button' name='confirm_delete' value='Yes, Delete' />
		</fieldset>
	</form>
<?php } ?>
<a href='/admin/pages'>Back to Pages Administration</a>