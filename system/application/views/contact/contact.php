<div id="RightColumn">
	<h2 class="first">Need Help?</h2>
	<div class="Entry">
		Don't know who to talk to? Below is a short decsription of who to talk to.
	</div>
	<?php
		foreach($contacts as $contact){
			echo('<h2>'.$contact['name'].'</h2>'."\n");
			echo('<div class="Entry">'."\n");
			echo('	<p>'.$contact['description'].'</p>'."\n");
			echo('</div>'."\n");
		}
	?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>Contact Us</h2>
		<form class="form" action="/contact/sendmail" method="post">
			<fieldset>
				<label for="recipient"> Contact: </label>
				<select id="recipient" name="recipient">
				<?php
					//Note plural becomes singular
					foreach ($contacts as $contact){
						echo('<option value="'.$contact['email'].'">'.$contact['name'].'</option>'."\n");
					}
				?>
				</select>
				<label for="contact_email" size="30">Your Email: </label>
				<input id="contact_email" name="contact_email" size="30" />
				<label for="contact_subject" size="30">Subject: </label>
				<input id="contact_subject" name="contact_subject" size="30" />
				<textarea name="contact_message" cols="40" rows="14"></textarea>
			</fieldset>
			<fieldset>
				<input type="submit" class="button" value="Send" id="contact_send" />
			</fieldset>
		</form>
	</div>
</div>
