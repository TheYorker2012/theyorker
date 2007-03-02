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

onLoadFunctions.push(drawCalendar);
</script>
<style>
#cal_headings, #cal_container {
	width: 100%;
}

#cal_headings .cal_heading {
	float: left;
	width: <?php echo($width_day_col + 1); /* Add 1px for border */ ?>px;
	font-weight: bold;
	text-align: center;
	border-bottom: 2px #000 solid;
}

#cal_hrs {
	float: left;
	width: <?php echo $width_time_col; ?>px;
	height: <?php echo($height_hour * 24); ?>px;
	background: url('/images/prototype/calendar/hour.jpg');
	border-right: 1px #000 solid;
	text-align: right;
	font-size: small;
}

#cal_hrs .cal_hour {
	height: <?php echo $height_hour; ?>px;
}

.cal_col_day {
	float: left;
	width: <?php echo $width_day_col; ?>px;
	height: <?php echo($height_hour * 24); ?>px;
	background: url('/images/prototype/calendar/half-hour.jpg');
	border-right: 1px #000 solid;
}

.cal_event {
	float: left;
	width: 100%;
	background-color: #999;
	font-size: small;
	color: #fff;
	overflow: hidden;
}

.info {
	padding: 0 3px;
}

.info .location {
	font-size: x-small;
}
</style>
	<div id="cal_headings">
		<div class="cal_heading" style="width: <?php echo($width_time_col + 2); /* Add 2px for border! */ ?>px;">&nbsp;</div>
		<div class="cal_heading">Mon</div>
		<div class="cal_heading">Tue</div>
		<div class="cal_heading">Wed</div>
		<div class="cal_heading">Thu</div>
		<div class="cal_heading">Fri</div>
		<div class="cal_heading">Sat</div>
		<div class="cal_heading">Sun</div>
	</div>
	<br style="clear: both;" />
	<div id="cal_container">
		<div id="cal_hrs" style="border-left: 1px #000 solid;">
			<?php for ($i = 0; $i <= 23; $i++) { ?>
			<div class="cal_hour">
				<div class="info">
					<?php echo $i; ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="cal_col_day" id="cal_mon">
			<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * 9); ?>px; height: <?php echo($height_hour * 2); ?>px;">
				<div class="info">
					Yorker Dev Meeting<br />
					<span class="location">L/N/006</span>
				</div>
			</div>
			<div class="cal_event" style="width: 50%; margin-top: <?php echo($height_hour * 9); ?>px; height: <?php echo($height_hour * 2); ?>px;">
				<div class="info">
					CLASH!<br />
					<span class="location">L/N/006</span>
				</div>
			</div>
		</div>
		<div class="cal_col_day" id="cal_tue">
		</div>
		<div class="cal_col_day" id="cal_wed">
			<div class="cal_event" style="margin-top: <?php echo($height_hour * 14.5); ?>px; height: <?php echo($height_hour * 2.5); ?>px;">
				<div class="info">
					Yorker Dev Meeting<br />
					<span class="location">L/N/006</span>
				</div>
			</div>
		</div>
		<div class="cal_col_day" id="cal_thu">
		</div>
		<div class="cal_col_day" id="cal_fri">
			<div class="cal_event" style="margin-top: <?php echo($height_hour * 0); ?>px; height: <?php echo($height_hour * 1); ?>px;">
				<div class="info">
					RDQ<br />
					<span class="location">L/N/028</span>
				</div>
			</div>
		</div>
		<div class="cal_col_day" id="cal_sat">
		</div>
		<div class="cal_col_day" id="cal_sun">
		</div>
	</div>