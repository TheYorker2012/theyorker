<div id='login' align='center'>
	<form id='login_form' name='login_form' action='/login/' method='POST' class='form'>
		<h5>Error : This is a login error example.</h5>
		<fieldset>
			<label for='username'>Username:</label>
			<input id='username' type='text' name='username' value='<?php echo $previous_username; ?>'>
			<br />
			<label for='password'>Password:</label>
			<input id='password' type='password' name='password'>
			<br />
			<label for='keep_login'>Remember me</label>
			<input type='checkbox' name='keep_login' id="keep_login" value="1">
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