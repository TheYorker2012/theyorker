<div class='RightToolbar'>
	<h4>Photo</h4>
	<div class="Entry">
		The photo associated with this contact, together with an [Add/Replace Photo] button, should go here.
	</div>
</div>

<div class='blue_box'>

<h2>Edit <?php if(!empty($business_card['name'])){echo $business_card['name'];} ?></h2>

<form name='member' method='post' action='/viparea/directory/contacts/' class='form'>
	<fieldset>
		<label for='member_name'>Name:</label>
		<input type='text' name='member_name' value='<?php if(!empty($business_card['name'])){echo $business_card['name'];} ?>'/>
		<br />
		<label for='member_title'>Title:</label>
		<input type='text' name='member_title' value='<?php if(!empty($business_card['title'])){echo $business_card['title'];} ?>'/>
		<br />
		<label for='member_group'>Group:</label>
		<select name='member_group'>
		<?php
		foreach ($business_card_groups as $group) {
			?>
			<option value='<?php echo $group['id'] ?>' <?php if ($group['id']==$business_card['group_id']) echo 'SELECTED'; ?>><?php echo $group['name'] ?></option>
			<?php
		}
		?>
		</select>
		<br />
		<label for='member_course'>Course:</label>
		<input type='text' name='member_course' value='<?php if(!empty($business_card['course'])){echo $business_card['course'];} ?>'/>
		<br />
		<label for='member_email'>Email:</label>
		<input type='text' name='member_email' value='<?php if(!empty($business_card['email'])){echo $business_card['email'];} ?>'/>
		<br />
		<label for='member_about'>About:</label>
		<textarea name='member_about' cols='28' rows='7'><?php if(!empty($business_card['about'])){echo $business_card['about'];} ?></textarea>
		<br />
		<label for='member_address'>Postal Address:</label>
		<textarea name='member_address' cols='25' rows='4'><?php if(!empty($business_card['postal_address'])){echo $business_card['postal_address'];} ?></textarea>
		<br />
		<label for='member_phone_mobile'>Phone Mobile:</label>
		<input type='text' name='member_phone_mobile' value='<?php if(!empty($business_card['mobile_phone'])){echo $business_card['mobile_phone'];} ?>'/>
		<br />
		<label for='member_phone_internal'>Phone Internal:</label>
		<input type='text' name='member_phone_internal' value='<?php if(!empty($business_card['phone_internal'])){echo $business_card['phone_internal'];} ?>'/>
		<br />
		<label for='member_phone_external'>Phone External:</label>
		<input type='text' name='member_phone_external' value='<?php if(!empty($business_card['phone_external'])){echo $business_card['phone_external'];} ?>'/>
		<br />
		<label for='member_addbutton'></label>
		<input name='member_cancelbutton' type='button' id='member_cancelbutton' value='Cancel' class='button' />
		<input name='member_addbutton' type='submit' id='member_addbutton' value='Add/Edit' class='button' />
		</fieldset>
	</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>
</div>