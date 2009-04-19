// Javascript for term date retrieval and usage
// Author: James Hogan (james_hogan at theyorker dot co dot uk)
// Copyright (C) 2009 The Yorker

var calendar_term_dates = {};

function calendar_term_read_simple_date(str)
{
	var sep = str.split('-');
	return new Date(sep[0], sep[1]-1, sep[2]);
}

// Calendar term class.
function CalendarTerm(year,term, xml)
{
	this.m_start       = calendar_term_read_simple_date(innerText(xml.getElementsByTagName('start')[0]));
	this.m_end         = calendar_term_read_simple_date(innerText(xml.getElementsByTagName('end')[0]));
	this.m_mondayWeek1 = calendar_term_read_simple_date(innerText(xml.getElementsByTagName('mondayweek1')[0]));
	this.m_weeks       = parseInt(innerText(xml.getElementsByTagName('weeks')[0]));

	this.acYear = function()
	{
		return year;
	}
	this.acTerm = function()
	{
		return term;
	}
	this.start = function()
	{
		return new Date(this.m_start);
	}
	this.end = function()
	{
		return new Date(this.m_end);
	}
	this.mondayWeek1 = function()
	{
		return new Date(this.m_mondayWeek1);
	}
	this.weeks = function()
	{
		return this.m_weeks;
	}
	this.prev = function()
	{
		return calendar_term((term <=0) ? year-1 : year, (term+5) % 6);
	}
	this.next = function()
	{
		return calendar_term((term >=5) ? year+1 : year, (term+1) % 6);
	}
}

// Initialise from a js array.
function calendar_term_dates_init(initial)
{
}

// Get term dates
function calendar_term(year, term)
{
	if (undefined == calendar_term_dates[year]) {
		return null;
	}
	else if (undefined == calendar_term_dates[year][term]) {
		return null;
	}
	else {
		return calendar_term_dates[year][term];
	}
}

// Prefetch term dates where necessary and call callback when done.
function calendar_term_dates_prefetch(yearstart, yearend, callback)
{
	if (yearstart < 1970) {
		yearstart = 1970;
	}
	if (yearend >= 2037) {
		yearstart = 2036;
	}
	// Only fetch if we're missing some years.
	var years = [];
	var first_missing = null;
	for (var year = yearstart; year <= yearend+1; ++year) {
		if (year <= yearend && undefined == calendar_term_dates[year]) {
			if (null == first_missing) {
				first_missing = year;
			}
		}
		else {
			if (null != first_missing) {
				years[years.length] = [first_missing, year-1];
				first_missing = null;
			}
		}
	}
	calendar_term_dates_fetch(years, callback);
}

// Fetch term dates and call callback when done.
function calendar_term_dates_fetch(years, callback)
{
	// Turn ranges into strings
	var ranges = new Array();
	for (var i = 0; i < years.length; ++i) {
		if (years[i][0] == years[i][1]) {
			ranges.push(years[i][0]);
		}
		else if (years[i][0] < years[i][1]) {
			ranges.push(years[i][0]+"-"+years[i][1]);
		}
	}
	// Don't bother asking if there aren't any ranges
	if (ranges.length < 1) {
		callback(true);
		return;
	}
	var post = {};
	post['years'] = ranges.join(",");

	// Handle a response from the web server
	var postCallback = function(responseXML)
	{
		if (responseXML) {
			var root = responseXML.documentElement;
			// Get errors node
			var mainEls = root.childNodes;
			var anyErrors = false;
			for (var i = 0; i < mainEls.length; ++i) {
				var el = mainEls[i];
				if (el.tagName == "errors") {
					var errors = el.getElementsByTagName("error");
					if (errors.length > 0) {
						anyErrors = true;
					}
				}
			}
			if (!anyErrors) {
				// read each academicyear
				var acyears = root.getElementsByTagName("academicyear");
				for (var year = 0; year < acyears.length; ++year) {
					var acyear = parseInt(acyears[year].getAttribute("year"),10);
					calendar_term_dates[acyear] = [];

					var acterms = acyears[year].getElementsByTagName("term");
					for (var term = 0; term < acterms.length; ++term) {
						var acterm = parseInt(acterms[term].getAttribute("term"),10);
						calendar_term_dates[acyear][acterm] = new CalendarTerm(acyear, acterm, acterms[term]);
					}
				}
				if (null != callback) {
					callback(true);
				}
			}
			else {
				if (null != callback) {
					callback(false);
				}
			}
		}
		else {
			if (null != callback) {
				callback(false);
			}
		}
	}

	// Handle a response from the web server
	var postCallbackFail = function(status, text)
	{
		if (null != callback) {
			callback(false);
		}
	}

	var ajax = new AJAXInteraction('/calendar/ajax/termdates', post, postCallback, postCallbackFail);
	ajax.doGet();
}
