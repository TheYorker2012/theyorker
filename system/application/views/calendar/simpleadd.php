<?php

/**
 * @param $EventCategories array[id => array('id'=>,'name'=>,'colour'=>)]
 * @param $AddForm id
 */

?>

<script type="text/javascript">
function FrequencyChange() {
	var repeat_select = document.getElementById("caladd_frequency");
	var repeat_type = repeat_select.options[repeat_select.selectedIndex].value
	var repeat_div = document.getElementById("frequency_div");

	if (repeat_type == "none") {
		repeat_div.innerHTML = "";
	} else if (repeat_type == "daily") {
		repeat_div.innerHTML = '<fieldset><label for="caladd_interval">Every:</label><input name="caladd_interval" id="caladd_interval" type="text" size="3" value="1"> day(s)</fieldset>'
	} else if (repeat_type == "weekly") {
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
	} else if (repeat_type == "yearly") {
		repeat_div.innerHTML = '<fieldset><label for="caladd_interval">Every:</label><input name="caladd_interval" id="caladd_interval" type="text" size="3" value="1"> year(s)</fieldset>'
	}
}

function CheckDates() {
	var date1 = document.getElementById('caladd_start');
	var date2 = document.getElementById('caladd_end');

	if (date1.value >= date2.value) {
		alert("Please ensure that the end date and time is after the start date and time, then try re-submitting your event.");
		return false;
	}
}
</script>

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

<div id="MainColumn">
	<div class="BlueBox">
		<h2>add event</h2>

		<form id="caladd" action="<?php echo($AddForm['target']); ?>" method="post" onsubmit="return CheckDates();">
			<fieldset>
				<label for="caladd_summary">Summary: </label>
				<input type="text" name="caladd_summary" id="caladd_summary" value="<?php echo($AddForm['summary']); ?>" size="30"/>
				<br />

				<label for="caladd_allday">All day:</label>
				<input type="checkbox" name="caladd_allday" <?php if ($AddForm['allday']) echo('checked="checked"');?> />
				<br />

				<label for="caladd_start_show">Start:</label>
				<div id="caladd_start_show">None</div>
				<input type="hidden" name="caladd_start" id="caladd_start" value="<?php echo($AddForm['caladd_start']); ?>" />
				<button id="caladd_start_trigger">Select</button>
				<br />

				<label for="caladd_end_show">Finish:</label>
				<div id="caladd_end_show">None</div>
				<input type="hidden" name="caladd_end" id="caladd_end" value="<?php echo($AddForm['caladd_end']); ?>" />
				<button id="caladd_end_trigger">Select</button>
				<br />

				<label for="caladd_location">Where: </label>
				<input type="text" id="caladd_location" name="caladd_location" />
				<br />

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
				<br />

				<label for="caladd_description">Description:</label>
				<textarea id="caladd_description" name="caladd_description"></textarea>
				<br />

				<label for="caladd_frequency">Repeats:</label>
				<select onchange="FrequencyChange();" id="caladd_frequency" name="caladd_frequency">
					<option value="none" selected="selected">None</option>
					<option value="daily">Daily</option>
					<option value="weekly">Weekly</option>
					<option value="yearly">Yearly</option>
				</select>
				<br />

			</fieldset>

			<div id="frequency_div"></div>

			<fieldset>
				<input class="button" type="submit" name="r_submit" id="r_submit" value="Submit" />
				<input class="button" type="reset" name="r_cancel" id="r_cancel" value="Reset" />
			</fieldset>
		</form>
	</div>
</div>

<script type='text/javascript'>
Calendar.setup(
	{
		inputField	: 'caladd_start',
		ifFormat	: '%s',
		displayArea	: 'caladd_start_show',
		daFormat	: '%a %e %b, %Y @ %H:%M',
		button		: 'caladd_start_trigger',
		singleClick	: false,
		firstDay	: 1,
		weekNumbers	: false,
		range		: [<?php echo (date('Y') . ',' . (date('Y') + 1)); ?>],
		showsTime	: true,
		timeFormat	: '24'
	}
);
Calendar.setup(
	{
		inputField	: 'caladd_end',
		ifFormat	: '%s',
		displayArea	: 'caladd_end_show',
		daFormat	: '%a %e %b, %Y @ %H:%M',
		button		: 'caladd_end_trigger',
		singleClick	: false,
		firstDay	: 1,
		weekNumbers	: false,
		range		: [<?php echo (date('Y') . ',' . (date('Y') + 1)); ?>],
		showsTime	: true,
		timeFormat	: '24'
	}
);
</script>