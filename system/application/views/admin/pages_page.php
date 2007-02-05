<?php if ($show_details) { ?>
<h2>Page Details:</h2>
<form name='page_form' action='<?php echo $target; ?>' method='POST' class='form'>
	<fieldset>
		<label for='codename'>Codename:</label>
		<input name='codename' size='35' value=<?php
			echo '"';
			if (!empty($codename)) { echo $codename; }
			echo '"';
			if (!$permissions['rename']) { echo ' READONLY'; }
			?>>
		<br />
		<label for='title'>Title:</label>
		<input name='title' size='35' value='<?php if (!empty($title)) { echo $title;} ?>'>
		<br />
		<label for='description'>Description</label>
		<input name='description' size='35' value='<?php if (!empty($description)) { echo $description;} ?>'>
		<br />
		<label for='keywords'>Keywords</label>
		<input name='keywords' size='35' value='<?php if (!empty($keywords)) { echo $keywords;} ?>'>
		<br />
	</fieldset>
	<fieldset>
		<label for='save_button'></label>
		<input type='submit' class='button' name='save_button' value='Save'>
	</fieldset>
</form>
<?php
}
if (!empty($properties) || $permissions['prop_add']) {
?>

<h2>Properties</h2>
<form name='property_edit_form' action='<?php echo $target; ?>' method='POST' class='form'>
	<fieldset>
			<?php
			foreach ($properties as $property) {
			?>
				<p style='font-size:small;'>
					<b>Property Name : </b><?php echo $property['label'];?><br />
					<b>Property Type : </b><?php echo $property['type'];?><br />
					<?php if ($permissions['prop_delete']) { ?>
						<input type='checkbox' name="delete-<?php echo $property['id'];?>"> Delete this property
					<?php } ?>
				</p>
				<input type="hidden" name="label-<?php echo $property['id'];?>" value="<?php echo $property['label'];?>">
				<input type="hidden" name="type-<?php echo $property['id'];?>" value="<?php echo $property['type'];?>">
				<textarea name="<?php echo $property['id'];?>" cols="60" rows="10" <?php if (!$permissions['prop_edit']) {echo 'READONLY';} ?>><?php echo $property['text'];?></textarea>
				<br />
			<?php
			}
			if ($permissions['prop_add']) {
			?>
			<?php
			// This div (source) is cloned into destination by the add property button.
			// the inputs must be the first level of tags (not within a <p style='font-size:small;'></p>)
			// See public_html/javascript/clone.js
			?>
			<div id="source" style="display:none">
				<b>Property Name : </b><input name="label-newprop" value=""><br />
				<b>Property Type : </b>
					<SELECT name="type-newprop">
						<option value ="text">Text</option>
						<option value ="wikitext">Wikitext</option>
						<option value ="xhtml">XHTML</option>
					</SELECT><br />
				<textarea name="newprop" cols="60" rows="10"></textarea>
				<br />
			</div>
			<?php
			// New properties are put here (destionation div)
			?>
			<input type="hidden" name="destination" id="destination" value="1" />
		<input type="button" class='button' onClick="AddClones()" value="Add Property"/>
		<?php }
		if ($permissions['prop_edit']) {
			?>
			<input type='submit' class='button' name='property_edit_button' value='Save Properties'>
		<?php } ?>
	</fieldset>
</form>
<?php
}
?>
<?php /*
<h2>Add a page property</h2>
<form name='property_form' action='<?php echo $target; ?>' method='POST' class='form'>
	<fieldset>
		<label for='properties_name'>Property name</label>
		<input size='35' name='properties_name'>
		<br />
		<label for='properties_type'>Property type</label>
		<input type='radio' name='properties_type' value='text'> Text 
		<input type='radio' name='properties_type' value='wikitext'> WikiText 
		<input type='radio' name='properties_type' value='image'> Image
		<br />
		<label for='properties_value'>Property value</label>
		<textarea name="properties_value" cols="60" rows="10"></textarea>
	</fieldset>
	<fieldset>
		<input type='submit' class='button' name='property_button' value='Add'>
	</fieldset>
</form> */ ?>
<a href='/admin/pages'>Back to Pages Administration</a>