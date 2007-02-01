Organisation name : <strong><?php echo $organisation['name']; ?></strong><br />
Organisation type : <strong><?php echo $organisation['type']; ?></strong>
<form id='orgdetails' name='orgdetails' action='/admin/directory/<?php echo $organisation['shortname']; ?>/editinformation' method='POST' class='form'>
	<fieldset>
		<label for='orgdetails_website'>Website:</label>
		<input type='text' name='orgdetails_website' value='<?php echo $organisation['website']; ?>'/>
		<br />
		<label for='orgdetails_email'>Email Address:</label>
		<input type='text' name='orgdetails_email' value='<?php echo $organisation['email_address']; ?>'/>
		<br />
		<label for='orgdetails_postal_address'>Postal Address:</label>
		<input type='text' name='orgdetails_postal_address' value='<?php echo $organisation['postal_address']; ?>'/>
		<br />
		<label for='orgdetails_postcode'>Postcode:</label>
		<input type='text' name='orgdetails_postcode' value='<?php echo $organisation['postcode']; ?>'/>
		<br />
		<label for='orgdetails_location'>Location:</label>
		<input type='text' name='orgdetails_location' value='<?php echo $organisation['location']; ?>' />
		<br />
		<label for='orgdetails_openingtimes'>Opening Times:</label>
		<input type='text' name='orgdetails_openingtimes' value='<?php echo $organisation['open_times']; ?>' />
		<br />
		<label for='orgdetails_phone_internal'>Phone Internal:</label>
		<input type='text' name='orgdetails_phone_internal' value='<?php echo $organisation['phone_internal']; ?>' />
		<br />
		<label for='orgdetails_phone_external'>Phone External:</label>
		<input type='text' name='orgdetails_phone_external' value='<?php echo $organisation['phone_external']; ?>' />
		<br />
		<label for='orgdetails_fax_number'>Fax Number:</label>
		<input type='text' name='orgdetails_fax_number' value='<?php echo $organisation['fax_number']; ?>' />
		<br />
		<label for='orgdetails_about'>About:</label>
		<textarea name='orgdetails_about' cols='50' rows='10'><?php echo $organisation['description']; ?></textarea>
		<br />
		<label for='orgdetails_submitbutton'></label>
		<input type='submit' name='orgdetails_submitbutton' value='Update' class='button' />
	</fieldset>
</form>