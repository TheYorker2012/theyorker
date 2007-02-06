<div class='RightToolbar'>
	<h4>Photo</h4>
	<div class="Entry">
		The photo associated with this contact, together with an [Add/Replace Photo] button, should go here.
	</div>
</div>

<div class='blue_box'>

<h2>Edit <?php if(!empty($editmember['name'])){echo $editmember['name'];} ?></h2>

<form name='member' method='post' action='/viparea/directory/<?php echo $organisation['shortname']; ?>/contacts/update' class='form'>
	<fieldset>
		<label for='member_name'>Name:</label>
		<input type='text' name='member_name' value='<?php if(!empty($editmember['name'])){echo $editmember['name'];} ?>'/>
		<br />
		<label for='member_title'>Title:</label>
		<input type='text' name='member_title' value='<?php if(!empty($editmember['title'])){echo $editmember['title'];} ?>'/>
		<br />
		<label for='member_group'>Group:</label>
		<select name='member_group'>
		<?php
		foreach ($business_card['groups'] as $group) {
		?>
		<option value='<?php echo $group['id'] ?>' <?php if ($group['id']==$editmember['group_id']) echo 'SELECTED'; ?>><?php echo $group['name'] ?></option>
		<?php
		}
		?>
		</select>
		<br />
		<label for='member_course'>Course:</label>
		<input type='text' name='member_course' value='<?php if(!empty($editmember['course'])){echo $editmember['course'];} ?>'/>
		<br />
		<label for='member_email'>Email:</label>
		<input type='text' name='member_email' value='<?php if(!empty($editmember['email'])){echo $editmember['email'];} ?>'/>
		<br />
		<label for='member_about'>About:</label>
		<textarea name='member_about' cols='28' rows='7'><?php if(!empty($editmember['about'])){echo $editmember['about'];} ?></textarea>
		<br />
		<label for='member_address'>Postal Address:</label>
		<textarea name='member_address' cols='25' rows='4'><?php if(!empty($editmember['postal_address'])){echo $editmember['postal_address'];} ?></textarea>
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
		<input name='member_cancelbutton' type='button' id='member_cancelbutton' value='Cancel' class='button' />
		<input name='member_addbutton' type='submit' id='member_addbutton' value='Add/Edit' class='button' />
		</fieldset>
	</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>
</div>