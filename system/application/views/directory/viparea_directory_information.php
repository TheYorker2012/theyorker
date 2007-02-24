<div class='RightToolbar'>
<h4>What's this?</h4>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
	<h4>Revisions</h4>
	<div class="Entry">
		<ol>
		<?php foreach($revisions as $revison) {
			echo '<li>';
			echo 'Author : '.$revison['author'].'<br />';
			echo 'Created : '.$revison['timestamp'].'<br />';
			echo ' <a href="'.vip_url('directory/information/view/'.$revison['id']).'">View</a>';
				if ($revison['published']==true){
					echo ' <span class="orange">(Published)</span>';
				} else {
					echo ' <a href="'.vip_url('directory/information/publish/'.$revison['id']).'">Publish</a>';
					echo ' <a href="'.vip_url('directory/information/delete/'.$revison['id']).'">Delete</a>';
				}
			echo '</li>';
		}?>
		</ol>
	</div>
	<h4>Visability</h4>
	<div class="Entry">
		<p>
		<?php echo $directory_visibility_text; ?>
		</p>
		<form action="<?php echo vip_url('directory/information/'); ?>" method="post" class="form">
			<input type='submit' name='directory_visibility' class='button' value='<?php if($directory_visibility){ echo "Hide Entry"; }else{ echo "Show Entry"; }?>' /> 
		</form>
	</div>
</div>

<form id='orgdetails' name='orgdetails' action='<?php echo vip_url('directory/information'); ?>' method='POST' class='form'>
<div class='blue_box'>
	<h2>about</h2>
	<p>
		Organisation name : <strong><?php echo $organisation['name']; ?></strong><br />
		Organisation type : <strong><?php echo $organisation['type']; ?></strong><br />
		Editing revision id <?php echo $organisation['revision_id']; ?>
	</p>
	<textarea name='description' cols='48' rows='10'><?php echo $organisation['description']; ?></textarea>
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
		<label for='location'>Location:</label>
		<input type='text' name='location' style='width: 220px;' value='<?php echo $organisation['location']; ?>' />
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