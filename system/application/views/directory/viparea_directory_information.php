<div id="RightColumn">
<?php if(!$user_is_editor) { ?>
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo($main_text); ?>
	</div>
<?php } ?>

	<h2>Visibility</h2>
	<div class="Entry">
		<p>
		<?php echo($directory_visibility_text); ?>
		</p>
<?php if($show_visibility) { ?>
		<form action="<?php echo vip_url('directory/information/'); ?>" method="post">
			<fieldset>
				<input type="submit" name="directory_visibility" class="button" value="<?php echo($directory_visibility ? "Show Entry" : "Hide Entry"); ?>" />
			</fieldset>
		</form>
<?php } ?>
	</div>

<?php if(PermissionsSubset('pr', GetUserLevel())) { ?>
	<h2>Editor Options</h2>
	<div class="Entry">
	<?php if ($show_acceptance) { ?>
		<p>Please decide on whether you wish to keep this entry.</p>
	<?php } ?>
		<form action="<?php echo(vip_url('directory/information/')); ?>" method="post">
			<fieldset>
			<input type="submit" name="directory_deletion" class="button" onclick="return confirm('This operation will perminently remove this entry from the directory. Are you sure?');" value="<?php echo($show_acceptance ? 'Reject' : 'Delete'); ?>" />
			<?php if($show_acceptance) { ?> <input type="submit" name="directory_acceptance" class="button" value="Accept" /> <?php } ?>
			</fieldset>
		</form>
	</div>
<? } ?>

	<h2>Revisions</h2>
	<div class="Entry">
		<ol>
		<?php foreach($revisions as $revison) {
			echo '<li>';
			if ($revison['deleted']){echo '<span class="red">';}
				echo 'Author : '.$revison['author'].'<br />';
				echo 'Created : '.$revison['timestamp'].'<br />';
				if ($organisation['revision_id'] == $revison['id']) {
					echo ' <b>(Editing)</b>';
				} else {
					echo ' <a href="'.vip_url('directory/information/view');
					if ($revison['deleted']){echo 'all';}
					echo '/'.$revison['id'].'">Edit</a>';
				}
				echo(' <a href="'.vip_url('directory/information/preview/'.$revison['id']).'">Preview');
				if (!$revison['published'] && $user_is_editor) {
					echo(' &amp; Publish');
				}
				echo('</a>');
					if ($revison['published']==true){
						echo ' <span class="orange">(Published)</span>';
					}
			if ($revison['deleted']){echo '</span>';}
			echo '</li>';
		}?>
		</ol>
		<?php
		if ($show_show_all_revisions_option){
			if ($show_all_revisions){
				echo '<p><a href="'.vip_url('directory/information/view').'">Hide deleted revisions</a></p>';
			}else{
				echo '<p><a href="'.vip_url('directory/information/viewall').'">Include deleted revisions.</a></p>';
			}
		}
		echo($revisions_information_text);
		?>
	</div>
</div>

<div id="MainColumn">
	<form id="orgdetails" action="<?php echo(vip_url('directory/information')); ?>" method="post">
	<div class="BlueBox">
		<h2>about</h2>
		<div id="name_details">
		<p>
			Organisation name : <strong><?php echo $organisation['name']; ?></strong><br />
			Organisation type : <strong><?php echo $organisation['type']; ?></strong><br />
		</p>
<?php
if (PermissionsSubset('pr', GetUserLevel())) {
?>
		<form>
			<fieldset>
				<input name="name_edit_button" type="button" onclick="document.getElementById('name_details').style.display = 'none'; document.getElementById('name_details_form').style.display = 'block';" value="Edit" class="button" />
			</fieldset>
		</form>
		</div>
		<div id="name_details_form" style="display: none;">
			<form id="org_name" action="<?php echo(vip_url('directory/information/changename')); ?>" method="post">
				<fieldset>
					<label for="organisation_name">Name:</label>
						<input type="text" name="organisation_name" id="organisation_name" value="<?php echo($organisation['name']); ?>"/>
					<label for="organisation_type">Type:</label>
					<select name="organisation_type" id="organisation_type" size="1">
						<?php
							foreach($organisation['types'] as $type){
								echo('<option value="'.$type['organisation_type_id'].'" ');
								if ($organisation['type'] == $type['organisation_type_name']) {
									echo('selected="selected"');
								}
								echo('>'.$type['organisation_type_name'].'</option>');
							}
						?>
					</select><br />
				</fieldset>
				<fieldset>
					<input name="name_update_button" type="submit" value="Update" class="button" />
					<input name="name_cancel_button" type="button" onclick="document.getElementById('name_details_form').style.display = 'none'; document.getElementById('name_details').style.display = 'block';" value="Cancel" class="button" />
				</fieldset>
			</form>
	<?php
	}
?>
		</div>
		<fieldset>
			<textarea cols="42" rows="7" name="description"><?php echo($organisation['description']); ?></textarea>
		</fieldset>
	</div>

	<div class="BlueBox">
		<h2>details</h2>
		<fieldset>
			<label for="email_address">Email Address:</label>
			<input type="text" name="email_address" id="email_address" value="<?php echo($organisation['email_address']); ?>"/>

			<label for="url">Website:</label>
			<input type="text" name="url" id="url" value="<?php echo($organisation['website']); ?>"/>

			<label for="postal_address">Postal Address:</label>
			<textarea name="postal_address" id="postal_address" rows="5" cols="18"><?php echo($organisation['postal_address']); ?></textarea>

			<label for="postcode">Postcode:</label>
			<input type="text" name="postcode" id="postcode" value="<?php echo($organisation['postcode']); ?>"/>

			<label for="opening_hours">Opening Times:</label>
			<textarea name="opening_hours" id="opening_hours" rows="5" cols="18"><?php echo($organisation['open_times']); ?></textarea>

			<label for="phone_internal">Phone Internal:</label>
			<input type="text" name="phone_internal" id="phone_internal" value="<?php echo($organisation['phone_internal']); ?>" />

			<label for="phone_external">Phone External:</label>
			<input type="text" name="phone_external" id="phone_external" value="<?php echo($organisation['phone_external']); ?>" />

			<label for="fax_number">Fax Number:</label>
			<input type="text" name="fax_number" id="fax_number" value="<?php echo($organisation['fax_number']); ?>" />

		</fieldset>
		<fieldset>
			<input type="submit" name="submitbutton" id="submitbutton" value="Create new revision" class="button" />
		</fieldset>
	</div>
	</form>
</div>
