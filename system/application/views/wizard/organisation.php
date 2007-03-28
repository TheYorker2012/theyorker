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
<input type="hidden" name="stage" value="<?php echo $stage; ?>">
<div class="grey_box">
<h2>start suggesting</h2>
<fieldset>
<h4>Type of directory entry</h4>
		<label for="type">Society</label>
		<input type="radio" name="type" id="type" value="society" checked="checked"/>
		<label for="type">Bar</label>
		<input type="radio" name="type" id="type" value="bar" />
		<label for="type">Restaurant</label>
		<input type="radio" name="type" id="type" value="restaurant" />
		<label for="type">Other</label>
		<input type="radio" name="type" id="type" value="other" />
<br /><br />
<h4>Are you connected to this organisation?</h4>
		<label for="connected">Yes</label>
		<input type="radio" name="connected" value="yes" />
		<label for="connected">No</label>
		<input type="radio" name="connected" value="no" checked="checked"/>
		<label for="submit_finish"></label>
		<input type="submit" name="submit_finish" value="Finish" class="disabled_button" disabled />
		<input type="submit" name="submit_next" value="Next" class="button" />

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
<input type="hidden" name="stage" value="<?php echo $stage; ?>">
<input type="hidden" name="is_connected" value="<?php echo $is_connected; ?>">
<div class="grey_box">
<h2>basic details</h2>
	<fieldset>
		<label for="name">Name: </label>
		<input type="text" name="name" style="width: 220px;" />
		<br />
		<label for="description">Description:</label>
		<textarea name="description" cols="29" rows="5">
		</textarea>
        	<label for="email_address">Email Address:</label>
		<input type="text" name="email_address" style="width: 220px;" value=""/>
		<br />
		<label for="website">Website: </label>
		<input type="text" name="website" style="width: 220px;" /><br />
		<br />
		<label for="submit_finish"></label>
		<input type="submit" name="submit_finish" value="Finish" class="button" />
		<input type="submit" name="submit_next" value="Next" class="button" />

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
<input type="hidden" name="stage" value="<?php echo $stage; ?>">
<input type="hidden" name="is_connected" value="<?php echo $is_connected; ?>">
<div class="grey_box">
<h2>more details</h2>
	<fieldset>
        <label for="email_address2">Campus Location:</label>
		<input type="text" name="email_address2" style="width: 220px;" value=""/>
		<label for="email_address2">Address:</label>
		<textarea></textarea>

        <label for="email_address2">Postcode:</label>
		<input type="text" name="email_address2" style="width: 120px;" value=""/>
		<label for="email_address2">Opening Times:</label>
		<input type="text" name="email_address2" style="width: 220px;" value=""/>
		<label for="email_address2">Internal Phone:</label>
		<input type="text" name="email_address2" style="width: 120px;" value=""/>
		<label for="email_address2">External Phone:</label>

		<input type="text" name="email_address2" style="width: 120px;" value=""/>
		<label for="email_address2">Fax Number:</label>
		<input type="text" name="email_address2" style="width: 120px;" value=""/>
		<label for="submitbutton"></label>
		<input type="submit" name="submit_finish" value="Finish" class="button" />
		<input type="submit" name="submit_next" value="Next" class="button" />

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
<input type="hidden" name="stage" value="<?php echo $stage; ?>">
<div class="grey_box">
<fieldset>
		<input type="submit" name="submit_finish" value="Finish" class="button" />
		<input type="submit" name="submit_finish" value="Next" class="button" />

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
<input type="hidden" name="stage" value="<?php echo $stage; ?>">
<div class="blue_box">
<fieldset>
	<h2>location map</h2>
		1) Choose your map type:
		<br />

		<input type="radio" name="map_type" onclick="document.getElementById("postcode_div").style.display = "block"; document.getElementById("building_div").style.display = "none";"/> Road Map<br />
		<input type="radio" name="map_type" onclick="document.getElementById("building_div").style.display = "block"; document.getElementById("postcode_div").style.display = "none";"/> Campus Map<br />

		<div id="postcode_div">
		2) Jump to postcode<br />
		<input type="text" name="map_postcode" style="width: 150px;"/><input type="button" value="Go" class="button" action="#"/>
		</div>
		<br />
		<div id="building_div" style="display: none;">
		2) Jump to Building<br />

		<select name="map_building_locations" size="1">
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
		<label for="map_submitbutton"></label>
		<input type="submit" name="submit_finish" value="Finish" class="button" />
		<input type="submit" name="submit_finish" value="Next" class="button" />
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
<input type="hidden" name="stage" value="<?php echo $stage; ?>">
<div class="grey_box">
<h2>who are you?</h2>
<fieldset>
		<label for="name">Name:</label>
		<input type="text" /><br />

		<label for="name">Email:</label>
		<input type="text" /><br />

		<label for="name">Any notes:</label>
		<textarea></textarea>

		<label for="name">Position in society:</label>
		<input type="text" /><br />


		<label for="">Enter the number:</label>

<input type="text" /><br /><label for=""></label><img src="captcha.jpg" style="margin-left: 0.7em; margin-top: 0.3em; height: 20px; width: 100px;"><br />


		<label for="submit_finish"></label>
		<input type="submit" name="submit_finish" value="Finish" class="button" />
		<input type="submit" name="submit_next" value="Next" class="disabled_button" disabled />

	</fieldset>
</div>
</form>
<?php
	}
?>

<pre>
<?php

print_r($data);

?>
</pre>