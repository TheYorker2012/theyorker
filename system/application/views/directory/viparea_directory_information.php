<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href='#'>Maintain my account</a></li>
	<li><a href='#'>Remove this directory entry</a></li>
</ul>
</div>

<form id='orgdetails' name='orgdetails' action='/admin/directory/<?php echo $organisation['shortname']; ?>/editinformation' method='POST' class='form'>
<div class='blue_box'>
	<h2>about</h2>
	<p>
		Organisation name : <strong><?php echo $organisation['name']; ?></strong><br />
		Organisation type : <strong><?php echo $organisation['type']; ?></strong><br />
	</p>
	<textarea name='orgdetails_about' cols='48' rows='10'><?php echo $organisation['description']; ?></textarea>
</div>
<div class='grey_box'>
<h2>details</h2>
	<fieldset>
		<label for='orgdetails_email'>Email Address:</label>
		<input type='text' name='orgdetails_email' style='width: 220px;' value='<?php echo $organisation['email_address']; ?>'/>
		<br />
		<label for='orgdetails_website'>Website:</label>
		<input type='text' name='orgdetails_website' style='width: 220px;' value='<?php echo $organisation['website']; ?>'/>
		<br />
		<label for='orgdetails_website'>Yorkipedia Entry:</label>
		<input type='text' name='orgdetails_yorkipedia' style='width: 220px;' value='<?php echo $organisation['yorkipedia']['title']; ?>'/>
		<br />
		<label for='orgdetails_website'>Alphabetically List As:</label>
		<input type='text' name='orgdetails_alphlisting' style='width: 220px;' value='<?php echo $organisation['alphlisting']; ?>'/>
		<br />
		<label for='orgdetails_location'>Location:</label>
		<input type='text' name='orgdetails_location' style='width: 220px;' value='<?php echo $organisation['location']; ?>' />
		<br />
		<label for='orgdetails_postal_address'>Postal Address:</label>
		<textarea type='text' name='orgdetails_postal_address' rows='5' style='width: 220px;'><?php echo $organisation['postal_address']; ?></textarea>
		<br />
		<label for='orgdetails_postcode'>Postcode:</label>
		<input type='text' name='orgdetails_postcode' style='width: 150px;' value='<?php echo $organisation['postcode']; ?>'/>
		<br />
		<label for='orgdetails_openingtimes'>Opening Times:</label>
		<textarea type='text' name='orgdetails_openingtimes' rows='4' style='width: 150px;'><?php echo $organisation['open_times']; ?></textarea>
		<br />
		<label for='orgdetails_phone_internal'>Phone Internal:</label>
		<input type='text' name='orgdetails_phone_internal' style='width: 150px;' value='<?php echo $organisation['phone_internal']; ?>' />
		<br />
		<label for='orgdetails_phone_external'>Phone External:</label>
		<input type='text' name='orgdetails_phone_external' style='width: 150px;' value='<?php echo $organisation['phone_external']; ?>' />
		<br />
		<label for='orgdetails_fax_number'>Fax Number:</label>
		<input type='text' name='orgdetails_fax_number' style='width: 150px;' value='<?php echo $organisation['fax_number']; ?>' />
		<br />
		<label for='orgdetails_submitbutton'></label>
		<input type='submit' name='orgdetails_submitbutton' value='Update' class='button' />
	</fieldset>
</div>
</form>