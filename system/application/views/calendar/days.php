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
?>







<?php define('HOUR_HEIGHT', 42); ?>

<script type="text/javascript">
var HOUR_HEIGHT = 42;
var COL_WIDTH = 88;
var MAX_ALL_DAY = 0;
var START_HOUR = 8;
var END_HOUR = 23;
var MAX_START_HOUR = 0;
var MAX_END_HOUR = 29;
var CREATE_EVENT = false;
var DAYS = new Array();
<?php foreach ($Days as $date => $day) { ?>
DAYS[DAYS.length] = '<?php echo($date); ?>';
<?php } ?>

function drawCalendar () {
	// Reset event counters
	for (i=0;i<=(DAYS.length-1);i++) {
		document.getElementById('cal_day_'+DAYS[i]+'_before').innerHTML = '';
		document.getElementById('cal_day_'+DAYS[i]+'_after').innerHTML = '';
	}

	clearCalendar();
	timeCalendar();
	resizeCalendar();

	drawAllDayEvent('901', 'Anniversary', '/test/link/', 'Birthday 20: Richard Ingle', '00:00 - 12:00', 24, 35.98, 0);
	drawAllDayEvent('903', 'Meeting', '/test/link/', 'Conference', '12:00 - 23:59', 60, 11.98, 1);
	drawAllDayEvent('900', 'Social', '/test/link/', 'Day Event', '00:00 - 23:59', 0, 23.98, 0);
	drawAllDayEvent('902', 'Facebook', '/test/link/', 'FragSoc', '12:00 - 18:00', 12, 29.98, 1);

	resizeCalendarAllDay();

	drawEvent('cal_day_20070716', '001', 'Academic', '/test/link/', 'RDQ', '10:15 - 11:15', 10.25, 1);
	drawEvent('cal_day_20070716', '002', 'Academic', '/test/link/', 'NDS', '11:15 - 13:15', 11.25, 2);
	drawEvent('cal_day_20070716', '003', 'Academic', '/test/link/', 'NDS', '13:15 - 14:15', 13.25, 1);
	drawEvent('cal_day_20070716', '004', 'Academic', '/test/link/', 'MCP', '14:15 - 16:15', 14.25, 2);
	drawEvent('cal_day_20070716', '005', 'Academic', '/test/link/', 'LPA', '17:15 - 18:15', 17.25, 1);
	drawEvent('cal_day_20070716', '006', 'Social', '/test/link/', 'Badminton', '19:30 - 22:00', 19.5, 2.5);

	drawEvent('cal_day_20070717', '007', 'Academic', '/test/link/', 'NDS', '10:15 - 11:15', 10.25, 1);
	drawEvent('cal_day_20070717', '008', 'Academic', '/test/link/', 'LPA', '13:15 - 14:15', 13.25, 1);
	drawEvent('cal_day_20070717', '009', 'Facebook', '/test/link/', 'FragSoc Pub Crawl', '19:30 - 00:00', 19.5, 4.5);
		drawEvent('cal_day_20070717', '025', 'Facebook', '/test/link/', 'Facebook', '01:00 - 04:00', 1, 3, 0, 4);
		drawEvent('cal_day_20070717', '026', 'Facebook', '/test/link/', 'Facebook', '02:00 - 06:00', 2, 4, 2, 4);
		drawEvent('cal_day_20070717', '027', 'Facebook', '/test/link/', 'Facebook', '01:30 - 03:00', 1.5, 1.5, 1, 4);
		drawEvent('cal_day_20070717', '028', 'Facebook', '/test/link/', 'Facebook', '03:30 - 06:00', 3.5, 2.5, 3, 4);


	drawEvent('cal_day_20070718', '010', 'Academic', '/test/link/', 'LPA', '10:15 - 11:15', 10.25, 1);
	drawEvent('cal_day_20070718', '018', 'Meeting', '/test/link/', 'Yorker Dev Meeting', '13:15 - 16:15', 13.25, 3);
	drawEvent('cal_day_20070718', '015', 'Anniversary', '/test/link/', 'Birthday 21: Pingu', '17:00 - 23:30', 17, 6.5);
		drawEvent('cal_day_20070718', '022', 'Social', '/test/link/', 'Viking Raid II', '03:00 - 07:00', 3, 4, 0, 3);
		drawEvent('cal_day_20070718', '023', 'Social', '/test/link/', 'Viking Raid II', '05:00 - 06:00', 5, 1, 2, 3);
		drawEvent('cal_day_20070718', '024', 'Social', '/test/link/', 'Viking Raid II', '06:00 - 09:00', 6, 3, 1, 3);

	drawEvent('cal_day_20070719', '011', 'Academic', '/test/link/', 'LPA', '11:15 - 12:15', 11.25, 1);
		drawEvent('cal_day_20070719', '021', 'Academic', '/test/link/', 'RDQ', '12:15 - 13:15', 12.25, 1, 0, 2);
		drawEvent('cal_day_20070719', '012', 'Academic', '/test/link/', 'RDQ', '12:15 - 13:15', 12.25, 1, 1, 2);
	drawEvent('cal_day_20070719', '013', 'Academic', '/test/link/', 'NDS', '15:15 - 16:15', 15.25, 1);
	drawEvent('cal_day_20070719', '014', 'Meeting', '/test/link/', 'Watch TV', '18:00 - 23:00', 18, 5);

	drawEvent('cal_day_20070720', '016', 'Academic', '/test/link/', 'RDQ', '09:15 - 10:15', 9.25, 1);
	drawEvent('cal_day_20070720', '017', 'Academic', '/test/link/', 'MCP', '13:15 - 16:15', 13.25, 3);

	drawEvent('cal_day_20070721', '019', 'Facebook', '/test/link/', 'FragSoc LAN', '12:00 - 00:00', 12, 12);

	drawEvent('cal_day_20070722', '020', 'Facebook', '/test/link/', 'FragSoc LAN', '00:00 - 17:00', 0, 17);

	return false;
}

function drawAllDayEvent (id, category, link, title, content, start_hour, duration, height) {
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
	event_link.appendChild(document.createTextNode(title));

	var event_title			= document.createElement('div');
	event_title.className	= 'cal_event_heading';
	event_title.appendChild(event_link);

	var event_content		= document.createElement('div');
	event_content.className	= 'cal_event_info';
	event_content.appendChild(document.createTextNode(content));

	var new_event 			= document.createElement('div');
	new_event.id			= 'cal_event_' + id;
	new_event.className		= 'cal_event cal_category_' + category;
	new_event.style.top		= findPos(p_ele)[1] + (height*(HOUR_HEIGHT/2)) + 'px';
	new_event.style.left	= start_left + 'px';
	new_event.style.height	= ((HOUR_HEIGHT/2)-2) + 'px';
	new_event.style.width	= (duration_width-2) + 'px';
	new_event.onclick		= function(){ alert('You clicked on this event!'); };

	new_event.appendChild(event_title);
	new_event.appendChild(event_content);

	document.getElementById('calendar_all_day_events').appendChild(new_event);

	if (height > MAX_ALL_DAY)
		MAX_ALL_DAY = height;
}

function drawEvent(parent, id, category, link, title, content, start_hour, duration, left, width) {
	var p_ele = document.getElementById(parent);
	if (p_ele == null)
		return;

	if (left == null)
		left = 0;
	if (width == null)
		width = 1;
	width = Math.floor((p_ele.offsetWidth / width)-2-5);

	var full_display = true;

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
	new_event.onclick		= function(){ alert('You clicked on this event!'); };

	if ((start_hour+duration) >= (END_HOUR+1)) {
		duration = (END_HOUR+1) - start_hour;
		new_event.className += ' cal_event_split_bottom';
	}
	start_hour = start_hour-START_HOUR;
	if (start_hour < 0) {
		duration += start_hour;
		start_hour = 0;
		new_event.className += ' cal_event_split_top';
	}
	new_event.style.top		= findPos(p_ele)[1] + 1 + ((start_hour*HOUR_HEIGHT)-2) + 'px';
	new_event.style.height	= ((duration*HOUR_HEIGHT)-2) + 'px';

	if (full_display) {
		var event_link			= document.createElement('a');
		event_link.href			= link;
		event_link.appendChild(document.createTextNode(title));

		var event_title			= document.createElement('div');
		event_title.className	= 'cal_event_heading';
		event_title.appendChild(event_link);

		var event_content		= document.createElement('div');
		event_content.className	= 'cal_event_info';
		event_content.appendChild(document.createTextNode(content));

		new_event.appendChild(event_title);
		new_event.appendChild(event_content);
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

function clearCalendar() {
	for (i=0;i<=(DAYS.length-1);i++) {
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

function clickDay (day,event) {
	var new_event = document.getElementById('cal_new_event');
	if (new_event !== null) {
		new_event.parentNode.removeChild(new_event);
	}

	var pos_relative = findMouse(event)[1] - findPos(day)[1];
	pos_relative = Math.floor((pos_relative - 1 + 2)/(HOUR_HEIGHT/4));

	var duration = 0.5;
	var width = Math.floor(day.offsetWidth-2-5);;

	new_event 				= document.createElement('div');
	new_event.id			= 'cal_new_event';
	new_event.className		= 'cal_event cal_category_new_event';
	new_event.style.left	= findPos(day)[0] + 'px';
	new_event.style.top		= findPos(day)[1] + 1 + ((pos_relative*(HOUR_HEIGHT/4))-2) + 'px';
	new_event.style.width	= width + 'px';
	new_event.style.height	= ((duration*HOUR_HEIGHT)-2) + 'px';
	new_event.onclick		= function(){ alert('CREATE NEW EVENT HERE!'); };

	var start_time			= document.createElement('div');
	start_time.id			= 'cal_new_event_start';
	var display					= (pos_relative + (START_HOUR*4));
	var display2 = (display%4)*15;
	if (display2 == '0')
		display2 = '00';
	var display3 = Math.floor(display/4);
	if (display3 > 23)
		display3 -= 24;
	start_time.appendChild(document.createTextNode('Start: ' + display3 + ':' + display2));

	var end_time			= document.createElement('div');
	end_time.id				= 'cal_new_event_end';
	end_time.appendChild(document.createTextNode('End'));

	new_event.appendChild(start_time);
	new_event.appendChild(end_time);
	day.appendChild(new_event);
	CREATE_EVENT = true;
}

function moveDay (day, event) {
	if (CREATE_EVENT) {
		var new_event = document.getElementById('cal_new_event');
		if (new_event == null)
			return;

		var pos_relative = findMouse(event)[1] - findPos(new_event)[1];
		if (pos_relative < 0)
			return;
		pos_relative = Math.ceil((pos_relative + 2)/(HOUR_HEIGHT/4));
		new_event.style.height = ((pos_relative*(HOUR_HEIGHT/4))-2) + 'px';

		var start_time = Math.floor((findPos(new_event)[1] - (findPos(day)[1]-1))/(HOUR_HEIGHT/4));
		var display = start_time + pos_relative;
		var display2 = (display%4)*15;
		if (display2 == '0')
			display2 = '00';
		var display3 = Math.floor(display/4)+START_HOUR;
		if (display3 > 23)
			display3 -= 24;
		document.getElementById('cal_new_event_end').innerHTML = 'Finish: ' + display3 + ':' + display2;

		document.getElementById('cal_new_event_start').focus();
	}
}

function unclickDay(day,event) {
	CREATE_EVENT = false;
	document.getElementById('cal_new_event_start').focus();
}
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

<a href="#" onclick="return drawCalendar();">Add New Event</a>

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

	<!-- Main Calendar Display -->
	<tr>
		<!-- Time Column -->
		<td id="calendar_time"></td>

		<!-- Day Columns -->
<?php foreach ($Days as $date => $day) { ?>
		<td id="cal_day_<?php echo($date); ?>" class="calendar_day" onmousedown="clickDay(this,event);" onmouseup="unclickDay(this,event);" onmousemove="moveDay(this,event);">
<?php	foreach ($day['events'] as $time => $ocs) {
			foreach ($ocs as $occurrence) {
				if ($occurrence->TimeAssociated) {
/*					$CI->load->view('calendar/event_box', array(
						'Occurrence'	=>	&$occurrence,
						'Categories'	=>	&$Categories,
						'Squash'		=>	$squash,
						'ReadOnly'		=>	$ReadOnly,
						'Path'			=>	$Path,
					));
*/				}
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

echo('<pre>');
print($TEST_OUTPUT);
echo('</pre>');
?>
