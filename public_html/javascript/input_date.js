// Javascript for date selector
// Author: James Hogan (james_hogan at theyorker dot co dot uk)
// Copyright (C) 2009 The Yorker

var input_date_last_day_sel = {};

function input_date_init(name)
{
	input_date_update_text(name);
	return input_selector_init(name+'__selector');
}

function input_date_update_text(name)
{
	var span = null;
	var select = null;
	// Update day of week
	span = document.getElementById(name+'__day');
	select = document.getElementsByName(name+'[day]')[0];
	var day = select.options[select.selectedIndex].textContent;
	span.textContent = day;
	// Update week
	span = document.getElementById(name+'__wk');
	select = document.getElementsByName(name+'[wk]')[0];
	var week = select.options[select.selectedIndex].textContent;
	span.textContent = week;
	// Update term
	span = document.getElementById(name+'__term');
	select = document.getElementsByName(name+'[term]')[0];
	span.textContent = select.options[select.selectedIndex].textContent;
	// Update hour
	span = document.getElementById(name+'__hr');
	select = document.getElementsByName(name+'[hr]')[0];
	span.textContent = select.options[select.selectedIndex].textContent;
	// Update minute
	span = document.getElementById(name+'__min');
	select = document.getElementsByName(name+'[min]')[0];
	span.textContent = select.options[select.selectedIndex].textContent;

	input_date_last_day_sel[name] = document.getElementById(name+"__"+week+"_"+day);
}

function input_date_click(name)
{
	return input_selector_click(name+'__selector');
}

function input_date_change(name, week, day)
{
	var select = null;
	select = document.getElementsByName(name+'[day]')[0];
	select.selectedIndex = day;
	select = document.getElementsByName(name+'[wk]')[0];
	select.selectedIndex = week-1;
	input_date_day_changed(name);
}

function input_date_day_changed(name)
{
	var prev_cell = input_date_last_day_sel[name];
	if (null != prev_cell) {
		CssRemove(prev_cell, "sel");
	}

	input_date_update_text(name);

	var new_cell = input_date_last_day_sel[name];
	if (null != new_cell) {
		CssAdd(new_cell, "sel");
	}
}

function input_date_term_changed(name)
{
	input_date_day_changed(name);
}

function input_date_time_changed(name)
{
	input_date_update_text(name);
}
