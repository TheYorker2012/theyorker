<div class='RightToolbar'>
	<?php if(!$user_is_editor) { ?>
	<h4>What's this?</h4>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
	<?php } ?>
	<h4>Visability</h4>
	<div class="Entry">
		<p>
		<?php echo $directory_visibility_text; ?>
		</p>
		<?php if($show_visibility) { ?>
		<form action="<?php echo vip_url('directory/information/'); ?>" method="post" class="form">
			<fieldset>
			<input type='submit' name='directory_visibility' class='button' value='<?php echo ($directory_visibility ? "Show Entry" : "Hide Entry"); ?>' />
			</fieldset>
		</form>
		<?php } ?>
	</div>
	<?php if($user_is_editor) { ?>
	<h4>Editor Options</h4>
	<div class="Entry">
		<p>
		<?php if($show_acceptance) echo 'Please decide on whether you wish to keep this entry.'; ?>
		</p>
		<form action="<?php echo vip_url('directory/information/'); ?>" method="post" class="form">
			<fieldset>
			<input type='submit' name='directory_deletion' class='button' onclick="return confirm('This operation will perminently remove this entry from the directory. Are you sure?');" value='<?php echo ($show_acceptance ? "Reject" : "Delete"); ?>' />
			<?php if($show_acceptance) { ?> <input type='submit' name='directory_acceptance' class='button' value='Accept' /> <?php } ?>
			</fieldset>
		</form>
	</div>
	<? } ?>
	<h4>Revisions</h4>
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
		echo $revisions_information_text;
		?>
	</div>
</div>
<div class='blue_box'>
	<h2>about</h2>
	<div name='name_details' id='name_details'>
	<p>
		Organisation name : <strong><?php echo $organisation['name']; ?></strong><br />
		Organisation type : <strong><?php echo $organisation['type']; ?></strong><br />
	</p>
	<?php
		if (PermissionsSubset('office', GetUserLevel()))
		{
		?>
		<form class='form'>
			<fieldset>
			<input name='name_edit_button' type='button' onClick="document.getElementById('name_details').style.display = 'none'; document.getElementById('name_details_form').style.display = 'block';" value='Edit' class='button' />
			</fieldset>
		</form>
		</div>
		<div name='name_details_form' id='name_details_form' style='display: none;'>
			<form name='org_name' action='<?php echo vip_url('directory/information/changename'); ?>' method='POST' class='form'>
				<fieldset>
					<label for='organisation_name'>Name:</label>
					<input type='text' name='organisation_name' value='<?php echo $organisation['name']; ?>'/>
					<br />
					<label for="organisation_type">Type:</label>
					<select name="organisation_type" size="1">
						<?php
							foreach($organisation['types'] as $type){
								echo "<option value='".$type['organisation_type_id']."' ";
									if ($organisation['type'] == $type['organisation_type_name'])
									{
									echo 'selected';
									}
								echo ">".$type['organisation_type_name']."</option>";
							}
						?>
					</select><br />
					<input name='name_update_button' type='submit' value='Update' class='button' />
					<input name='name_cancel_button' type='button' onClick="document.getElementById('name_details_form').style.display = 'none'; document.getElementById('name_details').style.display = 'block';" value='Cancel' class='button' />
				</fieldset>
			</form>
		<?php
		}
	?>
	</div>
<form id='orgdetails' name='orgdetails' action='<?php echo vip_url('directory/information'); ?>' method='POST' class='form'>
	<fieldset>
		<textarea name='description' cols='48' rows='10'><?php echo $organisation['description']; ?></textarea>
	</fieldset>
</div>
<div class='grey_box'>
<h2>details</h2>
	<fieldset>
		<label for='email_address'>Email Address:</label>
		<input type='text' name='email_address' style='width: 220px;' value='<?php echo $organisation['email_address']; ?>'/>
		<br />
		<label for='url'>Website:</label>
		<input type='text' name='url' style='width: 220px;' value='<?php echo $organisation['website']; ?>'/>
		<br />
		<label for='postal_address'>Postal Address:</label>
		<textarea type='text' name='postal_address' rows='5' style='width: 220px;'><?php echo $organisation['postal_address']; ?></textarea>
		<br />
		<label for='postcode'>Postcode:</label>
		<input type='text' name='postcode' style='width: 150px;' value='<?php echo $organisation['postcode']; ?>'/>
		<br />
		<label for='opening_hours'>Opening Times:</label>
		<textarea type='text' name='opening_hours' rows='4' style='width: 150px;'><?php echo $organisation['open_times']; ?></textarea>
		<br />
		<label for='phone_internal'>Phone Internal:</label>
		<input type='text' name='phone_internal' style='width: 150px;' value='<?php echo $organisation['phone_internal']; ?>' />
		<br />
		<label for='phone_external'>Phone External:</label>
		<input type='text' name='phone_external' style='width: 150px;' value='<?php echo $organisation['phone_external']; ?>' />
		<br />
		<label for='fax_number'>Fax Number:</label>
		<input type='text' name='fax_number' style='width: 150px;' value='<?php echo $organisation['fax_number']; ?>' />
		<br />
		<label for='submitbutton'></label>
		<input type='submit' name='submitbutton' value='Create new revision' class='button' />
	</fieldset>
</div>
</form>
<a href='<?php echo vip_url(); ?>'>Back to the vip area.</a>