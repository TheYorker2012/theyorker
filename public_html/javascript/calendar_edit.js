// Javascript for event edit page
// Author: James Hogan (james at albanarts dot com)
// Copyright (C) 2007 The Yorker
// Depends on javascript/css_classes.js

// History:
//  * initial commit 17th Oct 2007

// Manages:
//  * ajax recurrence updating
//  * include exclude dates
//  * minicalendar integration

/// Quick date functions.
var date_month_names = new Array(
	'January','February','March','April','May','June',
	'July','August','September','October','November','December'
);
function DateMonthName(date)
{
	return date_month_names[date.getMonth()];
}

var date_day_names = new Array(
	'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'
);
function DateDayName(date)
{
	return date_day_names[date.getDay()];
}

function DateMonthDayNth(date)
{
	switch (date.getDate()) {
		case 1:
		case 21:
		case 31:
			return 'st';
			break;
		case 2:
		case 22:
			return 'nd';
			break;
		case 3:
		case 23:
			return 'rd';
			break;
		default:
			return 'th';
			break;
	};
}


/// Validate url.
var url_ajax_recur_validate = "/calendar/ajax/recursimplevalidate";
/// Validate url setter.
function SetValidatePath(path)
{
	url_ajax_recur_validate = path;
}

/// show or hide affected1 and affected2 depending on state of checkbox.
function SimpleCheckboxChange(checkbox, affected1, affected2)
{
	var repeat_input = document.getElementById(checkbox);
	if (repeat_input.checked) {
		document.getElementById(affected1).style.display = "none";
		document.getElementById(affected2).style.display = "none";
	} else {
		document.getElementById(affected1).style.display = "block";
		document.getElementById(affected2).style.display = "block";
	}
}

/// Make element eved_recur_$index_$type_div visible based on checkbox eved_recur_$index_$type_use.
function CheckboxChange(type, index)
{
	var repeat_input = document.getElementById('eved_recur_'+index+'_'+type+'_use');
	document.getElementById('eved_recur_'+index+'_'+type+'_div').style.display = repeat_input.checked ? "block" : "none";
	/// @todo remove multiple references when stuff changes!!!
	UpdateRecurCalPreviewLoad();
}

/// Toggle the main recurrence switch
function MainRecurrenceToggle()
{
	var repeat_input = document.getElementById('eved_recur_simple_enable');
	document.getElementById('eved_recurrence_div').style.display = repeat_input.checked ? "block" : "none";
	UpdateRecurCalPreviewLoad();
}

// turn \d{1,2}:\d{1,2} into number of minutes past midnight
function TimeToMinutes(time)
{
	var hours   = Number(time.replace(/^(\d{1,2}):\d{1,2}$/, "$1"));
	var minutes = Number(time.replace(/^\d{1,2}:(\d{1,2})$/, "$1"));
	return hours*60 + minutes;
}

// turn number of seconds past midnight into \d{1,2}:\d{1,2}
function MinutesToTime(minutes)
{
	var hours = Math.floor(minutes / 60);
	var mins = minutes % 60;
	return hours + ':' + mins;
}

// Cycle the options in a select so that first_value is first.
function CycleSelect(select, first_value, first)
{
	var options = select.getElementsByTagName('option');
	var first_index = -1;
	var old_end_index = -1;
	
	// Check that first_value exists
	// Also find which one end selected (assume they're at regular intervals)
	var finish = 0;
	for (var i = 0; i < options.length && finish <2; ++i) {
		if (options[i].value == first_value) {
			first_index = i;
			++finish;
		}
		if (options[i].value == select.value) {
			old_end_index = i;
			++finish;
		}
	}
	// Only reorder if first_value exists and not already in correct order
	if (first_index > 0) {
		// Each time, remove and reappend the first option
		for (var i = 0; i < first_index; ++i) {
			var first = options[0];
			select.removeChild(first);
			select.appendChild(first);
		}
		// If we know which end was selected, use the same index
		if (old_end_index>=0) {
			options = select.getElementsByTagName('option');
			for (var i = 0; i < options.length; ++i) {
				if (i == old_end_index) {
					select.value = options[i].value;
					break;
				}
			}
		}
	}
}

/// Move the end time to maintain the duration
function StartTimeChange(start, end_name)
{
	var end = document.getElementById(end_name);
	
	CycleSelect(end, start.value, true);
}

/// Update the recurrence calendar preview by AJAX
function UpdateRecurCalPreviewLoad()
{
	document.getElementById('preview_calendar_div').style.display = "none";
	var url = url_ajax_recur_validate;
	var post = {};
	post['prefix'] = "eved";
	if (document.getElementById('eved_allday').checked) {
		post['eved_allday']  = "on";
	}
	post['eved_start[monthday]'] = document.getElementById('eved_start_monthday').value;
	post['eved_start[month]']    = document.getElementById('eved_start_month').value;
	post['eved_start[year]']     = document.getElementById('eved_start_year').value;
	post['eved_start[time]']     = document.getElementById('eved_start_time').value;
	post['eved_duration[days]']  = document.getElementById('eved_duration_days').value;
	post['eved_duration[time]']  = document.getElementById('eved_duration_time').value;
	if (document.getElementById('eved_recur_simple_enable').checked) {
		post['eved_recur_simple[enable]'   ] = "on";
	}
	post['eved_recur_simple[freq]'     ] = document.getElementById('eved_recur_simple_freq').value;
	post['eved_recur_simple[interval]' ] = document.getElementById('eved_recur_simple_interval').value;
	
	
	// frequency specific fields
	var freq = document.getElementById('eved_recur_simple_freq').value;
	switch (freq) {
		case "weekly":
			for (var day = 0; day < 7; ++day) {
				if (document.getElementById('eved_recur_simple_weekly_byday_'+day).checked) {
					post['eved_recur_simple[weekly_byday]['+day+']'] = "on";
				}
			}
			break;
		case "monthly":
			var month_method = "";
			if (document.getElementById('eved_recur_simple_monthly_method_monthday').checked) {
				month_method = "monthday";
				post['eved_recur_simple[monthly_monthday][monthday]'] = document.getElementById('eved_recur_simple_monthly_monthday_monthday').value;
			} else if (document.getElementById('eved_recur_simple_monthly_method_weekday').checked) {
				month_method = "weekday";
				post['eved_recur_simple[monthly_weekday][week]'] = document.getElementById('eved_recur_simple_monthly_weekday_week').value;
				post['eved_recur_simple[monthly_weekday][day]'] = document.getElementById('eved_recur_simple_monthly_weekday_day').value;
			}
			post['eved_recur_simple[monthly_method]'] = month_method;
			break;
		case "yearly":
			var year_method = "";
			if (document.getElementById('eved_recur_simple_yearly_method_monthday').checked) {
				year_method = "monthday";
				post['eved_recur_simple[yearly_monthday][monthday]'] = document.getElementById('eved_recur_simple_yearly_monthday_monthday').value;
				post['eved_recur_simple[yearly_monthday][month]'] = document.getElementById('eved_recur_simple_yearly_monthday_month').value;
			} else if (document.getElementById('eved_recur_simple_yearly_method_weekday').checked) {
				year_method = "weekday";
				post['eved_recur_simple[yearly_weekday][week]'] = document.getElementById('eved_recur_simple_yearly_weekday_week').value;
				post['eved_recur_simple[yearly_weekday][day]'] = document.getElementById('eved_recur_simple_yearly_weekday_day').value;
				post['eved_recur_simple[yearly_weekday][month]'] = document.getElementById('eved_recur_simple_yearly_weekday_month').value;
			} else if (document.getElementById('eved_recur_simple_yearly_method_yearday').checked) {
				year_method = "yearday";
				post['eved_recur_simple[yearly_yearday][yearday]'] = document.getElementById('eved_recur_simple_yearly_yearday_yearday').value;
			}
			post['eved_recur_simple[yearly_method]'] = year_method;
			break;
	}
	// Range method
	var range_method = "";
	if (document.getElementById('eved_recur_simple_range_method_noend').checked) {
		range_method = "noend";
	} else if (document.getElementById('eved_recur_simple_range_method_count').checked) {
		range_method = "count";
		post['eved_recur_simple[count]'] = document.getElementById('eved_recur_simple_count').value;
	} else if (document.getElementById('eved_recur_simple_range_method_until').checked) {
		range_method = "until";
		post['eved_recur_simple[until][monthday]'] = document.getElementById('eved_recur_simple_until_monthday').value;
		post['eved_recur_simple[until][month]'] = document.getElementById('eved_recur_simple_until_month').value;
		post['eved_recur_simple[until][year]'] = document.getElementById('eved_recur_simple_until_year').value;
	}
	post['eved_recur_simple[range_method]'] = range_method;
	var ajax = new AJAXInteraction(url, post, UpdateRecurCalPreviewCallback); 
	ajax.doPost();
}

/// Callback function for updating the calendar preview using xml from server.
function UpdateRecurCalPreviewCallback(responseXML)
{
	if (responseXML) {
		/// @todo Don't use innerHTML here
		var target = document.getElementById('preview_calendar_div'); 
		var errors = responseXML.getElementsByTagName('error');
		var html = "";
		if (errors.length > 0) {
			html = "errors:<br />";
			for (var i = 0; i < errors.length; ++i) {
				html += errors[i].firstChild.nodeValue+"<br />";
			}
		}
		target.innerHTML = html;
		var occurrences = responseXML.getElementsByTagName('occ');
		ResetMinicalDates();
		for (var i = 0; i < occurrences.length; ++i) {
			date  = occurrences[i].attributes.getNamedItem('date').value;
			classes  = occurrences[i].attributes.getNamedItem('class').value;
			AdjustMinicalDate(date, classes);
		}
		document.getElementById('preview_calendar_div').style.display = "block";
	}
}

/// The main recurrence frequency has been changed, adjust visibility of divs
function CalSimpleFreqChange()
{
	var freq = document.getElementById('eved_recur_simple_freq').value;
	document.getElementById('eved_recur_simple_freq_weekly').style.display = "none";
	document.getElementById('eved_recur_simple_freq_monthly').style.display = "none";
	document.getElementById('eved_recur_simple_freq_yearly').style.display = "none";
	document.getElementById('eved_recur_simple_interval_unit_daily').style.display = "none";
	document.getElementById('eved_recur_simple_interval_unit_weekly').style.display = "none";
	document.getElementById('eved_recur_simple_interval_unit_monthly').style.display = "none";
	document.getElementById('eved_recur_simple_interval_unit_yearly').style.display = "none";
	switch (freq) {
		case "daily":
			document.getElementById('eved_recur_simple_interval_unit_daily').style.display = "block";
			break;
		case "weekly":
			document.getElementById('eved_recur_simple_interval_unit_weekly').style.display = "block";
			document.getElementById('eved_recur_simple_freq_weekly').style.display = "block";
			break;
		case "monthly":
			document.getElementById('eved_recur_simple_interval_unit_monthly').style.display = "block";
			document.getElementById('eved_recur_simple_freq_monthly').style.display = "block";
			break;
		case "yearly":
			document.getElementById('eved_recur_simple_interval_unit_yearly').style.display = "block";
			document.getElementById('eved_recur_simple_freq_yearly').style.display = "block";
			break;
	}
}

/// From a gregorian date selector get a date in the form Ymd e.g. 20071015
function DateSelectToDateString(name)
{
	var monthday = document.getElementById(name+'_monthday').value;
	var month    = document.getElementById(name+'_month').value;
	var year     = document.getElementById(name+'_year').value;
	if (month < 10) {
		month = "0"+month;
	}
	if (monthday < 10) {
		monthday = "0"+monthday;
	}
	var result = '' + year + month + monthday;
	return result;
}

/// Dates indexed by date formatted as Ymd
var inc_dates = new Object();
var exc_dates = new Object();
/// Number of dates
var date_count = 0;

/// Get '', 'in', 'ex'.
function TestInexDate(date)
{
	if (inc_dates[date]) {
		return 'in';
	} else if (exc_dates[date]) {
		return 'ex';
	} else {
		return '';
	}
}

/// Remove an include/exclude date
function RemoveInexDate(inex, date)
{
	var div = document.getElementById('eved_inex_date_'+inex+'clude_'+date);
	if (div) {
		div.parentNode.removeChild(div);
		if (inex == 'in') {
			delete inc_dates[date];
		} else {
			delete exc_dates[date];
		}
		--date_count;
		document.getElementById('eved_inex_none').style.display = date_count ? "none" : "block";
		UpdateMinicalInexDate(date);
	}
	return false;
}

/// Add an include/exclude date.
function ModTemplateDiv(div, inex, date)
{
	var newFields = div.childNodes;
	for (var i=0; i<newFields.length; i++) {
		if (newFields[i].className == 'description') {
			var date_regexp = new RegExp("([0-9]{4})([0-9]{2})([0-9]{2})");
			var results = date_regexp.exec(date);
			// assume the regex matches
			var when = new Date(results[1], results[2]-1, results[3]);
			// format as: $DAY, $MONTHDAY<sup>$MONTHDAY_NTH</sup> $MONTH $YEAR
			newFields[i].appendChild(document.createTextNode(DateDayName(when) + ', ' + when.getDate()));
			
			var super_tag = document.createElement('sup');
			newFields[i].appendChild(super_tag);
			super_tag.appendChild(document.createTextNode(DateMonthDayNth(when)));
			
			newFields[i].appendChild(document.createTextNode(' ' + DateMonthName(when) + ' ' + when.getFullYear()));
			
		} else if (newFields[i].className == 'delete') {
			ModTemplateDiv(newFields[i], inex, date);
		}
		if (newFields[i].name == inex+'clude_remove_btn') {
			newFields[i].onclick = function onclick(event) {
				return RemoveInexDate(inex,date);
			};
		}
		if (newFields[i].id)
			newFields[i].id = newFields[i].id + '['+date+']';
		if (newFields[i].name)
			newFields[i].name = 'eved_inex['+newFields[i].name + 's]['+date+']';
		if (newFields[i].value == 'DATE')
			newFields[i].value = date;
	}
}
function NewInexDate(inex, date)
{
	var existing = TestInexDate(date);
	if (existing) {
		RemoveInexDate(existing, date);
	}
	var templateName = 'eved_'+inex+'clude_template';
	var newClone = document.getElementById(templateName).cloneNode(true);
	newClone.id = 'eved_inex_date_'+inex+'clude_'+date;
	ModTemplateDiv(newClone, inex, date);
	
	var destination = document.getElementById('eved_inex_list');
	var no_dates = document.getElementById('eved_inex_none');
	destination.appendChild(newClone);
	if (inex == 'in') {
		inc_dates[date] = newClone;
	} else {
		exc_dates[date] = newClone;
	}
	++date_count;
	document.getElementById('eved_inex_none').style.display = date_count ? "none" : "block";
	UpdateMinicalInexDate(date);
	return false;
}

var minical_base_classes = new Array();
var minical_recur_classes = new Array();
var minical_inex_classes = new Array();

function MiniTog(date)
{
	// If the main edit stuff is hidden (for confirmation) check before doing anything
	if (document.getElementById('main_edit_divs').style.display == 'none') {
		if (confirm("Clicking on the calendar will return to edit mode and alter the event.")) {
			document.getElementById('eved_confirm_edit_btn').onclick();
		}
	}
	if (document.getElementById('main_edit_divs').style.display != 'none') {
		/*
		none -> include -> exclude -> none
		*/
		var td = document.getElementById('mc'+date+'');
		if (1) {
			// Toggle the obvious values
			if (CssCheck(td, 'sel')) {
				// selected, so inc -> none -> exc
				if (inc_dates[date]) {
					// inc -> none
					RemoveInexDate('in', date);
				} else if (!exc_dates[date]) {
					// none -> exc
					NewInexDate('ex', date);
				} else {
					// exc -> none
					RemoveInexDate('ex', date);
				}
			} else {
				// deselected, so exc -> none -> inc
				if (exc_dates[date]) {
					// exc -> none
					RemoveInexDate('ex', date);
				} else if (!inc_dates[date]) {
					// none -> inc
					NewInexDate('in', date);
				} else {
					// inic -> none
					RemoveInexDate('in', date);
				}
			}
		} else {
			// Toggle all three values
			if (inc_dates[date]) {
				// inc -> exc
				RemoveInexDate('in', date);
				NewInexDate('ex', date);
			} else if (exc_dates[date]) {
				// exc -> none
				RemoveInexDate('ex', date);
			} else {
				// none -> inc
				NewInexDate('in', date);
			}
		}
	}
	return false;
}

/// Update inex on a date
function UpdateMinicalInexDate(date)
{
	date_cell = document.getElementById('mc'+date);
	if (date_cell) {
		var inex = TestInexDate(date);
		if (inex) {
			CssAdd(date_cell, inex+'c');
		}
		if (inex != 'ex') {
			CssRemove(date_cell, 'exc');
		}
		if (inex != 'in') {
			CssRemove(date_cell, 'inc');
		}
	}
}

/// Revert minical date back to before recur changes.
function ResetMinicalDate(date)
{
	date_cell = document.getElementById('mc'+date);
	if (date_cell != null) {
		date_cell.className = minical_base_classes[date];
		UpdateMinicalInexDate(date);
		delete minical_base_classes[date];
	}
}

/// Do the above for all which have been changed by recur.
function ResetMinicalDates()
{
	for (var date in minical_base_classes) {
		ResetMinicalDate(date);
	}
}

function AdjustMinicalDate(date, className)
{
	date_cell = document.getElementById('mc'+date);
	if (date_cell != null) {
		if (!(date in minical_base_classes)) {
			minical_base_classes[date] = date_cell.className;
		}
		date_cell.className += date_cell.className ? " "+className : className;
	}
}

/// Main onLoad function
function calendarEdit_onLoad()
{
	document.getElementById('recurrences_preview_noscript').style.display = "none";
	document.getElementById('recur_use_minical_nonjs').style.display = "none";
	document.getElementById('incex_jsonly').style.display = "block";
	document.getElementById('recurrences_preview').style.display = "block";
	document.getElementById('recur_use_minical_js').style.display = "block";
	
	
	CalSimpleFreqChange();
	MainRecurrenceToggle();
	SimpleCheckboxChange('eved_allday', 'eved_start_time', 'eved_duration_time_div');
	
	var start_time = document.getElementById('eved_start_time');
	var end_time = document.getElementById('eved_duration_time');
	CycleSelect(end_time, start_time.value, true);
	
	// Need to get info from data inserted by php.
	// inex dates - look in children of eved_inex_list
	var inex_list = document.getElementById('eved_inex_list');
	
	var inex_date_divs = inex_list.childNodes;
	var name_matcher = new RegExp("^eved_inex_date_(in|ex)clude_([0-9]{8})$");
	for (var i=0; i<inex_date_divs.length; i++) {
		if (inex_date_divs[i].id) {
			var results = name_matcher.exec(inex_date_divs[i].id);
			if (results) {
				// add to the arrays.
				if (results[1] == 'in') {
					inc_dates[results[2]] = inex_date_divs[i];
				} else {
					exc_dates[results[2]] = inex_date_divs[i];
				}
				++date_count;
				// update the preview calendar.
				UpdateMinicalInexDate(results[2]);
			}
		}
	}
}

// Register onLoad function
onLoadFunctions.push(calendarEdit_onLoad);

