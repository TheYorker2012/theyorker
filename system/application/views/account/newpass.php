<div class="BlueBox">
	<form id="newpass_form" action="<?php echo($this->uri->uri_string()); ?>" method="post">
		<fieldset>
			<label for="newpassword">New password:</label>
				<input id="newpassword" name="newpassword" type="password" />
			<label for="confirmpassword">Confirm new password:</label>
				<input id="confirmnewpassword" name="confirmnewpassword" type="password" />
		</fieldset>
		<fieldset>
			<input class="button" type="submit" value="Next >" />
		</fieldset>
	</form>
</div>
