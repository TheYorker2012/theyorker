<?php

// Put special headings at top
foreach ($dayinfo as $id => $info) {
	$eventBoxCode[$id] = '';
	$dayempty[$id] = TRUE;
	foreach ($info['special_headings'] as $name) {
		$eventBoxCode[$id] .= '<div class="calviewEBCSpecialDayHeading">' .
				$name . '</div>';
	}
}

// Then events
foreach ($events as $events_array_index => $event) {
	
	$replace = array (
		'%%arrid%%' => $events_array_index,
		'%%refid%%' => $event['ref_id'],
		'%%name%%' => $event['name'],
		'%%date%%' => $event['date'],
		'%%day%%' =>  $event['day'],
		'%%starttime%%' => $event['starttime'],
		'%%endtime%%' => $event['endtime'],
		'%%blurb%%' => $event['blurb'],
		'%%shortloc%%' => $event['shortloc'],
		'%%type%%' => $event['type'],
	);
	
	$mypath = pathinfo(__FILE__);
	$snippets_dir = $mypath['dirname'] . "/snippets";
	@$eventBoxCode[$event['day']] .= apinc ($snippets_dir . "/listviewEventBox.inc",$replace);
	$dayempty[$event['day']] = FALSE;
	
}

// put &nbsp; onto end of all days
for ($i = 0;$i < 7;$i++) {
	@$eventBoxCode[$i] .= '&nbsp;';
}


?>

		<div id="calviewEventMenu" style="display: none">
			<ul>
				<li>
					<div class="calviewEMBP">
					<a href="#"	onclick="hideEventMenu(); 
					eventSetHighlight();return false;">Highlight</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP">
					<a href="#" onclick="hideEventMenu();
					return false;">View Full Details</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP">
					<a href="#" onclick="hideEventMenu()
					return false;">Display Options</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP">
					<a href="#" onclick="hideEventMenu()
					removeEvent;return false;">Hide Event</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP">
					<a href="#" onclick="hideEventMenu()
					return false;">List Similar Events</a>
					</div>
				</li>
				<li>
					<div class="calviewEMBP" style="border-bottom: none">
					<a href="#" onclick="hideEventMenu();return false;">Cancel</a>
					</div>
				</li>
			</ul>
		</div>
		
		

<?php
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
	
	
</div>
<script type="text/javascript">
<?php echo $eventHandlerJS ?>
//Event.observe(document, "onmouseover", function (e) { hideEventMenu(e); });
</script>
