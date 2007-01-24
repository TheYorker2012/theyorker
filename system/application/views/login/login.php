<div align='center'>
	<form name='login_form' action='/login/' method='POST' class='form'>
		<fieldset>
			<label for='login_username'>Username:</label>
			<input type='text' name='login_username' value='<?php echo $login_username; ?>'>
			<br />
			<label for='login_password'>Password:</label>
			<input type='password' name='login_password'>
			<br />
			<label for='keep_login'>Remember me</label>
			<input type='checkbox' name='keep_login' value="1" <?php if(!empty($keep_login)){echo "checked";} ?>>
			<br />
			<label for='login_button'></label>
			<input type='submit' name='login_button' value='Login'>
			<input type='button' onClick="parent.location='/login/register'" name='register_button' value='Register'>
		</fieldset>
		<fieldset>
		</fieldset>
		<div style='font-size: small;'>If you have forgotten your password, <a href='/login/resetpassword/'>click here</a> to reset it.</div>
	</form>
</div>