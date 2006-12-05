<?php

// The below block takes the $dummies (current dummy event data) array from
// the $data var passed from the controller and parses it out into a load of
// JS array values in the format x[i][key]['val']
// where i is an arbitrary iterator which will be used to reference the local
// (i.e. client side) copy of the data while it is being displayed and edited.
// When sending a request to upadte the db to the server, the "ref_id" field is
// used as this is supplied by the controller and is a value which can be used 
// to find the correct data again server side.
if (0) { // maybe do it a different way rather than drawing it locally.
	foreach ($dummies as $events_array_index => $event) {
		// Create a subarray for each event
		echo "myEvents[$events_array_index] = new Array()\n";
		
		// Iterate through each field and populate the relevant subarray
		foreach ($event as $event_key => $event_val) {
			echo "myEvents[$events_array_index][\"$event_key\"] = \"$event_val\"\n";
		}
		echo "\n";
	}
}

foreach ($dummies as $events_array_index => $event) {
	
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
		'%%type%%' => $event['type']
	);
	
	$mypath = pathinfo(__FILE__);
	$snippets_dir = $mypath['dirname'] . "/snippets";
	@$eventBoxCode[$event['day']] .= apinc ($snippets_dir . "/calviewEventBox.inc",$replace);
	
	@$eventHandlerJS .= "Event.observe($('ev_mb_".$events_array_index."'), \"click\",  eventMenu(e));\n";
	
	
	
}




?>

</head>
<body>

		<div id="calviewEventMenu" style="display: none">
			<ul>
				<li>
					<a href="#"	onclick="hideEventMenu(); 
					eventSetHighlight()">Highlight</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu()">View Full Details</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu()">Display Options</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu()">Hide Event</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu()">List Similar Events</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu();return false;"
					style="background-color: #ff8855; color: #ffffff">Cancel</a>
				</li>
			</ul>
		</div>
		

<!-- Container div; contains everything
	will make it easier to shove in a template later! -->
<div id="calviewContainer">
	
	<!-- Holds left hand menu -->
	<div id="calviewLeftBar">

		This is an &uuml;ber mockup! The JS code is NOT a proper app and is not
		scalable in any way. This does not use any established conventions and is
		here as an interface "rfc" if you like...
		
		
	
	</div>
	<!-- Holds main calendary thinger -->
	<div id="calviewCalendarWindow">
		

		<table id="calviewCalTable" cellpadding="0" cellspacing="0" border="0">
			
			<!-- headings w/ date & time -->
			<tr>
				<td class="calviewCalHeadingCell">
					<strong>Monday</strong><br />
					<div style="text-align: center"><?php echo $days[0] ?></div>
				</td>
				<td class="calviewCalHeadingCell">
					<strong>Tuesday
					<div style="text-align: center"><?php echo $days[1] ?></div>
				</td>
				<td class="calviewCalHeadingCell">
					<strong>Wednesday
					<div style="text-align: center"><?php echo $days[2] ?></div>
				</td>
				<td class="calviewCalHeadingCell">
					<strong>Thursday
					<div style="text-align: center"><?php echo $days[3] ?></div>
				</td>
				<td class="calviewCalHeadingCell">
					<strong>Friday
					<div style="text-align: center"><?php echo $days[4] ?></div>
				</td>
				<td class="calviewCalHeadingCell">
					<strong>Saturday
					<div style="text-align: center"><?php echo $days[5] ?></div>
				</td>
				<td class="calviewCalHeadingCell">
					<strong>Sunday
					<div style="text-align: center"><?php echo $days[6] ?></div>
				</td>
			</tr>
			
			<!-- cells to contain javascript-fu -->
			<tr>
				<td class="calviewCalEventsCell" id="calviewMonday">
					&nbsp;
					<?php 
						// echo all of Monday's events
						echo @$eventBoxCode[0];
					?>
				</td>
				<td class="calviewCalEventsCell" id="calviewTuesday">
					&nbsp;
					<?php 
						// echo all of Tuesday's events
						echo @$eventBoxCode[1];
					?>
				</td>
				<td class="calviewCalEventsCell" id="calviewWednesday">
					&nbsp;
					<?php 
						// echo all of Wednesday's events
						echo @$eventBoxCode[2];
					?>
				</td>
				<td class="calviewCalEventsCell" id="calviewThursday">
					&nbsp;
					<?php 
						// echo all of Thursday's events
						echo @$eventBoxCode[3];
					?>
				</td>
				<td class="calviewCalEventsCell" id="calviewFriday">
					&nbsp;
					<?php 
						// echo all of Friday's events
						echo @$eventBoxCode[4];
					?>
				</td>
				<td class="calviewCalEventsCell" id="calviewSaturday">
					&nbsp;
					<?php 
						// echo all of Saturday's events
						echo @$eventBoxCode[5];
					?>
				</td>
				<td class="calviewCalEventsCell" id="calviewSunday">
					&nbsp;
					<?php 
						// echo all of Sunday's events
						echo @$eventBoxCode[6];
					?>
				</td>
				
			</tr>
			
			
			
		</table>
	
	</div>
	
	
</div>
<script type="text/javascript">
<?php echo $eventHandlerJS ?>
//Event.observe(document, "onmouseover", function (e) { hideEventMenu(e); });
</script>
