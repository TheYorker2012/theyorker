<?php if ($confirm) { ?>
<div class='RightToolbar'>
	<?php if (isset($main_text)) { ?>
		<h4>What's this?</h4>
		<p><?php echo($main_text); ?></p>
	<?php } ?>
</div>
<div class='blue_box'>
	<H2>Confirm page deletion</H2>
	<p><em>Are you sure you want to delete this page and its associated properties?</em></p>
	Codename: <?php echo(htmlentities($information['codename'],ENT_QUOTES,'utf-8')); ?><br />
	Title: <?php echo(htmlentities($information['head_title'],ENT_QUOTES,'utf-8')); ?><br />
	Description: <?php echo(htmlentities($information['description'],ENT_QUOTES,'utf-8')); ?><br />
	Keywords: <?php echo(htmlentities($information['keywords'],ENT_QUOTES,'utf-8')); ?><br />
	<br />
	<?php echo(count($information['properties'])); ?> Properties:<br />
	<?php
	foreach ($information['properties'] as $property) {
		?>
			&nbsp;&nbsp;Property: <?php echo(htmlentities($property['label'],ENT_QUOTES,'utf-8')); ?> (<?php echo(htmlentities($property['type'],ENT_QUOTES,'utf-8')); ?>)<br />
		<?php
	}
	?>
	<br />
	<form name='delete_confirm_form' action='<?php echo $target; ?>' method='POST' class='form'>
		<fieldset>
			<input type='hidden' name='confirm_delete' value='confirm' />
			<input type='submit' class='button' name='submit' value='Yes, Delete' />
		</fieldset>
	</form>
</div>
<?php } ?>
<a href='/admin/pages'>Back to Pages Administration</a>