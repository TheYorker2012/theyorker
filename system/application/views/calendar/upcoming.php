<?php

/**
 * @param $MiniMode bool
 * @param $Events
 * @param $Occurrences
 */

// split into today and tomorrow
$days = array();
$day_quantities = array();

foreach ($Occurrences as $key => $occurrence) {
	$date = (int)$occurrence->StartTime->Format('Ymd');
	if ($occurrence->TimeAssociated) {
		$time = (int)$occurrence->StartTime->Format('Him');
	} else {
		$time = -1;
	}
	$days[$date][$time][] = & $Occurrences[$key];
	if (!array_key_exists($date, $day_quantities)) {
		$day_quantities[$date] = 1;
	} else {
		++$day_quantities[$date];
	}
}

$today = (int)date('Ymd');
$tomorrow = (int)date('Ymd', strtotime('+1day'));
$special_names = array(
	$tomorrow => 'Tomorrow',
	$today    => 'Today',
);
$default_day = $today;

if (!array_key_exists($today, $day_quantities) &&
	array_key_exists($tomorrow, $day_quantities))
{
	$default_day = $tomorrow;
}

$start_table = '<table style="clear: both;" border="0" cellpadding="1" cellspacing="0">';
$hrule = '<hr style="color: #999999; background-color: #999999; height: 1px; border: 0;" />';

foreach ($special_names as $date => $name) {
	$default = ($date==$default_day);
	$lowername = strtolower($name);
	$div_id = 'upcoming_'.$lowername;
	
	echo('<div id="'.$div_id.'"'.($default ? '' : ' style="display: none"').'>');
	echo('<ul id="SideTabBar">');
	foreach ($special_names as $date1 => $name1) {
		$lowername1 = strtolower($name1);
		$div_id1 = 'upcoming_'.$lowername1;
		echo('<li onClick="');
		foreach ($special_names as $date2 => $name2) {
			$lowername2 = strtolower($name2);
			$div_id2 = 'upcoming_'.$lowername2;
			echo('document.getElementById(\''.$div_id2.'\').style.display=\''.($date2 !== $date1 ? 'none' : 'block').'\'; ');
		}
		echo('return false;');
		echo('"');
		if ($date1 === $date) {
			echo(' class="current"');
		}
		echo('>');
		echo($name1);
		echo('</li>');
	}
	echo('</ul><div>');
	if (array_key_exists($date, $days)) {
		ksort($days[$date]);
		echo($start_table);
		$previous_time_associative = TRUE;
		foreach ($days[$date] as $time => $occurrences) {
			$time_associative = ($time !== -1);
			foreach ($occurrences as $occurrence) {
				echo('<tr><td valign="top">');
				if ($time_associative) {
					echo($occurrence->StartTime->Format('H:i'));
					$any_time_associative = TRUE;
				}
				echo('</td><td valign="top"><img src="/images/prototype/homepage/arrow.png" /></td><td>');
				echo('<span><a href="' . '/calendar/event' . '/' . $occurrence->Event->Source->GetSourceId(). '/' . urlencode($occurrence->Event->SourceEventId) . '/' . urlencode($occurrence->SourceOccurrenceId) . $CI->uri->uri_string().'">'.$occurrence->Event->Name.'</a></span>');
				if (!empty($occurrence->LocationDescription)) {
					echo(' ('.$occurrence->LocationDescription.')');
				}
				echo('</td></tr>');
			}
			if ($time_associative && !$previous_time_associative) {
				echo('</table>');
				echo($hrule);
				echo($start_table);
			}
			$previous_time_associative = $time_associative;
		}
		echo('</table>');
	} else {
		echo('<p>You have no events '.$lowername.'</p>');
	}
	echo('</div><p><small><a class="RightColumnAction" href="/calendar/range/'.$lowername.'">Go to '.$lowername.'</a></small></p>');
	echo('</div>');
}

?>