<?php
/**
 * @file views/crosswords/office/tip_cat_edit.php
 * @param $Permissions array[string => bool]
 * @param $Form InputInterfaces
 * @param $Actions array of actions
 * @param $PostAction string URL to post the form data to.
 */
?>

<div class="BlueBox">
	<h2>tip category details</h2>
	<form	class="form"
			method="post" action="<?php echo(xml_escape($PostAction)); ?>">
		<fieldset>
<?php
		$Form->Load();
		foreach ($Actions as $action => $name) {
			?>
			<input	type="submit" class="button"
					id="tip_cat_<?php echo(xml_escape($action)); ?>"
					name="tip_cat_<?php echo(xml_escape($action)); ?>"
					value="<?php echo(xml_escape($name)); ?>" />
			<?php
		}
?>
		</fieldset>
	</form>
</div>
