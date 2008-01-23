<?php

/**
 * @file views/calendar/weeks.php
 * @brief For term (more than one week) view.
 * @author James Hogan (jh559)
 */

// Load the calendar css helper
get_instance()->load->helper('calendar_css_classes');

?>
<style type="text/css">
table#calendar_view {
	border-collapse: collapse;
	margin: 0;
}

table#calendar_view th {
	text-align: center;
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

table#calendar_view td.calendar_day {
	vertical-align: top;
	padding: 0;
}
table td.calendar_day {
	border: 1px #999 solid;
}

table#calendar_view td.calendar_day div.cal_event {
	overflow: hidden;
	position: absolute;
	width: auto;
	margin: 0 1px;
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
	margin-bottom: 2px;
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
<?php

echo('<div align="center">');
if (isset($BackwardUrl)) {
	echo('<a href="'.$BackwardUrl.'"><img src="/images/prototype/calendar/backward.gif" alt="Backward" /></a> ');
}
if (isset($NowUrl)) {
	echo('<a href="'.$NowUrl.'">'.$NowUrlLabel.'</a> ');
}
if (isset($ForwardUrl)) {
	echo('<a href="'.$ForwardUrl.'"><img src="/images/prototype/calendar/forward.gif" alt="Forward" /></a> ');
}
echo('</div>');
?>
<?php

// Term / week selector
$start_week = $Weeks[0]['start'];
$end_week = $Weeks[count($Weeks)-1]['start'];
$this->load->view('calendar/term_selector', array(
	'Start' => $start_week,
	'End'   => $end_week,
	'Path'  => $Path,
));


$squash = count($Days) > 3;

echo('<table id="calendar_view" class="recur-cal" border="0" cellpadding="0" cellspacing="0" width="100%">');
$last_term = $end_week->AcademicTerm();
foreach ($Weeks as $key => $week) {
	if ($last_term !== $week['start']->AcademicTerm()) {
		echo('<tr><td colspan="'.(count($week['days'])+1).'"><h2>');
		echo($week['start']->AcademicTermName().' '.
			 $week['start']->AcademicTermTypeName().' '.
			 $week['start']->AcademicYearName());
		echo('</h2></td></tr>');
		$last_term = $week['start']->AcademicTerm();
	}
	echo('<tr>');
	echo('<th></th>');
	foreach ($week['days'] as $date => $day) {
		echo('<th>');
		echo('<a href="'.$day['link'].'">');
		echo($day['date']->Format('D'));
		echo('<br />');
		echo($day['date']->Format('jS M'));
		echo('</a>');
		echo('</th>');
	}
	echo('</tr><tr>');
	echo('<th><a href="'.$week['link'].'">'.$week['start']->AcademicWeek().'</a></th>');
	foreach ($week['days'] as $date => $day) {
		$times = $day['events'];
		$classes_list = CalCssGetDateClasses($day['date'], $day['date']->DayOfWeek(), true);
		$classes_list[] = 'calendar_day';
		$classes = implode(' ',$classes_list);
		echo('<td class="'.$classes.'"><a href="'.$day['link'].'">');
		foreach ($times as $time => $ocs) {
			foreach ($ocs as $occurrence) {
				$CI->load->view('calendar/occurrence_cell', array(
					'Occurrence' => & $occurrence,
					'Categories' => & $Categories,
					'Squash' => $squash,
					'Path' => $Path,
				));
			}
		}
		echo('</a></td>');
	}
	echo('</tr>');
}
echo('</table>');

?>
</div>
