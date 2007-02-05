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
