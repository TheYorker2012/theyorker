<?php
/**
 * @file views/crosswords/office/category_edit.php
 * @param $Permissions array[string => bool]
 * @param $MaxLengths array[string => int]
 *  - name
 *  - short_name
 * @param $Layouts array of layout information
 * @param $Category array of category data
 *  - name
 *  - short_name
 *  - default_width
 *  - default_height
 *  - default_layout_id
 *  - default_has_normal_clues
 *  - default_has_cryptic_clues
 * @param $Actions array of actions
 * @param $PostAction string URL to post the form data to.
 */
?>

<div class="BlueBox">
	<h2>Category Details</h2>
	<form	class="form"
			method="POST" action="<?php echo(xml_escape($PostAction)); ?>">
		<fieldset>
			<label	for="xword_cat_name">Category Name</label>
			<input	id="xword_cat_name" name="xword_cat_name" type="text"
					maxlength="<?php echo((int)$MaxLengths['name']); ?>"
					value="<?php echo(xml_escape($Category['name'])); ?>" />

			<label	for="xword_cat_short_name">Category Short Name (URI compatible)</label>
			<input	id="xword_cat_short_name" name="xword_cat_short_name" type="text"
					maxlength="<?php echo((int)$MaxLengths['short_name']); ?>"
					value="<?php echo(xml_escape($Category['short_name'])); ?>" />

			<label	for="xword_cat_default_width">Default Crossword Width</label>
			<input	id="xword_cat_default_width" name="xword_cat_default_width" type="text"
					maxlength="2"
					value="<?php echo((int)$Category['default_width']); ?>" />

			<label	for="xword_cat_default_height">Default Crossword Height</label>
			<input	id="xword_cat_default_height" name="xword_cat_default_height" type="text"
					maxlength="2"
					value="<?php echo((int)$Category['default_height']); ?>" />

			<label	for="xword_cat_default_layout">Default Crossword Layout</label>
			<select id="xword_cat_default_layout" name="xword_cat_default_layout">
<?php
			foreach ($Layouts as $id => $layout) {
				?><option value="<?php echo((int)$id); ?>"<?php
					if ($id == $Category['default_layout_id']) {
						?> selected="selected"<?php
					}
					?>><?php echo(xml_escape($layout['name'])); ?></option><?php
			}
?>
			</select>

			<label	for="xword_cat_default_has_normal_clues">Default Crosswords Have Normal Clues</label>
			<input	id="xword_cat_default_has_normal_clues" name="xword_cat_default_has_normal_clues" type="checkbox"<?php
				if ($Category['default_has_normal_clues']) {
					?> checked="checked"<?php
				}
				?> />

			<label	for="xword_cat_default_has_cryptic_clues">Default Crosswords Have Cryptic Clues</label>
			<input	id="xword_cat_default_has_cryptic_clues" name="xword_cat_default_has_cryptic_clues" type="checkbox"<?php
				if ($Category['default_has_cryptic_clues']) {
					?> checked="checked"<?php
				}
				?> />

			<label	for="xword_cat_default_winners">Default Crossword Winners</label>
			<input	id="xword_cat_default_winners" name="xword_cat_default_winners" type="text"
					maxlength="2"
					value="<?php echo((int)$Category['default_winners']); ?>" />

<?php
		foreach ($Actions as $action => $name) {
			?>
			<input	type="submit" class="button"
					id="xword_cat_<?php echo(xml_escape($action)); ?>"
					name="xword_cat_<?php echo(xml_escape($action)); ?>"
					value="<?php echo(xml_escape($name)); ?>" />
			<?php
		}
?>
		</fieldset>
	</form>
</div>
