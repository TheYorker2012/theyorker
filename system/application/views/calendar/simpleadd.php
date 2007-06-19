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
	var repeat_select = document.getElementById("caladd_frequency");
	var repeat_type = repeat_select.options[repeat_select.selectedIndex].value
	var repeat_div = document.getElementById("frequency_div");

	if (repeat_type == "none")
	{
		repeat_div.innerHTML = "";
	}
	else if (repeat_type == "daily")
	{
		repeat_div.innerHTML = '<fieldset><label for="caladd_interval">Every:</label><input name="caladd_interval" id="caladd_interval" type="text" size="3" value="1"> day(s)</fieldset>'
	}
	else if (repeat_type == "weekly")
	{
		repeat_div.innerHTML = '\
		<fieldset>\
		<label for="caladd_interval">Every</label><input name="caladd_interval" id="caladd_interval" type="text" size="3" value="1"><br />\
		<label for="caladd_onday[mon]">Monday</label><input name="caladd_onday[mon]" id="caladd_onday[mon]" type="checkbox"><br />\
		<label for="caladd_onday[tue]">Tuesday</label><input name="caladd_onday[tue]" id="caladd_onday[tue]" type="checkbox"><br />\
		<label for="caladd_onday[wed]">Wednesday</label><input name="caladd_onday[wed]" id="caladd_onday[wed]" type="checkbox"><br />\
		<label for="caladd_onday[thu]">Thursday</label><input name="caladd_onday[thu]" id="caladd_onday[thu]" type="checkbox"><br />\
		<label for="caladd_onday[fri]">Friday</label><input name="caladd_onday[fri]" id="caladd_onday[fri]" type="checkbox"><br />\
		<label for="caladd_onday[sat]">Saturday</label><input name="caladd_onday[sat]" id="caladd_onday[sat]" type="checkbox"><br />\
		<label for="caladd_onday[sun]">Sunday</label><input name="caladd_onday[sun]" id="caladd_onday[sun]" type="checkbox"><br />\
		</fieldset>';
	}
	else if (repeat_type == "yearly")
	{
		repeat_div.innerHTML = '<fieldset><label for="caladd_interval">Every:</label><input name="caladd_interval" id="caladd_interval" type="text" size="3" value="1"> year(s)</fieldset>'
	}
}

</script>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>add event</h2>
		<form id="caladd" action="<?php echo($AddForm['target']); ?>" method="post" class="">
			<fieldset>
<?php // <br /> tags necessary for correct rendering in text based browsers ?>
				<label for="caladd_summary">Summary: </label>
				<input type="text" name="caladd_summary" id="caladd_summary" value="<?php echo($AddForm['summary']); ?>" size="30"/><br />
				
				<label for="caladd_allday">All day:</label>
				<input type="checkbox" name="caladd_allday" <?php if ($AddForm['allday']) echo('checked="checked"');?> />
				
				<label for="caladd_startdate">Start: </label>
				<input type="text" name="caladd_startdate" id="caladd_startdate" value="<?php echo($AddForm['startdate']); ?>" size="10" />
				<input type="text" name="caladd_starttime" id="caladd_starttime" value="<?php echo($AddForm['starttime']); ?>" size="5" />
				<label for="caladd_enddate">Finish: </label>
				<input type="text" name="caladd_enddate" id="caladd_enddate" value="<?php echo($AddForm['enddate']); ?>" size="10" />
				<input type="text" name="caladd_endtime" id="caladd_endtime" value="<?php echo($AddForm['endtime']); ?>" size="5" />
				<br />
			
				<label for="caladd_location">Where: </label>
				<input type="text" id="caladd_location" name="caladd_location" />
			
				<label for="caladd_category">Category: </label>
				<select id="caladd_category" name="caladd_category">
					<?php
					foreach ($EventCategories as $key => $category) {
						echo('<option value="'.$key.'"'.($key == $AddForm['eventcategory'] ? ' selected="selected"':'').'>');
						echo($category['name']);
						echo('</option>');
					}
					?>
				</select>
			
				<label for="caladd_description">Description:</label>
				<textarea id="caladd_description" name="caladd_description"></textarea><br />
				
				<label for="caladd_frequency">Repeats:</label>
				<select onchange="FrequencyChange()" id="caladd_frequency" name="caladd_frequency">
					<option value="none" selected="selected">None</option>
					<option value="daily">Daily</option>
					<option value="weekly">Weekly</option>
					<option value="yearly">Yearly</option>
				</select>
				<br /><br />
			</fieldset>
			<div id="frequency_div" style=""></div>
			<fieldset>
				<input class="button" type="submit" name="r_submit" id="r_submit" value="Submit" />
				<input class="button" type="reset" name="r_cancel" id="r_cancel" value="Cancel" onclick="hideFeedback();" />
			</fieldset>
		</form>
	</div>
</div>