<h2>Preview</h2>
<div id="preview" style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px;">
	preview goes here
</div>
<h2>Create/edit a page:</h2>
<form name='custom_page_form' action='/admin/pages/newcustom/save' method='POST' class='form'>
	<fieldset>
		<label for='codename'>Codename:</label>
		<input name='codename'>
		<br />
		<label for='title'>Title:</label>
		<input name='title'>
		<br />
		<label for='description'>Description</label>
		<input name='description'>
		<br />
		<label for='keywords'>Keywords</label>
		<input name='keywords'>
		<br />
		<label for='comments'>Comments</label>
		<input type='checkbox' name='comments' id="comments">
		<br />
		<label for='ratings'>Ratings</label>
		<input type='checkbox' name='ratings' id="ratings">
		<br />
	</fieldset>
	<fieldset>
		<label for='preview_button'></label>
		<input type='submit' class='button' name='preview_button' value='Preview'>
		&nbsp;
		<input type='submit' class='button' name='save_button' value='Save'>
	</fieldset>
</form>
<h2>Page properties</h2>
<form name='custom_page_form' action='/admin/pages/newcustom/save' method='POST' class='form'>
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
		<textarea name="properties_value" cols="60" rows="10" id="main"></textarea>
	</fieldset>
	<fieldset>
		<input type='submit' class='button' name='property_button' value='Add'>
	</fieldset>
</form>
<div id="properties" style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px;">
	Property_name property_type property_value (delete)<br />
	Property_name property_type property_value (delete)<br />
	Property_name property_type property_value (delete)<br />
	Property_name property_type property_value (delete)<br />
</div>
<a href='/admin/pages'>Back to Pages Administration</a>