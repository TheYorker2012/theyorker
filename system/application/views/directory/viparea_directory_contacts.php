<h3>Current Contacts</h3>
<?php
foreach ($organisation['cards'] as $member) {
	echo $member['name']." - ".$member['title']." <a href='/viparea/directory/".$organisation['shortname']."/contacts/edit/".$organisation['id']."'>(Edit)</a> <a href='/viparea/directory/".$organisation['shortname']."/delete/".$organisation['id']."'>(Delete)</a><br />";
}
?>
<form name='member' method='post' action='/viparea/directory/<?php echo $organisation['shortname']; ?>/contacts/update' class='form'>
	<fieldset>
		<legend>Add/Edit member</legend>
		<label for='member_name'>Name:</label>
		<input type='text' name='member_name' value='<?php if(!empty($editmember['name'])){echo $editmember['name'];} ?>'/>
		<br />
		<label for='member_title'>Title:</label>
		<input type='text' name='member_title' value='<?php if(!empty($editmember['title'])){echo $editmember['title'];} ?>'/>
		<br />
		<label for='member_title'>Image:</label>
		<input type='file' name='member_image'/>
		<br />
		<label for='member_course'>Course:</label>
		<input type='text' name='member_course' value='<?php if(!empty($editmember['course'])){echo $editmember['course'];} ?>'/>
		<br />
		<label for='member_email'>Email:</label>
		<input type='text' name='member_email' value='<?php if(!empty($editmember['email'])){echo $editmember['email'];} ?>'/>
		<br />
		<label for='member_about'>About:</label>
		<textarea name='member_about' cols='40' rows='5'><?php if(!empty($editmember['about'])){echo $editmember['about'];} ?></textarea>
		<br />
		<label for='member_address'>Postal Address:</label>
		<textarea name='member_address' cols='20' rows='4'><?php if(!empty($editmember['postal_address'])){echo $editmember['postal_address'];} ?></textarea>
		<br />
		<label for='member_phone_mobile'>Phone Mobile:</label>
		<input type='text' name='member_phone_mobile' value='<?php if(!empty($editmember['mobile_phone'])){echo $editmember['mobile_phone'];} ?>'/>
		<br />
		<label for='member_phone_internal'>Phone Internal:</label>
		<input type='text' name='member_phone_internal' value='<?php if(!empty($editmember['phone_internal'])){echo $editmember['phone_internal'];} ?>'/>
		<br />
		<label for='member_phone_external'>Phone External:</label>
		<input type='text' name='member_phone_external' value='<?php if(!empty($editmember['phone_external'])){echo $editmember['phone_external'];} ?>'/>
		<br />
		<label for='member_addbutton'></label>
		<input name='member_addbutton' type='submit' id='member_addbutton' value='Add/Edit' class='button' />
	</fieldset>
</form>
<a href='/viparea/'>Back to the vip area.</a>