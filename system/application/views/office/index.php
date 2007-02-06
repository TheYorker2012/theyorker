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

<a href="/office/faq/add">Add faq</a><br />
<a href="/office/howdoi/add">Add to how do I</a><br />
<a href="/office/faq/edit">Edit FAQ entry</a><br />
<br />
<a href="/office/news/request">Make new article request</a><br />
<a href="/office/news/article">View/Edit Article</a><br />
<br />
<a href="/admin/pages">Page properties, custom pages, etc.</a><br />