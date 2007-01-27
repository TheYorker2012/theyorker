<h2>Preview</h2>
<div id="preview" style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px;">
	preview goes here
</div>
<h2>Page Details:</h2>
<form name='page_form' action='<?php echo $target; ?>' method='POST' class='form'>
	<fieldset>
		<label for='codename'>Codename:</label>
		<input name='codename' value='<?php if (!empty($codename)) { echo $codename;} ?>'>
		<br />
		<label for='title'>Title:</label>
		<input name='title' value='<?php if (!empty($title)) { echo $title;} ?>'>
		<br />
		<label for='description'>Description</label>
		<input name='description' value='<?php if (!empty($description)) { echo $description;} ?>'>
		<br />
		<label for='keywords'>Keywords</label>
		<input name='keywords' value='<?php if (!empty($keywords)) { echo $keywords;} ?>'>
		<br />
		<label for='comments'>Comments</label>
		<input type='checkbox' name='comments' <?php if (!empty($comments)){if ($comments == 1) { echo "checked";}} ?>>
		<br />
		<label for='ratings'>Ratings</label>
		<input type='checkbox' name='ratings' <?php if (!empty($ratings)){if ($ratings == 1) { echo "checked";}} ?>>
		<br />
	</fieldset>
	<fieldset>
		<label for='save_button'></label>
		<input type='submit' class='button' name='save_button' value='Save'>
	</fieldset>
</form>
<h2>Add a page property</h2>
<form name='property_form' action='#' method='POST' class='form'>
	<fieldset>
		<label for='properties_name'>Property name</label>
		<input name='properties_name'>
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
</form>
<h2>Edit properties</h2>
<form name='property_edit_form' action='#' method='POST' class='form'>
	<fieldset>
		<div style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px;">
			<b>Property Name :</b> property_name
			<b>Property Type :</b> property_type <a href='#'>(del)</a><br />
			<textarea name="property_name_property" cols="60" rows="10">
			property_value
			</textarea>
			<br />
			<b>Property Name :</b> property_name
			<b>Property Type :</b> property_type <a href='#'>(del)</a><br />
			<textarea name="property_name_property" cols="60" rows="10">
			property_value
			</textarea>
			<br />
		</div>
		<input type='submit' class='button' name='property_edit_button' value='Update'>
	</fieldset>
</form>
<a href='/admin/pages'>Back to Pages Administration</a>