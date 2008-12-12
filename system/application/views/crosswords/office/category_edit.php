<?php
/**
 * @file views/crosswords/office/categorie_edit.php
 * @param $Permissions array[string => bool]
 * @param $MaxLengths array[string => int]
 *  - name
 *  - short_name
 * @param $Category array of category data
 *  - name
 *  - short_name
 */
?>

<div class="BlueBox">
	<h2>Category Details</h2>
	<form>
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

			<label	for="xword_cat_default_layout">Default Layout</label>
			<select id="xword_cat_default_layout" name="xword_cat_default_layout">
			</select>
		</fieldset>
	</form>
</div>
