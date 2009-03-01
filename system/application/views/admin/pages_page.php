<?php
/**
 * @file views/admin/pages_page.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 */

$property_types = array(
	'text' => 'Text',
	'wikitext' => 'Wikitext',
	'xhtml' => 'XHTML Block (advanced users only)',
	'xhtml_inline' => 'Inline XHTML (advanced users only)',
);

?>
<div id="RightColumn">
	<?php if (isset($main_text)) { ?>
		<h2 class="first">What's this?</h2>
		<div class="Entry">
			<p><a href="/admin/pages">Back to Pages Administration</a></p>
			<?php echo($main_text); ?>
		</div>
	<?php } ?>
	<?php if (isset($page_help)) { ?>
		<h2>Helper</h2>
		<div class="Entry">
			<?php echo($page_help); ?>
		</div>
	<?php } ?>
</div>
<div id="MainColumn">
<?php if ($show_details) { ?>
	<div class="BlueBox">
		<h2>Page Details</h2>
		<form action="<?php echo($target); ?>" method="POST" class="form">
			<fieldset>
				<label for="codename">Codename:</label>
				<input name="codename" size="35" value=<?php
					echo '"';
					if (!empty($codename)) {
						echo(xml_escape($codename));
					}
					echo '"';
					if (!$permissions['rename']) {
						echo(' readonly="readonly"');
					}
					?> />
				<br />
				<label id="title_label" for="title">Header Title:</label>
				<input name="head_title" size="35" value="<?php if (!empty($head_title)) { echo(xml_escape($head_title));} ?>" />
				<label for="title_separate">Separate header and body titles</label>
				<input type="checkbox" name="title_separate"<?php echo(($title_separate) ? ' checked="checked"' : ''); ?> />
				<div id="separate_title">
					<label for="body_title">Body Title:</label>
					<input name="body_title" size="35" value="<?php if (!empty($body_title)) { echo(xml_escape($body_title));} ?>" />
				</div>
				
				<br />
				<label for="description">Description</label>
				<input name="description" size="35" value="<?php if (!empty($description)) { echo(xml_escape($description));} ?>" />
				<br />
				<label for="keywords">Keywords</label>
				<input name="keywords" size="35" value="<?php if (!empty($keywords)) { echo(xml_escape($keywords));} ?>" />
				<br />
				<label for="type_id">Page type</label>
				<select name="type_id">
					<?php
						foreach($page_types as $k => $page_type) {
							echo('<option value ="'.$k.'"');
							if ($k == $type_id) {
								echo(' selected="selected"');
							}
							echo('>'.xml_escape($page_type['name']).'</option>');
						}
					?>
				</select><br />
			</fieldset>
			<fieldset>
				<label for="save_button"></label>
				<input type="submit" class="button" name="save_button" value="Save" />
			</fieldset>
		</form>
	</div>
<?php
}
if (!empty($properties) || $permissions['prop_add']) {
?>

<div class="BlueBox">
	<h2>Page Properties</h2>
	<form action="<?php echo($target); ?>" method="POST" class="form">
		<fieldset>
			<?php
			foreach ($properties as $property) {
			?>
				<label>Property Name:</label>
				<div style="float:left;margin: 5px 0 0 10px;"><?php echo(xml_escape($property['label'])); ?></div>
				<br />
				<label>Property Type:</label>
				<div style="float:left;margin:5px 0 0 10px;"><?php echo(xml_escape(isset($property_types[$property['type']]) ? $property_types[$property['type']] : $property['type'])); ?></div>
				<br />
				<?php if ($permissions['prop_delete']) { ?>
					<label for="delete-<?php echo($property['id']);?>">Delete Property:</label>
					<input type="checkbox" name="delete-<?php echo($property['id']);?>" id="delete-<?php echo($property['id']);?>" />
				<?php } ?>
				<input type="hidden" name="label-<?php echo($property['id']);?>" value="<?php echo(xml_escape($property['label']));?>">
				<input type="hidden" name="type-<?php echo($property['id']);?>" value="<?php echo(xml_escape($property['type']));?>">
				<textarea name="<?php echo($property['id']);?>" class="full" rows="10" cols="40" style="clear:left;" <?php if (!$permissions['prop_edit']) {echo 'READONLY';} ?>><?php echo(xml_escape($property['text']));?></textarea>
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
				<label>Property Name:</label>
				<input name="label-newprop" value="" />
				<br />
				<label>Property Type:</label>
				<select name="type-newprop">
<?php foreach ($property_types as $code => $name) { ?>
					<option value="<?php echo(xml_escape($code)); ?>"><?php echo(xml_escape($name)); ?></option>
<?php } ?>
				</select>
				<br />
				<textarea name="newprop" class="full" rows="10" cols="40" style="clear:left"></textarea>
				<br />
			</div>
			<?php
			// New properties are put here (destination div)
			?>
			<input type="hidden" name="destination" id="destination" value="1" />
		</fieldset>
		<fieldset>
			<input type="button" class="button" onClick="AddClone('source', 'destination')" value="Add Property"/>
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
</div>