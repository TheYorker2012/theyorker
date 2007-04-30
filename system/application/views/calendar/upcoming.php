<?php

/**
 * @param $MiniMode bool
 * @param $Events
 * @param $Occurrences
 */

// split into today and tomorrow
$days = array();
$today = (int)date('Ymd');
$tomorrow = (int)date('Ymd', strtotime('+1day'));
$special_names = array(
	$tomorrow => 'Tomorrow',
	$today    => 'Today',
);
$default_day = $today;

foreach ($Occurrences as $key => $occurrence) {
	$date = (int)$occurrence->StartTime->Format('Ymd');
	if ($occurrence->TimeAssociated) {
		$time = (int)$occurrence->StartTime->Format('Him');
	} else {
		$time = -1;
	}
	$days[$date][$time][] = & $Occurrences[$key];
}

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
		echo('<table style="clear: both;" border="0" cellpadding="1" cellspacing="0">');
		foreach ($days[$date] as $time => $occurrences) {
			$time_associative = ($time !== -1);
			foreach ($occurrences as $occurrence) {
				echo('<tr><td valign="top">');
				if ($time_associative) {
					echo($occurrence->StartTime->Format('H:i'));
				}
				echo('</td><td valign="top"><img src="/images/prototype/homepage/arrow.png" /></td><td>');
				echo($occurrence->Event->Name);
				if (!empty($occurrence->LocationDescription)) {
					echo(' ('.$occurrence->LocationDescription.')');
				}
				echo('</td></tr>');
			}
		}
		echo('</table>');
	} else {
		echo('<p>You have no events '.$lowername.'</p>');
	}
	echo('</div><p><small><a href="/calendar/add/'.$lowername.'">Add event '.$lowername.'</a></small></p>');
	echo('</div>');
}

?>