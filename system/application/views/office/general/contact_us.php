<div>
	<div class="blue_box">
		<h2>Add a New Contact</h2>
		<form action="/office/contactus/addcontact" method="post">
			<fieldset>
				<label for="name">Contact Name:	</label>
				<input id="name" name="name" />
				<br />
				<label for="email">Contact's Email: </label>
				<input id="email" name="email" />
				<br />
				<label for="description">Description of Contact (Be nice!)</label>
				<textarea id="description" name="description" cols="20" rows="5">
				</textarea>
			</fieldset>
			<fieldset>
				<input type="submit" class="button" value="Add Contact" id="contact_add" />
			</fieldset>
		</form>
	</div>
</div>
