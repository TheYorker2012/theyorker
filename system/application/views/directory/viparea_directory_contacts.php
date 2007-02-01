<h3>Current Contacts</h3>
<?php
foreach ($organisation['cards'] as $member) {
	echo $member['name']." - ".$member['title']." <a href='/admin/directory/".$organisation['shortname']."/edit'>(Edit)</a> <a href='/admin/directory/".$organisation['shortname']."/delete'>(Delete)</a><br />";
}
?>
<form name='memberadd' method='post' action='/admin/directory/<?php echo $organisation['shortname']; ?>/addmember' class='form'>
	<fieldset>
		<legend>Add/Edit member</legend>
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
		<label for='memberadd_addbutton'></label>
		<input name='memberadd_addbutton' type='submit' id='memberadd_addbutton' value='Add/Edit' class='button' />
	</fieldset>
</form>