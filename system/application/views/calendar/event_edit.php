<?php

/**
 * @file views/calendar/event_edit.php
 * @brief View for editing event information.
 *
 * @param $ExtraFormData array Extra form data array.
 * @param $Event CalendarEvent Event information.
 * @param $EventInfo Array with: 'summary', 'description', 'start', 'duration' etc
 * @param $SimpleRecur Array Simple recurrence information.
 * @param $ReadOnly bool Whether the source is read only.
 * @param $Attendees array[string] Attending users.
 * @param $FailRedirect string URL fail redirect path.
 * @param $FormPrefix string Prefix of all fields.
 * @param $EventCategories array[id => array('id'=>,'name'=>,'colour'=>)]
 * @param $Help Help text array, indexed by title.
 *
 * @todo Send information about what fields have changed, and only send the deltas
 */

if (!is_array($SimpleRecur)) {
	$SimpleRecur = array(
		'freq' => 'weekly',
		'range_method' => 'count',
		'count' => 10,
	);
}

// Reference arrays
global $nth_set;
$nth_set = array(
	1 => '1st',  '2nd',  '3rd',  '4th',  '5th',  '6th',  '7th',  '8th',
		 '9th',  '10th', '11th', '12th', '13th', '14th', '15th', '16th',
		 '17th', '18th', '19th', '20th', '21st', '22nd', '23rd', '24th',
		 '25th', '26th', '27th', '28th', '29th', '30th', '31st',
 	-1 => 'last', -2 => '2nd last', -3 => '3rd last', -4 => '4th last',
 	-5 => '5th last',
);
$daysofweek = array(
	'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
	'1,2,3,4,5' => 'Weekday'
);
global $months;
$months = array(
	1 => 'January', 'February', 'March',     'April',   'May',      'June',
	     'July',    'August',   'September', 'October', 'November', 'December'
);

$now_year = (int)date('Y');

/// Echo a simple gregorian date selector.
function DateSelectorGregorian($id, $name, $default, $minyear, $maxyear)
{
	global $nth_set;
	global $months;
?><select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($id); ?>_monthday" name="<?php echo($name); ?>[monthday]">
		<?php
			foreach ($nth_set as $monthday => $monthdayname) {
				if ($monthday > 0) {
					echo("<option value=\"$monthday\"");
					if ($default['monthday'] == $monthday) {
						echo(' selected="yes"');
					}
					echo(">$monthdayname</option>\n");
				}
			}
		?>
	</select>
	<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($id); ?>_month" name="<?php echo($name); ?>[month]">
		<?php
			foreach ($months as $month => $monthname) {
				echo("<option value=\"$month\"");
				if ($default['month'] == $month) {
					echo(' selected="yes"');
				}
				echo(">$monthname</option>\n");
			}
		?>
	</select>
	<?php
	// Make the year and time line up on the next line
	if (isset($default['year']) || isset($default['time'])) {
		?><label></label><?php
	}
	if (isset($default['year'])) {
	?>
	<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($id); ?>_year" name="<?php echo($name); ?>[year]">
		<?php
			if ($default['year'] < $minyear) {
				echo('<option value="'.$default['year'].'" selected="yes">'.$default['year'].'</option>');
			}
			for ($year = $minyear; $year < $maxyear; ++$year) {
				echo("<option value=\"$year\"");
				if ($default['year'] == $year) {
					echo(' selected="yes"');
				}
				echo(">$year</option>");
			}
			if ($default['year'] >= $year) {
				echo('<option value="'.$default['year'].'" selected="yes">'.$default['year'].'</option>');
			}
		?>
	</select><?php
	}
	if (isset($default['time'])) {
		list($default_hour, $default_minute) = split(':', $default['time']);
		$default_minute -= $default_minute % 15;
		$timeFormat24 = get_instance()->user_auth->timeFormat == 24;
		?><select id="<?php echo($id); ?>_time" name="<?php echo($name); ?>[time]">
		<?php
			for ($hour = 0; $hour < 24; ++$hour) {
				for ($minute = 0; $minute < 60; $minute += 15) {
					echo("<option value=\"$hour:$minute\"");
					if ($default_hour == $hour and
						$default_minute == $minute)
					{
						echo(' selected="yes"');
					}
					echo('>');
					if ($timeFormat24) {
						echo(sprintf('%02d:%02d', $hour, $minute));
					} else {
						echo(sprintf('%d:%02d %s', (($hour+11)%12)+1, $minute, $hour >= 12 ? 'pm' : 'am'));
					}
					echo('</option>');
				}
			}
		?>
		</select><?php
	}
}

/// Echo a simple duration selector.
function DurationSelector($name, $default)
{
	?><label for="<?php echo($name); ?>_days">End</label><select id="<?php echo($name); ?>_days" name="<?php echo($name); ?>[days]">
<?php
		for ($days = 0; $days <= 7; ++$days) {
			echo("<option value=\"$days\"");
			if ($default['days'] == $days) {
				echo(' selected="yes"');
			}
			echo(">$days day".($days != 1 ? 's':'')." later</option>\n");
		}
	?></select>
	<?php
	if (isset($default['time'])) {
		list($default_hour, $default_minute) = split(':', $default['time']);
		$default_minute -= $default_minute % 15;
		$timeFormat24 = get_instance()->user_auth->timeFormat == 24;
		?><div id="<?php echo($name); ?>_time_div" style="display:inline"><label for="<?php echo($name); ?>_time">At</label>
		<select id="<?php echo($name); ?>_time" name="<?php echo($name); ?>[time]">
		<?php
			for ($hour = 0; $hour < 24; ++$hour) {
				for ($minute = 0; $minute < 60; $minute += 15) {
					echo("<option value=\"$hour:$minute\"");
					if ($default_hour == $hour and
						$default_minute == $minute)
					{
						echo(' selected="yes"');
					}
					echo('>');
					if ($timeFormat24) {
						echo(sprintf('%02d:%02d', $hour, $minute));
					} else {
						echo(sprintf('%d:%02d %s', (($hour+11)%12)+1, $minute, $hour >= 12 ? 'pm' : 'am'));
					}
					echo('</option>');
				}
			}
		?>
		</select></div><?php
	}
}

$CI = & get_instance();
?>

<style type="text/css">
	fieldset.inline-checks label {
/* 		border: thin solid black; */
		float: left;
		width: 50px;
		clear: none;
	}
</style>
<script type="text/javascript">

var browserType;

if (document.layers) {browserType = "nn4"}
if (document.all) {browserType = "ie"}
if (window.navigator.userAgent.toLowerCase().match("gecko")) {
   browserType= "gecko"
}

function hide(id)
{
  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById(id)');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById(id)');
  else
     document.poppedLayer =   
        eval('document.layers[id]');
  document.poppedLayer.style.display = "none";
}

function show(id)
{
  if (browserType == "gecko" )
     document.poppedLayer = 
         eval('document.getElementById(id)');
  else if (browserType == "ie")
     document.poppedLayer = 
        eval('document.getElementById(id)');
  else
     document.poppedLayer = 
         eval('document.layers[id]');
  document.poppedLayer.style.display = "block";
}


function SimpleCheckboxChange(checkbox, affected1, affected2)
{
	var repeat_input = document.getElementById(checkbox);
	if (repeat_input.checked) {
		show(affected1);
		show(affected2);
	} else {
		hide(affected1);
		hide(affected2);
	}
}

function CheckboxChange(type, index)
{
	var repeat_input = document.getElementById("<?php echo($FormPrefix); ?>_recur_"+index+"_"+type+"_use");
	if (repeat_input.checked) {
		show("<?php echo($FormPrefix); ?>_recur_"+index+"_"+type+"_div");
	} else {
		hide("<?php echo($FormPrefix); ?>_recur_"+index+"_"+type+"_div");
	}
	<?php /** @todo remove multiple references when stuff changes!!! */ ?>
	UpdateRecurCalPreviewLoad();
}

function MainRecurrenceToggle()
{
	var repeat_input = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_enable");
	if (repeat_input.checked) {
		show("<?php echo($FormPrefix); ?>_recurrence_div");
	} else {
		hide("<?php echo($FormPrefix); ?>_recurrence_div");
	}
	UpdateRecurCalPreviewLoad();
}

function AJAXInteraction(url, post, callback)
{

	var req = init();
	req.onreadystatechange = processRequest;
		
	function init() {
		if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
		} else if (window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	
	function processRequest () {
		// readyState of 4 signifies request is complete
		if (req.readyState == 4) {
			// status of 200 signifies sucessful HTTP call
			if (req.status == 200) {
				if (callback) callback(req.responseXML);
			}
		}
	}
	
	this.doGet = function() {
		// make a HTTP GET request to the URL asynchronously
		var post_string = url+'?';
		var first = 1;
		for (var key in post) {
			if (!first) {
				post_string += '&';
			} else {
				first = 0;
			}
			post_string += key+'='+encodeURIComponent(post[key]);
		}
		req.open("GET", post_string, true);
		req.send(null);
	}
}

function UpdateRecurCalPreviewLoad()
{
	hide("preview_calendar_div");
	var url = "<?php echo(site_url($Path->SimpleRecurValidate())); ?>";
	var post = {};
	post["prefix"] = "<?php echo($FormPrefix); ?>";
	if (document.getElementById("<?php echo($FormPrefix); ?>_timeassociated").checked) {
		post["<?php echo($FormPrefix); ?>_timeassociated"]  = "on";
	}
	post["<?php echo($FormPrefix); ?>_start[monthday]"] = document.getElementById("<?php echo($FormPrefix); ?>_start_monthday").value;
	post["<?php echo($FormPrefix); ?>_start[month]"]    = document.getElementById("<?php echo($FormPrefix); ?>_start_month").value;
	post["<?php echo($FormPrefix); ?>_start[year]"]     = document.getElementById("<?php echo($FormPrefix); ?>_start_year").value;
	post["<?php echo($FormPrefix); ?>_start[time]"]     = document.getElementById("<?php echo($FormPrefix); ?>_start_time").value;
	post["<?php echo($FormPrefix); ?>_duration[days]"]  = document.getElementById("<?php echo($FormPrefix); ?>_duration_days").value;
	post["<?php echo($FormPrefix); ?>_duration[time]"]  = document.getElementById("<?php echo($FormPrefix); ?>_duration_time").value;
	if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_enable").checked) {
		post["<?php echo($FormPrefix); ?>_recur_simple[enable]"   ] = "on";
	}
	post["<?php echo($FormPrefix); ?>_recur_simple[freq]"     ] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_freq").value;
	post["<?php echo($FormPrefix); ?>_recur_simple[interval]" ] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_interval").value;
	
	
	// frequency specific fields
	var freq = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_freq").value;
	switch (freq) {<?php /*
		case 'daily':
			
			break; */ ?>
		case 'weekly':
			for (var day = 0; day < 7; ++day) {
				if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_weekly_byday_"+day).checked) {
					post["<?php echo($FormPrefix); ?>_recur_simple[weekly_byday]["+day+"]"] = "on";
				}
			}
			break;
		case 'monthly':
			var month_method = "";
			if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_monthly_method_monthday").checked) {
				month_method = "monthday";
				post["<?php echo($FormPrefix); ?>_recur_simple[monthly_monthday][monthday]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_monthly_monthday_monthday").value;
			} else if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_monthly_method_weekday").checked) {
				month_method = "weekday";
				post["<?php echo($FormPrefix); ?>_recur_simple[monthly_weekday][week]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_monthly_weekday_week").value;
				post["<?php echo($FormPrefix); ?>_recur_simple[monthly_weekday][day]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_monthly_weekday_day").value;
			}
			post["<?php echo($FormPrefix); ?>_recur_simple[monthly_method]"] = month_method;
			break;
		case 'yearly':
			var year_method = "";
			if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_method_monthday").checked) {
				year_method = "monthday";
				post["<?php echo($FormPrefix); ?>_recur_simple[yearly_monthday][monthday]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_monthday_monthday").value;
				post["<?php echo($FormPrefix); ?>_recur_simple[yearly_monthday][month]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_monthday_month").value;
			} else if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_method_weekday").checked) {
				year_method = "weekday";
				post["<?php echo($FormPrefix); ?>_recur_simple[yearly_weekday][week]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_weekday_week").value;
				post["<?php echo($FormPrefix); ?>_recur_simple[yearly_weekday][day]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_weekday_day").value;
				post["<?php echo($FormPrefix); ?>_recur_simple[yearly_weekday][month]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_weekday_month").value;
			} else if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_method_yearday").checked) {
				year_method = "yearday";
				post["<?php echo($FormPrefix); ?>_recur_simple[yearly_yearday][yearday]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_yearly_yearday_yearday").value;
			}
			post["<?php echo($FormPrefix); ?>_recur_simple[yearly_method]"] = year_method;
			break;
	}
	// Range method
	var range_method = "";
	if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_range_method_noend").checked) {
		range_method = "noend";
	} else if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_range_method_count").checked) {
		range_method = "count";
		post["<?php echo($FormPrefix); ?>_recur_simple[count]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_count").value;
	} else if (document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_range_method_until").checked) {
		range_method = "until";
		post["<?php echo($FormPrefix); ?>_recur_simple[until][monthday]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_until_monthday").value;
		post["<?php echo($FormPrefix); ?>_recur_simple[until][month]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_until_month").value;
		post["<?php echo($FormPrefix); ?>_recur_simple[until][year]"] = document.getElementById("<?php echo($FormPrefix); ?>_recur_simple_until_year").value;
	}
	post["<?php echo($FormPrefix); ?>_recur_simple[range_method]"] = range_method;
	var ajax = new AJAXInteraction(url, post, UpdateRecurCalPreviewCallback); 
	ajax.doGet();
}

function UpdateRecurCalPreviewCallback(responseXML)
{
	var target = document.getElementById("preview_calendar_div"); 
	var errors = responseXML.getElementsByTagName("error");
	if (errors.length > 0) {
		target.innerHTML = "errors:<br />";
		for (var i = 0; i < errors.length; ++i) {
			target.innerHTML += errors[i].firstChild.nodeValue+"<br />";
		}
	} else {
		target.innerHTML = "";
	}
	var occurrences = responseXML.getElementsByTagName("occ");
	previewResetMinicalDates();
	for (var i = 0; i < occurrences.length; ++i) {
		date  = occurrences[i].attributes.getNamedItem("date").value;
		classes  = occurrences[i].attributes.getNamedItem("class").value;
		previewAdjustMinicalDate(date, classes);
	}
	show("preview_calendar_div");
}

function CalSimpleFreqChange()
{
	var freq = document.getElementById('<?php echo($FormPrefix); ?>_recur_simple_freq').value;
	hide('<?php echo($FormPrefix); ?>_recur_simple_freq_weekly');
	hide('<?php echo($FormPrefix); ?>_recur_simple_freq_monthly');
	hide('<?php echo($FormPrefix); ?>_recur_simple_freq_yearly');
	hide('<?php echo($FormPrefix); ?>_recur_simple_interval_unit_daily');
	hide('<?php echo($FormPrefix); ?>_recur_simple_interval_unit_weekly');
	hide('<?php echo($FormPrefix); ?>_recur_simple_interval_unit_monthly');
	hide('<?php echo($FormPrefix); ?>_recur_simple_interval_unit_yearly');
	switch (freq) {
		case 'daily':
			show('<?php echo($FormPrefix); ?>_recur_simple_interval_unit_daily');
			break;
		case 'weekly':
			show('<?php echo($FormPrefix); ?>_recur_simple_interval_unit_weekly');
			show('<?php echo($FormPrefix); ?>_recur_simple_freq_weekly');
			break;
		case 'monthly':
			show('<?php echo($FormPrefix); ?>_recur_simple_interval_unit_monthly');
			show('<?php echo($FormPrefix); ?>_recur_simple_freq_monthly');
			break;
		case 'yearly':
			show('<?php echo($FormPrefix); ?>_recur_simple_interval_unit_yearly');
			show('<?php echo($FormPrefix); ?>_recur_simple_freq_yearly');
			break;
	}
}


</script>

<div id="RightColumn">
	<?php
	// Echo the xhtml for help.
	echo($Help);
	/*$first = true;
	foreach ($Help as $title => $html) {
		echo('<h2'.($first ? ' class="first"' : '' ).">$title</h2>\n");
		echo("<div class=\"Entry\">\n$html</div>");
		$first = false;
	}*/
	?>
	
	<h2>Occurrences of this Event</h2>
	<div class="Entry">
		<div id="recurrences_preview_noscript" style="display: block">
			<p>Please enable javascript to take full advantage of this interface.</p>
		</div>
		<div id="recurrences_preview" style="display: none">
			<div id="preview_calendar_div" style="display: none">
			</div>
		</div>
		<div id="recurrences_preview_test">
			<?php get_instance()->load->view('calendar/minicalendar', array('Prefix' => 'preview')); ?>
		</div>
	</div>
</div>

<div id="MainColumn">
	<div>
		<form class="form" method="post" action="<?php echo(get_instance()->uri->uri_string()); ?>">
			<input type="hidden" id="prefix" name="prefix" value="<?php echo($FormPrefix); ?>" />
			<?php if (isset($ExtraFormData)) foreach ($ExtraFormData as $key => $value) {
?>			<input type="hidden" id="<?php echo($FormPrefix.'_'.$key); ?>" name="<?php echo($FormPrefix.'_'.$key); ?>" value="<?php echo(htmlentities($value, ENT_QUOTES, 'utf-8')); ?>" />
			<?php } ?>
			<div class="BlueBox">
				<h2>Event</h2>
				<fieldset>
					<label for="<?php echo($FormPrefix); ?>_summary">Summary</label>
					<input type="text" id="<?php echo($FormPrefix); ?>_summary" name="<?php echo($FormPrefix); ?>_summary" value="<?php echo(htmlentities($EventInfo['summary'], ENT_QUOTES, 'utf-8')); ?>" />
					
					<label for="<?php echo($FormPrefix); ?>_category">Category</label>
					<select id="<?php echo($FormPrefix); ?>_category" name="<?php echo($FormPrefix); ?>_category">
						<?php
						foreach ($EventCategories as $key => $category) {
							echo('<option value="'.$category['id'].'"'.($category['id'] == $EventInfo['category'] ? ' selected="selected"':'').'>');
							echo($category['name']);
							echo('</option>');
						}
						?>
					</select>
					
					<label for="<?php echo($FormPrefix); ?>_location">Location</label>
					<input type="text" id="<?php echo($FormPrefix); ?>_location" name="<?php echo($FormPrefix); ?>_location" value="<?php echo(htmlentities($EventInfo['location'], ENT_QUOTES, 'utf-8')); ?>" />
					
					<label for="<?php echo($FormPrefix); ?>_description">Description</label>
					<textarea rows="10" id="<?php echo($FormPrefix); ?>_description" name="<?php echo($FormPrefix); ?>_description"><?php echo(htmlentities($EventInfo['description'], ENT_QUOTES, 'utf-8')); ?></textarea>
					
				</fieldset>
			</div>
			<div class="BlueBox">
				<h2>Dates and Times</h2>
				<fieldset>
					<label for="<?php echo($FormPrefix); ?>_timeassociated">Time Associated</label>
					<input type="checkbox" onchange="SimpleCheckboxChange('<?php echo($FormPrefix); ?>_timeassociated', '<?php echo($FormPrefix); ?>_start_time', '<?php echo($FormPrefix); ?>_duration_time_div');" id="<?php echo($FormPrefix); ?>_timeassociated" name="<?php echo($FormPrefix); ?>_timeassociated"<?php if ($EventInfo['timeassociated']) { ?>  checked="yes"<?php } ?> />
					
					<label for="<?php echo($FormPrefix); ?>_start">Starts on</label>
					<?php
					DateSelectorGregorian(
						$FormPrefix.'_start',
						$FormPrefix.'_start',
						$EventInfo['start'],
						$now_year,
						$now_year+5);
					?>
					<?php
					DurationSelector(
						$FormPrefix.'_duration',
						$EventInfo['duration']);
					?>
					<label for="<?php echo($FormPrefix); ?>_recur_simple_enable">Use Recurrence</label>
					<input type="checkbox" onchange="MainRecurrenceToggle()" id="<?php echo($FormPrefix); ?>_recur_simple_enable" name="<?php echo($FormPrefix); ?>_recur_simple[enable]"<?php if (isset($SimpleRecur['enable'])) { ?>  checked="yes"<?php } ?> />
				</fieldset>
				<div id="<?php echo($FormPrefix); ?>_recurrence_div" style="display: block">
					<h2>Recurrence</h2>
					<fieldset>
						<label for="<?php echo($FormPrefix); ?>_recur_simple_freq">Recurrence Rule</label>
						<select onchange="CalSimpleFreqChange(), UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_freq" name="<?php echo($FormPrefix); ?>_recur_simple[freq]">
							<option value="daily"<?php if ($SimpleRecur['freq'] == 'daily') { ?> selected="yes"<?php } ?>>Daily</option>
							<option value="weekly"<?php if ($SimpleRecur['freq'] == 'weekly') { ?> selected="yes"<?php } ?>>Weekly</option>
							<option value="monthly"<?php if ($SimpleRecur['freq'] == 'monthly') { ?> selected="yes"<?php } ?>>Monthly</option>
							<option value="yearly"<?php if ($SimpleRecur['freq'] == 'yearly') { ?> selected="yes"<?php } ?>>Yearly</option>
						</select>
					</fieldset>
					
					<fieldset>
						<label for="<?php echo($FormPrefix); ?>_recur_simple_interval">Recur every</label>
						<input id="<?php echo($FormPrefix); ?>_recur_simple_interval" name="<?php echo($FormPrefix); ?>_recur_simple[interval]" type="text" size="3" value="<?php echo(isset($SimpleRecur['interval']) ? $SimpleRecur['interval'] : 1); ?>" />
						<div id="<?php echo($FormPrefix); ?>_recur_simple_interval_unit_daily" style="display: none">
							day(s)
						</div>
						<div id="<?php echo($FormPrefix); ?>_recur_simple_interval_unit_weekly" style="display: none">
							week(s)
						</div>
						<div id="<?php echo($FormPrefix); ?>_recur_simple_interval_unit_monthly" style="display: none">
							month(s)
						</div>
						<div id="<?php echo($FormPrefix); ?>_recur_simple_interval_unit_yearly" style="display: none">
							year(s)
						</div>
					</fieldset>
					
					<div id="<?php echo($FormPrefix); ?>_recur_simple_freq_weekly" style="display: block">
						<h3>Recur weekly</h3>
						<label>On days</label>
						<fieldset class="block-checks">
							<?php
								$day_names = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
								foreach ($day_names as $day_id => $day_name) {
									echo('<label><input onchange="UpdateRecurCalPreviewLoad();" type="checkbox"  id="'.$FormPrefix.'_recur_simple_weekly_byday_'.$day_id.'" name="'.$FormPrefix.'_recur_simple[weekly_byday]['.$day_id.']"');
									if (/*(*/isset($SimpleRecur['weekly_byday'][$day_id])/*) or
										((!isset($SimpleRecur['weekly_byday']) or empty($SimpleRecur['weekly_byday'])) and $EventInfo['start']['day'] == $day_id)*/)
									{
										echo(' checked="yes"');
									}
									echo(" />$day_name</label>\n");
								}
							?>
						</fieldset>
					</div>
					
					<div id="<?php echo($FormPrefix); ?>_recur_simple_freq_monthly" style="display: block">
						<h3>Recur monthly</h3>
						<fieldset>
							<label for="<?php echo($FormPrefix); ?>_recur_simple_monthly_method_monthday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="<?php echo($FormPrefix); ?>_recur_simple_monthly_method_monthday" name="<?php echo($FormPrefix); ?>_recur_simple[monthly_method]" value="monthday"<?php
								if (!isset($SimpleRecur['monthly_method']) or $SimpleRecur['monthly_method'] == 'monthday') {
									echo(' checked="yes"');
								}
								?> /> Recur on the</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_monthly_monthday_monthday" name="<?php echo($FormPrefix); ?>_recur_simple[monthly_monthday][monthday]">
								<?php
									foreach ($nth_set as $monthday => $monthdayname) {
										echo("<option value=\"$monthday\"");
										if ((isset($SimpleRecur['monthly_monthday']) and $SimpleRecur['monthly_monthday']['monthday'] == $monthday) or
											(!isset($SimpleRecur['monthly_monthday']) and $EventInfo['start']['monthday'] == $monthday))
										{
											echo(' selected="yes"');
										}
										echo(">$monthdayname</option>\n");
									}
								?>
							</select> day of the month.
						</fieldset>
						
						<fieldset>
							<label for="<?php echo($FormPrefix); ?>_recur_simple_monthly_method_weekday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="<?php echo($FormPrefix); ?>_recur_simple_monthly_method_weekday" name="<?php echo($FormPrefix); ?>_recur_simple[monthly_method]" value="weekday"<?php
								if (isset($SimpleRecur['monthly_method']) and $SimpleRecur['monthly_method'] == 'weekday') {
									echo(' checked="yes"');
								}
								?> /> On the</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_monthly_weekday_week" name="<?php echo($FormPrefix); ?>_recur_simple[monthly_weekday][week]">
								<?php
									foreach ($nth_set as $index => $nth) {
										if ($index <= 5 and $index >= -5) {
											echo("<option value=\"$index\"");
											if ((isset($SimpleRecur['monthly_weekday']) and $SimpleRecur['monthly_weekday']['week'] == $index) or
												(!isset($SimpleRecur['monthly_weekday']) and $EventInfo['start']['monthweek'] == $index))
											{
												echo(' selected="yes"');
											}
											echo(">$nth</option>\n");
										}
									}
								?>
							</select>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_monthly_weekday_day" name="<?php echo($FormPrefix); ?>_recur_simple[monthly_weekday][day]">
								<?php
									foreach ($daysofweek as $index => $day) {
										echo("<option value=\"$index\"");
										if ((isset($SimpleRecur['monthly_weekday']) and $SimpleRecur['monthly_weekday']['day'] == $index) or
											(!isset($SimpleRecur['monthly_weekday']) and $EventInfo['start']['day'] == $index))
										{
											echo(' selected="yes"');
										}
										echo(">$day</option>\n");
									}
								?>
							</select>
						</fieldset>
					</div>
					
					<div id="<?php echo($FormPrefix); ?>_recur_simple_freq_yearly" style="display: block">
						<h3>Recur yearly</h3>
						<fieldset>
							<label for="<?php echo($FormPrefix); ?>_recur_simple_yearly_method_monthday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_method_monthday" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_method]" value="monthday"<?php
								if (!isset($SimpleRecur['yearly_method']) or $SimpleRecur['yearly_method'] == 'monthday') {
									echo(' checked="yes"');
								}
								?> /> Recur on the</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_monthday_monthday" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_monthday][monthday]">
								<?php
									foreach ($nth_set as $monthday => $monthdayname) {
										echo("<option value=\"$monthday\"");
										if ((isset($SimpleRecur['yearly_monthday']) and $SimpleRecur['yearly_monthday']['monthday'] == $monthday) or
											(!isset($SimpleRecur['yearly_monthday']) and $EventInfo['start']['monthday'] == $monthday))
										{
											echo(' selected="yes"');
										}
										echo(">$monthdayname</option>\n");
									}
								?>
							</select>
							
							<label for="<?php echo($FormPrefix); ?>_recur_simple_yearly_monthday_month">day of</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_monthday_month" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_monthday][month]">
								<?php
									foreach ($months as $month => $monthname) {
										echo("<option value=\"$month\"");
										if ((isset($SimpleRecur['yearly_monthday']) and $SimpleRecur['yearly_monthday']['month'] == $month) or
											(!isset($SimpleRecur['yearly_monthday']) and $EventInfo['start']['month'] == $month))
										{
											echo(' selected="yes"');
										}
										echo(">$monthname</option>\n");
									}
								?>
							</select>
						</fieldset>
						
						<fieldset>
							<label for="<?php echo($FormPrefix); ?>_recur_simple_yearly_method_weekday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_method_weekday" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_method]" value="weekday"<?php
								if (isset($SimpleRecur['yearly_method']) and $SimpleRecur['yearly_method'] == 'weekday') {
									echo(' checked="yes"');
								}
								?> /> Recur on the</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_weekday_week" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_weekday][week]">
								<?php
									foreach ($nth_set as $index => $nth) {
										if ($index <= 5 and $index >= -5) {
											echo("<option value=\"$index\"");
											if ((isset($SimpleRecur['yearly_weekday']) and $SimpleRecur['yearly_weekday']['week'] == $index) or
												(!isset($SimpleRecur['yearly_weekday']) and $EventInfo['start']['monthweek'] == $index))
											{
												echo(' selected="yes"');
											}
											echo(">$nth</option>\n");
										}
									}
								?>
							</select>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_weekday_day" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_weekday][day]">
								<?php
									foreach ($daysofweek as $index => $day) {
										if (is_numeric($index)) {
											echo("<option value=\"$index\"");
											if ((isset($SimpleRecur['yearly_weekday']) and $SimpleRecur['yearly_weekday']['day'] == $index) or
												(!isset($SimpleRecur['yearly_weekday']) and $EventInfo['start']['day'] == $index))
											{
												echo(' selected="yes"');
											}
											echo(">$day</option>\n");
										}
									}
								?>
							</select>
							
							<label>in</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_weekday_month" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_weekday][month]">
								<?php
									foreach ($months as $month => $monthname) {
										echo("<option value=\"$month\"");
										if ((isset($SimpleRecur['yearly_weekday']) and $SimpleRecur['yearly_weekday']['month'] == $month) or
											(!isset($SimpleRecur['yearly_weekday']) and $EventInfo['start']['month'] == $month))
										{
											echo(' selected="yes"');
										}
										echo(">$monthname</option>\n");
									}
								?>
							</select>
						</fieldset>
						
						<fieldset>
							<label for="<?php echo($FormPrefix); ?>_recur_simple_yearly_method_yearday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_method_yearday" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_method]" value="yearday"<?php
								if (isset($SimpleRecur['yearly_method']) and $SimpleRecur['yearly_method'] == 'yearday') {
									echo(' checked="yes"');
								}
								?> /> Recur on day</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="<?php echo($FormPrefix); ?>_recur_simple_yearly_yearday_yearday" name="<?php echo($FormPrefix); ?>_recur_simple[yearly_yearday][yearday]">
								<?php
									for ($i = 1; $i <= 366; ++$i) {
										echo('<option value="'. $i .'"');
										if ((isset($SimpleRecur['yearly_yearday']) and $SimpleRecur['yearly_yearday']['yearday'] == $i) or
											(!isset($SimpleRecur['yearly_yearday']) and $EventInfo['start']['yearday'] == $i))
										{
											echo(' selected="yes"');
										}
										echo(">$i</option>\n");
									}
								?>
							</select>
							of the year.
						</fieldset>
					</div>
					
					<div id="<?php echo($FormPrefix); ?>_recur_simple_range">
						<h3>Recurrence range</h3>
						
						<fieldset>
							<label for="<?php echo($FormPrefix); ?>_recur_simple_range_method_noend"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="<?php echo($FormPrefix); ?>_recur_simple_range_method_noend" name="<?php echo($FormPrefix); ?>_recur_simple[range_method]" value="noend"<?php
									if (!isset($SimpleRecur['range_method']) || $SimpleRecur['range_method'] === 'noend') {
										echo(' checked="yes"');
									}
								?> />No end</label>
						</fieldset>
						
						<fieldset>
							<label for="<?php echo($FormPrefix); ?>_recur_simple_range_method_count"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="<?php echo($FormPrefix); ?>_recur_simple_range_method_count" name="<?php echo($FormPrefix); ?>_recur_simple[range_method]" value="count"<?php
									if (isset($SimpleRecur['range_method']) && $SimpleRecur['range_method'] === 'count') {
										echo(' checked="yes"');
									}
								?> />End after</label>
							<input type="text" id="<?php echo($FormPrefix); ?>_recur_simple_count" name="<?php echo($FormPrefix); ?>_recur_simple[count]" value="<?php echo(isset($SimpleRecur['count']) ? $SimpleRecur['count'] : 1); ?>" size="5" /> Occurrences
						</fieldset>
						
						<fieldset>
							<label for="<?php echo($FormPrefix); ?>_recur_simple_range_method_until"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="<?php echo($FormPrefix); ?>_recur_simple_range_method_until" name="<?php echo($FormPrefix); ?>_recur_simple[range_method]" value="until"<?php
									if (isset($SimpleRecur['range_method']) && $SimpleRecur['range_method'] === 'until') {
										echo(' checked="yes"');
										$until = $SimpleRecur['until'];
									} else {
										$until = array(
											'monthday' => $EventInfo['start']['monthday'],
											'month'    => $EventInfo['start']['month'],
											'year'     => $EventInfo['start']['year'],
										);
									}
								?> />End on</label>
							<?php
							DateSelectorGregorian($FormPrefix.'_recur_simple_until', $FormPrefix.'_recur_simple[until]', $until, $now_year, $now_year+5);
							?>
						</fieldset><?php /*
						<p><small><em>Advanced reccurrence tools will be made available at a later date.</em></small></p> */ ?>
					</div>
				</div>
				
				<?php if (0) { ?>
				<h2>Include/Exclude Dates</h2>
				<div class="cal_incex_date">
					
				</div>
				<?php } ?>
			</div>
			<div class="BlueBox">
				<fieldset>
					<input class="button" type="submit" name="<?php echo($FormPrefix); ?>_save" value="Save" />
					<input class="button" type="submit" name="<?php echo($FormPrefix); ?>_return" value="Cancel" />
				</fieldset>
			</div>
			<script type="text/javascript">
				CalSimpleFreqChange();
				hide("recurrences_preview_noscript");
				show("recurrences_preview");
				MainRecurrenceToggle();
				SimpleCheckboxChange('<?php echo($FormPrefix); ?>_timeassociated', '<?php echo($FormPrefix); ?>_start_time', '<?php echo($FormPrefix); ?>_duration_time_div');
			</script>
		</form>
		<?php
		// Attendee list
		if (isset($Attendees) && !empty($Attendees)) {
			echo('<h2>Attendees</h2>');
			echo('<ul>');
			foreach (array(true,false) as $friend) {
				foreach ($Attendees as $attendee) {
					if ($attendee['friend'] === $friend) {
						echo('<li>');
						$linked = array_key_exists('link', $attendee);
						if ($attendee['friend']) {
							echo('<b>');
						}
						if ($linked) {
							echo('<a href="'.$attendee['link'].'" target="_blank">');
						}
						echo($attendee['name']);
						if ($linked) {
							echo('</a>');
						}
						if ($attendee['friend']) {
							echo('</b>');
						}
						echo(' '.$attendee['attend'].'</li>');
					}
				}
			}
			echo('</ul>');
		}
		?>
	</div>
</div>