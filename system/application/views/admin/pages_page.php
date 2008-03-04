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
$hidden_property_types = array(
	'wikitext_cache' => 'Wikitext XHTML Cache',
);

?>
<div class="RightToolbar">
	<?php if (isset($main_text)) { ?>
		<h4>What's this?</h4>
		<p><a href="/admin/pages">Back to Pages Administration</a></p>
		<p><?php echo($main_text); ?></p>
	<?php } ?>
	<?php if (isset($page_help)) { ?>
		<h4>Helper</h4>
		<?php echo($page_help); ?>
	<?php } ?>
</div>
<?php if ($show_details) { ?>
	<div class="blue_box">
		<h2>Page Details</h2>
		<form action="<?php echo($target); ?>" method="POST" class="form">
			<fieldset>
				<?php
				if (!$override) {
					$this->load->view('general/message', array(
						'class' => 'information',
						'text' => 'This page is using default settings built into the site. You will need to save the page to be able to override the page properties.',
					));
				}
				?>
				<label for="codename">Codename:</label>
				<input id="codename" name="codename" size="35" value=<?php
					echo '"';
					if (!empty($codename)) {
						echo(xml_escape($codename));
					}
					echo '"';
					if (!$permissions['rename']) {
						echo(' readonly="readonly"');
					}
					?>>
				<br />
				<label for="head_title">Header Title:</label>
				<input id="head_title" name="head_title" size="35" value="<?php if (!empty($head_title)) { echo(xml_escape($head_title));} ?>" />
				<label for="title_separate">Separate header and body titles</label>
				<input	type="checkbox"
						id="title_separate"
						name="title_separate"<?php echo($title_separate ? ' checked="checked"' : ''); ?>
						onclick="document.getElementById('separate_title').style.display=(this.checked ? '' : 'none');"/>
				<div	id="separate_title"
						style="display: <?php echo($title_separate ? 'block' : 'none'); ?>;">
					<label for="body_title">Body Title:</label>
					<input id="body_title" name="body_title" size="35" value="<?php if (!empty($body_title)) { echo(xml_escape($body_title));} ?>" />
				</div>
				
				<br />
				<label for="description">Description</label>
				<input id="description" name="description" size="35" value="<?php if (!empty($description)) { echo(xml_escape($description));} ?>" />
				<br />
				<label for="keywords">Keywords</label>
				<input id="keywords" name="keywords" size="35" value="<?php if (!empty($keywords)) { echo(xml_escape($keywords));} ?>" />
				<br />
				<label for="type_id">Page type</label>
				<select id="type_id" name="type_id">
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
	<form action="<?php echo($target); ?>" method="POST" class="form">
		<fieldset>
			<?php
			foreach ($properties as $label => $labelProperty) {
				foreach ($labelProperty as $type => $property) {
					if ($type == 'wikitext_cache') {
						continue;
					}
					$overridden = null !== $property['override'];
					$defaultAvailable = null !== $property['default'];
			?>
				<p style="font-size:small;">
					<b>Property Name : </b><?php echo(xml_escape($label));?><br />
					<b>Property Type : </b><?php
						echo(xml_escape(isset($property_types[$type])        ? $property_types[$type]
						              : (isset($hidden_property_types[$type]) ? $hidden_property_types[$type]
						              : $type)));
					?><br />
					<?php if ($permissions['prop_delete'] && $overridden && !$defaultAvailable) { ?>
						<input type="checkbox" name="delete-<?php echo($property['tagName']);?>"> Delete this property
					<?php } ?>
				</p><?php /*
				<input type="hidden" name="label-<?php echo($property['tagName']);?>" value="<?php echo(xml_escape($label));?>">
				<input type="hidden" name="type-<?php echo($property['tagName']);?>" value="<?php echo(xml_escape($type));?>"> */ ?>
				<?php
				if (null !== $property['about']) {
					$this->load->view('general/message', array(
						'class' => 'help',
						'text' => str_replace(array("\n"), array('<br />'), xml_escape($property['about'])),
					));
				}
				?>
				<div	id="edit-<?php echo($property['tagId']); ?>"
						style="display: <?php echo($overridden ? 'inline' : 'none') ?>;">
					<?php if ($permissions['prop_edit'] && $defaultAvailable) { ?>
						<input	type="checkbox"
								id="revert-<?php echo($property['tagId']);?>"
								name="revert-<?php echo($property['tagName']);?>"
								<?php if (!$overridden) { ?> checked="checked" <?php } ?>
								onclick="if (this.checked) { document.getElementById('edit-<?php echo($property['tagId']); ?>').style.display='none'; document.getElementById('view-<?php echo($property['tagId']); ?>').style.display='inline'; }"> Revert this property
					<?php } ?>
					<textarea name="<?php echo($property['tagName']);?>" class="full" rows="10" <?php if (!$permissions['prop_edit']) {echo 'readonly="readonly"';} ?>><?php echo(xml_escape($property['text']));?></textarea>
				</div>
				<?php if ($defaultAvailable) { ?>
				<div	id="view-<?php echo($property['tagId']); ?>"
						style="display: <?php echo($overridden ? 'none' : 'block') ?>;"
						<?php if ($override) { ?> onclick="document.getElementById('edit-<?php echo($property['tagId']); ?>').style.display='inline'; this.style.display='none'; document.getElementById('revert-<?php echo($property['tagId']);?>').checked=false;" <?php } ?>
						title="click to change">
					<?php
					$this->load->view('general/message', array(
						'class' => 'information',
						'text' => 'This property is at its default value'.($override ? '(click to edit)' : ''),
					));
					?>
					<div style="border:1px solid #aaa; cursor:pointer;">
						<?php echo(str_replace(array("\n"), array('<br />'), xml_escape($property['default']))); ?>
					</div>
				</div>
				<?php } ?>
			<?php
				}
				?><br /><hr /><?php
			}
			if ($override) {
				if ($permissions['prop_add']) {
				?>
				<?php
				// This div (source) is cloned into destination by the add property button.
				// the inputs must be the first level of tags (not within a <p style="font-size:small;"></p>)
				// See public_html/javascript/clone.js
				?>
				<div id="source" style="display:none">
					<label>Property Name : <input name="label-newprop" value=""></label>
					<label>Property Type : 
						<select name="type-newprop">
<?php foreach ($property_types as $code => $name) { ?>
							<option value="<?php echo(xml_escape($code)); ?>"><?php echo(xml_escape($name)); ?></option>
<?php } ?>
						</select></label>
					<textarea name="newprop" class="full" rows="10"></textarea>
					<br />
				</div>
				<?php
				// New properties are put here (destination div)
				?>
				<input type="hidden" name="destination" id="destination" value="1" />
				<input type="button" class="button" onClick="AddClone('source', 'destination')" value="Add Property"/>
				<?php }
				if ($permissions['prop_edit']) {
					?>
					<input type="hidden" name="save_properties" value="1">
					<input type="submit" class="button" name="save_property_button" value="Save Properties">
				<?php } ?>
			<?php } ?>
		</fieldset>
	</form>
</div>
<?php
}
?>