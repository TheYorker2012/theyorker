<?php

//$session_var defined in controller, but for some reason won't let it be accessed in these functions so i pass it through $sessionvar

// prints the given list of a certain name filling in default if no data is in the session
function PrintRadioList($input_name, $items, $default, $sessionvar)
{
	foreach ($items as $key => $item)
	{
		echo '<label for="'.$input_name.'_'.$key.'">'.$item.'</label>';
		echo '<input type="radio" name="'.$input_name.'" id="'.$input_name.'_'.$key.'" value="'.$item.'" ';
		if (isset($_SESSION[$sessionvar][$input_name]) && 
			$_SESSION[$sessionvar][$input_name] == $item) 
			echo 'checked="checked" ';
		if (isset($_SESSION[$sessionvar][$input_name]) == false &&
			$item == $default)
		{
			echo 'checked="checked" ';
		}
		echo '/><br />';
	}
}

function PrintTextBox ($input_name, $item, $sessionvar)
{
	echo '<label for="'.$input_name.'">'.$item.'</label>';
	echo '<input type="text" name="'.$input_name.'" id="'.$input_name.'" style="width: 220px;" ';
	if (isset($_SESSION[$sessionvar][$input_name]))
		echo 'value="'.$_SESSION[$sessionvar][$input_name].'" ';
	echo '/>';	
}

function PrintTextArea ($input_name, $item, $sessionvar)
{
	echo '<label for="'.$input_name.'">'.$item.'</label>';
	echo '<textarea name="'.$input_name.'" id="'.$input_name.'" cols="25" rows="5">';
	if (isset($_SESSION[$sessionvar][$input_name]))
		echo $_SESSION[$sessionvar][$input_name];
	echo '</textarea>';
}

function addstrong($text)
{
	return '<strong>'.$text.'</strong>';
}

function addstrike($text)
{
	return '<strike>'.$text.'</strike>';
}	

?>

<div id="RightColumn">
	<h2 class="first">Pages</h2>
	<div class="Entry">
		<ol>
		<?php

		$headings = $stage_list['headings'];

		foreach ($headings as $key => &$heading)
		{
			if ($stage == $key)
				$heading = addstrong($heading);
			if ($is_connected == 'No' && in_array($key, $stage_list['skip']))
				$heading = addstrike($heading);
			echo '<li>'.$heading.'</li>';
		}
		
		?>
		</ol>
	</div>
	
	<h2>What's this?</h2>
	<div class="Entry">
		Blah blah blah blah blah.
        </div>
</div>

<div id="MainColumn">
<?php
	if ($stage == 1)
	{
?>
<div class="BlueBox">
<h2>start suggesting</h2>
	<form id="orgdetails" action="/wizard/organisation" method="post" class="form">
		<fieldset>
			<input type="hidden" name="r_stage" value="<?php echo $stage; ?>" />
			<input type="hidden" name="r_dump" value="<?php echo htmlentities(serialize($_SESSION[$session_var]), ENT_QUOTES); ?>" />
			<h3>Type of directory entry</h3>	
			<?php PrintRadioList('a_type', array('Society', 'Bar', 'Restaurant', 'Other'), 'Society', $session_var); ?>
		</fieldset>
		<fieldset>
			<h3>Are you connected to this organisation?</h3>
			<?php PrintRadioList('a_connected', array('Yes', 'No'), 'No', $session_var); ?>
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_finish" value="Finish" class="button" disabled="disabled" />
			<input type="submit" name="r_submit_next" value="Next" class="button" />
			<input type="submit" name="r_submit_back" value="Back" class="button" disabled="disabled" />
		</fieldset>
	</form>
</div>
<?php
	}
?>
<?php
	if ($stage == 2)
	{
?>
<div class="BlueBox">
<h2>basic details</h2>
	<form id="orgdetails" action="/wizard/organisation" method="post" class="form">
		<fieldset>
			<input type="hidden" name="r_stage" value="<?php echo $stage; ?>" />
			<input type="hidden" name="r_dump" value="<?php echo htmlentities(serialize($_SESSION[$session_var]), ENT_QUOTES); ?>" />
			<?php PrintTextBox('a_name', 'Name: ', $session_var); ?>
			<?php PrintTextArea('a_description', 'Description: ', $session_var); ?>
			<?php PrintTextBox('a_email_address', 'Email Address: ', $session_var); ?>
			<?php PrintTextBox('a_website', 'Website: ', $session_var); ?>
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_finish" value="Finish" class="button" />
			<input type="submit" name="r_submit_next" value="Next" class="button" />
			<input type="submit" name="r_submit_back" value="Back" class="button" />
		</fieldset>
	</form>
</div>
<?php
	}
?>

<?php
	if ($stage == 3)
	{
?>
<div class="BlueBox">
<h2>more details</h2>
	<form id="orgdetails" action="/wizard/organisation" method="post" class="form">
		<fieldset>
			<input type="hidden" name="r_stage" value="<?php echo $stage; ?>" />
			<input type="hidden" name="r_dump" value="<?php echo htmlentities(serialize($_SESSION[$session_var]), ENT_QUOTES); ?>" />
			<?php PrintTextBox('a_location', 'Campus Location: ', $session_var); ?>
			<?php PrintTextArea('a_address', 'Address: ', $session_var); ?>
			<?php PrintTextBox('a_postcode', 'Opening Times: ', $session_var); ?>
			<?php PrintTextBox('a_opening_times', 'Campus Location: ', $session_var); ?>
			<?php PrintTextBox('a_phone_internal', 'Internal Phone: ', $session_var); ?>
			<?php PrintTextBox('a_phone_external', 'External Phone: ', $session_var); ?>
			<?php PrintTextBox('a_fax', 'Fax Number: ', $session_var); ?>
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_finish" value="Finish" class="button" />
			<input type="submit" name="r_submit_next" value="Next" class="button" />
			<input type="submit" name="r_submit_back" value="Back" class="button" />
		</fieldset>
	</form>
</div>
<?php
	}
?>

<?php
	if ($stage == 4)
	{
?>
<div class="BlueBox">
<h2>photo upload </h2>
	<form action="/viparea/theyorker/directory/photos/upload" method="post" class="form" enctype="multipart/form-data">
		Photo's should be in jpg format. The upload size limit is 2mb(?).<br />
		<fieldset>
			<label for="title1">Photo Title:</label><input type="text" name="title1" id="title1" size="30" />
			<label for="userfile1">Photo File:</label><input type="file" name="userfile1" id="userfile1" size="20" />
			<input type="hidden" name="destination" id="destination" value="1" />
		</fieldset>
		<fieldset>
			<input type="button" onClick="AddClones()" value="Another"/>
			<input type="submit" value="upload" />
		</fieldset>
	</form>
</div>
<div class="BlueBox">
	<form id="orgdetails" action="/wizard/organisation" method="post" class="form">
		<fieldset>
			<input type="hidden" name="r_stage" value="<?php echo $stage; ?>" />
			<input type="hidden" name="r_dump" value="<?php echo htmlentities(serialize($_SESSION[$session_var]), ENT_QUOTES); ?>" />
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_finish" value="Finish" class="button" />
			<input type="submit" name="r_submit_next" value="Next" class="button" />
			<input type="submit" name="r_submit_back" value="Back" class="button" />
		</fieldset>
	</form>
</div>
<div class="blue_box">
	<img src="./images/100.jpg" alt="Array image Tux" />
	<br />
	<a href="/viparea/theyorker/directory/photos/move/100/up" title="move up">move up</a> | 
	<a href="/viparea/theyorker/directory/photos/move/100/down" title="move down">move down</a> | 
	<a href="/viparea/theyorker/directory/photos/delete/100" title="delete">delete</a> 
	<br />

	<img src="./images/120.jpg" alt="Array image Stress"/>
	<br />
	<a href="/viparea/theyorker/directory/photos/move/120/up" title="move up">move up</a> | 
	<a href="/viparea/theyorker/directory/photos/move/120/down" title="move down">move down</a> | 
	<a href="/viparea/theyorker/directory/photos/delete/120" title="delete">delete</a> 
	<br />
</div>

<?php
	}
?>

<?php
	if ($stage == 5)
	{
?>
<div class="blue_box">
	<form id="orgdetails" action="/wizard/organisation" method="post" class="form">
		<fieldset>
			<input type="hidden" name="r_stage" value="<?php echo $stage; ?>" />
			<input type="hidden" name="r_dump" value="<?php echo htmlentities(serialize($_SESSION[$session_var]), ENT_QUOTES); ?>" />
		</fieldset>
		<h2>location map</h2>
		1) Choose your map type:
		<br />
		<fieldset>
			<input type="radio" name="a_map_type" onclick="document.getElementById("postcode_div").style.display = "block"; document.getElementById("building_div").style.display = "none";"/> Road Map<br />
			<input type="radio" name="a_map_type" onclick="document.getElementById("building_div").style.display = "block"; document.getElementById("postcode_div").style.display = "none";"/> Campus Map<br />
		</fieldset>
		<div id="postcode_div">
			2) Jump to postcode<br />
			<fieldset>
				<input type="text" name="a_map_postcode" style="width: 150px;"/><input type="button" value="Go" class="button" />
			</fieldset>
		</div>
		<br />
		<div id="building_div" style="display: none;">
			2) Jump to Building<br />
			<fieldset>
			<select name="a_map_building_locations" size="1">
				<option value="" selected="selected"></option>
				<option value="1">The list is a little short.</option>
			</select>
			<input type="button" value="Go" class="button" />
			</fieldset>
		</div>
		3) Refine your location
		<br />
		click and drag the red pin to the appropriate location on the map using the tools on the left to zoom in or out as appropriate.
		<br />
		<img width="390" src="./images/gmapwhereamI.png" alt="where am i?" />
		<fieldset>
			<input type="submit" name="r_submit_finish" value="Finish" class="button" />
			<input type="submit" name="r_submit_finish" value="Next" class="button" />
			<input type="submit" name="r_submit_back" value="Back" class="button" />
		</fieldset>
	</form>
</div>


<?php
	}
?>

<?php
	if ($stage == 6)
	{
?>
<div class="BlueBox">
<h2>who are you?</h2>
	<form id="orgdetails" action="/wizard/organisation" method="post" class="form">
		<fieldset>
			<input type="hidden" name="r_stage" value="<?php echo $stage; ?>" />
			<input type="hidden" name="r_dump" value="<?php echo htmlentities(serialize($_SESSION[$session_var]), ENT_QUOTES); ?>" />
			<?php PrintTextBox('a_user_name', 'Name: ', $session_var); ?>
			<?php PrintTextBox('a_user_email', 'Email: ', $session_var); ?>
			<?php PrintTextArea('a_user_notes', 'Any Notes: ', $session_var); ?>
			<?php PrintTextBox('a_user_position', 'Position In Society: ', $session_var); ?>
			<label for="captcha">Enter The Number: </label>
			<img src="captcha.jpg" id="captcha" alt="captcha" style="margin-left: 0.7em; margin-top: 0.3em; height: 20px; width: 100px;" /><br />
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_finish" value="Finish" class="button" />
			<input type="submit" name="r_submit_next" value="Next" class="button" disabled="disabled" />
			<input type="submit" name="r_submit_back" value="Back" class="button" />
		</fieldset>
	</form>
</div>
<?php
	}
?>
</div>