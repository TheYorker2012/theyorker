<?php

/**
 * @param $EventCategories array[id => array('id'=>,'name'=>,'colour'=>)]
 * @param $AddForm id
 */

?>

<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<p>Some useful information about adding events.</p>

	</div>
	<h2>Hints</h2>
	<div class="Entry">
		<p>hello!</p>
	</div>
</div>

<script type="text/javascript">

function FrequencyChange()
{
	var repeat_select = document.getElementById("a_frequency");
	var repeat_type = repeat_select.options[repeat_select.selectedIndex].value
	var repeat_div = document.getElementById("frequency_div");

	if (repeat_type == "none")
	{
		repeat_div.innerHTML = "";
	}
	else if (repeat_type == "daily")
	{
		repeat_div.innerHTML = '<fieldset><label for="a_interval">Every:</label><input name="a_interval" id="a_interval" type="text" size="3" value="1"> day(s)</fieldset>'
	}
	else if (repeat_type == "weekly")
	{
		repeat_div.innerHTML = '\
		<fieldset>\
		<label for="a_interval">Every</label><input name="a_interval" id="a_interval" type="text" size="3" value="1"><br />\
		<label for="a_onday[mon]">Monday</label><input name="a_onday[mon]" id="a_onday[mon]" type="checkbox"><br />\
		<label for="a_onday[tue]">Tuesday</label><input name="a_onday[tue]" id="a_onday[tue]" type="checkbox"><br />\
		<label for="a_onday[wed]">Wednesday</label><input name="a_onday[wed]" id="a_onday[wed]" type="checkbox"><br />\
		<label for="a_onday[thu]">Thursday</label><input name="a_onday[thu]" id="a_onday[thu]" type="checkbox"><br />\
		<label for="a_onday[fri]">Friday</label><input name="a_onday[fri]" id="a_onday[fri]" type="checkbox"><br />\
		<label for="a_onday[sat]">Saturday</label><input name="a_onday[sat]" id="a_onday[sat]" type="checkbox"><br />\
		<label for="a_onday[sun]">Sunday</label><input name="a_onday[sun]" id="a_onday[sun]" type="checkbox"><br />\
		</fieldset>';
	}
	else if (repeat_type == "yearly")
	{
		repeat_div.innerHTML = '<fieldset><label for="a_interval">Every:</label><input name="a_interval" id="a_interval" type="text" size="3" value="1"> year(s)</fieldset>'
	}
}

</script>

<div id="MainColumn">
<div class="BlueBox">
		<h2>add event</h2>
		<form id="" action="<?php echo($AddForm['target']); ?>" method="post" class="">

		<fieldset>
		<!-- <br /> tags necessary for correct rendering in text based browsers -->
		<label for="a_summary">Summary: </label>
		<input type="text" name="a_summary" id="a_summary" value="<?php echo($AddForm['default_summary']); ?>" size="30"/><br />
		
		<label for="a_allday">All day:</label>
		<input type="checkbox" name="a_allday" <?php if ($AddForm['default_allday']) echo('checked="checked"');?> />
		
		<label for="a_startdate">Start: </label>
		<input type="text" name="a_startdate" id="a_startdate" value="<?php echo($AddForm['default_startdate']); ?>" size="10" />
		<input type="text" name="a_starttime" id="a_starttime" value="<?php echo($AddForm['default_starttime']); ?>" size="5" />
		<label for="a_enddate">Finish: </label>
		<input type="text" name="a_enddate" id="a_enddate" value="<?php echo($AddForm['default_enddate']); ?>" size="10" />
		<input type="text" name="a_endtime" id="a_endtime" value="<?php echo($AddForm['default_endtime']); ?>" size="5" />
		<br />

		<label for="a_location">Where: </label>
		<input type="text" id="a_location" name="a_location" />

		<label for="a_category">Category: </label>
		<select id="a_category" name="a_category">
			<?php
			foreach ($EventCategories as $key => $category) {
				echo('<option value="'.$key.'"'.($key == $AddForm['default_eventcategory'] ? ' selected="selected"':'').'>');
				echo($category['name']);
				echo('</option>');
			}
			?>
		</select>

		<label for="a_description">Description:</label>
		<textarea id="a_description" name="a_description"></textarea><br />
		
		<label for="a_frequency">Repeats:</label>
		<select onchange="FrequencyChange()" id="a_frequency" name="a_frequency">
			<option value="none" selected="selected">None</option>
			<option value="daily">Daily</option>
			<option value="weekly">Weekly</option>
			<option value="yearly">Yearly</option>
		</select>
		<br /><br />
	</fieldset>
		<div id="frequency_div" style=""></div>
	<fieldset>
	</fieldset>
	<fieldset>
		<input class="button" type="submit" name="r_submit" id="r_submit" value="Submit" />
		<input class="button" type="reset" name="r_cancel" id="r_cancel" value="Cancel" onclick="hideFeedback();"./>
	</fieldset>
</form>
</div>
</div>