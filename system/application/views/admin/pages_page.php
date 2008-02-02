<div class="RightToolbar">
	<?php if (isset($main_text)) { ?>
		<h4>What's this?</h4>
		<p><?php echo $main_text; ?></p>
	<?php } ?>
	<?php if (isset($page_help)) { ?>
		<h4>Helper</h4>
		<?php echo($page_help); ?>
	<?php } ?>
</div>
<?php if ($show_details) { ?>
	<div class="blue_box">
		<h2>Page Details</h2>
		<form name="page_form" action="<?php echo($target); ?>" method="POST" class="form">
			<fieldset>
				<label for="codename">Codename:</label>
				<input name="codename" size="35" value=<?php
					echo '"';
					if (!empty($codename)) { echo(htmlentities($codename, ENT_QUOTES, 'utf-8')); }
					echo '"';
					if (!$permissions['rename']) { echo(' READONLY'); }
					?>>
				<br />
				<label id="title_label" for="title">Header Title:</label>
				<input name="head_title" size="35" value="<?php if (!empty($head_title)) { echo(htmlentities($head_title, ENT_QUOTES, 'UTF-8'));} ?>" />
				<label for="title_separate">Separate header and body titles</label>
				<input type="checkbox" name="title_separate"<?=($title_separate ? ' checked="checked"' : '')?> />
				<div id="separate_title">
					<label for="body_title">Body Title:</label>
					<input name="body_title" size="35" value="<?php if (!empty($body_title)) { echo(htmlentities($body_title, ENT_QUOTES, 'UTF-8'));} ?>" />
				</div>
				
				<br />
				<label for="description">Description</label>
				<input name="description" size="35" value="<?php if (!empty($description)) { echo(htmlentities($description, ENT_QUOTES, 'UTF-8'));} ?>" />
				<br />
				<label for="keywords">Keywords</label>
				<input name="keywords" size="35" value="<?php if (!empty($keywords)) { echo(htmlentities($keywords, ENT_QUOTES, 'UTF-8'));} ?>" />
				<br />
				<label for="type_id">Page type</label>
				<select name="type_id">
					<?php
						foreach($page_types as $k => $page_type) {
							echo('<option value ="'.$k.'"');
							if ($k == $type_id) {
								echo(' selected="selected"');
							}
							echo('>'.htmlentities($page_type['name'], ENT_QUOTES, 'UTF-8').'</option>');
						}
					?>
				</select><br />
			</fieldset>
			<fieldset>
				<label for="save_button"></label>
				<input type="submit" class="button" name="save_button" value="Save">
			</fieldset>
		</form>
	</div>
<?php
}
if (!empty($properties) || $permissions['prop_add']) {
?>

<div class="blue_box">
	<h2>Page Properties</h2>
	<form name="property_edit_form" action="<?php echo($target); ?>" method="POST" class="form">
		<fieldset>
			<?php
			foreach ($properties as $property) {
			?>
				<p style="font-size:small;">
					<b>Property Name : </b><?php echo(htmlentities($property['label'], ENT_QUOTES, 'UTF-8'));?><br />
					<b>Property Type : </b><?php echo(htmlentities($property['type'], ENT_QUOTES, 'UTF-8'));?><br />
					<?php if ($permissions['prop_delete']) { ?>
						<input type="checkbox" name="delete-<?php echo($property['id']);?>"> Delete this property
					<?php } ?>
				</p>
				<input type="hidden" name="label-<?php echo($property['id']);?>" value="<?php echo(htmlentities($property['label'], ENT_QUOTES, 'UTF-8'));?>">
				<input type="hidden" name="type-<?php echo($property['id']);?>" value="<?php echo(htmlentities($property['type'], ENT_QUOTES, 'UTF-8'));?>">
				<textarea name="<?php echo($property['id']);?>" class="full" rows="10" <?php if (!$permissions['prop_edit']) {echo 'READONLY';} ?>><?php echo(htmlentities($property['text'], ENT_QUOTES, 'UTF-8'));?></textarea>
				<br />
			<?php
			}
			if ($permissions['prop_add']) {
			?>
			<?php
			// This div (source) is cloned into destination by the add property button.
			// the inputs must be the first level of tags (not within a <p style="font-size:small;"></p>)
			// See public_html/javascript/clone.js
			?>
			<div id="source" style="display:none">
				<b>Property Name : </b><input name="label-newprop" value=""><br />
				<b>Property Type : </b>
					<select name="type-newprop">
						<option value ="text">Text</option>
						<option value ="wikitext">Wikitext</option>
						<option value ="xhtml">XHTML</option>
					</select><br />
				<textarea name="newprop" class="full" rows="10"></textarea>
				<br />
			</div>
			<?php
			// New properties are put here (destionation div)
			?>
			<input type="hidden" name="destination" id="destination" value="1" />
			<input type="button" class="button" onClick="AddClones()" value="Add Property"/>
			<?php }
			if ($permissions['prop_edit']) {
				?>
				<input type="hidden" name="save_properties" value="1">
				<input type="submit" class="button" name="save_property_button" value="Save Properties">
			<?php } ?>
		</fieldset>
	</form>
</div>
<?php
}
?>
<?php /*
<h2>Add a page property</h2>
<form name="property_form" action="<?php echo($target); ?>" method="POST" class="form">
	<fieldset>
		<label for="properties_name">Property name</label>
		<input size="35" name="properties_name">
		<br />
		<label for="properties_type">Property type</label>
		<input type="radio" name="properties_type" value="text"> Text 
		<input type="radio" name="properties_type" value="wikitext"> WikiText 
		<input type="radio" name="properties_type" value="image"> Image
		<br />
		<label for="properties_value">Property value</label>
		<textarea name="properties_value" class="full" rows="10"></textarea>
	</fieldset>
	<fieldset>
		<input type="submit" class="button" name="property_button" value="Add">
	</fieldset>
</form> */ ?>
<a href="/admin/pages">Back to Pages Administration</a>