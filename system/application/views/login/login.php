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
	<h2><?php echo $title; ?></h2>
	<?php
		if (isset($login_message)) {
			echo '<P>'.$login_message.'</P>';
		}
	?>
	<form name='login_form' action='<?php echo $target; ?>' method='POST' class='form'>
		<fieldset>
			<?php if (isset($login_id)) { ?>
				<input type='hidden' name='login_id' value='<?php echo $login_id; ?>'>
			<?php } if (isset($usernames)) { ?>
				<label for='username'>Select organisation:</label>
				<SELECT name="username">
				<?php foreach ($usernames as $id => $name) { ?>
					<OPTION VALUE="<?php echo $id; ?>"><?php echo $name; ?></OPTION>
				<?php } ?>
				</SELECT>
				<br />
			<?php } elseif (isset($username)) { ?>
				<label for='username'>Username:</label>
				<input name='username' value='<?php echo $username; ?>'>
			<br />
			<?php } ?>
			<label for='password'>Password:</label>
			<input type='password' name='password'>
			<br />
			<?php if (isset($keep_login)) { ?>
				<label for='keep_login'>Remember me</label>
				<input type='checkbox' name='keep_login' id="keep_login" value="<?php echo $keep_login; ?>">
				<br />
			<?php } ?>
			<label for='login_button'></label>
			<input type='submit' class='button' name='login_button' value='Login'>
		</fieldset>
		<fieldset>
		</fieldset>
	</form>
</div>
