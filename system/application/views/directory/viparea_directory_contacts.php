<div class='RightToolbar'>
	<h4>Photo</h4>
	<div class="Entry">
		The photo associated with this contact, together with an [Add/Replace Photo] button, should go here.
	</div>
</div>

<div class='blue_box'>

<h2>Edit <?php if(!empty($business_card['name'])){echo $business_card['name'];} ?></h2>

<form name='edit_card' method='post' action='<?php echo vip_url('members/cards/'.$business_card['id'].'/edit'); ?>' class='form'>
	<fieldset>
		<label for='card_name'>Name:</label>
		<input type='text' name='card_name' value='<?php if(!empty($card_form['card_name'])){echo $card_form['card_name'];} ?>'/>
		<br />
		<label for='card_title'>Title:</label>
		<input type='text' name='card_title' value='<?php if(!empty($card_form['card_title'])){echo $card_form['card_title'];} ?>'/>
		<br />
		<label for='group_id'>Group:</label>
		<select name='group_id'>
		<?php
		foreach ($groups as $group) {
			?>
			<option value='<?php echo $group['id'] ?>'
			<?php if(!empty($card_form['group_id']))
					{
						if ($group['id']==$card_form['group_id'])
						{echo 'selected';}
					}
				?>
				>
				<?php echo $group['name'] ?></option>
			<?php
		}
		?>
		</select>
		<br />
		<label for='card_username'>Yorker Username:</label>
		<input type='text' name='card_username' value='<?php if(!empty($card_form['card_username'])){echo $card_form['card_username'];} ?>'/>
		<br />
		<label for='card_course'>Course:</label>
		<input type='text' name='card_course' value='<?php if(!empty($card_form['card_course'])){echo $card_form['card_course'];} ?>'/>
		<br />
		<label for='email'>Email:</label>
		<input type='text' name='email' value='<?php if(!empty($card_form['email'])){echo $card_form['email'];} ?>'/>
		<br />
		<label for='card_about'>About:</label>
		<textarea name='card_about' cols='28' rows='7'><?php if(!empty($card_form['card_about'])){echo $card_form['card_about'];} ?></textarea>
		<br />
		<label for='postal_address'>Postal Address:</label>
		<textarea name='postal_address' cols='25' rows='4'><?php if(!empty($card_form['postal_address'])){echo $card_form['postal_address'];} ?></textarea>
		<br />
		<label for='phone_mobile'>Phone Mobile:</label>
		<input type='text' name='phone_mobile' value='<?php if(!empty($card_form['phone_mobile'])){echo $card_form['phone_mobile'];} ?>'/>
		<br />
		<label for='phone_internal'>Phone Internal:</label>
		<input type='text' name='phone_internal' value='<?php if(!empty($card_form['phone_internal'])){echo $card_form['phone_internal'];} ?>'/>
		<br />
		<label for='phone_external'>Phone External:</label>
		<input type='text' name='phone_external' value='<?php if(!empty($card_form['phone_external'])){echo $card_form['phone_external'];} ?>'/>
		<br />
		<label for='card_editbutton'></label>
		<input name='card_revertbutton' type='button' onClick="parent.location='<?php echo vip_url('members/cards/'.$business_card['id'].'/edit'); ?>'"value='Undo Changes' class='button' />
		<input name='card_editbutton' type='submit' id='card_editbutton' value='Update' class='button' />
		</fieldset>
	</form>
</div>
<a href='<?php echo vip_url('directory/contacts'); ?>'>Back to the Viparea contacts editor.</a>
</div>