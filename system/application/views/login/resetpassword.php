<div class="BlueBox">
	<p>If you have forgoten your yorker password it is easy to get it reset. Enter your yorker username or university email address to get your new password emailed to you. If you reset the password for a group account you may lock others in the group out.  </p>
	<form id="reset_password_form" action="/login/" method="post">
		<fieldset>
			<label for="username">Username: </label>
				<input id="username" type="text" name="username" /><br />
			<label for="email">Email: </label>
				<input id="email" type="text" name="email" /><br />
			<br />
			<input class="button" type="submit" name="reset_button" value="Reset" />
		</fieldset>
	</form>
</div>
