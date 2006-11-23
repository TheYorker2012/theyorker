<html>
<head>
<title><?=$title?></title>
<!-- Load up scriptaculous (and prototype upon which it is based) for eyecandy -->
<script src="/public_html/js/prototype.js" type="text/javascript"></script>
<script src="/public_html/js/scriptaculous.js" type="text/javascript"></script>

<script type="text/javascript">
/* 
	TODO
	backompat comments round script block/externalise
	"upgrade your browser you shit" message
	get coffee

*/

var blah;

var myEvents=new Array()

<?php

// The below block takes the $dummies (current dummy event data) array from
// the $data var passed from the controller and parses it out into a load of
// JS array values in the format x[i][key]['val']
// where i is an arbitrary iterator which will be used to reference the local
// (i.e. client side) copy of the data while it is being displayed and edited.
// When sending a request to upadte the db to the server, the "ref_id" field is
// used as this is supplied by the controller and is a value which can be used 
// to find the correct data again server side.

foreach ($dummies as $events_array_index => $event) {
	// Create a subarray for each event
	echo "myEvents[$events_array_index] = new Array()\n";
	
	// Iterate through each field and populate the relevant subarray
	foreach ($event as $event_key => $event_val) {
		echo "myEvents[$events_array_index][\"$event_key\"] = \"$event_val\"\n";
	}
	echo "\n";
}


?>


function eventMenu(e) {
	if (1) { //($(Event.element(e)) == 'ev_1') {
		var xPos = Event.pointerX(e);
		var yPos = Event.pointerY(e);
		
		$('xeventMenu').style.top=yPos;
		$('xeventMenu').style.left=xPos;
		
		new Effect.Appear ('xeventMenu', {duration:0.2});
	}	
}

function eventSetHighlight () {
	//$('ev_1').class="indEventBoxHL";
	$('ev_1').style.color="#ff0000";
}

function hideEventMenu (e) {
	new Effect.Fade ('xeventMenu',{duration:0.2});
}

function eventCreate(date,time,sid,title,loc,blurb) {
	
}

function hideEvent (id) {
	new Effect.Fade ('ev_'+id);
}

function expandEvent (id) {
	new Effect.Appear ('ev_es_'+id);
}

function collapseEvent (id) {
	new Effect.Fade ('ev_es_'+id);
}

</script>
<style type="text/css">

body {
	padding: 0px;
	margin: 0px;
	font-family: sans-serif;
}

#leftBar {
	position: absolute;
	padding: 5px;
	margin: 0px;
	top: 5px;
	left: 5px;
	width: 200px;
	background-color: #dddddd;
	border: 1px solid #cccccc;
}

#calendarWindow {
	position: absolute;
	padding: 0px;
	margin: 0px;
	top: 5px;
	left: 220px;
	width: 700px;
	background-color: #dedede;
	
}

#calTable {
	width: 100%;
	padding: 0px;
	margin: 0px;
	border: none;
	background-color: #ffffff;
	border-top: 1px solid #dddddd;
	border-left: 1px solid #cccccc;
}

#calTable td {
	margin: 0px;
	padding: 2px;
	border-bottom: 1px solid #cccccc;
	border-right: 1px solid #dddddd;
}
.calHeadingCell {
	width: 100px;
	text-align: center;
	font-weight: bold;
	font-size: 11px;
	height: 20px;
	background-color: #f9f9f9;
	border-bottom: 1px solid #cccccc;
	margin: 0px;
}
.calEventsCell {
	height: 500px;
	vertical-align: top;
	background-color: #f0f0f0;
	border-bottom: 1px solid #cccccc;
	margin: 0px;
}
.indEventBox {
	border: 2px solid #99bb99;
	background-color: #dddddd;
	padding: 2px;
	font-size: 9px;
	margin: 1px;

}

.indEventBoxHL {
	border: 2px solid #bb3300;
	background-color: #ffffff;
	padding: 2px;
	font-size: 9px;
	margin: 1px;

}
.indEventBox .closeButton {
	margin-top: -2px;
	margin-right: -2px;
	margin-bottom: 2px;
	margin-left: 2px;
	float: right;
	background-color: #bb0000;
	color: #ffffff;
	padding: 2px;
	font-weight: bold;
}
.indEventBox .expandButton {
	margin-top: -2px;
	margin-right: -2px;
	margin-bottom: 2px;
	margin-left: 2px;
	float: right;
	background-color: #00bb00;
	color: #ffffff;
	padding: 2px;
	display: block;
	font-weight: bold;
}
.indEventBox .menuButton {
	margin-top: -2px;
	margin-right: -2px;
	margin-bottom: 2px;
	margin-left: 2px;
	float: right;
	background-color: #ddbb00;
	color: #ffffff;
	padding: 2px;
	font-weight: bold;
}

#xeventMenu {
	position: absolute;
	top: 100px;
	left: 100px;
	z-index: 100;
	width: 150px;
	background-color: #f7f7f7;
	border: 1px solid #000000;
}

#xeventMenu ul {
	display: block;
	list-style-type: none;
	padding: 0px;
	margin: 0px;
}
#xeventMenu ul li:hover {
	background-color: #009900;
}
#xeventMenu ul li a {
	display: block;
	text-decoration: none;
	font-size: 11px;
	color: #00bb00;
	padding: 2px;
}
#xeventMenu ul li a:hover {
	color: #ffffff;
}

</style>
</head>
<body>

		<div id="xeventMenu" style="display: none">
			<ul id="xeventMenuUl">
				<li>
					<a href="#"	onclick="hideEventMenu(this); 
					eventSetHighlight(this)">Highlight</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu(this)">View Full Details</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu(this)">Display Options</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu(this)">Hide Event</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu(this)">List Similar Events</a>
				</li>
				<li>
					<a href="#" onclick="hideEventMenu(this)"
					style="background-color: #ff8855; color: #ffffff">Cancel</a>
				</li>
			</ul>
		</div>
		

<!-- Container div; contains everything
	will make it easier to shove in a template later! -->
<div id="sumContainer">
	
	<!-- Holds left hand menu -->
	<div id="leftBar">

		This is an &uuml;ber mockup! The JS code is NOT a proper app and is not
		scalable in any way. This does not use any established conventions and is
		here as an interface "rfc" if you like...
	
	</div>
	<!-- Holds main calendary thinger -->
	<div id="calendarWindow">
		

		<table id="calTable" cellpadding="0" cellspacing="0" border="0">
			
			<!-- headings w/ date & time -->
			<tr>
				<td class="calHeadingCell">
					Monday
				</td>
				<td class="calHeadingCell">
					Tuesday
				</td>
				<td class="calHeadingCell">
					Wednesday
				</td>
				<td class="calHeadingCell">
					Thursday
				</td>
				<td class="calHeadingCell">
					Friday
				</td>
				<td class="calHeadingCell">
					Saturday
				</td>
				<td class="calHeadingCell">
					Sunday
				</td>
			</tr>
			
			<!-- cells to contain javascript-fu -->
			<tr>
				<td class="calEventsCell">
					&nbsp;
					
					<div id="ev_1" class="indEventBox">
					<div>
						
						<div class="closeButton" onclick="hideEvent(1)">
							X
						</div>
						<div class="menuButton" id="ev_mb_1" onclick="eventMenu(this)">
							>
						</div>
						<div class="expandButton"
							onmouseover="expandEvent(1)"
							onmouseout="collapseEvent(1)">
							V
						</div>
						
						<strong>Title of Event!</strong>
						
						<div class="expandedSmall" id="ev_es_1" style="display: none">
						<div>
							12:49 in <i>G/145</i> until 23:00
						</div>
						</div>
						
					</div>
					</div>
					
				</td>
				<td class="calEventsCell">
					&nbsp;
				</td>
				<td class="calEventsCell">
					&nbsp;
				</td>
				<td class="calEventsCell">
					&nbsp;
				</td>
				<td class="calEventsCell">
					&nbsp;
				</td>
				<td class="calEventsCell">
					&nbsp;
				</td>
				<td class="calEventsCell">
					&nbsp;
				</td>
				
			</tr>
			
			
			
		</table>
	
	</div>
	
	
</div>
<script type="text/javascript">
Event.observe($('ev_mb_1'), "click", function (e) { eventMenu(e); });
Event.observe(document, "onmouseover", function (e) { hideEventMenu(e); });
</script>
</body>
</html>