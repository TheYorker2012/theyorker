<div class="RightToolbar">
	<h4>Forgotten your Password</h4>
	<div class="Entry">
		If you have forgotten your password <a href="/login/resetpassword/">click here</a> to reset it.
	</div>
	<h4>Register</h4>
	<div class="Entry">
		If you have not registered with us yet then you probably can't login.<br /><a href="/register/">Click here</a> to register with us and get exclusive access to the best information.
	</div>
</div>
<div class="blue_box">
	<h2>log in</h2>
	<form name='login_form' action='/login' method='POST' class='form'>
		<fieldset>
			<label for='username'>Username:</label>
			<input name='username' value='<?php echo $initial_username; ?>'>
			<br />
			<label for='password'>Password:</label>
			<input type='password' name='password'>
			<br />
			<label for='keep_login'>Remember me</label>
			<input type='checkbox' name='keep_login' id="keep_login" value="1">
			<br />
			<label for='login_button'></label>
			<input type='submit' class='button' name='login_button' value='Login'>
		</fieldset>
		<fieldset>
		</fieldset>
	</form>
</div>
