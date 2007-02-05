<h3>Current Contacts</h3>
<?php
foreach ($organisation['cards'] as $member) {
	echo $member['name']." - ".$member['title']." <a href='/viparea/directory/".$organisation['shortname']."/edit/".$organisation['id']."'>(Edit)</a> <a href='/viparea/directory/".$organisation['shortname']."/delete/".$organisation['id']."'>(Delete)</a><br />";
}
?>
<form name='member' method='post' action='/viparea/directory/<?php echo $organisation['shortname']; ?>/contacts/update' class='form'>
	<fieldset>
		<legend>Add/Edit member</legend>
		<label for='member_name'>Name:</label>
		<input type='text' name='member_name' />
		<br />
		<label for='member_title'>Title:</label>
		<input type='text' name='member_title' />
		<br />
		<label for='member_title'>Image:</label>
		<input type='file' name='member_image' />
		<br />
		<label for='member_course'>Course:</label>
		<input type='text' name='member_course' />
		<br />
		<label for='member_email'>Email:</label>
		<input type='text' name='member_email' />
		<br />
		<label for='member_about'>About:</label>
		<textarea name='member_about' cols='40' rows='5'></textarea>
		<br />
		<label for='member_address'>Postal Address:</label>
		<textarea name='member_address' cols='20' rows='4'></textarea>
		<br />
		<label for='member_phone_mobile'>Phone Mobile:</label>
		<input type='text' name='member_phone_mobile' />
		<br />
		<label for='member_phone_internal'>Phone Internal:</label>
		<input type='text' name='member_phone_internal' />
		<br />
		<label for='member_phone_external'>Phone External:</label>
		<input type='text' name='member_phone_external' />
		<br />
		<label for='member_addbutton'></label>
		<input name='member_addbutton' type='submit' id='member_addbutton' value='Add/Edit' class='button' />
	</fieldset>
</form>