<?php
// make sure a few things are defined
if (!isset($ReadOnly)) {
	$ReadOnly = true;
}
if (!isset($Path)) {
	$Path = array(
		'add' => '/dead',
		'edit' => '/dead',
	);
}
$squash = (count($Days) > 3);



$show_attendence = !$squash;
$attendence_actions = ($show_attendence
	? array('yes' => 'attend', 'no' => 'don&apos;t attend', 'maybe' => 'maybe attend')
	: array('yes' => 'Y', 'no' => 'N', 'maybe' => '?')
);
$attend_state_images = array(
	'yes' => array(
		'/images/prototype/calendar/filter_rsvp_unselect.gif',
		'/images/prototype/calendar/filter_rsvp_select.gif',
		'attend',
	),
	'maybe' => array(
		'/images/prototype/calendar/filter_visible_unselect.png',
		'/images/prototype/calendar/filter_visible_select.png',
		'maybe attend',
	),
	'no' => array(
		'/images/prototype/calendar/filter_hidden_unselect.gif',
		'/images/prototype/calendar/filter_hidden_select.gif',
		'do not attend',
	),
);
?>





<?php
define('HOUR_HEIGHT', 42);

function js_nl2br ($string) {
	return str_replace(array("\r\n", "\r", "\n"), "&lt;br /&gt;", $string);
}
?>

<script type="text/javascript">
var HOUR_HEIGHT = <?php echo(HOUR_HEIGHT); ?>;
var COL_WIDTH = 88;
var MAX_ALL_DAY = 0;
var START_HOUR = 8;
var END_HOUR = 23;
var MAX_START_HOUR = 0;
var MAX_END_HOUR = 29;
var CREATE_EVENT = false;
var CREATE_EVENT_MOVE = false;
var DESELECTING_EVENT = false;
var FIRST_DAY = 0;
var DAYS = new Array();
var CREATE_EVENT_DAY = null;
var CREATE_EVENT_START_TIME = 0;
var CREATE_EVENT_END_TIME = 0;
var CREATE_EVENT_MOVE_GRAB = 0;
<?php foreach ($Days as $date => $day) { ?>
if (FIRST_DAY == 0) {
	FIRST_DAY = new Date();
	FIRST_DAY.setUTCDate(<?php echo(0+substr($date,6,2)); ?>);
	FIRST_DAY.setUTCMonth(<?php echo((substr($date,4,2))-1); ?>);
	FIRST_DAY.setUTCFullYear(<?php echo(substr($date,0,4)); ?>);
	FIRST_DAY.setUTCHours(0);
	FIRST_DAY.setUTCMinutes(0);
	FIRST_DAY.setUTCSeconds(0);
	FIRST_DAY.setUTCMilliseconds(0);
}
DAYS[DAYS.length] = '<?php echo($date); ?>';
<?php } ?>

var EVENT_CACHE = new Array();
var EVENT_COUNT = 0;
var ALL_EVENT_CACHE = new Array();
var ALL_EVENT_COUNT = 0;
<?php
// Create event cache
foreach ($Occurrences as $event_info) {
	if ($event_info->DisplayOnCalendar) {
		if ($event_info->TimeAssociated) { ?>
EVENT_CACHE[EVENT_COUNT] = new Array();
EVENT_CACHE[EVENT_COUNT][0]	= '<?php
	if ($event_info->UserHasPermission('set_attend') &&
		$event_info->State == 'published')
	{
		echo('<div class="cal_event_heading_box">');
		$attendence_writeable = $event_info->Event->Source->IsSupported('attend');
		foreach (array('yes','maybe','no') as $attend_state) {
			$in_state = ($attend_state == $event_info->UserAttending);
			if (!$in_state && $attendence_writeable) {
				echo('<a href="'.
						site_url($Path->OccurrenceAttend($event_info, $attend_state)).$CI->uri->uri_string().
						'">');
			}
			echo('<img src="'.$attend_state_images[$attend_state][$in_state?1:0].'" alt="'.$attend_state_images[$attend_state][2].'" />');
			if (!$in_state && $attendence_writeable) {
				echo('</a>');
			}
		}
		echo('</div>');
	}
	echo(js_nl2br(htmlentities($event_info->Event->Name, ENT_QUOTES, 'UTF-8'))); 
	?>';
EVENT_CACHE[EVENT_COUNT][1]	= '<?php echo($event_info->Event->Category); ?>';
EVENT_CACHE[EVENT_COUNT][2]	= '<?php echo(js_nl2br(htmlentities($event_info->GetLocationDescription(), ENT_QUOTES, 'UTF-8'))); ?>';
EVENT_CACHE[EVENT_COUNT][3]	= '<?php echo(js_nl2br(htmlentities($event_info->Event->Description, ENT_QUOTES, 'UTF-8'))); ?>';
EVENT_CACHE[EVENT_COUNT][4]	= '<?php echo($event_info->StartTime->Timestamp()); ?>';
EVENT_CACHE[EVENT_COUNT][5]	= '<?php echo($event_info->EndTime->Timestamp()); ?>';
EVENT_CACHE[EVENT_COUNT][6]	= '<?php echo(site_url(
										$Path->OccurrenceInfo($event_info).
										$CI->uri->uri_string())); ?>';
EVENT_CACHE[EVENT_COUNT][7]	= -1;
EVENT_CACHE[EVENT_COUNT][8]	= 1;
EVENT_CACHE[EVENT_COUNT][9]	= 0;
EVENT_COUNT++;
<?php	} else { ?>
ALL_EVENT_CACHE[ALL_EVENT_COUNT] = new Array();
ALL_EVENT_CACHE[ALL_EVENT_COUNT][0]	= '<?php echo(js_nl2br(htmlentities($event_info->Event->Name, ENT_QUOTES, 'UTF-8'))); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][1]	= '<?php echo($event_info->Event->Category); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][2]	= '<?php echo(js_nl2br(htmlentities($event_info->LocationDescription, ENT_QUOTES, 'UTF-8'))); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][3]	= '<?php echo(js_nl2br(htmlentities($event_info->Event->Description, ENT_QUOTES, 'UTF-8'))); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][4]	= '<?php echo($event_info->StartTime->Timestamp()); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][5]	= '<?php echo($event_info->EndTime->Timestamp()); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][6]	= '<?php echo(site_url(
												$Path->OccurrenceInfo($event_info).
												$CI->uri->uri_string())); ?>';
ALL_EVENT_COUNT++;
<?php	}
	}
} ?>

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
			Number(((eventEndDate.getTime() - eventStartDate.getTime())/(1000*60*60)).toFixed(2))
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
		var eventStartDate = new Date(EVENT_CACHE[i][4]*1000);
		var eventEndDate = new Date(EVENT_CACHE[i][5]*1000);
		TEMP_CACHE[temp_count] = new Array();
		TEMP_CACHE[temp_count] = EVENT_CACHE[i].slice(0);
		temp_count++;
		/* Display on previous day too*/
		if (eventStartDate.getHours() <= (MAX_END_HOUR-24)) {
			TEMP_CACHE[temp_count] = new Array();
			TEMP_CACHE[temp_count] = EVENT_CACHE[i].slice(0);
			TEMP_CACHE[temp_count][4] = Number(TEMP_CACHE[temp_count][4]) - 86400;
			TEMP_CACHE[temp_count][5] = Number(TEMP_CACHE[temp_count][5]) - 86400;
			TEMP_CACHE[temp_count][9] = 24;
			temp_count++;
		}
		/* Display on next day too */
		if ((((eventEndDate.getTime()-eventStartDate.getTime())/3600000)+eventStartDate.getHours()+(eventStartDate.getMinutes()/60)) > 24) {
			TEMP_CACHE[temp_count] = new Array();
			TEMP_CACHE[temp_count] = EVENT_CACHE[i].slice(0);
			TEMP_CACHE[temp_count][4] = Number(TEMP_CACHE[temp_count][4]) + 86400;
			TEMP_CACHE[temp_count][5] = Number(TEMP_CACHE[temp_count][5]) + 86400;
			TEMP_CACHE[temp_count][9] = -24;
			temp_count++;
		}
	}

	/* Determine Event Clashes */
	for (var i=0; i<temp_count; i++) {
		var clashes = new Array();
		var clash_pos = new Array();
		var clash_count = 0;
		var clash_width = TEMP_CACHE[i][8];
		for (var j=i+1; j<temp_count; j++) {
			if (((TEMP_CACHE[j][4] >= TEMP_CACHE[i][4]) && (TEMP_CACHE[j][4] < TEMP_CACHE[i][5])) || // start of j during i
				((TEMP_CACHE[j][5] > TEMP_CACHE[i][4]) && (TEMP_CACHE[j][5] <= TEMP_CACHE[i][5])) || // end of j during i
				((TEMP_CACHE[j][4] < TEMP_CACHE[i][4]) && (TEMP_CACHE[j][5] > TEMP_CACHE[i][5])))  // j encompases i
			{
				clashes[clash_count] = j;
				if (TEMP_CACHE[j][7] != -1)
					clash_pos[clash_pos.length] = TEMP_CACHE[j][7];
				if (TEMP_CACHE[j][8] > clash_width)
					clash_width = TEMP_CACHE[j][8];
				clash_count++;
			}
		}
		if (TEMP_CACHE[i][7] != -1)
			clash_pos[clash_pos.length] = TEMP_CACHE[i][7];
		if ((clash_count+1) > clash_width)
			clash_width = clash_count + 1;
		var current_pos = 0;
		while (in_array(current_pos, clash_pos)) { current_pos++; }
		TEMP_CACHE[i][8] = clash_width;
		if (TEMP_CACHE[i][7] == -1) {
			TEMP_CACHE[i][7] = current_pos;
			while (in_array(current_pos++, clash_pos)) { current_pos++; }
		}
		for (var j=0; j<clash_count; j++) {
			TEMP_CACHE[clashes[j]][8] = clash_width;
			if (TEMP_CACHE[clashes[j]][7] == -1) {
				TEMP_CACHE[clashes[j]][7] = current_pos;
				while (in_array(current_pos++, clash_pos)) { current_pos++; }
			}

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
			Number((eventStartDate.getHours()+(eventStartDate.getMinutes()/60)+TEMP_CACHE[i][9]).toFixed(2)),
			Number(((eventEndDate.getTime() - eventStartDate.getTime())/(1000*60*60)).toFixed(2)),
			TEMP_CACHE[i][7],
			TEMP_CACHE[i][8]
		);
	}
	return false;
}

function drawAllDayEvent (id, category, link, title, start_hour, duration, height) {
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
	start_left = start_left + findPos(p_day)[0];
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
	new_event.className		= 'cal_event cal_category_' + category;
	new_event.style.top		= findPos(p_ele)[1] + (height*(HOUR_HEIGHT/2)) + 'px';
	new_event.style.left	= start_left + 'px';
	new_event.style.height	= ((HOUR_HEIGHT/2)-2) + 'px';
	new_event.style.width	= (duration_width-2) + 'px';
// 	new_event.onclick		= function(){ alert('You clicked on this event!'); };

	new_event.appendChild(event_title);

	document.getElementById('calendar_all_day_events').appendChild(new_event);

	if (height > MAX_ALL_DAY)
		MAX_ALL_DAY = height;
}

function drawEvent(parent, id, category, link, title, content_time, content_location, content_description, start_hour, duration, left, width) {
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
	new_event.className		= 'cal_event cal_category_' + category;
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
	document.getElementById('cal_new_event_start').innerHTML = 'Start: ' + start_display3 + ':' + start_display2;
	
	var end_display2 = (end_time%4)*15;
	if (end_display2 == '0')
		end_display2 = '00';
	var end_display3 = Math.floor(end_time/4);
	if (end_display3 > 23)
		end_display3 -= 24;
	document.getElementById('cal_new_event_end').innerHTML = 'Finish: ' + end_display3 + ':' + end_display2;
}

function clickDay (day,event) {
	var new_event = document.getElementById('cal_new_event');
	
	// Swap stored start and end time if they're the wrong way
	if (CREATE_EVENT_END_TIME < CREATE_EVENT_START_TIME) {
		var tmp = CREATE_EVENT_END_TIME;
		CREATE_EVENT_END_TIME = CREATE_EVENT_START_TIME;
		CREATE_EVENT_START_TIME = tmp;
	}

	var pos_relative = findMouse(event)[1] - findPos(day)[1];
	pos_relative = Math.floor((pos_relative - 1 + 2)/(HOUR_HEIGHT/4));
	
	DESELECTING_EVENT = false;
	if (new_event != null) {
		DESELECTING_EVENT = new_event.style.display != 'none'; + (START_HOUR/4)
		if (day != CREATE_EVENT_DAY ||
			new_event.style.display == 'none' ||
			pos_relative + (START_HOUR*4) < CREATE_EVENT_START_TIME ||
			pos_relative + (START_HOUR*4) > CREATE_EVENT_END_TIME)
		{
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
			CREATE_EVENT_MOVE_GRAB = grab_position;
			return true;
		}
	}
	CREATE_EVENT_DAY = day;

	var duration = 1.0;
	var width = Math.floor(day.offsetWidth-2-5);;

	new_event 				= document.createElement('div');
	new_event.id			= 'cal_new_event';
	new_event.className		= 'cal_event cal_category_new_event';
	new_event.style.display	= 'none';
	new_event.style.left	= findPos(day)[0] + 'px';
	new_event.style.top		= findPos(day)[1] + ((pos_relative*(HOUR_HEIGHT/4))) + 'px';
	new_event.style.width	= width + 'px';
	new_event.style.height	= ((duration*HOUR_HEIGHT)-2) + 'px';
	new_event.ondblclick	= function(){ confirm('Create a new event here!'); };

	var display					= (pos_relative + (START_HOUR*4));
	CREATE_EVENT_START_TIME	= display;
	CREATE_EVENT_END_TIME = CREATE_EVENT_START_TIME + 4;
	var display2 = (display%4)*15;
	if (display2 == '0')
		display2 = '00';
	var display3 = Math.floor(display/4);
	if (display3 > 23)
		display3 -= 24;
		
	var start_time			= document.createElement('div');
	start_time.id			= 'cal_new_event_start';
	start_time.appendChild(document.createTextNode('Start: ' + display3 + ':' + display2));

	var end_display2 = (CREATE_EVENT_END_TIME%4)*15;
	if (end_display2 == '0')
		end_display2 = '00';
	var end_display3 = Math.floor(CREATE_EVENT_END_TIME/4);
	if (end_display3 > 23)
		end_display3 -= 24;
	
	var end_time			= document.createElement('div');
	end_time.id				= 'cal_new_event_end';
	end_time.appendChild(document.createTextNode('Finish: ' + end_display3 + ':' + end_display2));

	new_event.appendChild(start_time);
	new_event.appendChild(end_time);
// 	day.insertBefore(new_event, day.firstChild);
	day.appendChild(new_event);
	CREATE_EVENT = true;
}

function moveDay (day, event) {
	if (CREATE_EVENT) {
		var new_event = document.getElementById('cal_new_event');
		if (new_event == null)
			return;
		
		
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
		new_event.style.height = (((end_time-start_time)*(HOUR_HEIGHT/4))-2) + 'px';

		updateNewEventTimes(start_time+START_HOUR*4, end_time+START_HOUR*4);

		document.getElementById('cal_new_event_start').focus();
		
	} else if (CREATE_EVENT_MOVE) {
		var new_event = document.getElementById('cal_new_event');
		if (new_event == null)
			return;
		
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
			CREATE_EVENT_START_TIME += move_position;
			CREATE_EVENT_END_TIME += move_position;
			new_event.style.top = (findPos(day)[1] + (CREATE_EVENT_START_TIME-(START_HOUR*4)) * (HOUR_HEIGHT/4)) + "px";
			
			updateNewEventTimes(CREATE_EVENT_START_TIME, CREATE_EVENT_END_TIME+1);
		}
	}
}

function unclickDay(day,event) {
	if (CREATE_EVENT) {
		CREATE_EVENT = false;
		var new_event = document.getElementById('cal_new_event');
		if (new_event != null && !DESELECTING_EVENT) {
			new_event.style.display	= 'block';
		}
		document.getElementById('cal_new_event_start').focus();
	}
	if (CREATE_EVENT_MOVE) {
		CREATE_EVENT_MOVE = false;
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

function removeEventListener(event) {
	var instance = event.instance;
	if (instance.removeEventListener) {
		instance.removeEventListener(event.name, event.listener, false);
	} else if (instance.detachEvent) {
		instance.detachEvent("on" + event.name, event.listener);
	}
}

onLoadFunctions.push(drawCalendar);
window.onresize = drawCalendar;
//var window_resize = addEventListener(window, "resize", drawCalendar);
</script>

<style type="text/css">
table#calendar_view {
	border-collapse: collapse;
	margin: 0;
}

table#calendar_view th {
	text-align: center;
}

table#calendar_view td#calendar_all_day_events {
	border: 1px #999 solid;
	height: <?php echo(HOUR_HEIGHT/2); ?>px;
}

table#calendar_view td#calendar_time_up {
	text-align: center;
	vertical-align: bottom;
}

table#calendar_view td#calendar_time_down {
	text-align: center;
	vertical-align: top;
}

table#calendar_view td.cal_day_counts {
	text-align: center;
	vertical-align: bottom;
}

table#calendar_view td.cal_day_counts2 {
	text-align: center;
	vertical-align: top;
}

table#calendar_view td#calendar_time {
	vertical-align: top;
}

table#calendar_view td#calendar_time div {
	height: <?php echo(HOUR_HEIGHT); ?>px;
	text-align: right;
}

table#calendar_view td.calendar_day {
	border: 1px #999 solid;
	background-image: url('/images/prototype/calendar/grid2.gif');
	background-position: top left;
	height: <?php echo(24*HOUR_HEIGHT); ?>px;
	width: <?php echo(floor(100/count($Days))); ?>%;
	vertical-align: top;
	padding: 0;
}

table#calendar_view td.calendar_day div.cal_event {
	overflow: hidden;
	position: absolute;
	width: auto;
	margin: 0 2px;
	padding: 0;
	-moz-opacity:0.8;
}

table#calendar_view td.calendar_day div.cal_event_nojs {
	position: static;
	margin-bottom: 5px;
}

table#calendar_view td#calendar_all_day_events div.cal_event {
	overflow: hidden;
	position: absolute;
	width: auto;
	margin: 0 0 2px 0;
	padding: 0;
	-moz-opacity:0.8;
}

table#calendar_view div.cal_event div.cal_event_heading {
	padding: 0 2px;
}

table#calendar_view div.cal_event div.cal_event_heading div.cal_event_heading_box {
	float: right;
	clear: none;
}
table#calendar_view div.cal_event div.cal_event_heading div.cal_event_heading_box img {
	width: 12px;
	height: 12px;
}

table#calendar_view div.cal_event div.cal_event_heading a {
	color: #fff;
}

table#calendar_view div.cal_event div.cal_event_heading a:hover {
	text-decoration: none;
}

table#calendar_view div.cal_category_new_event {
	border: 1px #20C1F0 solid;
	background-color: #a9ecff;
}

table#calendar_view div.cal_category_new_event div {
	text-align: right;
	font-size: x-small;
}

table#calendar_view div.cal_category_new_event input {
	width: 80%;
	border: 1px #20C1F0 solid;
	background-color: #d4f4fd;
}

<?php foreach ($Categories as $Name => $Settings) { ?>
table#calendar_view div.cal_category_<?php echo($Name); ?> {
	border: 1px #<?php echo($Settings['border_colour']); ?> solid;
	background-color: #<?php echo($Settings['colour']); ?>;
	background-position: bottom right;
	background-repeat: no-repeat;
	<?php if ($Settings['image'] !== NULL) { ?>
	background-image: url('<?php echo($Settings['image']); ?>');
	<?php } ?>
}

table#calendar_view div.cal_category_<?php echo($Name); ?> div.cal_event_heading {
	background-color: #<?php echo($Settings['heading_colour']); ?>;
}
<?php } ?>

table#calendar_view div.cal_event div.cal_event_info {
	font-size: x-small;
	padding: 0 2px;
}

table#calendar_view div.cal_event_split_top {
	border-top: 0;
}

table#calendar_view div.cal_event_split_bottom {
	border-bottom: 0;
}
</style>


<div class="BlueBox">
<div align="center">
<?php
if (isset($BackwardUrl)) {
	echo('<a href="'.$BackwardUrl.'"><img src="'.site_url('images/prototype/calendar/backward.gif').'" alt="Backward" /></a> ');
}
if (isset($NowUrl)) {
	echo('<a href="'.$NowUrl.'">'.$NowUrlLabel.'</a> ');
}
if (isset($ForwardUrl)) {
	echo('<a href="'.$ForwardUrl.'"><img src="'.site_url('images/prototype/calendar/forward.gif').'" alt="Forward" /></a> ');
}
?>
</div>

<table id="calendar_view">
	<!-- Day Headings -->
	<tr>
		<td rowspan="3">&nbsp;</td>
<?php foreach ($Days as $date => $times) { ?>
		<th>
			<a href="<?php echo($times['link']); ?>">
				<?php echo($times['date']->Format('D')); ?><br />
				<?php echo($times['date']->Format('jS M')); ?>
			</a>
		</th>
<?php } ?>
	</tr>

	<!-- Spacer Row -->
	<tr>
		<td colspan="<?php echo(count($Days)); ?>">&nbsp;</td>
	</tr>

	<!-- All Day Events -->
	<tr>
		<td id="calendar_all_day_events" colspan="<?php echo(count($Days)); ?>"></td>
	</tr>

	<!-- Spacer Row -->
	<tr>
		<td id="calendar_time_up">
			<a href="#" onclick="return alterTime(-1);"><img src="/images/prototype/calendar/arrow_up.jpg" alt="Move Time -1hr" title="Move Time -1hr" /></a>
			<a href="#" onclick="return alterTime(1);"><img src="/images/prototype/calendar/arrow_down.jpg" alt="Move Time +1hr" title="Move Time +1hr" /></a>
		</td>
<?php foreach ($Days as $date => $day) { ?>
		<td id="cal_day_<?php echo($date); ?>_before" class="cal_day_counts"></td>
<?php } ?>
	</tr>
	<tr>
		<td id="debug_dump"></td>
	</tr>

	<!-- Main Calendar Display -->
	<tr>
		<!-- Time Column -->
		<td id="calendar_time"></td>

		<!-- Day Columns -->
<?php
	foreach ($Days as $date => $day) { ?>
		<td id="cal_day_<?php echo($date); ?>" class="calendar_day" onmousedown="clickDay(this,event);" onmouseup="unclickDay(this,event);" onmousemove="moveDay(this,event);">
<?php	foreach ($day['events'] as $time => $ocs) {
			foreach ($ocs as $event_info) {
				if (($event_info->DisplayOnCalendar) && ($event_info->TimeAssociated)) {
?>
			<div class="cal_event cal_event_nojs cal_category_<?php echo($event_info->Event->Category); ?>"<?php /* onclick="alert('You clicked on this event!');"*/ ?>>
				<div class="cal_event_heading">
					<?php
					if ($event_info->UserHasPermission('set_attend') &&
						$event_info->State == 'published')
					{
					?><div class="cal_event_heading_box">
					<?php
						$attendence_writeable = $event_info->Event->Source->IsSupported('attend');
						foreach (array('yes','maybe','no') as $attend_state) {
							$in_state = ($attend_state == $event_info->UserAttending);
							if (!$in_state && $attendence_writeable) {
								echo('<a href="'.
										site_url($Path->OccurrenceAttend($event_info, $attend_state)).$CI->uri->uri_string().
										'">');
							}
							echo('<img src="'.$attend_state_images[$attend_state][$in_state?1:0].'" alt="'.$attend_state_images[$attend_state][2].'" />');
							if (!$in_state && $attendence_writeable) {
								echo('</a>');
							}
						}
					?></div>
					<?php
					}
					?>
					<a href="<?php echo(site_url(
									$Path->OccurrenceInfo($event_info).
									$CI->uri->uri_string())); ?>">
						<?php echo(js_nl2br(htmlentities($event_info->Event->Name, ENT_QUOTES, 'UTF-8'))); ?>
					</a>
				</div>
				<div class="cal_event_info">
					<?php echo($event_info->StartTime->Format('H:i') . ' - ' . $event_info->EndTime->Format('H:i')); ?>
				</div>
				<div class="cal_event_info">
					<i><?php echo(js_nl2br(htmlentities($event_info->GetLocationDescription(), ENT_QUOTES, 'UTF-8'))); ?></i>
					<?php if (!$squash && !empty($event_info->Event->Description)) {
						echo('<br />'.htmlentities($event_info->Event->Description, ENT_QUOTES, 'UTF-8'));
					} ?>
				</div>
			</div>
<?php			}
			}
		}
?>
		</td>
<?php } ?>
		<!-- End of Day Columns -->
	</tr>
	<!-- End of Main Calendar Display -->

	<!-- Spacer Row -->
	<tr>
		<td id="calendar_time_down">
			<a href="#" onclick="return alterTime(-1);"><img src="/images/prototype/calendar/arrow_up.jpg" alt="Move Time -1hr" title="Move Time -1hr" /></a>
			<a href="#" onclick="return alterTime(1);"><img src="/images/prototype/calendar/arrow_down.jpg" alt="Move Time +1hr" title="Move Time +1hr" /></a>
		</td>
<?php foreach ($Days as $date => $day) { ?>
		<td id="cal_day_<?php echo($date); ?>_after" class="cal_day_counts2"></td>
<?php } ?>
	</tr>
</table>

</div>











<?php /* ?>


<div class="BlueBox">
<?php

echo('<div align="center">');
if (isset($BackwardUrl)) {
	echo('<a href="'.$BackwardUrl.'"><img src="'.site_url('images/prototype/calendar/backward.gif').'" alt="Backward" /></a> ');
}
if (isset($NowUrl)) {
	echo('<a href="'.$NowUrl.'">'.$NowUrlLabel.'</a> ');
}
if (isset($ForwardUrl)) {
	echo('<a href="'.$ForwardUrl.'"><img src="'.site_url('images/prototype/calendar/forward.gif').'" alt="Forward" /></a> ');
}
echo('</div>');


echo('<table id="calviewCalTable" border="0" cellpadding="0" cellspacing="0" width="100%">');
echo('<tr>');
foreach ($Days as $date => $times) {
	echo('<th class="calviewCalHeadingCell">');
	echo('<a href="'.$times['link'].'">');
	echo($times['date']->Format('l'));
	echo('<br />');
	echo($times['date']->Format('jS M'));
	echo('</a>');
	echo('</th>');
}
echo('</tr><tr>');
foreach ($Days as $date => $day) {
	$times = $day['events'];
	echo('<td>');
	if (array_key_exists('000000',$times)) {
		foreach ($times['000000'] as $occurrence) {
			if (!$occurrence->TimeAssociated) {
				$CI->load->view('calendar/occurrence_cell', array(
					'Occurrence' => & $occurrence,
					'Categories' => & $Categories,
					'Squash' => $squash,
					'ReadOnly' => $ReadOnly,
					'Path' => $Path,
				));
			}
		}
	}
	echo('</td>');
}
echo('</tr><tr>');


$TEST_OUTPUT = '';
foreach ($Days as $date => $day) {
	$TEST_OUTPUT .= "----- ".$date."-----\n";
	$times = $day['events'];
	echo('<td class="calviewCalEventsCell">');
	foreach ($times as $time => $ocs) {
		foreach ($ocs as $occurrence) {
			if ($occurrence->TimeAssociated) {
				$TEST_OUTPUT .= "-> EVENT <-\n";
				$TEST_OUTPUT .= print_r($occurrence, TRUE);
				$TEST_OUTPUT .= "\n\n";
				$CI->load->view('calendar/occurrence_cell', array(
					'Occurrence' => & $occurrence,
					'Categories' => & $Categories,
					'Squash' => $squash,
					'ReadOnly' => $ReadOnly,
					'Path' => $Path,
				));
			}
		}
	}
	$TEST_OUTPUT .= "\n\n\n\n\n\n\n";
	echo('</td>');
}
echo('</tr>');
echo('</table>');

//echo('<pre>');
//print($TEST_OUTPUT);
//echo('</pre>');

//print('<pre>');
//print_r($Occurrences);
//print('</pre>');
?>
<?php //*/ ?>