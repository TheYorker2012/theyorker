	<script type='text/javascript' src='/javascript/current_time.js'></script>
	<script type='text/javascript'>
	function collSelect (college, index_no) {
		<?php foreach ($colleges as $college) { ?>
		document.getElementById('coll_<?php echo $college['college_id']; ?>').className = 'unselected';
		<?php } ?>
		document.getElementById('coll_' + college).className = 'selected';
		document.getElementById('college').selectedIndex = index_no;
	}
	</script>

	<div class='grey_box' style='width: auto;'>
		<h2><?php echo $intro_heading; ?></h2>
		<?php echo $intro; ?>
	</div>
	<form name='general' id='general' action='/register' method='post' class='form'>
		<div class='blue_box' style='width: auto;'>
			<fieldset>
				<label for='name'>First Name:</label>
				<input type='text' id='fname' name='fname' value='<?php echo $this->validation->fname; ?>' />
				<br />
				<label for='name'>Surname:</label>
				<input type='text' id='sname' name='sname' value='<?php echo $this->validation->sname; ?>' />
				<br />
				<label for='email'>E-Mail Address:</label>
				<input type='text' id='email' name='email' value='<?php echo $this->validation->email; ?>' />
				<br />
				<label for='nick'>Nickname:</label>
				<input type='text' id='nick' name='nick' value='<?php echo $this->validation->nick; ?>' style='margin-bottom: 5px;' />
				<br />
				<label for='gender'>Gender:</label>
				<input type='radio' id='genderm' name='gender' value='m' <?php if ($this->validation->gender == 'm') { echo 'checked=\'checked\' '; } ?>/> Male
				<input type='radio' id='genderf' name='gender' value='f' <?php if ($this->validation->gender == 'f') { echo 'checked=\'checked\' '; } ?>/> Female
				<br />
				<label for='college'>College:</label>
				<div id='college_select' class='hide'>
					<?php $college_count = ceil(count($colleges) / 2) - 1;

					if (!$this->validation->college) {
						$this->validation->college = 0;
					}
					$college_index = 0;
					$college_counter = 0;
					foreach ($colleges as $college) {
						echo '<div id=\'coll_' . $college['college_id'] . '\' class=\'';
						if ($this->validation->college != $college['college_id']) {
							echo 'un';
						}
						echo 'selected\'>';
						?><img src='/images/prototype/prefs/college_<?php echo strtolower($college['college_name']); ?>.jpg' alt='<?php echo $college['college_name']; ?>' title='<?php echo $college['college_name']; ?>' onClick="collSelect('<?php echo $college['college_id']; ?>','<?php echo $college_counter; ?>')" /></div>
						<?php
						$college_index++;
						$college_counter++;
						if ($college_index > $college_count) {
							echo '<br />';
							$college_index = 0;
						}
					} ?>
				</div>
				<select name='college' id='college' size='1' onChange='collSelect(this.selectedIndex)'>
				<?php foreach ($colleges as $college) {
					echo '<option value=\'' . $college['college_id'] . '\'';
					if ($college['college_id'] == $this->validation->college) {
						echo ' selected=\'selected\'';
					}
					echo '>' . $college['college_name'] . '</option>';
				} ?>
				</select>
				<br />
				<label for='year'>Year of Enrollment:</label>
				<select name='year' id='year' size='1'>
					<?php foreach ($years as $year) { ?>
					<option value='<?php echo $year['year_id']; ?>'<?php if ($this->validation->year == $year['year_id']) { echo ' selected=\'selected\''; } ?>><?php echo $year['year_id']; ?></option>
					<?php } ?>
				</select>
				<br />
				<script type="text/javascript">
				<!--
				document.getElementById('college').className = 'hide';
				document.getElementById('college_select').className = 'show';
				//-->
				</script>
				<label for='time'>Time Format:</label>
				<select name='time' id='time' size='1'>
					<option value='12'<?php if ($this->validation->time == '12') { echo ' selected=\'selected\''; } ?>>12hr</option>
					<option value='24'<?php if ($this->validation->time == '24') { echo ' selected=\'selected\''; } ?>>24hr</option>
				</select>
				<br />
				<label for='current_time'>Current Time:</label>
				<div id='current_time' style='float: left; margin: 5px 0 0 10px; font-size: small;'>!!! Javascript Disabled !!!</div>
				<br />
			</fieldset>
		</div>
		<div>
		 	<input type='submit' name='submit' id='submit' value='Next >' class='button' />
		</div>
	</form>