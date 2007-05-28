<div class="BlueBox">
	<?php echo($intro); ?>
	<form id="reset_password_form" action="<?php echo($this->uri->uri_string()); ?>" method="post">
		<fieldset>
			<label for="username">Username: </label>
				<input id="username" type="text" name="username" /><br />
			<input class="button" type="submit" name="reset_button" value="<?php echo($submit); ?>" />
		</fieldset>
	</form>
</div>
