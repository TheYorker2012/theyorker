<script type='text/javascript' src='/javascript/prototype.js'></script>
<script type='text/javascript' src='/javascript/scriptaculous.js?load=effects'></script>
<script type="text/javascript">
var Events = new Array();
var EventCount = 0;

<?php foreach ($events as $event) { ?>
	Events[EventCount] = new Array();
	Events[EventCount]['day'] = '<?php echo $event['day']; ?>';
	Events[EventCount]['title'] = '<?php echo $event['title']; ?>';
	Events[EventCount]['location'] = '<?php echo $event['location']; ?>';
	EventCount++;
<?php } ?>

function drawCalendar () {
	for (i = 0; i < EventCount; i++) {
		var day_col = document.getElementById("cal_" + Events[i]['day']);
		day_col.innerHTML = day_col.innerHTML + "<div class='cal_event' style='margin-top: <?php echo($height_hour * 14.5); ?>px; height: <?php echo($height_hour * 2.5); ?>px;'><div class='info'>" + Events[i]['title'] + "<br /><span class='location'>" + Events[i]['location'] + "</span></div></div>";
	}
}

function getObject(obj) {
	if (document.getElementById) {
		obj = document.getElementById(obj);
	} else if (document.all) {
		obj = document.all.item(obj);
	} else {
		obj = null;
	}
	return obj;
}

function moveObject(obj,e) {
	// step 1
	var tempX = 0;
	var tempY = 0;
	var offset = 55;
	var objHolder = obj;

	// step 2
	obj = getObject(obj);
	if (obj==null) return;

	// step 3
	if (document.all) {
		tempX = event.clientX + document.body.scrollLeft;
		tempY = event.clientY + document.body.scrollTop;
	} else {
		tempX = e.pageX;
		tempY = e.pageY;
	}

	// step 4
	if (tempX < 0){ tempX = 0 }
	if (tempY < 0){ tempY = 0 }

	// step 5
	obj.style.top  = (tempY - offset) + 'px';
	obj.style.left = (tempX - offset) + 'px';

	// step 6
	displayObject(objHolder, true);

	// step 7
	return false;
}

function displayObject(obj,show) {
	obj = getObject(obj);
	if (obj==null) return;
	obj.style.display = show ? 'block' : 'none';
	obj.style.visibility = show ? 'visible' : 'hidden';
	return false;
}


function showDay () {
	Effect.Appear('cal_planner_day');
	return false;
}

function hideDay() {
	Effect.Fade('cal_planner_day');
	return false;
}
</script>

<style>
#cal_container {
	width: 100%;
}

#cal_container #cal_time {
	float: left;
	width: <?php echo($width_time_col - 2); ?>px;
	border-left: 1px #000 solid;
	border-right: 1px #000 solid;
}

#cal_container #cal_time #cal_time_heading {
	font-weight: bold;
	text-align: center;
	border-bottom: 2px #000 solid;
}

#cal_container #cal_time .cal_hour {
	height: <?php echo $height_hour; ?>px;
	text-align: right;
	font-size: small;
	padding: 0 3px;
	color: #000;
	background: url('/images/prototype/calendar/hour.jpg');
}

#cal_container #cal_planner {
	float: left;
	position: relative;
	width: <?php echo($width_page - $width_time_col); ?>px;
}

#cal_container #cal_planner #cal_planner_day {
	position: absolute;
	top: 0;
	left: 0;
	z-index: 2;
	width: <?php echo($width_page - $width_time_col); ?>px;
}

#cal_container #cal_planner #cal_planner_week {
	position: absolute;
	top: 0;
	left: 0;
	z-index: 1;
	width: <?php echo($width_page - $width_time_col); ?>px;
}

#cal_container #cal_planner .cal_day {
	float: left;
	border-right: 1px #000 solid;
}

#cal_container #cal_planner #cal_planner_day .cal_day {
	width: <?php echo($width_page - $width_time_col - 1); ?>px;
}

#cal_container #cal_planner #cal_planner_week .cal_day {
	width: <?php echo($width_day_col - 1); ?>px;
}

#cal_container #cal_planner .cal_day .cal_day_heading {
	font-weight: bold;
	text-align: center;
	border-bottom: 2px #000 solid;
	background-color: #fff;
}

#cal_container #cal_planner .cal_day .cal_day_plan {
/*	position: relative;*/
	height: <?php echo($height_hour * 24); ?>px;
	background: url('/images/prototype/calendar/half-hour.jpg');
}

#cal_container #cal_planner .cal_day .cal_day_plan .cal_event {
	float: left;
/*	position: absolute;*/
	overflow: hidden;
	width: 100%;
}

#cal_container .info {
	margin: 0 1px;
	padding: 0 3px;
	font-size: small;
	overflow: hidden;
	border: 1px #999 solid;
	border-top: 0;
	background-color: #eee;
	color: #000;
}

#cal_container .info a {
	color: #000;
	font-weight: bold;
}

#cal_container .info .location {
	font-size: x-small;
}

#test_popup {
	width: 110px;
	height: 110px;
	display: none;
	position: absolute;
	top: 0px;
	left:0px;
	z-index: 10;
}
</style>

<div id="test_popup">
	<a href="#" onclick="return displayObject('test_popup',false);">
		<img src="/images/prototype/calendar/pie.png" alt="Click me" title="Click me" />
	</a>
</div>

<div id="cal_container">
	<div id="cal_time">
		<div id="cal_time_heading">
			&nbsp;
		</div>
		<?php for ($i = 0; $i <= 23; $i++) { ?>
		<div class="cal_hour">
			<?php echo $i; ?>
		</div>
		<?php } ?>
	</div>

	<div id="cal_planner">

		<div id="cal_planner_day" style="display:none;">
			<div id="cal_day_full" class="cal_day">
				<div class="cal_day_heading">
					<span style="float: right;">
						<a href="#" onclick="return hideDay();">
							<img src="/images/prototype/calendar/close.gif" alt="Return to week planner" title="Return to week planner" />
						</a>
					</span>
					Monday 6th March 2007
				</div>
				<div id="cal_day_full_list" class="cal_day_plan">
					<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * 0); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * -0.5); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="margin-top: <?php echo($height_hour * 6); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 33%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 33%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 33%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 25%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 25%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 25%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 25%; margin-top: <?php echo($height_hour * 1); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return moveObject('test_popup',event);">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="cal_planner_week">
			<div id="cal_day_mon" class="cal_day">
				<div class="cal_day_heading">Mon</div>
				<div id="cal_day_mon_list" class="cal_day_plan">

					<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * 9); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return showDay();" alt="LPA" title="LPA">LPA</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
					<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * 9); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return showDay();" alt="CLASH!" title="CLASH!">CLASH!</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
		
				</div>
			</div>
		
			<div id="cal_day_tue" class="cal_day">
				<div class="cal_day_heading">Tue</div>
				<div id="cal_day_tue_list" class="cal_day_plan">
		
				</div>
			</div>
		
			<div id="cal_day_wed" class="cal_day">
				<div class="cal_day_heading">Wed</div>
				<div id="cal_day_wed_list" class="cal_day_plan">
		
					<div class="cal_event" style="margin-top: <?php echo($height_hour * 14.5); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2.5) - 1); ?>px;">
							<a href="#" onclick="return showDay();" alt="Yorker Dev Meeting" title="Yorker Dev Meeting">Yorker Dev Meeting</a><br />
							<span class="location">L/N/006</span>
						</div>
					</div>
		
				</div>
			</div>
		
			<div id="cal_day_thu" class="cal_day">
				<div class="cal_day_heading">Thu</div>
				<div id="cal_day_thu_list" class="cal_day_plan">

					<div class="cal_event" style="width: 33%; margin-top: <?php echo($height_hour * 6); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return showDay();" alt="CLASH!" title="CLASH!">CLASH!</a><br />
							<span class="location">Earth</span>
						</div>
					</div>
					<div class="cal_event" style="width: 33%; margin-top: <?php echo($height_hour * 6); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return showDay();" alt="CLASH!" title="CLASH!">CLASH!</a><br />
							<span class="location">Earth</span>
						</div>
					</div>
					<div class="cal_event" style="width: 33%; margin-top: <?php echo($height_hour * 6); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 2) - 1); ?>px;">
							<a href="#" onclick="return showDay();" alt="CLASH!" title="CLASH!">CLASH!</a><br />
							<span class="location">Earth</span>
						</div>
					</div>

				</div>
			</div>
		
			<div id="cal_day_fri" class="cal_day">
				<div class="cal_day_heading">Fri</div>
				<div id="cal_day_fri_list" class="cal_day_plan">
		
					<div class="cal_event" style="margin-top: <?php echo($height_hour * 0); ?>px;">
						<div class="info" style="height: <?php echo(($height_hour * 1) - 1); ?>px;">
							<a href="#" onclick="return showDay();" alt="RDQ" title="RDQ">RDQ</a><br />
							<span class="location">L/N/028</span>
						</div>
					</div>
		
				</div>
			</div>
		
			<div id="cal_day_sat" class="cal_day">
				<div class="cal_day_heading">Sat</div>
				<div id="cal_day_sat_list" class="cal_day_plan">
		
				</div>
			</div>
		
			<div id="cal_day_sun" class="cal_day">
				<div class="cal_day_heading">Sun</div>
				<div id="cal_day_sun_list" class="cal_day_plan">
		
				</div>
			</div>
		</div>
	</div>
</div>


<script type='text/javascript'>
//drawCalendar();
</script>