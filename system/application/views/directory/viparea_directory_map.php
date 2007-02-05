<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href='#'>Do not show a map</a></li>
</ul>
</div>

<form id='orgdetails' name='orgdetails' action='/viparea/directory/<?php echo $organisation['shortname']; ?>/updatemap' method='POST' class='form'>
<div class='blue_box'>
	<h2>location map</h2>
		1) Choose your map type:
		<br />
		<input type='radio' name='map_type' onclick="document.getElementById('postcode_div').style.display = 'block'; document.getElementById('building_div').style.display = 'none';"/> Road Map
		<input type='radio' name='map_type' onclick="document.getElementById('building_div').style.display = 'block'; document.getElementById('postcode_div').style.display = 'none';"/> Campus Map
		<div id='postcode_div'>
		2) Jump to postcode<br />
		<input type='text' name='map_postcode' style='width: 150px;'/><input type='button' value='Go' class='button' action='#'/>
		</div>
		<div id='building_div'>
		2) Jump to Building<br />
		<select name='map_building_locations' size='1'>
			<option value='' selected='selected'></option>
			<option value='1'>The list is a little short.</option>
		</select>
		<input type='button' value='Go' class='button' action='#'/>
		</div>
		3) Refine your location
		<p>
		<?php echo $map_text; ?>
		</p>
		<p>
			<img width='390' src='/images/prototype/directory/about/gmapwhereamI.png' />
		</p>
		<label for='map_submitbutton'></label>
		<input type='submit' name='map_submitbutton' value='Save' class='button' />
</div>
</form>
<a href='/viparea/'>Back to the vip area.</a>