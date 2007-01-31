
	<?php echo $this->validation->error_string; ?>
	<form name='general' id='general' action='/register/general' method='post' class='form'>
	<fieldset>
		<label for='name'>Name:</label>
		<input type='text' id='name' name='name' value='<?php echo $this->validation->name; ?>' />
		<br />
		<label for='email'>E-Mail Address:</label>
		<input type='text' id='email' name='email' value='<?php echo $this->validation->email; ?>' />
		<br />
		<label for='nick'>Nickname:</label>
		<input type='text' id='nick' name='nick' value='<?php echo $this->validation->nick; ?>' style='margin-bottom: 5px;' />
		<br />
		<label for='college'>College:</label>

<script type='text/javascript'>
function collSelect (college) {
	<?php foreach ($colleges as $college) { ?>
	document.getElementById('coll_<?php echo $college['college_id']; ?>').className = 'unselected';
	<?php } ?>
	document.getElementById('coll_' + college).className = 'selected';
	document.getElementById('college').selectedIndex = college;
}
</script>
<style>
.error {
	border: 1px #D893A1 solid;
	background-color: #FBE6F2;
	margin-bottom: 3px;
	padding: 2px;
}

.unselected {
	border: 1px #fff solid;
	background-color: #fff;
	padding: 3px;
	width: 65px;
	float: left;
	text-align: center;
	cursor: pointer;
}

.hide {
	display: none;
}

.show {
	display: block;
}

.selected {
	border: 1px #000000 dashed;
	background-color: #20c1f0;
	padding: 3px;
	width: 65px;
	float: left;
	text-align: center;
	cursor: pointer;
}

#college_select {
	float: left;
	margin-left: 10px;
}
</style>
		<div id='college_select' class='hide'>
		<?php $college_count = ceil(count($colleges) / 2) - 1;
		if (!$this->validation->college) {
			$this->validation->college = 0;
		}
		$college_index = 0;
		foreach ($colleges as $college) {
			echo '<div id=\'coll_' . $college['college_id'] . '\' class=\'';
			if ($this->validation->college != $college['college_id']) {
				echo 'un';
			}
			echo 'selected\'>';
			?><img src='/images/prototype/prefs/college_<?php echo strtolower($college['college_name']); ?>.jpg' alt='<?php echo $college['college_name']; ?>' title='<?php echo $college['college_name']; ?>' onClick="collSelect('<?php echo $college['college_id']; ?>')" /></div>
			<?php
			$college_index++;
			if ($college_index > $college_count) {
				echo '<br />';
				$college_index = 0;
			}
		} ?>
		</div>
		<select name='college' id='college' size='1' onChange='collSelect(this.selectedIndex)'>
		<?php foreach ($colleges as $college) { ?>
			<option value='<?php echo $college['college_id']; ?>'<?php echo $this->validation->set_select('college', $college['college_id']); ?>><?php echo $college['college_name']; ?></option>
		<?php } ?>
		</select>
		<br />
		<label for='year'>Year of Enrollment:</label>
		<select name='year' id='year' size='1'>
			<?php foreach ($years as $year) { ?>
			<option value='<?php echo $year['year_id']; ?>'<?php echo $this->validation->set_select('year', $year['year_id']); ?>><?php echo $year['year_id']; ?></option>
			<?php } ?>
		</select>
		<br />
<script type="text/javascript">
<!--
document.getElementById('college').className = 'hide';
document.getElementById('college_select').className = 'show';

function updateTime () {
	var currentTime = new Date();
	var hours = currentTime.getHours();
	var minutes = currentTime.getMinutes();
	var seconds = currentTime.getSeconds();
	var temp = "";
	if (minutes < 10) { minutes = "0" + minutes; }
	if (seconds < 10) { seconds = "0" + seconds; }
	if (document.getElementById('time').selectedIndex == 1) {
		if (hours < 10) { temp = "0"; }
		temp = temp + hours + ":" + minutes + ":" + seconds;
		document.getElementById('current_time').innerHTML = temp;
	} else {
		if (hours > 12) { hours = hours - 12; }
		if (hours < 10) {
			temp = "0";
		}
		temp = temp + hours + ":" + minutes + ":" + seconds;
		if (hours > 11){
			temp = temp + " PM";
		} else {
			temp = temp + " AM";
		}
		document.getElementById('current_time').innerHTML = temp;
	}
	setTimeout('updateTime()',1000);
}
setTimeout('updateTime()',0);
//-->
</script>
		<label for='time'>Time Format:</label>
		<select name='time' id='time' size='1' onChange='updateTime()'>
			<option value='12'<?php echo $this->validation->set_select('time', '12'); ?>>12hr</option>
			<option value='24'<?php echo $this->validation->set_select('time', '24'); ?>>24hr</option>
		</select>
		<br />
		<label for='current_time'>Current Time:</label>
		<div id='current_time' style='float: left; margin: 5px 0 0 10px; font-size: small;'>!!! Javascript Disabled !!!</div>
		<br />
		</fieldset>
		<fieldset>
			<label for='submit'></label>
		 	<input type='submit' name='submit' id='submit' value='Next >' class='button' />
		        <br />
		</fieldset>
		</form>