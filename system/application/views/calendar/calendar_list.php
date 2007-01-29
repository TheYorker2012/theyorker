<?php

// Put special headings at top of days
foreach ($dayinfo as $id => $info) {
	$eventBoxCode[$id] = '';
	$dayempty[$id] = TRUE;
	foreach ($info['special_headings'] as $name) {
		$eventBoxCode[$id] .= '<div class="calviewEBCSpecialDayHeading">' .
				$name . '</div>';
	}
}

// Then put the events
foreach ($events as $events_array_index => $event) {
	
	$replace = array (
		'%%arrid%%' => $events_array_index + 1, // arrid of 0 is invalid (js issue)
		'%%refid%%' => $event['ref_id'],
		'%%name%%' => $event['name'],
		'%%date%%' => $event['date'],
		'%%day%%' =>  $event['day'],
		'%%starttime%%' => $event['starttime'],
		'%%endtime%%' => $event['endtime'],
		'%%blurb%%' => $event['blurb'],
		'%%shortloc%%' => $event['shortloc'],
		'%%type%%' => $event['type'],
		'%%organisation%%' => $event['organisation'],
		'%%organisation_link%%' => '/directory/'.$event['organisation_directory'],
	);
	
	$mypath = pathinfo(__FILE__);
	$snippets_dir = $mypath['dirname'] . "/snippets";
	@$eventBoxCode[$event['day']] .= apinc ($snippets_dir . "/listviewEventBox.inc",$replace);
	$dayempty[$event['day']] = FALSE;
	
}

// Now process the days, outputting HTML
$pre_ac_title = '';
foreach ($dayinfo as $id => $info) {
	if (!$dayempty[$id]) {
		$ac_title =
			'Week '.$info['academic_week' ].
			' of the '.$info['academic_term' ].
			' '.$info['academic_year' ];
		if ($ac_title !== $pre_ac_title) {
			echo '<H2>'.$ac_title.'</H2>'."\n";
			$pre_ac_title = $ac_title;
		}
		$title =
			$info['day_of_week'   ].
			' '.$info['date'].
			' '.$info['month_long'];
		echo '<H3>'.$title.'</H3>'."\n";
		echo $eventBoxCode[$id];
	}
}

?>
