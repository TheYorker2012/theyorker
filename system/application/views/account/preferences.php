	<div class="BlueBox">
		<h2><?php echo($intro_heading); ?></h2>
		<?php echo($intro); ?>
	</div>
	<form id="general" action="/register" method="post">
		<div class="BlueBox">
			<fieldset>
				<label for="fname">First Name: </label>
				<input type="text" id="fname" name="fname" value="<?php echo(htmlentities($this->validation->fname)); ?>" />
				<br />
				<label for="sname">Surname: </label>
				<input type="text" id="sname" name="sname" value="<?php echo(htmlentities($this->validation->sname)); ?>" />
				<br />
				<label for="email">E-mail Address: </label>
				<input type="text" id="email" name="email" value="<?php echo(htmlentities($this->validation->email)); ?>" />
				<br />
				<label for="nick">Nickname: </label>
				<input type="text" id="nick" name="nick" value="<?php echo(htmlentities($this->validation->nick)); ?>" />
				<br />
				<span class="label">Gender: </span>
				<label class="radio" for="genderm">Male</label>
				<input class="radio" type="radio" id="genderm" name="gender" value="m" <?php if ($this->validation->gender == 'm') { echo('checked="checked" '); } ?>/>
				<label class="radio" for="genderf">Female</label>
				<input class="radio" type="radio" id="genderf" name="gender" value="f" <?php if ($this->validation->gender == 'f') { echo 'checked="checked" '; } ?>/>
				<br />
				<label for="college">College: </label>
				<!-- TODO: create fancy picture based college selection -->
				<select name="college" id="college">
<?php
foreach ($colleges as $college) {
	echo('					');
	echo('<option value="' . $college['college_id'] . '"');
	if ($college['college_id'] == $this->validation->college) {
		echo( ' selected="selected"');
	}
	echo('>'.$college['college_name'].'</option>'."\n");
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
						if ($this->validation->time == '12') { echo ' selected="selected"'; } 
					?>>12hr</option>
					<option value="24"<?php 
						if ($this->validation->time == '24') { echo ' selected="selected"'; } 
					?>>24hr</option>
				</select>
				<br />
				<!-- TODO: add current time thing -->
			</fieldset>
		</div>
		<div>
		 	<input type='submit' name='submit' id='submit' value='Next >' class='button' />
		</div>
	</form>
