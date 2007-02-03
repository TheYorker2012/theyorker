<div class="RightToolbar">
	<h4>Register</h4>
	<div class="Entry">
		To find out more about getting your organisation on theyorker, <a href="/register/">Click here</a>.
	</div>
</div>
<div class="blue_box">
	<h2>log in</h2>
	Good afternoon, John. Welcome to the VIP area. Please select the organisation that you wish to represent, and then reenter your university password.
	<form name='login_form' action='/viparea/main' method='POST' class='form'>
		<fieldset>
			<label for='organisation'>Choose Your Organisation:</label>
			<select name="organisation">
				<option value="theyorker">The Yorker</option>
			</select>
			<br />
			<label for='password'>Reenter Your Password:</label>
			<input type='password' name='password'>
			<br />
			<label for='login_button'></label>
			<input type='submit' class='button' name='login_button' value='Enter'>
		</fieldset>
		<fieldset>
		</fieldset>
	</form>
</div>
