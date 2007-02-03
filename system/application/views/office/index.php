<div class="RightToolbar">
	<h4>Forgotten your Password</h4>
	<div class="Entry">
		If you have forgotten your password, get in contact with your editor, and he will reset it.
	</div>
	<h4>Get Involved</h4>
	<div class="Entry">
		If you would like to get involved in writing for the yorker, click <a href='/office/register/'>here</a>.
	</div>
</div>
<div class='grey_box'>
	<h2>welcome</h2>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div class="blue_box">
	<h2>enter our office</h2>
	Please reenter/enter your [office] password to proceed.
	<form name='login_form' action='/office/' method='POST' class='form'>
		<fieldset>
			<label for='password'>Office Password:</label>
			<input type='password' name='password'>
			<br />
			<label for='login_button'></label>
			<input type='submit' class='button' name='login_button' value='Enter'>
		</fieldset>
	</form>
</div>