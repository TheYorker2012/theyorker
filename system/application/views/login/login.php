<div id='login' align='center'>
	<?php echo form_open('login/loginsubmit'); ?>
		<fieldset>
			<label for='username'>Username:</label>
			<?php echo form_input($username); ?>
			<br />
			<label for='password'>Password:</label>
			<?php echo form_input($password); ?>
			<br />
			<label for='keep_login'>Remember me</label>
			<input type='checkbox' name='keep_login' id="keep_login" value="1">
			<br />
			<label for='login_button'></label>
			<?php echo form_submit('submit', 'Submit'); ?>
			<!--<input type='button' onClick="parent.location='/login/register'" name='register_button' value='Register'>-->
		</fieldset>
		<fieldset>
		</fieldset>
		<div style='font-size: small;'>If you have forgotten your password, <a href='/login/resetpassword/'>click here</a> to reset it.</div>
	<?php echo form_close(); ?>
</div>
