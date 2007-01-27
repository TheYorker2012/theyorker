<h2>Organisation Information</h2>
Organisation name : <strong><?php echo $organisation['name']; ?></strong><br />
Organisation type : <strong><?php echo $organisation['type']; ?></strong>
<form id='orgdetails' name='orgdetails' action='/admin/yorkerdirectoryview/theyorker/editdetails' method='POST' class='form'>
	<fieldset>
		<legend>Update Information</legend>
		<label for='orgdetails_image'>Image:</label>
		<input type='file' name='orgdetails_image' />
		<br />
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
		<textarea name='orgdetails_about' cols='40' rows='10'><?php echo $organisation['description']; ?></textarea>
	</fieldset>
	<fieldset>
		<label for='orgdetails_submitbutton'></label>
		<input type='submit' name='orgdetails_submitbutton' value='Update' class='button' />
	</fieldset>
</form>

<h2>Members</h2>
The members of your society or organisation you list here will all be shown online but only the members that are givin a title will apear on the main part of the website. Only members with a title will have their full information listed.
<h3>Full member list</h3>
<p>This is a complete list of the members of your society/organisation.</p>
<p>
	<table width='100%'>
		<tr>
			<td><strong>Name</strong></td>
			<td><strong>Email</strong></td>
			<td><strong>Paid</strong></td>
			<td><strong>Delete?</strong></td>
		</tr>
		<tr>
			<td>Leroy Jenkins</td>
			<td><a href='mailto:lj500@york.ac.uk'>lj500@york.ac.uk</a></td>
			<td><a href='/admin/yorkerdirectoryview/theyorker/paid/123'>N</a></td>
			<td><a href='/admin/yorkerdirectoryview/theyorker/delete/123'>X</a></td>
		</tr>
		<tr>
			<td>Brian Peppers</td>
			<td><a href='mailto:bp500@york.ac.uk'>bp500@york.ac.uk</a></td>
			<td><a href='/admin/yorkerdirectoryview/theyorker/unpaid/456'>Y</a></td>
			<td><a href='/admin/yorkerdirectoryview/theyorker/delete/456'>X</a></td>
		</tr>
	</table>
</p>
<form name='memberadd' method='post' action='/admin/yorkerdirectoryview/theyorker/addmember' class='form'>
	<fieldset>
		<legend>Add member</legend>
		<label for='memberadd_name'>Name:</label>
		<input type='text' name='memberadd_name' />
		<br />
		<label for='memberadd_title'>Title:</label>
		<input type='text' name='memberadd_title' />
		<br />
		<label for='memberadd_course'>Course:</label>
		<input type='text' name='memberadd_course' />
		<br />
		<label for='memberadd_email'>Email:</label>
		<input type='text' name='memberadd_email' />
		<br />
		<label for='memberadd_about'>About:</label>
		<textarea name='memberadd_about' cols='40' rows='5'></textarea>
		<br />
		<label for='memberadd_address'>Postal Address:</label>
		<textarea name='memberadd_address' cols='20' rows='4'></textarea>
		<br />
		<label for='memberadd_phone_mobile'>Phone Mobile:</label>
		<input type='text' name='memberadd_phone_mobile' />
		<br />
		<label for='memberadd_phone_internal'>Phone Internal:</label>
		<input type='text' name='memberadd_phone_internal' />
		<br />
		<label for='memberadd_phone_external'>Phone External:</label>
		<input type='text' name='memberadd_phone_external' />
		<br />
		<label for='memberadd_paid'>Paid:</label>
		<input type='checkbox' name='memberadd_paid' value='1' />
	</fieldset>
	<fieldset>
		<label for='memberadd_addbutton'></label>
		<input name='memberadd_addbutton' type='submit' id='memberadd_addbutton' value='Add' class='button' />
	</fieldset>
</form>
