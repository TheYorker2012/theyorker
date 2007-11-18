// Javascript for calendar days page
// Author: Chris Travis (cdt502)
// Author: James Hogan (james at albanarts dot com)
// Copyright (C) 2007 The Yorker

// History:
//  * initial commit 30th Oct 2007

// Manages:
//  * redrawing calendar
//  * selections for new events

// add a number of seconds to a date then offsets by timezone change.
function dateChange(date, milliseconds)
{
	var new_date = new Date(Number(date) + milliseconds);
	return new Date(Number(new_date) + (new_date.getTimezoneOffset()-date.getTimezoneOffset())*60000);
}

function drawCalendar () {
	// Reset event counters
	for (var i=0;i<=(DAYS.length-1);i++) {
		document.getElementById('cal_day_'+DAYS[i]+'_before').innerHTML = '';
		document.getElementById('cal_day_'+DAYS[i]+'_after').innerHTML = '';
	}

	clearCalendar();
	timeCalendar();
	resizeCalendar();
	resizeCalendarAllDay();

	for (var i=0; i<ALL_EVENT_COUNT; i++) {
		var eventStartDate = new Date(ALL_EVENT_CACHE[i][4]*1000);
		var eventEndDate = new Date(ALL_EVENT_CACHE[i][5]*1000);
		//function drawAllDayEvent (id, category, link, title, start_hour, duration, height)

		drawAllDayEvent('a'+i,
			ALL_EVENT_CACHE[i][1],
			ALL_EVENT_CACHE[i][6],
			ALL_EVENT_CACHE[i][0],
			Number(((eventStartDate.getTime() - FIRST_DAY.getTime())/(1000*60*60)).toFixed(2)),
			Number(((eventEndDate.getTime() - eventStartDate.getTime())/(1000*60*60)).toFixed(2)),
			ALL_EVENT_CACHE[i][7]
		);
	}

/*
	drawAllDayEvent('901', 'Anniversary', '/test/link/', 'Birthday 20: Richard Ingle', 24, 35.98, 0);
	drawAllDayEvent('903', 'Meeting', '/test/link/', 'Conference', 60, 11.98, 1);
	drawAllDayEvent('900', 'Social', '/test/link/', 'Day Event', 0, 23.98, 0);
	drawAllDayEvent('902', 'Facebook', '/test/link/', 'FragSoc', 12, 29.98, 1);
*/

	/* Get all the events we need to display */
	var TEMP_CACHE = new Array();
	var temp_count = 0;
	for (var i=0; i<EVENT_COUNT; i++) {
		TEMP_CACHE[temp_count] = new Array();
		TEMP_CACHE[temp_count] = EVENT_CACHE[i].slice(0);
		temp_count++;
	}

	/* Determine Event Clashes */
	for (var i=0; i<temp_count; i++) {
		var clashes = new Array();
		var clash_pos = new Array();
		var clash_count = 0;
		var clash_width = TEMP_CACHE[i][9];
		for (var j=i+1; j<temp_count; j++) {
			if (((TEMP_CACHE[j][4] >= TEMP_CACHE[i][4]) && (TEMP_CACHE[j][4] < TEMP_CACHE[i][5])) || // start of j during i
				((TEMP_CACHE[j][5] > TEMP_CACHE[i][4]) && (TEMP_CACHE[j][5] <= TEMP_CACHE[i][5])) || // end of j during i
				((TEMP_CACHE[j][4] < TEMP_CACHE[i][4]) && (TEMP_CACHE[j][5] > TEMP_CACHE[i][5])))  // j encompases i
			{
				clashes[clash_count] = j;
				if (TEMP_CACHE[j][8] != -1)
					clash_pos[clash_pos.length] = TEMP_CACHE[j][8];
				if (TEMP_CACHE[j][9] > clash_width)
					clash_width = TEMP_CACHE[j][9];
				clash_count++;
			}
		}
		if (TEMP_CACHE[i][8] != -1)
			clash_pos[clash_pos.length] = TEMP_CACHE[i][8];
		if ((clash_count+1) > clash_width)
			clash_width = clash_count + 1;
		var current_pos = 0;
		while (in_array(current_pos, clash_pos)) { current_pos++; }
		TEMP_CACHE[i][9] = clash_width;
		if (TEMP_CACHE[i][8] == -1) {
			TEMP_CACHE[i][8] = current_pos;
			while (in_array(current_pos++, clash_pos)) { current_pos++; }
		}
		for (var j=0; j<clash_count; j++) {
			TEMP_CACHE[clashes[j]][9] = clash_width;
			if (TEMP_CACHE[clashes[j]][8] == -1) {
				TEMP_CACHE[clashes[j]][8] = current_pos;
				while (in_array(current_pos++, clash_pos)) { current_pos++; }
			}

		}
	}
	
	var temp_count_2 = temp_count;
	for (var i=0; i<temp_count_2; i++) {
		var eventStartDate = new Date(TEMP_CACHE[i][4]*1000);
		var eventEndDate = new Date(TEMP_CACHE[i][5]*1000);
		/* Display on previous day too*/
		if (eventStartDate.getHours() <= (MAX_END_HOUR-24)) {
			TEMP_CACHE[temp_count] = new Array();
			TEMP_CACHE[temp_count] = TEMP_CACHE[i].slice(0);
			TEMP_CACHE[temp_count][4] = Number(dateChange(eventStartDate, -86400000))/1000;
			TEMP_CACHE[temp_count][5] = Number(dateChange(eventEndDate, -86400000))/1000;
			TEMP_CACHE[temp_count][10] = 24;
			temp_count++;
		}
		/* Display on next day too */
		if ((((eventEndDate.getTime()-eventStartDate.getTime())/3600000)+eventStartDate.getHours()+(eventStartDate.getMinutes()/60)) > 24) {
			TEMP_CACHE[temp_count] = new Array();
			TEMP_CACHE[temp_count] = TEMP_CACHE[i].slice(0);
			TEMP_CACHE[temp_count][4] = Number(dateChange(eventStartDate, 86400000))/1000;
			TEMP_CACHE[temp_count][5] = Number(dateChange(eventEndDate, 86400000))/1000;
			TEMP_CACHE[temp_count][10] = -24;
			temp_count++;
		}
	}

	/* Draw each Event */
	for (var i=0; i<temp_count; i++) {
		var eventStartDate = new Date(TEMP_CACHE[i][4]*1000);
		var eventEndDate = new Date(TEMP_CACHE[i][5]*1000);
		drawEvent('cal_day_'+zeroTime(eventStartDate.getFullYear())+zeroTime(eventStartDate.getMonth()+1)+zeroTime(eventStartDate.getDate()),
			i,
			TEMP_CACHE[i][1],
			TEMP_CACHE[i][6],
			TEMP_CACHE[i][0],
			zeroTime(eventStartDate.getHours())+':'+zeroTime(eventStartDate.getMinutes())+' - '+zeroTime(eventEndDate.getHours())+':'+zeroTime(eventEndDate.getMinutes()),
			TEMP_CACHE[i][2],
			TEMP_CACHE[i][3],
			Number((eventStartDate.getHours()+(eventStartDate.getMinutes()/60)+TEMP_CACHE[i][10]).toFixed(2)),
			Number(((eventEndDate.getTime() - eventStartDate.getTime())/(1000*60*60)).toFixed(2)),
			TEMP_CACHE[i][8],
			TEMP_CACHE[i][9],
			TEMP_CACHE[i][7]
		);
	}
	
	// current selection
	removeCreateBox();
	
	return false;
}

function drawAllDayEvent (id, category, link, title, start_hour, duration, height, classNames) {
	var p_ele = document.getElementById('calendar_all_day_events');
	if (p_ele == null)
		return;

	if (height == null)
		height = 0;

	var start_day = 0;
	var start_left = 0;
	var p_day = document.getElementById('cal_day_'+DAYS[start_day]);
	if (p_day == null)
		return;
	start_left = start_left;// + findPos(p_day)[0];
	while (start_hour >= 24) {
		start_day++;
		start_hour = start_hour - 24;
		var p_day = document.getElementById('cal_day_'+DAYS[start_day]);
		if (p_day == null)
			return;
		start_left = start_left + (p_day.offsetWidth-1);
	}
	var p_day = document.getElementById('cal_day_'+DAYS[start_day]);
	if (p_day == null)
		return;
	start_left = start_left + ((p_day.offsetWidth/24)*start_hour);

	var day_remainder = 24 - start_hour;
	var duration_width = 0;
	if (duration <= day_remainder) {
		duration_width = duration_width + ((p_day.offsetWidth/24)*duration);
	} else {
		duration_width = duration_width + (((p_day.offsetWidth/24)*day_remainder)-1);
		duration = duration - day_remainder;
		while (duration > 0) {
			start_day++;
			var p_day = document.getElementById('cal_day_'+DAYS[start_day]);
			if (p_day == null) {
				duration = 0;
			} else if (duration >= 24) {
				duration_width = duration_width + (p_day.offsetWidth-1);
				duration = duration - 24;
			} else {
				duration_width = duration_width + ((p_day.offsetWidth/24)*duration);
				duration = 0;
			}
		}
	}

	var event_link			= document.createElement('a');
	event_link.href			= link;
	event_link.innerHTML	= title;
// 	event_link.appendChild(document.createTextNode(title));

	var event_title			= document.createElement('div');
	event_title.className	= 'cal_event_heading';
	event_title.appendChild(event_link);

	var new_event 			= document.createElement('div');
	new_event.id			= 'cal_event_' + id;
	new_event.className		= 'cal_event new cal_category_' + category + (classNames != '' ? ' '+classNames : '');
	new_event.style.position= 'relative';
	new_event.style.top		= '0%';//findPos(p_ele)[1] + (height*(HOUR_HEIGHT/2)) + 'px';
	new_event.style.left	= start_left + 'px';
	new_event.style.height	= '100%';//((HOUR_HEIGHT/2)-2) + 'px';
	new_event.style.width	= (duration_width-2) + 'px';
// 	new_event.onclick		= function(){ alert('You clicked on this event!'); };

	new_event.appendChild(event_title);

	document.getElementById('calendar_all_day_events').appendChild(new_event);

	if (height > MAX_ALL_DAY)
		MAX_ALL_DAY = height;
}

function drawEvent(parent, id, category, link, title, content_time, content_location, content_description, start_hour, duration, left, width, classNames) {
	var p_ele = document.getElementById(parent);
	if (p_ele == null)
		return;

	if ((left == null) || (left == -1))
		left = 0;
	if (width == null)
		width = 1;
	width = Math.floor((p_ele.offsetWidth / width)-2-5);

	var full_display = true;
	var height_adjustment = 0;

	// Check if should be displayed
	if ((start_hour+duration) < START_HOUR) {
		var counter = document.getElementById(parent+'_before');
		counter.innerHTML = Number(counter.innerHTML) + 1;
		return;
	}
	if (start_hour >= (END_HOUR+1)) {
		var counter = document.getElementById(parent+'_after');
		counter.innerHTML = Number(counter.innerHTML) + 1;
		return;
	}
	if (start_hour < START_HOUR)
		full_display = false;

	var new_event 			= document.createElement('div');
	new_event.id			= 'cal_event_' + id;
	new_event.className		= 'cal_event cal_category_' + category + (classNames != '' ? ' '+classNames : '');
	new_event.style.left	= findPos(p_ele)[0] + (left*(width+2+5)) + 'px';
	new_event.style.width	= width + 'px';
// 	new_event.onclick		= function(){ alert('You clicked on this event!'); };

	if ((start_hour+duration) >= (END_HOUR+1)) {
		duration = (END_HOUR+1) - start_hour;
		new_event.className += ' cal_event_split_bottom';
		height_adjustment = 1;
	}
	start_hour = start_hour-START_HOUR;
	if (start_hour < 0) {
		duration += start_hour;
		start_hour = 0;
		new_event.className += ' cal_event_split_top';
		height_adjustment = 1;
	}
	new_event.style.top		= findPos(p_ele)[1] + 1 + ((start_hour*HOUR_HEIGHT)-2) + 'px';
	new_event.style.height	= ((duration*HOUR_HEIGHT)-2+height_adjustment) + 'px';

	if (full_display) {
		var event_link			= document.createElement('a');
		event_link.href			= link;
		event_link.innerHTML	= title;
// 		event_link.appendChild(document.createTextNode(title));

		var event_title			= document.createElement('div');
		event_title.className	= 'cal_event_heading';
		event_title.appendChild(event_link);

		var event_content_i			= document.createElement('i');
		event_content_i.innerHTML	= content_location;
// 		event_content_i.appendChild(document.createTextNode(content_location));

		var event_content		= document.createElement('div');
		event_content.className	= 'cal_event_info';
		event_content.appendChild(document.createTextNode(content_time));

		var event_content2		= document.createElement('div');
		event_content2.className= 'cal_event_info';
		event_content2.appendChild(event_content_i);
		if (content_description != '') {
			event_content2.innerHTML	+= "<br />"+content_description;
// 			event_content2.appendChild(document.createTextNode(content_description));
		}

		new_event.appendChild(event_title);
		new_event.appendChild(event_content);
		new_event.appendChild(event_content2);
	}

	document.getElementById(parent).appendChild(new_event);
}

function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}

function findMouse(e) {
	var posx = 0;
	var posy = 0;
	if (!e) var e = window.event;
	if (e.pageX || e.pageY) 	{
		posx = e.pageX;
		posy = e.pageY;
	} else if (e.clientX || e.clientY) 	{
		posx = e.clientX + document.body.scrollLeft
			+ document.documentElement.scrollLeft;
		posy = e.clientY + document.body.scrollTop
			+ document.documentElement.scrollTop;
	}
	return [posx,posy];
}

function resizeCalendarAllDay() {
	document.getElementById('calendar_all_day_events').style.height = ((MAX_ALL_DAY+1)*(HOUR_HEIGHT/2)) + 'px';
}

function resizeCalendar() {
	cal = document.getElementById("calendar_view");
	numberOfColumns = cal.getElementsByTagName('th').length;

	// Now, what is the actual width of the table
	// (in case there are borders or the width is a percentage)
	actualWidth = cal.scrollWidth;
	// Is there a border?
	//totalBorder = cal.border;
	// Is there some cell spacing?
    //totalSpacing = cal.cellSpacing * (numberOfColumns + 1);
	// The width we have to play with...
	//CalWidth = actualWidth - totalSpacing;
	CalWidth = actualWidth - document.getElementById('calendar_time').style.width - (numberOfColumns + 1);
	// ...which makes each cell this wide
	COL_WIDTH = Math.floor(CalWidth / numberOfColumns);

	for(i = 0;i < numberOfColumns; i++) {
		// Apply cell width
		cal.getElementsByTagName('th')[i].style.width = COL_WIDTH + "px";
	}
	return false;
}

function alterTime (amount) {
	// Check hour ranges
	if (((START_HOUR+amount) >= MAX_START_HOUR) && ((END_HOUR+amount) <= MAX_END_HOUR)) {
		START_HOUR = START_HOUR + amount;
		END_HOUR = END_HOUR + amount;
		drawCalendar();
	}
	return false;
}

function timeCalendar() {
	var time_col = document.getElementById('calendar_time');
	if (time_col == null)
		return;

	var current_end = END_HOUR;
	if (current_end > 23)
		current_end = 23;

	for (h=START_HOUR; h<=current_end; h++) {
		drawHour(h, time_col);
	}
	for (h=0; h<=(END_HOUR-24); h++) {
		drawHour(h, time_col);
	}

	for (i=0;i<=(DAYS.length-1);i++) {
		document.getElementById('cal_day_'+DAYS[i]).style.height = ((END_HOUR-START_HOUR+1)*HOUR_HEIGHT) + 'px';
	}

}

function drawHour (hour, time_col) {
	var new_hour = document.createElement('div');
	new_hour.appendChild(document.createTextNode(hour+':00'));
	time_col.appendChild(new_hour);
}

function zeroTime (time) {
	return ((time < 10) ? "0" : "") + time;
}

function clearCalendar() {
	for (var i=0; i<=(DAYS.length-1); i++) {
		removeChildrenFromNode(document.getElementById('cal_day_'+DAYS[i]));
	}
	removeChildrenFromNode(document.getElementById('calendar_all_day_events'));
	removeChildrenFromNode(document.getElementById('calendar_time'));
}

function removeChildrenFromNode(node) {
	if ((node == undefined) || (node == null))
		return;

	while (node.hasChildNodes()) {
		node.removeChild(node.firstChild);
	}
}

function updateNewEventTimes(start_time, end_time) {
	var start_display2 = (start_time%4)*15;
	if (start_display2 == '0')
		start_display2 = '00';
	var start_display3 = Math.floor(start_time/4);
	if (start_display3 > 23)
		start_display3 -= 24;
	var end_display2 = (end_time%4)*15;
	if (end_display2 == '0')
		end_display2 = '00';
	var end_display3 = Math.floor(end_time/4);
	if (end_display3 > 23)
		end_display3 -= 24;
	document.getElementById('cal_new_event_times').innerHTML =
		start_display3 + ':' + start_display2 + " - " +
		end_display3 + ':' + end_display2;
}

function showCreateBox(new_event) {
	// If existing, just update it
	var new_event_box	= document.getElementById('cal_new_event_box');
	
	var new_event_pos			= findPos(new_event);
	var new_event_parent_pos	= findPos(new_event.parentNode);
	
	new_event_box.style.display	= 'block';
	updateCreateBox(new_event);
}

function updateCreateBox(new_event) {
	// If not existing don't do anything
	var new_event_box	= document.getElementById('cal_new_event_box');
	if (new_event_box.style.display != "none") {
		var new_event_pos			= findPos(new_event);
		var new_event_parent_pos	= findPos(new_event.parentNode);
	
		new_event_box.style.left	= (new_event_pos[0]+50) + 'px';
		new_event_box.style.top		= (new_event_pos[1]+30) + 'px';
	}
	// update the time fields
	var evad_date	= document.getElementById('evad_date');
	var evad_start	= document.getElementById('evad_start');
	var evad_end	= document.getElementById('evad_end');
	
	// Swap stored start and end time if they're the wrong way
	var start = CREATE_EVENT_START_TIME;
	var end = CREATE_EVENT_END_TIME;
	if (end < start) {
		start = CREATE_EVENT_END_TIME;
		end = CREATE_EVENT_START_TIME;
	}
	evad_date.value = new_event.parentNode.attributes.getNamedItem('name').value;
	evad_start.value = start*15;
	evad_end.value = (end+1)*15;
}

function removeCreateBox()
{
	var new_event_box	= document.getElementById('cal_new_event_box');
	if (new_event_box) {
		new_event_box.style.display	= 'none';
	}
}

function toggleCreateBox(new_event)
{
	var new_event_box	= document.getElementById('cal_new_event_box');
	if (new_event_box.style.display == 'none') {
		showCreateBox(new_event);
	} else {
		new_event_box.style.display	= 'none';
	}
}

function setCreateBoxSummary(new_summary)
{
	if (new_summary == '') {
		new_summary = 'New event';
	}
	var new_event_heading	= document.getElementById('cal_new_event_heading');
	if (new_event_heading.firstChild) {
		new_event_heading.removeChild(new_event_heading.firstChild);
	}
	new_event_heading.appendChild(document.createTextNode(new_summary));
}
function setCreateBoxCategory(new_category)
{
	var category_name	= document.getElementById('evad_category_op_'+new_category);
	if (category_name) {
		category_name		= category_name.innerHTML;
		var new_event		= document.getElementById('cal_new_event');
		new_event.className	= "cal_event new cal_category_"+category_name;
	}
}

function setCreateBoxLocation(new_location)
{
	var new_event_location	= document.getElementById('cal_new_event_location');
	if (new_event_location.firstChild) {
		new_event_location.removeChild(new_event_location.firstChild);
	}
	new_event_location.appendChild(document.createTextNode(new_location));
}

// Swap stored start and end time if they're the wrong way
function reorder_start_end()
{
	if (CREATE_EVENT_END_TIME < CREATE_EVENT_START_TIME) {
		var tmp = CREATE_EVENT_END_TIME;
		CREATE_EVENT_END_TIME = CREATE_EVENT_START_TIME;
		CREATE_EVENT_START_TIME = tmp;
	}
}

function clickDay (day,event) {
	var new_event = document.getElementById('cal_new_event');
	
	reorder_start_end();

	var pos_relative = findMouse(event)[1] - findPos(day)[1];
	pos_relative = Math.floor((pos_relative - 1 + 2)/(HOUR_HEIGHT/4));
	
	DESELECTING_EVENT = true;
	if (new_event != null) {
// 		DESELECTING_EVENT = new_event.style.display != 'none';
		if (day != CREATE_EVENT_DAY ||
			new_event.style.display == 'none' ||
			pos_relative + (START_HOUR*4) < CREATE_EVENT_START_TIME ||
			pos_relative + (START_HOUR*4) > CREATE_EVENT_END_TIME)
		{
			removeCreateBox();
			new_event.parentNode.removeChild(new_event);
		} else {
			var grab_position = findMouse(event)[1] - findPos(new_event)[1];
			var event_height = (CREATE_EVENT_END_TIME - CREATE_EVENT_START_TIME + 1) * (HOUR_HEIGHT/4);
			var grab_ratio = grab_position / event_height;
			if (grab_ratio >= 0.75 && grab_position > event_height-HOUR_HEIGHT/2) {
				CREATE_EVENT = true;
			} else if (grab_ratio <= 0.25 && grab_position < HOUR_HEIGHT/2) {
				var tmp = CREATE_EVENT_END_TIME;
				CREATE_EVENT_END_TIME = CREATE_EVENT_START_TIME;
				CREATE_EVENT_START_TIME = tmp;
				CREATE_EVENT = true;
			} else {
				CREATE_EVENT_MOVE = true;
			}
			CREATE_EVENT_MOVED = false;
			CREATE_EVENT_MOVE_GRAB = grab_position;
			return true;
		}
	}
	CREATE_EVENT_DAY = day;

	var duration = 1.0;
	var width = Math.floor(day.offsetWidth-2-5);;

	new_event 				= document.createElement('div');
	new_event.id			= 'cal_new_event';
	new_event.style.display	= 'none';
	new_event.style.left	= findPos(day)[0] + 'px';
	new_event.style.top		= findPos(day)[1] - 1 + ((pos_relative*(HOUR_HEIGHT/4))) + 'px';
	new_event.style.width	= width-2 + 'px';
	new_event.style.height	= ((duration*HOUR_HEIGHT)-4) + 'px';
	new_event.style.cursor	= 's-resize';
	new_event.ondblclick	= function(){ return showCreateBox(this); };

	var heading				= document.createElement('a');
	heading.id				= 'cal_new_event_heading';
	
	var heading_div			= document.createElement('div');
	heading_div.className		= 'cal_event_heading';
	heading_div.appendChild(heading);

	var display					= (pos_relative + (START_HOUR*4));
	CREATE_EVENT_START_TIME	= display;
	CREATE_EVENT_END_TIME = CREATE_EVENT_START_TIME + 4;
	var display2 = (display%4)*15;
	if (display2 == '0')
		display2 = '00';
	var display3 = Math.floor(display/4);
	if (display3 > 23)
		display3 -= 24;
	var end_display2 = (CREATE_EVENT_END_TIME%4)*15;
	if (end_display2 == '0')
		end_display2 = '00';
	var end_display3 = Math.floor(CREATE_EVENT_END_TIME/4);
	if (end_display3 > 23)
		end_display3 -= 24;
	
	var times				= document.createElement('div');
	times.id				= 'cal_new_event_times';
	times.className			= 'cal_event_info';
	times.appendChild(document.createTextNode(
		display3 + ':' + display2 + " - " + end_display3 + ':' + end_display2));
	
	var location_name		= document.createElement('i');
	location_name.id		= 'cal_new_event_location';
	
	var info				= document.createElement('div');
	info.className			= 'cal_event_info';
	info.id					= 'cal_new_event_info';
	info.appendChild(location_name);
	
	new_event.appendChild(heading_div);
	new_event.appendChild(times);
	new_event.appendChild(info);
	
	day.appendChild(new_event);
	setCreateBoxSummary(document.getElementById('evad_summary').value);
	setCreateBoxCategory(document.getElementById('evad_category').value);
	setCreateBoxLocation(document.getElementById('evad_location').value);
	CREATE_EVENT = true;
}

function moveDay (day, event) {
	var new_event = document.getElementById('cal_new_event');
	if (new_event == null)
		return;
	if (CREATE_EVENT) {
		var start_time = CREATE_EVENT_START_TIME - (START_HOUR*4);
		var end_time = findMouse(event)[1] - findPos(day)[1];
		end_time = Math.floor((end_time - 1 + 2)/(HOUR_HEIGHT/4));
		if (end_time < 0) {
			end_time = 0;
		} else if (end_time > (END_HOUR-START_HOUR+1)*4-1) {
			end_time = (END_HOUR-START_HOUR+1)*4-1;
		}
		CREATE_EVENT_END_TIME = end_time + START_HOUR*4;
		if (end_time < start_time) {
			var tmp = start_time;
			start_time = end_time;
			end_time = tmp;
		}
		end_time++;
		if (end_time - start_time > 2) {
			new_event.style.display	= 'block';
			DESELECTING_EVENT = false;
		}
		new_event.style.top = (findPos(day)[1] + (start_time*(HOUR_HEIGHT/4))) + 'px';
		new_event.style.height = (((end_time-start_time)*(HOUR_HEIGHT/4))-4) + 'px';

		updateNewEventTimes(start_time+START_HOUR*4, end_time+START_HOUR*4);
		updateCreateBox(new_event);

		document.getElementById('cal_new_event_times').focus();
		
	} else if (CREATE_EVENT_MOVE) {
		var event_pos = findPos(new_event)[1];
		var move_position = findMouse(event)[1] - event_pos;
		move_position -= CREATE_EVENT_MOVE_GRAB;
		if (move_position > 0) {
			move_position = Math.floor(move_position / (HOUR_HEIGHT/4));
		} else {
			move_position = Math.ceil(move_position / (HOUR_HEIGHT/4));
		}
		if (move_position != 0) {
			if (CREATE_EVENT_START_TIME + move_position < START_HOUR*4) {
				move_position = START_HOUR*4 - CREATE_EVENT_START_TIME;
			} else if (CREATE_EVENT_END_TIME + move_position+1 > (END_HOUR+1)*4) {
				move_position = (END_HOUR+1)*4 - CREATE_EVENT_END_TIME - 1;
			}
			if (move_position != 0) {
				CREATE_EVENT_MOVED = true;
				CREATE_EVENT_START_TIME += move_position;
				CREATE_EVENT_END_TIME += move_position;
				new_event.style.top = (findPos(day)[1] + (CREATE_EVENT_START_TIME-(START_HOUR*4)) * (HOUR_HEIGHT/4)) + "px";
				
				updateNewEventTimes(CREATE_EVENT_START_TIME, CREATE_EVENT_END_TIME+1);
				updateCreateBox(new_event);
			}
		}
	} else {
		reorder_start_end();
		
		var grab_position = findMouse(event)[1] - findPos(new_event)[1];
		var event_height = (CREATE_EVENT_END_TIME - CREATE_EVENT_START_TIME + 1) * (HOUR_HEIGHT/4);
		var grab_ratio = grab_position / event_height;
		if (grab_ratio >= 0.75 && grab_position > event_height-HOUR_HEIGHT/2) {
			new_event.style.cursor	= 's-resize';
		} else if (grab_ratio <= 0.25 && grab_position < HOUR_HEIGHT/2) {
			new_event.style.cursor	= 'n-resize';
		} else {
			new_event.style.cursor	= 'move';
		}
	}
}

function unclickDay(day,event) {
	var new_event = document.getElementById('cal_new_event');
	if (CREATE_EVENT) {
		CREATE_EVENT = false;
		if (new_event != null && !DESELECTING_EVENT) {
			new_event.style.display	= 'block';
			updateCreateBox(new_event);
			document.getElementById('cal_new_event_times').focus();
		}
	}
	if (CREATE_EVENT_MOVE) {
		CREATE_EVENT_MOVE = false;
		if (new_event) {
			if (!CREATE_EVENT_MOVED) {
				// click
				toggleCreateBox(new_event);
			} else {
				updateCreateBox(new_event);
			}
		}
	}
}

function in_array(value, a) {
	for (var pos=0; pos<a.length; pos++) {
		if (a[pos] == value) {
			return true;
		}
	}
	return false;
}

function addEventListener(instance, eventName, listener) {
	var listenerFn = listener;
	if (instance.addEventListener) {
		instance.addEventListener(eventName, listenerFn, false);
	} else if (instance.attachEvent) {
		listenerFn = function() {
			listener(window.event);
		}
		instance.attachEvent("on" + eventName, listenerFn);
	} else {
		throw new Error("Event registration not supported");
	}
	return {
		instance: instance,
		name: eventName,
		listener: listenerFn
	};
}

onLoadFunctions.push(drawCalendar);
window.onresize = drawCalendar;
// var window_resize = addEventListener(window, "resize", drawCalendar);

