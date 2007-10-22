<?php

/**
 * @file views/calendar/event_edit.php
 * @brief View for editing event information.
 *
 * @param $ExtraFormData array Extra form data array.
 * @param $Event CalendarEvent Event information.
 * @param $EventInfo Array with: 'summary', 'description', 'start', 'duration' etc
 * @param $SimpleRecur Array Simple recurrence information.
 * @param $InExDates Array Include/Exclude date information.
 * @param $ReadOnly bool Whether the source is read only.
 * @param $Attendees array[string] Attending users.
 * @param $FailRedirect string URL fail redirect path.
 * @param $EventCategories array[id => array('id'=>,'name'=>,'colour'=>)]
 * @param $Help Help text array, indexed by title.
 * @param $Confirms Changes to be confirmed.
 * @param $CanPublish bool Whether the user can publish this event.
 * @param $Create bool whether the event is new.
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

$confirm_types = array(
	'cancel'  => array(
		'description' => 'This occurrence is currently published and will be cancelled.',
		'descriptions' => 'These occurrences are currently published and will be cancelled.',
	),
	'delete'  => array(
		'description' => 'This occurrences will be deleted.',
		'descriptions' => 'These occurrences will be deleted.',
		'description_pub' => 'This occurrences is not published and will be deleted.',
		'descriptions_pub' => 'These occurrences are not published and will be deleted.',
	),
	'move'    => array(
		'description' => 'The time and/or duration of this occurrences will be altered.',
		'descriptions' => 'The times and/or durations of these occurrences will be altered.',
	),
	'restore' => array(
		'description' => 'This occurrence is currently cancelled and will be restored.',
		'descriptions' => 'These occurrences are currently cancelled and will be restored.',
	),
	'create' => array(
		'description' => 'This occurrence will be created.',
		'descriptions' => 'These occurrences will be created.',
		'description_pub' => 'This occurrences will be created. You can publish it now if you wish by selecting it.',
		'descriptions_pub' => 'These occurrences will be created. You can publish some now if you wish by selecting them.',
		'checkbox' => 'publish',
	),
	'draft' => array(
		'description' => 'This occurrence is a draft. You can publish it now if you wish by selecting it.',
		'descriptions' => 'These occurrences are drafts. You can publish some now if you wish by selecting them.',
		'checkbox' => 'publish',
	),
);

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
function DateSelectorGregorian($id, $name, $default, $minyear, $maxyear, $onchange = NULL)
{
	global $nth_set;
	global $months;
?><select<?php if (NULL !== $onchange) { echo(" onchange=\"$onchange\""); } ?> id="<?php echo($id); ?>_monthday" name="<?php echo($name); ?>[monthday]">
		<?php
			foreach ($nth_set as $monthday => $monthdayname) {
				if ($monthday > 0) {
					echo("<option value=\"$monthday\"");
					if ($default['monthday'] == $monthday) {
						echo(' selected="selected"');
					}
					echo(">$monthdayname</option>\n");
				}
			}
		?>
	</select>
	<select<?php if (NULL !== $onchange) { echo(" onchange=\"$onchange\""); } ?> id="<?php echo($id); ?>_month" name="<?php echo($name); ?>[month]">
		<?php
			foreach ($months as $month => $monthname) {
				echo("<option value=\"$month\"");
				if ($default['month'] == $month) {
					echo(' selected="selected"');
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
	<select<?php if (NULL !== $onchange) { echo(" onchange=\"$onchange\""); } ?> id="<?php echo($id); ?>_year" name="<?php echo($name); ?>[year]">
		<?php
			if ($default['year'] < $minyear) {
				echo('<option value="'.$default['year'].'" selected="selected">'.$default['year'].'</option>');
			}
			for ($year = $minyear; $year < $maxyear; ++$year) {
				echo("<option value=\"$year\"");
				if ($default['year'] == $year) {
					echo(' selected="selected"');
				}
				echo(">$year</option>");
			}
			if ($default['year'] >= $year) {
				echo('<option value="'.$default['year'].'" selected="selected">'.$default['year'].'</option>');
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
						echo(' selected="selected"');
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
				echo(' selected="selected"');
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
						echo(' selected="selected"');
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
			<?php get_instance()->load->view('calendar/minicalendar', array('Onclick' => 'MinicalToggle')); ?>
		</div>
	</div>
</div>
<div id="MainColumn">
	<div>
		<form class="form" method="post" action="<?php echo(get_instance()->uri->uri_string()); ?>">
			<?php
	if (isset($Confirms) && isset($Confirms['draft']) && !$CanPublish) {
		unset($Confirms['draft']);
		if (empty($Confirms)) {
			unset($Confirms);
		}
	}
	if (isset($Confirms)) { ?>
			<div id="confirm_changes_div" class="BlueBox">
				<h2>Confirm changes</h2>
	<?php if (count($Confirms) == 1 && isset($Confirms['draft'])) { ?>
				<p>	This event has one or more draft occurrences which are not
					publicly visible. You can publish some of them now if you
					wish. </p>
	<?php } elseif (!$Create) { ?>
				<p>	The following changes will be made to your event. Please
					confirm that they are correct. <p>
				<p>	<em>Occurrences which are in the past will
					<strong>not</strong> be altered.</em> </p>
	<?php } else { ?>
				<p>	The following changes will be made to your new event. Please
					confirm that they are correct. <p>
	<?php } ?>
				<?php
				foreach ($confirm_types as $class => $class_info) {
					if (isset($Confirms[$class])) { ?>
						<div id="confirm_changes_<?php echo($class); ?>_div">
							<h3><?php echo(ucfirst($class)); ?> occurrences</h3>
							<p><em><?php
	// Get the description key based on plurality and publishability
	$description_key = 'description';
	if (count($Confirms[$class]) > 1) {
		$description_key .= 's';
	}
	if ($CanPublish && isset($confirm_types[$class][$description_key.'_pub'])) {
		$description_key .= '_pub';
	}
	echo($confirm_types[$class][$description_key]);
							?></em></p>
							<?php foreach ($Confirms[$class] as $id => $confirm_occ) { ?>
							<div id="eved_confirm_<?php echo($class); ?>_list">
								<div class="<?php echo($class); ?>"><?php
	// Description as label or div tag
	$show_checkbox = $CanPublish && isset($confirm_types[$class]['checkbox']);
	$description_tag_type = $show_checkbox ? 'label' : 'div';
	echo("<$description_tag_type class=\"description\">");
	// Show a checkbox if necessary
	if ($show_checkbox) {
		$checkbox_id = 'eved_confirm_'.$class.'_'.$confirm_types[$class]['checkbox'].'_'.$confirm_occ['day'];
		$checkbox_name = 'eved_confirm['.$class.'_'.$confirm_types[$class]['checkbox'].']['.$confirm_occ['day'].']';
		echo('<input id="'.$checkbox_id.'" name="'.$checkbox_name.'" type="checkbox" />');
	}

	// Display the date and time of the occurrence
	$ts = $confirm_occ['start_time'];
	$atime = new Academic_time($ts);
	echo(date('l, j',$ts));                // DAY MONTHDAY
	echo('<sup>'.date('S',$ts).'</sup> '); // <sup>NTH</sup>
	echo(date('F Y',$ts));                 // MONTH YEAR
	echo(' at '.$atime->Format('%T'));     // at TIME
	// Show simplistic changes to the time
	if (isset($confirm_occ['new_start_time'])) {
		if ($confirm_occ['new_start_time'] != $confirm_occ['start_time']) {
			$new_atime = new Academic_time($confirm_occ['new_start_time']);
			echo(' (moving to <strong>'.$new_atime->Format('%T').'</strong>)');
		} elseif ($confirm_occ['new_end_time'] != $confirm_occ['end_time']) {
			$new_atime = new Academic_time($confirm_occ['new_end_time']);
			echo(' (ending <strong>'.$new_atime->Format('%T').'</strong>)');
		}
	}

	// close the description tag
	echo("</$description_tag_type>");
								?></div>
							</div>
							<?php } ?>
						</div>
						<?php
					}
				} ?>
				<fieldset>
					<input type="submit" class="button" id="eved_confirm_edit_btn" name="eved_confirm[edit_btn]" onclick="document.getElementById('main_edit_divs').style.display='inline'; document.getElementById('confirm_changes_div').style.display='none'; return false;" value="Make further changes" />
					<input type="submit" class="button" name="eved_confirm[confirm_btn]" value="Confirm changes" />
				</fieldset>
			</div>
			<?php } ?>
			<div id="main_edit_divs" style="display:<?php echo(isset($Confirms) ? 'none' : 'block'); ?>;">
			<div class="BlueBox">
				<input type="hidden" id="prefix" name="prefix" value="eved" />
				<?php if (isset($ExtraFormData)) foreach ($ExtraFormData as $key => $value) {
?>				<input type="hidden" id="<?php echo('eved_'.$key); ?>" name="<?php echo('eved_'.$key); ?>" value="<?php echo(htmlentities($value, ENT_QUOTES, 'utf-8')); ?>" />
				<?php } ?>
				<h2>Event</h2>
				<fieldset>
					<label for="eved_summary">Summary</label>
					<input type="text" id="eved_summary" name="eved_summary" value="<?php echo(htmlentities($EventInfo['summary'], ENT_QUOTES, 'utf-8')); ?>" />
					
					<label for="eved_category">Category</label>
					<select id="eved_category" name="eved_category">
						<?php
						foreach ($EventCategories as $key => $category) {
							echo('<option value="'.$category['id'].'"'.($category['id'] == $EventInfo['category'] ? ' selected="selected"':'').'>');
							echo($category['name']);
							echo('</option>');
						}
						?>
					</select>
					
					<label for="eved_location">Location</label>
					<input type="text" id="eved_location" name="eved_location" value="<?php echo(htmlentities($EventInfo['location'], ENT_QUOTES, 'utf-8')); ?>" />
					
					<label for="eved_description">Description</label>
					<textarea rows="10" cols="20" id="eved_description" name="eved_description"><?php echo(htmlentities($EventInfo['description'], ENT_QUOTES, 'utf-8')); ?></textarea>
					
				</fieldset>
			</div>
			<div class="BlueBox">
				<h2>Dates and Times</h2>
				<p>Set the date and time of the first occurrence of this event:</p>
				<fieldset>
					<label for="eved_timeassociated">Time Associated</label>
					<input type="checkbox" onchange="SimpleCheckboxChange('eved_timeassociated', 'eved_start_time', 'eved_duration_time_div');" id="eved_timeassociated" name="eved_timeassociated"<?php if ($EventInfo['timeassociated']) { ?>  checked="checked"<?php } ?> />
					
					<label for="eved_start_monthday">Starts on</label>
					<?php
					DateSelectorGregorian(
						'eved_start',
						'eved_start',
						$EventInfo['start'],
						$now_year,
						$now_year+5,
						'UpdateRecurCalPreviewLoad();');
					?>
					<?php
					DurationSelector(
						'eved_duration',
						$EventInfo['duration']);
					?>
					<label for="eved_recur_simple_enable">Use Recurrence</label>
					<input type="checkbox" onchange="MainRecurrenceToggle()" id="eved_recur_simple_enable" name="eved_recur_simple[enable]"<?php if (isset($SimpleRecur['enable'])) { ?>  checked="checked"<?php } ?> />
				</fieldset>

				<div id="eved_recurrence_div" style="display: block">
					<h2>Recurrence</h2>
					<p>Set a recurrence rule to determine when the event is repeated.</p>
					<div id="recur_use_minical_nonjs" style="display:block"><p>Remember to select <em>Use Recurrence</em> above to use this section.</p></div>
					<div id="recur_use_minical_js" style="display:none"><p>Use the calendar on the right of this page to see a preview of when occurrences will be.</p></div>
					<fieldset>
						<label for="eved_recur_simple_freq">Recurrence Rule</label>
						<select onchange="CalSimpleFreqChange(), UpdateRecurCalPreviewLoad();" id="eved_recur_simple_freq" name="eved_recur_simple[freq]">
							<option value="daily"<?php if ($SimpleRecur['freq'] == 'daily') { ?> selected="selected"<?php } ?>>Daily</option>
							<option value="weekly"<?php if ($SimpleRecur['freq'] == 'weekly') { ?> selected="selected"<?php } ?>>Weekly</option>
							<option value="monthly"<?php if ($SimpleRecur['freq'] == 'monthly') { ?> selected="selected"<?php } ?>>Monthly</option>
							<option value="yearly"<?php if ($SimpleRecur['freq'] == 'yearly') { ?> selected="selected"<?php } ?>>Yearly</option>
						</select>
					</fieldset>
					
					<fieldset>
						<label for="eved_recur_simple_interval">Recur every</label>
						<input id="eved_recur_simple_interval" name="eved_recur_simple[interval]" type="text" size="3" value="<?php echo(isset($SimpleRecur['interval']) ? $SimpleRecur['interval'] : 1); ?>" />
						<div id="eved_recur_simple_interval_unit_daily" style="display: none">
							day(s)
						</div>
						<div id="eved_recur_simple_interval_unit_weekly" style="display: none">
							week(s)
						</div>
						<div id="eved_recur_simple_interval_unit_monthly" style="display: none">
							month(s)
						</div>
						<div id="eved_recur_simple_interval_unit_yearly" style="display: none">
							year(s)
						</div>
					</fieldset>
					
					<div id="eved_recur_simple_freq_weekly" style="display: block">
						<h3>Recur weekly</h3>
						<label>On days</label>
						<fieldset class="block-checks">
							<?php
								$day_names = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
								foreach ($day_names as $day_id => $day_name) {
									echo('<label><input onchange="UpdateRecurCalPreviewLoad();" type="checkbox"  id="eved_recur_simple_weekly_byday_'.$day_id.'" name="eved_recur_simple[weekly_byday]['.$day_id.']"');
									if (/*(*/isset($SimpleRecur['weekly_byday'][$day_id])/*) or
										((!isset($SimpleRecur['weekly_byday']) or empty($SimpleRecur['weekly_byday'])) and $EventInfo['start']['day'] == $day_id)*/)
									{
										echo(' checked="checked"');
									}
									echo(" />$day_name</label>\n");
								}
							?>
						</fieldset>
					</div>
					
					<div id="eved_recur_simple_freq_monthly" style="display: block">
						<h3>Recur monthly</h3>
						<fieldset>
							<label for="eved_recur_simple_monthly_method_monthday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="eved_recur_simple_monthly_method_monthday" name="eved_recur_simple[monthly_method]" value="monthday"<?php
								if (!isset($SimpleRecur['monthly_method']) or $SimpleRecur['monthly_method'] == 'monthday') {
									echo(' checked="checked"');
								}
								?> /> Recur on the</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_monthly_monthday_monthday" name="eved_recur_simple[monthly_monthday][monthday]">
								<?php
									foreach ($nth_set as $monthday => $monthdayname) {
										echo("<option value=\"$monthday\"");
										if ((isset($SimpleRecur['monthly_monthday']) and $SimpleRecur['monthly_monthday']['monthday'] == $monthday) or
											(!isset($SimpleRecur['monthly_monthday']) and $EventInfo['start']['monthday'] == $monthday))
										{
											echo(' selected="selected"');
										}
										echo(">$monthdayname</option>\n");
									}
								?>
							</select> day of the month.
						</fieldset>
						
						<fieldset>
							<label for="eved_recur_simple_monthly_method_weekday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="eved_recur_simple_monthly_method_weekday" name="eved_recur_simple[monthly_method]" value="weekday"<?php
								if (isset($SimpleRecur['monthly_method']) and $SimpleRecur['monthly_method'] == 'weekday') {
									echo(' checked="checked"');
								}
								?> /> On the</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_monthly_weekday_week" name="eved_recur_simple[monthly_weekday][week]">
								<?php
									foreach ($nth_set as $index => $nth) {
										if ($index <= 5 and $index >= -5) {
											echo("<option value=\"$index\"");
											if ((isset($SimpleRecur['monthly_weekday']) and $SimpleRecur['monthly_weekday']['week'] == $index) or
												(!isset($SimpleRecur['monthly_weekday']) and $EventInfo['start']['monthweek'] == $index))
											{
												echo(' selected="selected"');
											}
											echo(">$nth</option>\n");
										}
									}
								?>
							</select>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_monthly_weekday_day" name="eved_recur_simple[monthly_weekday][day]">
								<?php
									foreach ($daysofweek as $index => $day) {
										echo("<option value=\"$index\"");
										if ((isset($SimpleRecur['monthly_weekday']) and $SimpleRecur['monthly_weekday']['day'] == $index) or
											(!isset($SimpleRecur['monthly_weekday']) and $EventInfo['start']['day'] == $index))
										{
											echo(' selected="selected"');
										}
										echo(">$day</option>\n");
									}
								?>
							</select>
						</fieldset>
					</div>
					
					<div id="eved_recur_simple_freq_yearly" style="display: block">
						<h3>Recur yearly</h3>
						<fieldset>
							<label for="eved_recur_simple_yearly_method_monthday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="eved_recur_simple_yearly_method_monthday" name="eved_recur_simple[yearly_method]" value="monthday"<?php
								if (!isset($SimpleRecur['yearly_method']) or $SimpleRecur['yearly_method'] == 'monthday') {
									echo(' checked="checked"');
								}
								?> /> Recur on the</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_yearly_monthday_monthday" name="eved_recur_simple[yearly_monthday][monthday]">
								<?php
									foreach ($nth_set as $monthday => $monthdayname) {
										echo("<option value=\"$monthday\"");
										if ((isset($SimpleRecur['yearly_monthday']) and $SimpleRecur['yearly_monthday']['monthday'] == $monthday) or
											(!isset($SimpleRecur['yearly_monthday']) and $EventInfo['start']['monthday'] == $monthday))
										{
											echo(' selected="selected"');
										}
										echo(">$monthdayname</option>\n");
									}
								?>
							</select>
							
							<label for="eved_recur_simple_yearly_monthday_month">day of</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_yearly_monthday_month" name="eved_recur_simple[yearly_monthday][month]">
								<?php
									foreach ($months as $month => $monthname) {
										echo("<option value=\"$month\"");
										if ((isset($SimpleRecur['yearly_monthday']) and $SimpleRecur['yearly_monthday']['month'] == $month) or
											(!isset($SimpleRecur['yearly_monthday']) and $EventInfo['start']['month'] == $month))
										{
											echo(' selected="selected"');
										}
										echo(">$monthname</option>\n");
									}
								?>
							</select>
						</fieldset>
						
						<fieldset>
							<label for="eved_recur_simple_yearly_method_weekday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="eved_recur_simple_yearly_method_weekday" name="eved_recur_simple[yearly_method]" value="weekday"<?php
								if (isset($SimpleRecur['yearly_method']) and $SimpleRecur['yearly_method'] == 'weekday') {
									echo(' checked="checked"');
								}
								?> /> Recur on the</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_yearly_weekday_week" name="eved_recur_simple[yearly_weekday][week]">
								<?php
									foreach ($nth_set as $index => $nth) {
										if ($index <= 5 and $index >= -5) {
											echo("<option value=\"$index\"");
											if ((isset($SimpleRecur['yearly_weekday']) and $SimpleRecur['yearly_weekday']['week'] == $index) or
												(!isset($SimpleRecur['yearly_weekday']) and $EventInfo['start']['monthweek'] == $index))
											{
												echo(' selected="selected"');
											}
											echo(">$nth</option>\n");
										}
									}
								?>
							</select>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_yearly_weekday_day" name="eved_recur_simple[yearly_weekday][day]">
								<?php
									foreach ($daysofweek as $index => $day) {
										if (is_numeric($index)) {
											echo("<option value=\"$index\"");
											if ((isset($SimpleRecur['yearly_weekday']) and $SimpleRecur['yearly_weekday']['day'] == $index) or
												(!isset($SimpleRecur['yearly_weekday']) and $EventInfo['start']['day'] == $index))
											{
												echo(' selected="selected"');
											}
											echo(">$day</option>\n");
										}
									}
								?>
							</select>
							
							<label>in</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_yearly_weekday_month" name="eved_recur_simple[yearly_weekday][month]">
								<?php
									foreach ($months as $month => $monthname) {
										echo("<option value=\"$month\"");
										if ((isset($SimpleRecur['yearly_weekday']) and $SimpleRecur['yearly_weekday']['month'] == $month) or
											(!isset($SimpleRecur['yearly_weekday']) and $EventInfo['start']['month'] == $month))
										{
											echo(' selected="selected"');
										}
										echo(">$monthname</option>\n");
									}
								?>
							</select>
						</fieldset>
						
						<fieldset>
							<label for="eved_recur_simple_yearly_method_yearday"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="eved_recur_simple_yearly_method_yearday" name="eved_recur_simple[yearly_method]" value="yearday"<?php
								if (isset($SimpleRecur['yearly_method']) and $SimpleRecur['yearly_method'] == 'yearday') {
									echo(' checked="checked"');
								}
								?> /> Recur on day</label>
							<select onchange="UpdateRecurCalPreviewLoad();" id="eved_recur_simple_yearly_yearday_yearday" name="eved_recur_simple[yearly_yearday][yearday]">
								<?php
									for ($i = 1; $i <= 366; ++$i) {
										echo('<option value="'. $i .'"');
										if ((isset($SimpleRecur['yearly_yearday']) and $SimpleRecur['yearly_yearday']['yearday'] == $i) or
											(!isset($SimpleRecur['yearly_yearday']) and $EventInfo['start']['yearday'] == $i))
										{
											echo(' selected="selected"');
										}
										echo(">$i</option>\n");
									}
								?>
							</select>
							of the year.
						</fieldset>
					</div>
					
					<div id="eved_recur_simple_range">
						<h3>Recurrence range</h3>
						
						<fieldset>
							<label for="eved_recur_simple_range_method_noend"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="eved_recur_simple_range_method_noend" name="eved_recur_simple[range_method]" value="noend"<?php
									if (!isset($SimpleRecur['range_method']) || $SimpleRecur['range_method'] === 'noend') {
										echo(' checked="checked"');
									}
								?> />No end</label>
						</fieldset>
						
						<fieldset>
							<label for="eved_recur_simple_range_method_count"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="eved_recur_simple_range_method_count" name="eved_recur_simple[range_method]" value="count"<?php
									if (isset($SimpleRecur['range_method']) && $SimpleRecur['range_method'] === 'count') {
										echo(' checked="checked"');
									}
								?> />End after</label>
							<input type="text" id="eved_recur_simple_count" name="eved_recur_simple[count]" value="<?php echo(isset($SimpleRecur['count']) ? $SimpleRecur['count'] : 1); ?>" size="5" /> Occurrences
						</fieldset>
						
						<fieldset>
							<label for="eved_recur_simple_range_method_until"><input onchange="UpdateRecurCalPreviewLoad();" type="radio" id="eved_recur_simple_range_method_until" name="eved_recur_simple[range_method]" value="until"<?php
									if (isset($SimpleRecur['range_method']) && $SimpleRecur['range_method'] === 'until') {
										echo(' checked="checked"');
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
							DateSelectorGregorian('eved_recur_simple_until', 'eved_recur_simple[until]', $until, $now_year, $now_year+5, 'UpdateRecurCalPreviewLoad();');
							?>
						</fieldset><?php /*
						<p><small><em>Advanced reccurrence tools will be made available at a later date.</em></small></p> */ ?>
					</div>
				</div>
				
				<?php if (1) { ?>
					<h2>Extra Dates</h2>
					<p>Set the dates of any other occurrences of the event below.</p>
					<div id="incex_jsonly" style="display:none">
						<p><em>Tip: You can click on the calendar in the sidebar to specify dates.</em></p>
					</div>
					<p>If you are using recurrence, you can use the &quot;Exclude Date&quot; button
						to specify exceptions to the recurrence rule.</p>
					<div>
						<fieldset>
							<label>New Date</label>
							<?php
							DateSelectorGregorian('eved_new_inex_date', 'eved_inex[new_date]', array(
									'monthday' => 1,
									'month' => 11,
									'year' => 2007
								), 2007, 2010);
							?>
						</fieldset>
						<fieldset>
							<input class="button" type="submit" id="eved_inex_add_exclude" name="eved_inex[add_exclude]" onclick="return NewInexDate('exclude', DateSelectToDateString('eved_new_inex_date'));" value="Exclude Date" />
							<input class="button" type="submit" id="eved_inex_add_include" name="eved_inex[add_include]" onclick="return NewInexDate('include', DateSelectToDateString('eved_new_inex_date'));" value="Add Date" /><?php
							// Useful for testing coz with buttons it does non-js way when js fails:
							/*<a onclick="return NewInexDate('include', DateSelectToDateString('eved_new_inex_date'));">include</a>
							<a onclick="return NewInexDate('exclude', DateSelectToDateString('eved_new_inex_date'));">exclude</a>*/
							?>
						</fieldset>
					</div>
					<?php /* Hidden templates for when creating new dates with js */ ?>
					<div id="eved_inex_hidden" style="display:none">
						<div id="eved_include_template" class="include">
							<div class="description" id="eved_include_description">include: </div>
							<fieldset class="delete">
								<input type="hidden" name="include" value="DATE" />
								<input class="button" type="submit" name="include_remove_btn" value="Delete" />
							</fieldset>
						</div>
						<div id="eved_exclude_template" class="exclude">
							<div class="description" id="eved_exclude_description">exclude: </div>
							<fieldset class="delete">
								<input type="hidden" name="exclude" value="DATE" />
								<input class="button" type="submit" name="exclude_remove_btn" value="Delete" />
							</fieldset>
						</div>
					</div>
					<div id="eved_inex_list">
						<?php
							$any_inex_dates =	(isset($InExDates['includes']) && !empty($InExDates['includes'])) ||
												(isset($InExDates['excludes']) && !empty($InExDates['excludes']));
						?>
						<div id="eved_inex_none" class="none" style="display:<?php echo($any_inex_dates?'none':'block'); ?>">
							<div class="description"><em>There are no dates specified except the first occurrence and those from recurrence if used.</em></div>
						</div>
						<?php
						foreach (array('include','exclude') as $inex) {
							$inexes = $inex.'s';
							if (isset($InExDates[$inexes])) {
								foreach ($InExDates[$inexes] as $date => $dummydate) {
									$ts = strtotime($date);
							?><div id="eved_inex_date_<?php echo($inex.'_'.$date); ?>" class="<?php echo($inex); ?>">
								<div class="description" id="eved_<?php echo($inex); ?>_description_<?php echo($date); ?>"><?php echo(
									$inex.': '.date('l, j',$ts).'<sup>'.date('S',$ts).'</sup> '.date('F Y',$ts)
								) ?></div>
								<fieldset class="delete">
									<input type="hidden" name="eved_inex[<?php echo($inexes.']['.$date); ?>]" value="<?php echo($date); ?>" />
									<input class="button" type="submit" name="eved_inex[<?php echo($inex); ?>_remove_btns][<?php echo($date); ?>]" value="Delete" onclick="return RemoveInexDate('<?php echo($inex); ?>','<?php echo($date); ?>');" />
								</fieldset>
							</div>
									<?php
								}
							}
						}
						?>
					</div>
				<?php } ?>
			</div>
			<div class="BlueBox">
				<fieldset>
					<input class="button" type="submit" name="eved_save" value="Save" />
					<input class="button" type="submit" name="eved_return" value="Cancel" />
				</fieldset>
			</div>
			</div>
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
