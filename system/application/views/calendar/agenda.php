<?php

/**
 * @param $MiniMode bool
 * @param $Events
 * @param $Occurrences
 * @param $Path CalendarPaths object
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
<div id="MainColumn">
	<div class="BlueBox"><?php
if (!empty($Occurrences)) {
	?>
	<table id="calendar_view" border="0" cellpadding="0" cellspacing="0" width="100%">
	<?
	$sorted_occurrences = array();
	foreach ($Occurrences as $key => $occurrence) {
		$sorted_occurrences[$occurrence->StartTime->Timestamp()][] = & $Occurrences[$key];
	}
	$last_date = '';
	foreach ($Occurrences as $occurrence) {
// 		$new_date = $occurrence->StartTime->Format('%D');
// 		if ($new_date != $last_date) {
// 			echo('<tr><td colspan="3"><div class="Date">'.$new_date.'</div></td></tr>');
// 			$last_date = $new_date;
// 		}
		echo('<tr><td class="calendar_day">');
		$CI->load->view('calendar/occurrence_cell', array(
			'Occurrence' => & $occurrence,
			'Categories' => & $Categories,
			'Squash' => false,
			'Path' => $Path,
		));
// 		echo($occurrence->StartTime->Format('%T'));
// 		echo('</td><td valign="top"><img src="/images/prototype/homepage/arrow.png" /></td><td>');
// 		echo(htmlentities($occurrence->Event->Name, ENT_QUOTES, 'utf-8'));
// 		if (!empty($occurrence->LocationDescription)) {
// 			echo(' ('.htmlentities($occurrence->LocationDescription, ENT_QUOTES, 'utf-8').')');
// 		}
		echo('</td></tr>');
	}
	?>
	</table>
	<?php
} else {
	?>
	You have no events today.
	<?php
}

?>
	</div>
</div>