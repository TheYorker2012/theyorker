	<?php if (isset($main_heading)) { ?>
	<div class="BlueBox">
		<h2>What's this?</h2>
		This information will allow us to personalise your experience of our site, and will allow you to use our facilities to communicate with any societies that you are a member of.
	</div>
	<?php } ?>
	<form id="general" action="<?php echo($form_action); ?>" method="post">
		<div class="BlueBox">
			<h2>About You</h2>
<?php if ($in_wizard) { ?>
			<p><?php echo($email_prompt); ?></p>
			<fieldset>
				<label for="username">Username: </label>
				<input type="text" id="username" name="username" value="<?php echo(xml_escape($this->validation->username)); ?>" />
				<br />
			</fieldset>
<?php } ?>
			<p>Please enter your name:</p>
			<fieldset>
				<label for="fname">Forename: </label>
				<input type="text" id="fname" name="fname" value="<?php echo(xml_escape($this->validation->fname)); ?>" />
				<br />
				<label for="sname">Surname: </label>
				<input type="text" id="sname" name="sname" value="<?php echo(xml_escape($this->validation->sname)); ?>" />
				<br />
			</fieldset>
			<p>To allow us to personalise your experience of our site, please tell us more about yourself:</p>
			<fieldset>
				<label for="nick">Nickname: </label>
				<input type="text" id="nick" name="nick" value="<?php echo(xml_escape($this->validation->nick)); ?>" />
				<br />
				<span class="label">Gender: </span>
				<label class="radio" for="genderm">Male</label>
				<input class="radio" type="radio" id="genderm" name="gender" value="m" <?php if ($this->validation->gender == 'm') { echo('checked="checked" '); } ?>/>
				<label class="radio" for="genderf">Female</label>
				<input class="radio" type="radio" id="genderf" name="gender" value="f" <?php if ($this->validation->gender == 'f') { echo 'checked="checked" '; } ?>/>
				<label class="radio" for="gendern">Prefer not to say</label>
				<input class="radio" type="radio" id="gendern" name="gender" value="n" <?php if ($this->validation->gender === NULL) { echo 'checked="checked" '; } ?>/>
				<br />
				<label for="college">College: </label>
				<!-- TODO: create fancy picture based college selection -->
				<select name="college" id="college">
<?php
foreach ($colleges as $college) {
	echo('					');
	echo('<option value="'.$college['college_id'].'"');
	if ($college['college_id'] == $this->validation->college) {
		echo( ' selected="selected"');
	}
	echo('>'.xml_escape($college['college_name']).'</option>'."\n");
}
?>
				</select>
				<br />
				<label for="year">Year of Enrollment: </label>
				<select name="year" id="year">
<?php
foreach ($years as $year) {
	echo('				');
	echo('<option');
	echo(' value="'.$year['year_id'].'"');
	if ($this->validation->year == $year['year_id'])
		echo(' selected="selected"');
	echo('>'.$year['year_id'].'</option>'."\n");
}
?>
				</select>
				<br />
				<label for="time">Time Format:</label>
				<select name="time" id="time">
					<option value="12"<?php
						if ($this->validation->time == '12') { echo(' selected="selected"'); }
					?>>12hr</option>
					<option value="24"<?php
						if ($this->validation->time == '24') { echo(' selected="selected"'); }
					?>>24hr</option>
				</select>
				<br />
				<!-- TODO: add current time thing -->
			</fieldset>
			<div style="display: none;">
			<p>If you would like to see how many unread e-mails you have in your inbox, tick this box.</p>
			<fieldset>
				<label class="radio" for="storepassword">Save YorkMail Password:</label>
				<input class="checkbox" type="checkbox" id="storepassword" name="storepassword" value="1" <?php if ($this->validation->storepassword) { echo('checked="checked" '); } ?>/>
				<br />
			</fieldset>
			<p>If you would like to see your facebook events on your calendar, tick the box below, and facebook will soon prompt you to log in.</p>
			<fieldset>
				<label class="radio" for="facebook">Facebook Integration:</label>
				<input class="checkbox" type="checkbox" id="facebook" name="facebook" value="1" <?php if ($this->validation->facebook) { echo('checked="checked" '); } ?>/>
				<br />
			</fieldset>
			</div>
		<?php if(!$in_wizard) { ?>
		 	<input type='submit' name='submit' id='submit' value='Save' class='button' />
		<?php } ?>
			</div>
		<?php if($in_wizard) { ?>
		<div>
		 	<input type='submit' name='submit' id='submit' value='Next >' class='button' />
		</div>
		<?php } ?>
	</form>