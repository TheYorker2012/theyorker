<h2>Page Details:</h2>
<form name='page_form' action='<?php echo $target; ?>' method='POST' class='form'>
	<fieldset>
		<label for='codename'>Codename:</label>
		<input name='codename' size='35' value='<?php if (!empty($codename)) { echo $codename;} ?>'>
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
 <?php
 //if (!empty($properties)){
 ?>
<h2>Edit properties</h2>
<form name='property_edit_form' action='<?php echo $target; ?>' method='POST' class='form'>
	<fieldset>
			<?php
			foreach ($properties as $property) {
			?>
				<p style='font-size:small;'>
					<b>Property Name : </b><?php echo $property['label'];?><br />
					<b>Property Type : </b><?php echo $property['type'];?><br />
					<a href='/admin/pages/delete/property/<?php echo $property['id'];?>'>Delete this property</a>
				</p>
				<input type="hidden" name="label-<?php echo $property['id'];?>" value="<?php echo $property['label'];?>">
				<input type="hidden" name="type-<?php echo $property['id'];?>" value="<?php echo $property['type'];?>">
				<textarea name="<?php echo $property['id'];?>" cols="60" rows="10"><?php echo $property['text'];?></textarea>
				<br />
			<?php
			}
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
			<input type="hidden" name="destination" id="destination" value="1" />
		<input type="button" class='button' onClick="AddClones()" value="Add Property"/>
		<input type='submit' class='button' name='property_edit_button' value='Save Properties'>
	</fieldset>
</form>
<?php
//}
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