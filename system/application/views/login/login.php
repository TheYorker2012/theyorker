<div id="RightColumn">
<?php
	$first = true;
	foreach ($rightbar as $entry) {
		if ($first) {
			echo('	<h2 class="first">'.$entry['title'].'</h2>'."\n");
			$first = false;
		} else {
			echo('	<h2>'.$entry['title'].'</h2>'."\n");
		}
		echo('	<div class="Entry">'."\n");
		echo('		'.$entry['text']);
		echo('	</div>'."\n");
	}
?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo $title; ?></h2>
<?php
if (isset($failure) && $failure) {
	echo $failure_text;
} else {
	if (isset($login_message)) {
		echo('		'.$login_message."\n");
	}
?>
		<form id="login_form" action="<?php echo $target; ?>" method="post" class="form"><fieldset>
<?php 
	if (isset($login_id)) {
?>
			<input type="hidden" name="login_id" value="<?php echo $login_id; ?>" />
<?php
	} 
	if (isset($usernames)) {
?>
		<label for="username">Select Organisation:</label>
			<select name="username">
			<?php foreach ($usernames as $id => $name) { ?>
				<option value="<?php echo $id; ?>"<?php if (isset($default_username) && $id==$default_username) { echo ' selected="selected"'; } ?>><?php echo $name; ?></option>
			<?php } ?>
			</select>
			<br />
<?php
	} elseif (isset($username)) { 
?>
			<label for="username">Username: </label>
			<input name="username" id="username" value="<?php echo $username; ?>" /><br />
<?php
	}
?>
			<label for="password">Password: </label>
			<input type="password" id="password" name="password" /><br />
<?php
	if (isset($keep_login)) {
?>
			<label for="keep_login">Remember me: </label>
			<input type="checkbox" name="keep_login" id="keep_login" <?php if ($keep_login) echo 'checked="checked" '; ?>/><br />
<?php
	}
	if (isset($previous_post_data)) {
?>
			<input type="hidden" name="previous_post_data" id="previous_post_data" value="<? echo htmlspecialchars($previous_post_data); ?>" />
<?php
	}
?>
			<input type="submit" class="button" name="login_button" value="Login" />
		</fieldset></form>
		<script type="text/javascript">
		var element = document.getElementById('password');
		if (element) {
			element.focus();
		}
		element = document.getElementById('username');
		if (element) {
			element.focus();
		}
		</script>
<?php
}
?>
	</div>
</div>
