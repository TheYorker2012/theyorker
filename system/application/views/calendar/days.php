<?php

/**
 * @file views/calendar/days.php
 * @param $Categories Category information.
 * @param $AllowEventCreate bool Whether creation of events is permitted.
 * @param $Path string Path information.
 */
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
$display_attendence_links = !$squash;

if (!isset($AllowEventCreate)) {
	$AllowEventCreate = false;
}


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
		'states' => array('published'),
	),
	'maybe' => array(
		'/images/prototype/calendar/filter_visible_unselect.png',
		'/images/prototype/calendar/filter_visible_select.png',
		'maybe attend',
		'states' => array('published'),
	),
	'no' => array(
		'/images/prototype/calendar/filter_hidden_unselect.gif',
		'/images/prototype/calendar/filter_hidden_select.gif',
		'do not attend',
		'states' => array('published'),
	),
	'dismiss' => array(
		'/images/prototype/calendar/filter_hidden_select.gif',
		NULL,
		'dismiss',
		'states' => array('cancelled'),
		'alias' => 'maybe',
	),
);

// Load the calendar css helper
get_instance()->load->helper('calendar_css_classes');

?>





<?php
$HourHeight = 42;
?>

<script type="text/javascript">
var HOUR_HEIGHT = <?php echo($HourHeight); ?>;
var COL_WIDTH = 88;
var MAX_ALL_DAY = 0;
var START_HOUR = 8;
var END_HOUR = 23;
var MAX_START_HOUR = 0;
var MAX_END_HOUR = 29;
var CREATE_EVENT = false;
var CREATE_EVENT_MOVE = false;
var CREATE_EVENT_MOVED = false;
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
	FIRST_DAY.setUTCHours(0,0,0,0);
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
	if ($display_attendence_links &&
		$event_info->UserHasPermission('set_attend') &&
		in_array($event_info->State, array('published', 'cancelled')))
	{
		echo('<div class="cal_event_heading_box">');
		$attendence_writeable = $event_info->Event->Source->IsSupported('attend');
		foreach ($attend_state_images as $attend_state => $info) {
			if (in_array($event_info->State, $info['states'])) {
				if (isset($info['alias'])) {
					$attend_state = $info['alias'];
				}
				$in_state = ($attend_state == $event_info->UserAttending);
				if (NULL !== $info[$in_state?1:0]) {
					if (!$in_state && $attendence_writeable) {
						echo('<a href="'.
								site_url($Path->OccurrenceAttend($event_info, $attend_state)).$CI->uri->uri_string().
								'">');
					}
					echo('<img src="'.$info[$in_state?1:0].'" alt="'.$info[2].'" title="'.$info[2].'" />');
					if (!$in_state && $attendence_writeable) {
						echo('</a>');
					}
				}
			}
		}
		echo('</div>');
	}
	echo(js_nl2br(htmlentities($event_info->Event->Name, ENT_QUOTES, 'UTF-8'))); 
	?>';
EVENT_CACHE[EVENT_COUNT][1]	= '<?php echo($event_info->Event->Category); ?>';
EVENT_CACHE[EVENT_COUNT][2]	= '<?php
	if ($event_info->State == 'cancelled') {
		echo('Cancelled');
	} else {
		echo(js_nl2br(htmlentities($event_info->GetLocationDescription(), ENT_QUOTES, 'UTF-8')));
	}
?>';
EVENT_CACHE[EVENT_COUNT][3]	= '<?php echo(js_nl2br(htmlentities($event_info->Event->Description, ENT_QUOTES, 'UTF-8'))); ?>';
EVENT_CACHE[EVENT_COUNT][4]	= '<?php echo($event_info->StartTime->Timestamp()); ?>';
EVENT_CACHE[EVENT_COUNT][5]	= '<?php echo($event_info->EndTime->Timestamp()); ?>';
EVENT_CACHE[EVENT_COUNT][6]	= '<?php echo(site_url(
										$Path->OccurrenceInfo($event_info).
										$CI->uri->uri_string())); ?>';
EVENT_CACHE[EVENT_COUNT][7]	= '<?php echo(implode(' ', CalCssGetEventClasses($event_info))); ?>';
EVENT_CACHE[EVENT_COUNT][8]	= -1;
EVENT_CACHE[EVENT_COUNT][9]	= 1;
EVENT_CACHE[EVENT_COUNT][10]	= 0;
EVENT_COUNT++;
<?php	} else { ?>
ALL_EVENT_CACHE[ALL_EVENT_COUNT] = new Array();
ALL_EVENT_CACHE[ALL_EVENT_COUNT][0]	= '<?php echo(js_nl2br(htmlentities($event_info->Event->Name, ENT_QUOTES, 'UTF-8'))); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][1]	= '<?php echo($event_info->Event->Category); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][2]	= '<?php
	if ($event_info->State == 'cancelled') {
		echo('Cancelled');
	} else {
		echo(js_nl2br(htmlentities($event_info->GetLocationDescription(), ENT_QUOTES, 'UTF-8')));
	}
?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][3]	= '<?php echo(js_nl2br(htmlentities($event_info->Event->Description, ENT_QUOTES, 'UTF-8'))); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][4]	= '<?php echo($event_info->StartTime->Timestamp()); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][5]	= '<?php echo($event_info->EndTime->Timestamp()); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][6]	= '<?php echo(site_url(
												$Path->OccurrenceInfo($event_info).
												$CI->uri->uri_string())); ?>';
ALL_EVENT_CACHE[ALL_EVENT_COUNT][7]	= '<?php echo(implode(' ', CalCssGetEventClasses($event_info))); ?>';
ALL_EVENT_COUNT++;
<?php	}
	}
} ?>

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
	height: <?php echo($HourHeight/2); ?>px;
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
	height: <?php echo($HourHeight); ?>px;
	text-align: right;
}

table#calendar_view td.calendar_day {
	border: 1px #999 solid;
	background-image: url('/images/prototype/calendar/grid2.gif');
	background-position: top left;
	height: <?php echo(24*$HourHeight); ?>px;
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

div.cal_new_event_box {
	border: 1px #20C1F0 solid;
	background-color: #FFFFFF;
	position: absolute;
	-moz-opacity:0.9;
}
div.cal_new_event_box h2 {
	margin: 2px 4px;
}

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


table#calendar_view td.calendar_day div.cal_event.new {
	border: 2px dashed #00FF00;
}

table#calendar_view td.calendar_day div.cal_event.personal {
	border: 1px dotted #FF0000;
}

table#calendar_view td.calendar_day div.cal_event.attend {
	border: 2px dashed #FF0000;
}

table#calendar_view td.calendar_day div.cal_event.noattend {
	border: 2px dashed #808080;
}

table#calendar_view td.calendar_day div.cal_event_nojs {
	position: static;
	margin-bottom: 5px;
}
/*
table#calendar_view td.calendar_day div.cal_event.attend.cancelled {
	border: 2px solid #FF0000;
}
*/
table#calendar_view td.calendar_day div.cal_event.draft {
	border: 2px dashed #808080;
}
table#calendar_view td.calendar_day div.cal_event.cancelled div.cal_event_heading {
	background-color: black;
	color: white;
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
		<td id="cal_day_<?php echo($date); ?>_before" class="cal_day_counts" onclick="return alterTime(-1);"></td>
<?php } ?>
	</tr>

	<!-- Main Calendar Display -->
	<tr>
		<!-- Time Column -->
		<td id="calendar_time"></td>

		<!-- Day Columns -->
<?php
	foreach ($Days as $date => $day) { ?>
		<td id="cal_day_<?php echo($date); ?>" name="<?php echo($date); ?>" class="calendar_day"<?php if ($AllowEventCreate) { ?> onmousedown="clickDay(this,event);" onmouseup="unclickDay(this,event);" onmousemove="moveDay(this,event);"<?php } ?>>
<?php	foreach ($day['events'] as $time => $ocs) {
			foreach ($ocs as $event_info) {
				if (($event_info->DisplayOnCalendar) && ($event_info->TimeAssociated)) {
?>
			<div class="cal_event cal_event_nojs cal_category_<?php
				echo($event_info->Event->Category);
				$classNames = implode(' ', CalCssGetEventClasses($event_info));
				if ($classNames != '') {
					echo(" $classNames");
				}
				?>"<?php /* onclick="alert('You clicked on this event!');"*/ ?>>
				<div class="cal_event_heading">
					<?php
					if ($display_attendence_links &&
						$event_info->UserHasPermission('set_attend') &&
						in_array($event_info->State, array('published', 'cancelled')))
					{
					?><div class="cal_event_heading_box">
					<?php
						$attendence_writeable = $event_info->Event->Source->IsSupported('attend');
						foreach ($attend_state_images as $attend_state => $info) {
							if (in_array($event_info->State, $info['states'])) {
								if (isset($info['alias'])) {
									$attend_state = $info['alias'];
								}
								$in_state = ($attend_state == $event_info->UserAttending);
								if (!$in_state && $attendence_writeable) {
									echo('<a href="'.
											site_url($Path->OccurrenceAttend($event_info, $attend_state)).$CI->uri->uri_string().
											'">');
								}
								echo('<img src="'.$info[$in_state?1:0].'" alt="'.$info[2].'" />');
								if (!$in_state && $attendence_writeable) {
									echo('</a>');
								}
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
					<i><?php
	if ($event_info->State == 'cancelled') {
		echo('Cancelled');
	} else {
		echo(js_nl2br(htmlentities($event_info->GetLocationDescription(), ENT_QUOTES, 'UTF-8')));
	}
?></i>
					<?php if (!$squash && !empty($event_info->Event->Description)) {
						echo('<br />'.js_nl2br(htmlentities($event_info->Event->Description, ENT_QUOTES, 'UTF-8')));
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
		<td id="cal_day_<?php echo($date); ?>_after" class="cal_day_counts2" onclick="return alterTime(1);"></td>
<?php } ?>
	</tr>
</table>
<?php if ($AllowEventCreate) { ?>
	<div id="cal_new_event_box" class="cal_new_event_box" style="display:none;">
		<h2>create new event</h2>
		<form class="form" method="post" action="<?php echo(site_url($Path->EventCreateQuickRaw(0)).get_instance()->uri->uri_string()); ?>">
			<fieldset>
				<input type="hidden" id="evad_date"  name="evad_date" value="" />
				<input type="hidden" id="evad_start" name="evad_start" value="" />
				<input type="hidden" id="evad_end"   name="evad_end"   value="" />
				
				<label for="evad_summary">Summary</label>
				<input id="evad_summary" name="evad_summary" type="text" value="" onchange="setCreateBoxSummary(this.value);" />
				
				<label for="evad_category">Category</label>
				<select id="evad_category" name="evad_category" onchange="setCreateBoxCategory(this.value);">
				<?php
				foreach ($Categories as $key => $category) {
					echo('<option id="evad_category_op_'.$category['id'].'" value="'.$category['id'].'"'.($category['id'] == $EventInfo['category'] ? ' selected="selected"':'').'>');
					echo($category['name']);
					echo('</option>');
				}
				?>
				</select>
				
				<label for="evad_location">Location</label>
				<input id="evad_location" name="evad_location" type="text" value="" onchange="setCreateBoxLocation(this.value);" />
				
				<input class="button" type="submit" name="evad_create" value="Create" />
			</fieldset>
		</form>
	</div>
<?php } ?>
</div>
