<div class="RightToolbar">
	<?php
	foreach ($rightbar as $entry) {
		echo '<H4>'.$entry['title'].'</H4>';
		echo '<DIV class="Entry">'.$entry['text'].'</DIV>';
	}
	?>
</div>
<div class="blue_box">
	<h2><?php echo $title; ?></h2>
	<?php
		if (isset($login_message)) {
			echo '<P>'.$login_message.'</P>';
		}
	?>
	<form name='login_form' action='<?php echo $target; ?>' method='post' class='form'>
		<fieldset>
			<?php if (isset($login_id)) { ?>
				<input type='hidden' name='login_id' value='<?php echo $login_id; ?>' />
			<?php } if (isset($usernames)) { ?>
				<label for='username'>Select organisation:</label>
				<select name="username">
				<?php foreach ($usernames as $id => $name) { ?>
					<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
				<?php } ?>
				</select>
				<br />
			<?php } elseif (isset($username)) { ?>
				<label for='username'>Username:</label>
				<input name='username' id='username' value='<?php echo $username; ?>' />
			<br />
			<?php } ?>
			<label for='password'>Password:</label>
			<input type='password' id='password' name='password' />
			<br />
			<?php if (isset($keep_login)) { ?>
				<label for='keep_login'>Remember me</label>
				<input type='checkbox' name='keep_login' id='keep_login' value="<?php echo $keep_login; ?>" />
				<br />
			<?php }  if (isset($previous_post_data)) { ?>
				<input type='hidden' name='previous_post_data' id='previous_post_data' value='<? echo htmlentities($previous_post_data, ENT_QUOTES); ?>' />
			<? } ?>
			<input type='submit' class='button' name='login_button' value='Login' />
		</fieldset>
	</form>
	<script type='text/javascript'>
	var element = document.getElementById('password');
	if (element) {
		element.focus();
	}
	element = document.getElementById('username');
	if (element) {
		element.focus();
	}
	</script>
</div>
