<?php
/**
 * @file views/crosswords/office/layout_edit.php
 * @param $Permissions array[string => bool]
 * @param $MaxLengths array[string => int]
 *  - name
 * @param $Layout array of layout data
 *  - name
 *  - description 
 * @param $Actions array of actions
 * @param $PostAction string URL to post the form data to.
 */
?>

<div class="BlueBox">
	<h2>Layout Details</h2>
	<form	class="form"
			method="POST" action="<?php echo(xml_escape($PostAction)); ?>">
		<fieldset>
			<label	for="xword_layout_name">Layout Name</label>
			<input	id="xword_layout_name" name="xword_layout_name" type="text"
					maxlength="<?php echo((int)$MaxLengths['name']); ?>"
					value="<?php echo(xml_escape($Layout['name'])); ?>" />

			<label	for="xword_layout_description">Description</label>
			<textarea id="xword_layout_description" name="xword_layout_description"
					rows="10" cols="50"
					><?php echo(xml_escape($Layout['description'])); ?></textarea>

<?php
		foreach ($Actions as $action => $name) {
			?>
			<input	type="submit" class="button"
					id="xword_layout_<?php echo(xml_escape($action)); ?>"
					name="xword_layout_<?php echo(xml_escape($action)); ?>"
					value="<?php echo(xml_escape($name)); ?>" />
			<?php
		}
?>
		</fieldset>
	</form>
</div>
