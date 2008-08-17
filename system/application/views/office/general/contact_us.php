<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>Add a New Contact</h2>
		<form action="/office/contactus/addcontact" method="post">
			<fieldset>
				<label for="name">Contact Name:	</label>
				<input id="name" name="name" />
				<br />
				<label for="email">Contact's Email: </label>
				<input id="email" name="email" />
				<br />
				<label for="description">Description of Contact</label>
				<textarea id="description" name="description" cols="20" rows="5">
				</textarea>
			</fieldset>
			<fieldset>
				<input type="submit" class="button" value="Add Contact" id="contact_add" />
			</fieldset>
		</form>
	</div>
	<a href="/admin/">Back to the admin homepage.</a>
</div>