<div class="RightToolbar">
	<h4>Pages</h4>
	<div class="Entry">
	<ol>
	<?php

	function addstrong($text)
	{
		return '<strong>'.$text.'</strong>';
	}

	function addstrike($text)
	{
		return '<strike>'.$text.'</strike>';
	}	

	$headings = $stage_list['headings'];

	foreach ($headings as $key => &$heading)
	{
		if ($stage == $key)
			$heading = addstrong($heading);
		if ($is_connected == 'no' && in_array($key, $stage_list['skip']))
			$heading = addstrike($heading);
		echo '<li>'.$heading.'</li>';
	}

	?>
	</ol>
	</div>
	<h4>What"s this?</h4>
	<div class="Entry">
		Blah blah blah blah blah.
        </div>
</div>

<?php
	if ($stage == 1)
	{
?>
<form id="orgdetails" name="orgdetails" action="/wizard/organisation" method="POST" class="form">
<input type="hidden" name="r_stage" value="<?php echo $stage; ?>">
<div class="grey_box">
<h2>start suggesting</h2>
<fieldset>
<h4>Type of directory entry</h4>
		<label for="a_type">Society</label>
		<input type="radio" name="a_type" id="a_type" value="society" checked="checked"/>
		<label for="a_type">Bar</label>
		<input type="radio" name="a_type" id="a_type" value="bar" />
		<label for="a_type">Restaurant</label>
		<input type="radio" name="a_type" id="a_type" value="restaurant" />
		<label for="a_type">Other</label>
		<input type="radio" name="a_type" id="a_type" value="other" />
<br /><br />
<h4>Are you connected to this organisation?</h4>
		<label for="a_connected">Yes</label>
		<input type="radio" name="a_connected" value="yes" />
		<label for="a_connected">No</label>
		<input type="radio" name="a_connected" value="no" checked="checked"/>
		<label for="submit_finish"></label>
		<input type="submit" name="r_submit_finish" value="Finish" class="disabled_button" disabled />
		<input type="submit" name="r_submit_next" value="Next" class="button" />

	</fieldset>
</div>
</form>
<?php
	}
?>
<?php
	if ($stage == 2)
	{
?>
<form id="orgdetails" name="orgdetails" action="/wizard/organisation" method="POST" class="form">
<input type="hidden" name="r_stage" value="<?php echo $stage; ?>">
<div class="grey_box">
<h2>basic details</h2>
	<fieldset>
		<label for="a_name">Name: </label>
		<input type="text" name="a_name" style="width: 220px;" />
		<br />
		<label for="a_description">Description:</label>
		<textarea name="a_description" cols="29" rows="5"></textarea>
        	<label for="a_email_address">Email Address:</label>
		<input type="text" name="a_email_address" style="width: 220px;" value=""/>
		<br />
		<label for="a_website">Website: </label>
		<input type="text" name="a_website" style="width: 220px;" /><br />
		<br />
		<label for="submit_finish"></label>
		<input type="submit" name="r_submit_finish" value="Finish" class="button" />
		<input type="submit" name="r_submit_next" value="Next" class="button" />

	</fieldset>
</div>
</form>
<?php
	}
?>

<?php
	if ($stage == 3)
	{
?>
<form id="orgdetails" name="orgdetails" action="/wizard/organisation" method="POST" class="form">
<input type="hidden" name="r_stage" value="<?php echo $stage; ?>">
<div class="grey_box">
<h2>more details</h2>
	<fieldset>
        <label for="a_location">Campus Location:</label>
		<input type="text" name="a_location" style="width: 220px;" value=""/>
		<label for="a_address">Address:</label>
		<textarea name="a_address"></textarea>

        	<label for="a_postcode">Postcode:</label>
		<input type="text" name="a_postcode" style="width: 120px;" value=""/>
		<label for="a_opening_times">Opening Times:</label>
		<input type="text" name="a_opening_times" style="width: 220px;" value=""/>
		<label for="a_phone_internal">Internal Phone:</label>
		<input type="text" name="a_phone_internal" style="width: 120px;" value=""/>
		<label for="a_phone_external">External Phone:</label>
		<input type="text" name="a_phone_external" style="width: 120px;" value=""/>
		<label for="a_fax">Fax Number:</label>
		<input type="text" name="a_fax" style="width: 120px;" value=""/>
		<label for="r_submit_finish"></label>
		<input type="submit" name="r_submit_finish" value="Finish" class="button" />
		<input type="submit" name="r_submit_next" value="Next" class="button" />

	</fieldset>
</div>

</form>
<?php
	}
?>

<?php
	if ($stage == 4)
	{
?>
<div class="grey_box">
<h2> photo upload </h2>
<form action="/viparea/theyorker/directory/photos/upload" method="post" class="form" enctype="multipart/form-data">
<fieldset>
Photo's should be in jpg format. The upload size limit is 2mb(?).<br />
	<label for="title1">Photo Title:</label><input type="text" name="title1" size="30" />
	<br />
	<label for="userfile1">Photo File:</label><input type="file" name="userfile1" size="30" />

	<br />
<input type="hidden" name="destination" id="destination" value="1" />

<input type="button" onClick="AddClones()" value="Another"/>
<input type="submit" value="upload" />
</fieldset>
</form>
</div>
<form id="orgdetails" name="orgdetails" action="/wizard/organisation" method="POST" class="form">
<input type="hidden" name="r_stage" value="<?php echo $stage; ?>">
<div class="grey_box">
<fieldset>
		<input type="submit" name="r_submit_finish" value="Finish" class="button" />
		<input type="submit" name="r_submit_next" value="Next" class="button" />

</fieldset>
</div>
</form>

<div class="blue_box">
		<img src="./images/100.jpg" alt="Array image Tux"/>
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

</div>

<?php
	}
?>

<?php
	if ($stage == 5)
	{
?>
<form id="orgdetails" name="orgdetails" action="/wizard/organisation" method="POST" class="form">
<input type="hidden" name="r_stage" value="<?php echo $stage; ?>">
<div class="blue_box">
<fieldset>
	<h2>location map</h2>
		1) Choose your map type:
		<br />

		<input type="radio" name="a_map_type" onclick="document.getElementById("postcode_div").style.display = "block"; document.getElementById("building_div").style.display = "none";"/> Road Map<br />
		<input type="radio" name="a_map_type" onclick="document.getElementById("building_div").style.display = "block"; document.getElementById("postcode_div").style.display = "none";"/> Campus Map<br />

		<div id="postcode_div">
		2) Jump to postcode<br />
		<input type="text" name="a_map_postcode" style="width: 150px;"/><input type="button" value="Go" class="button" action="#"/>
		</div>
		<br />
		<div id="building_div" style="display: none;">
		2) Jump to Building<br />

		<select name="a_map_building_locations" size="1">
			<option value="" selected="selected"></option>
			<option value="1">The list is a little short.</option>
		</select>
		<input type="button" value="Go" class="button" action="#"/>
		</div>
		3) Refine your location
		<p>
		<p>click and drag the red pin to the appropriate location on the map using the tools on the left to zoom in or out as appropriate.</p>

		</p>
		<p>
			<img width="390" src="./images/gmapwhereamI.png" />
		</p>
		<label for="r_submit_finish"></label>
		<input type="submit" name="r_submit_finish" value="Finish" class="button" />
		<input type="submit" name="r_submit_finish" value="Next" class="button" />
	</fieldset>
</div>
</form>


<?php
	}
?>

<?php
	if ($stage == 6)
	{
?>
<form id="orgdetails" name="orgdetails" action="/wizard/organisation" method="POST" class="form">
<input type="hidden" name="r_stage" value="<?php echo $stage; ?>">
<div class="grey_box">
<h2>who are you?</h2>
<fieldset>
		<label for="a_user_name">Name:</label>
		<input type="text" name="a_user_name" /><br />

		<label for="a_user_email">Email:</label>
		<input type="text" name="a_user_email" /><br />

		<label for="a_user_notes">Any notes:</label>
		<textarea name="a_user_notes"></textarea>

		<label for="a_user_position">Position in society:</label>
		<input type="text" name="a_user_position" /><br />


		<label for="a_captcha">Enter the number:</label>
		<input type="text" name="a_captcha"/><br />
		<img src="captcha.jpg" style="margin-left: 0.7em; margin-top: 0.3em; height: 20px; width: 100px;"><br />


		<label for="r_submit_finish"></label>
		<input type="submit" name="r_submit_finish" value="Finish" class="button" />
		<input type="submit" name="r_submit_next" value="Next" class="disabled_button" disabled />

	</fieldset>
</div>
</form>
<?php
	}
?>

<pre>
<?php

print_r($_SESSION);

echo '<br /><br />asd<br /><br />';

print_r($data);

?>
</pre>