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
	select = document.getElementById(name+'__day_select');
	var day = innerText(select.options[select.selectedIndex]);
	setInnerText(span, day);
	// Update week
	span = document.getElementById(name+'__wk');
	select = document.getElementById(name+'__wk_select');
	var week = innerText(select.options[select.selectedIndex]);
	setInnerText(span, week);
	// Update term
	span = document.getElementById(name+'__term');
	select = document.getElementById(name+'__term_select');
	setInnerText(span, innerText(select.options[select.selectedIndex]));
	// Update hour
	span = document.getElementById(name+'__hr');
	select = document.getElementById(name+'__hr_select');
	if (null != span && null != select) {
		setInnerText(span, innerText(select.options[select.selectedIndex]));
	}
	// Update minute
	span = document.getElementById(name+'__min');
	select = document.getElementById(name+'__min_select');
	if (null != span && null != select) {
		setInnerText(span, innerText(select.options[select.selectedIndex]));
	}

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
	var days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
	var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
	var terms = ['Autumn','Christmas','Spring','Easter','Summer','Holiday'];
	var term_year_offsets = [0,0,1,1,1,1];

	var select = document.getElementById(name+'__term_select');
	var original_opt = select.options[select.selectedIndex];
	var term = original_opt.value;
	if (term == 'earlier') {
		// find first term and add 6 more terms
		var first_term_opt = select.options[1];
		var year_term = first_term_opt.value.split('-');
		var ac_year = parseInt(year_term[0],10);
		var term = parseInt(year_term[1],10);
		for (var i = 0; i < 6; ++i) {
			--term;
			if (term < 0) {
				term = 5;
				--ac_year;
			}
			// add the option to the list
			var opt = document.createElement('option');
			opt.value = ac_year+'-'+term;
			setInnerText(opt, terms[term] + ' ' + (ac_year+term_year_offsets[term]));
			select.insertBefore(opt, first_term_opt);
			first_term_opt = opt;
		}
		// go straight to the first one added
		select.selectedIndex = 6;
	}
	else if (term == 'later') {
		// find last term and add 6 more terms
		var last_term_opt = select.options[select.options.length-2];
		var year_term = last_term_opt.value.split('-');
		var ac_year = parseInt(year_term[0],10);
		var term = parseInt(year_term[1],10);
		for (var i = 0; i < 6; ++i) {
			++term;
			if (term >= 6) {
				term = 0;
				++ac_year;
			}
			// add the option to the list
			var opt = document.createElement('option');
			opt.value = ac_year+'-'+term;
			setInnerText(opt, terms[term] + ' ' + (ac_year+term_year_offsets[term]));
			select.insertBefore(opt, original_opt);
		}
		// go straight to the first one added
		select.selectedIndex = select.options.length-7;
	}
	term = select.options[select.selectedIndex].value;

	select.disabled = "disabled";
	// first clear the table
	var wk = 1;
	var tbody = null;
	while (true) {
		var tr = document.getElementById(name+'__wk_'+wk);
		if (tr == null) {
			break;
		}
		tbody = tr.parentNode;
		tbody.removeChild(tr);
		++wk;
	}
	// put a dummy row to say we're waiting
	var dummy = document.createElement('tr');
	dummy.id=name+'__wk_1';
	var dummy_td = document.createElement('th');
	dummy_td.colSpan = 8;
	var dummy_div = document.createElement('div');
	dummy_div.className="loading";
	setInnerText(dummy_div, "Loading term data");
	dummy_td.appendChild(dummy_div);
	dummy.appendChild(dummy_td);
	tbody.appendChild(dummy);

	// prefetch term data
	var year_term = term.split('-');
	if (year_term.length == 2) {
		var ac_year = parseInt(year_term[0],10);
		var ac_term = parseInt(year_term[1],10);
		if (isNaN(ac_year) || ac_year != year_term[0]) {
			setInnerText(dummy_td, "Bad year in term id");
			select.disabled = "";
		}
		else if (isNaN(ac_term) || ac_term != year_term[1]) {
			setInnerText(dummy_td, "Bad term in term id");
			select.disabled = "";
		}
		else {
			calendar_term_dates_prefetch(ac_year, ac_year, function(success) {
				var term_info = calendar_term(ac_year, ac_term);
				if (null != term_info) {
					// Clear dummy
					tbody.removeChild(dummy);
					// Start adding weeks
					var today = new Date();
					var today_year = today.getYear();
					var today_month = today.getMonth();
					var today_date = today.getDate();
					var cur_date = term_info.mondayWeek1();
					var num_weeks = term_info.weeks();
					var last_month = null;
					var onclick_setter = function(td, wk, day)
					{
						td.onclick = function()
						{
							return input_date_change(name, wk, day);
						}
					}
					for (var wk = 1; wk <= num_weeks; ++wk) {
						var tr = document.createElement('tr');
						tr.id = name+'__wk_'+wk;
						tbody.appendChild(tr);
						var th = document.createElement('th');
						setInnerText(th, wk);
						tr.appendChild(th);
						for (var day = 0; day < 7; ++day) {
							var td = document.createElement('td');
							td.id = name+'__'+wk+'_'+days[day];
							var year = cur_date.getYear();
							var month = cur_date.getMonth();
							var text = cur_date.getDate();
							if (month != last_month) {
								text = months[month]+'&nbsp;'+text;
								last_month = month;
							}
							td.innerHTML = text;

							var classes = new Array;
							if (day >= 5) {
								classes.push('we');
							}
							if (month % 2 == 1) {
								classes.push('ev');
							}
							if (year  == today_year  &&
								month == today_month &&
								text  == today_date) {
								classes.push('tod');
							}
							else if (cur_date.valueOf() < today.valueOf()) {
								classes.push('pa');
							}
							td.className = classes.join(' ');
							onclick_setter(td,wk,day);
							tr.appendChild(td);

							// Next day
							cur_date.setDate(cur_date.getDate()+1);
						}
					}
					var week_select = document.getElementById(name+'__wk_select');
					var cur_week = week_select.selectedIndex;
					while (null != week_select.firstChild) {
						week_select.removeChild(week_select.firstChild);
					}
					for (var wk = 1; wk <= num_weeks; ++wk) {
						var opt = document.createElement('option');
						opt.value = wk;
						setInnerText(opt, wk);
						week_select.appendChild(opt);
					}
					if (cur_week >= num_weeks) {
						cur_week = num_weeks-1;
						week_select.selectedIndex = cur_week;
					}
				}
				else {
					setInnerText(dummy_td, "Term data could not be retrieved");
				}
				select.disabled = "";
				input_date_day_changed(name);
			});
		}
	}
	else {
		setInnerText(dummy_td, "Bad term id");
	}
}

function input_date_time_changed(name)
{
	input_date_update_text(name);
}
