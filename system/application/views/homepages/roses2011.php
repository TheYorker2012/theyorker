<div class="FlexiBox Box23">
	<div id="DisplayBox">
		<div style="background-color:#999;color:#999;font-weight:bold;font-size:30px;position:absolute;bottom:0;left:0;opacity:0.6;">York <?php echo(xml_escape($score_york)); ?></div>
		<div style="color:#fff;font-weight:bold;font-size:30px;position:absolute;bottom:0;left:0;">York <?php echo(xml_escape($score_york)); ?></div>
		<div style="background-color:#999;color:#999;font-weight:bold;font-size:30px;position:absolute;bottom:0;right:0;opacity:0.6;">Lancaster <?php echo(xml_escape($score_lancs)); ?></div>
		<div style="color:#ba0000;font-weight:bold;font-size:30px;position:absolute;bottom:0;right:0;">Lancaster <?php echo(xml_escape($score_lancs)); ?></div>

		<div id="DisplayBoxBg" style="top:0;bottom:auto;"><?php echo(xml_escape($liveblog[0]['headline'])); ?></div>
		<div id="DisplayBoxText" style="top:0;bottom:auto;"><a href="/news/<?php echo($liveblog[0]['id']); ?>"><?php echo(xml_escape($liveblog[0]['headline'])); ?></a></div>
		<a href="/news/<?php echo($liveblog[0]['id']); ?>"><img src="/photos/home/<?php echo($liveblog[0]['photo_id']); ?>" alt="<?php echo(xml_escape($liveblog[0]['photo_title'])); ?>" /></a>
	</div>
</div>

<div style="float:right;" class="ArticleListBox FlexiBox Box13 FlexiBoxLast">
	<div class="ArticleListTitle">
		<a href="/news/<?php echo($liveblog[0]['id']); ?>">latest updates</a>
	</div>
	<?php
	foreach ($latest as $l) {
		echo('<div>');
		$cache = str_replace('/medium/', '/small/', $l['cache']);
		$cache = str_replace('/large/', '/small/', $cache);
		echo($cache);
		echo('</div>');
	}
	?>
	<div style="text-align:center">
		<a href="/news/<?php echo($liveblog[0]['id']); ?>">Read more...</a>
	</div>
</div>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Year');
        data.addColumn('number', 'Sales');
        data.addColumn('number', 'Expenses');
        data.addRows(4);
        data.setValue(0, 0, '2004');
        data.setValue(0, 1, 1000);
        data.setValue(0, 2, 400);
        data.setValue(1, 0, '2005');
        data.setValue(1, 1, 1170);
        data.setValue(1, 2, 460);
        data.setValue(2, 0, '2006');
        data.setValue(2, 1, 860);
        data.setValue(2, 2, 580);
        data.setValue(3, 0, '2007');
        data.setValue(3, 1, 1030);
        data.setValue(3, 2, 540);

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, {width: 400, height: 240, title: 'Company Performance'});
      }
    </scrip>



<?php function ArticleList ($section, $articles, $last = false) {
	if (count($articles) == 0) return; ?>
	<div class="ArticleListBox FlexiBox Box13<?php if ($last) echo(' FlexiBoxLast'); ?>">
		<div class="ArticleListTitle">
			<a href="/roses"><?php echo($section); ?></a>
		</div>
		<?php foreach ($articles as $article) { ?>
		<div>
			<a href="/news/<?php echo(xml_escape($article['id'])); ?>">
				<img src="/photos/small/<?php echo(xml_escape($article['photo_id'])); ?>" alt="<?php echo(xml_escape($article['photo_title'])); ?>" title="<?php echo(xml_escape($article['photo_title'])); ?>" />
				<?php echo(xml_escape($article['headline'])); ?>
			</a>
			<div class="Date"><?php echo(xml_escape(date('l, jS F Y', $article['date']))); ?></div>
			<div class="clear"></div>
		</div>
		<?php } ?>
	</div>
	<?php /*if ($last) { ?><div class="clear"></div><?php }*/ ?>
<?php } ?>

<?php
$fri = mktime(8, 0, 0, 4, 30, 2010);
$sat = mktime(8, 0, 0, 5, 1, 2010);
$sun = mktime(8, 0, 0, 5, 2, 2010);
?>
<script type="text/javascript">
function switchResults (name) {
	document.getElementById('resultFri').style.display = 'none';
	document.getElementById('resultSat').style.display = 'none';
	document.getElementById('resultSun').style.display = 'none';
	document.getElementById('result' + name).style.display = 'block';
	return false;
}
</script>
<div class="FlexiBox Box23">
	<div class="ArticleListTitle">
		Fixtures &amp; Results
	</div>
	<div style="text-align:center">
		<a href="#" onclick="return switchResults('Fri');">Friday</a>&nbsp;&nbsp;-&nbsp;&nbsp;
		<a href="#" onclick="return switchResults('Sat');">Saturday</a>&nbsp;&nbsp;-&nbsp;&nbsp;
		<a href="#" onclick="return switchResults('Sun');">Sunday</a>
	</div>
	<table style="width:100%;<?php if(mktime() > $sat) { ?>display:none;<?php } ?>" id="resultFri">
		<tr>
			<th>Start</th>
			<th>Sport</th>
			<th>Event</th>
			<th>Venue</th>
			<th>Points</th>
			<th>Score</th>
			<th>Winner</th>
		</tr>
		<?php
		foreach ($events as $event) {
			if (strtotime($event['event_time']) >= $sat) continue;
		?>
		<tr>
			<td><?php echo(xml_escape(date('D H:i', strtotime($event['event_time'])))); ?></td>
			<td><?php echo(xml_escape($event['event_sport'])); ?></td>
			<td><?php echo(xml_escape($event['event_name'])); ?></td>
			<td><?php echo(xml_escape($event['event_venue'])); ?></td>
			<td><?php echo(xml_escape($event['event_points'])); ?></td>
			<?php if ($event['event_score_time'] !== NULL) { ?>
				<td>
					<?php
					if ($event['event_york_score'] > 0 || $event['event_lancaster_score'] > 0) {
						echo(xml_escape($event['event_york_score'] . ' - ' . $event['event_lancaster_score']));
					} ?>
				</td>
				<td>
					<?php if ($event['event_york_score'] > $event['event_lancaster_score']) { ?>
						<img src="/images/version2/rose_yorkshire.png" alt="York Wins!" title="York Wins!" />
					<?php } elseif ($event['event_york_score'] < $event['event_lancaster_score']) { ?>
						<img src="/images/version2/rose_lancashire.png" alt="Lancaster Wins!" title="Lancaster Wins!" />
					<?php } else { ?>
						<img src="/images/version2/rose_draw.png" alt="Draw!" title="Draw!" />
					<?php } ?>
					&nbsp;
				</td>
			<?php } else { ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</table>
	<table style="width:100%;<?php if(mktime() < $sat || mktime() > $sun) { ?>display:none;<?php } ?>" id="resultSat">
		<tr>
			<th>Start</th>
			<th>Sport</th>
			<th>Event</th>
			<th>Venue</th>
			<th>Points</th>
			<th>Score</th>
			<th>Winner</th>
		</tr>
		<?php
		foreach ($events as $event) {
			if (strtotime($event['event_time']) < $sat || strtotime($event['event_time']) >= $sun) continue;
		?>
		<tr>
			<td><?php echo(xml_escape(date('D H:i', strtotime($event['event_time'])))); ?></td>
			<td><?php echo(xml_escape($event['event_sport'])); ?></td>
			<td><?php echo(xml_escape($event['event_name'])); ?></td>
			<td><?php echo(xml_escape($event['event_venue'])); ?></td>
			<td><?php echo(xml_escape($event['event_points'])); ?></td>
			<?php if ($event['event_score_time'] !== NULL) { ?>
				<td>
					<?php
					if ($event['event_york_score'] > 0 || $event['event_lancaster_score'] > 0) {
						echo(xml_escape($event['event_york_score'] . ' - ' . $event['event_lancaster_score']));
					} ?>
				</td>
				<td>
					<?php if ($event['event_york_score'] > $event['event_lancaster_score']) { ?>
						<img src="/images/version2/rose_yorkshire.png" alt="York Wins!" title="York Wins!" />
					<?php } elseif ($event['event_york_score'] < $event['event_lancaster_score']) { ?>
						<img src="/images/version2/rose_lancashire.png" alt="Lancaster Wins!" title="Lancaster Wins!" />
					<?php } else { ?>
						<img src="/images/version2/rose_draw.png" alt="Draw!" title="Draw!" />
					<?php } ?>
					&nbsp;
				</td>
			<?php } else { ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			<?php } ?>
		</tr>
		<?php } ?>
	</table>
	<table style="width:100%;<?php if(mktime() < $sun) { ?>display:none;<?php } ?>" id="resultSun">
		<tr>
			<th>Start</th>
			<th>Sport</th>
			<th>Event</th>
			<th>Venue</th>
			<th>Points</th>
			<th>Score</th>
			<th>Winner</th>
		</tr>
		<?php
		foreach ($events as $event) {
			if (strtotime($event['event_time']) < $sun) continue;
		?>
		<tr>
			<td><?php echo(xml_escape(date('D H:i', strtotime($event['event_time'])))); ?></td>
			<td><?php echo(xml_escape($event['event_sport'])); ?></td>
			<td><?php echo(xml_escape($event['event_name'])); ?></td>
			<td><?php echo(xml_escape($event['event_venue'])); ?></td>
			<td><?php echo(xml_escape($event['event_points'])); ?></td>
			<?php if ($event['event_score_time'] !== NULL) { ?>
				<td>
					<?php
					if ($event['event_york_score'] > 0 || $event['event_lancaster_score'] > 0) {
						echo(xml_escape($event['event_york_score'] . ' - ' . $event['event_lancaster_score']));
					} ?>
				</td>
				<td>
					<?php if ($event['event_york_score'] > $event['event_lancaster_score']) { ?>
						<img src="/images/version2/rose_yorkshire.png" alt="York Wins!" title="York Wins!" />
					<?php } elseif ($event['event_york_score'] < $event['event_lancaster_score']) { ?>
						<img src="/images/version2/rose_lancashire.png" alt="Lancaster Wins!" title="Lancaster Wins!" />
					<?php } else { ?>
						<img src="/images/version2/rose_draw.png" alt="Draw!" title="Draw!" />
					<?php } ?>
					&nbsp;
				</td>
			<?php } else { ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			<?php }  ?>
		</tr>
		<?php } ?>
	</table>
</div>

<script type="text/javascript">
function flickr_enlarge (e, photo) {
	preview_img = document.getElementById('flickr_img');
	if (preview_img.src != photo.src.replace('_s.jpg','_m.jpg')) {
		preview_img.src = photo.src.replace('_s.jpg','_m.jpg');
		preview_img.alt = photo.alt;
		preview_img.title = photo.title;
	}
}
</script>

<div style="float:left;" class="FlexiBox Box13 FlexiBoxLast">
	<div class="ArticleListTitle">
		<a href="http://www.flickr.com/photos/theyorker/sets/72157626585372481/">latest photos</a>
	</div>
	<div style="text-align:center">
	<?php
	$first = true;
	foreach ($photos as $photo) {
		if ($first) {
			echo('<div style="text-align:center">');
			echo('<a id="flickr_link" target="_blank" href="http://www.flickr.com/photos/theyorker/sets/72157626585372481/">');
			echo('<img id="flickr_img" src="' . str_replace('_s', '_m', $photo['photo']) . '" alt="' . $photo['title'] . '" />');
			echo('</a>');
			echo('</div>');
			$first = false;
		}
		echo('<a href="' . $photo['link'] . '" target="_blank">');
		echo('<img style="margin-right:2px" src="' . $photo['photo'] . '" alt="' . $photo['title'] . '" title="' . $photo['title'] . '" onmousemove="flickr_enlarge(event, this);" />');
		echo('</a>');
	}
	?>
	</div>
	<div style="text-align:center">
		<a href="http://www.flickr.com/photos/theyorker/sets/72157626585372481/" target="_blank">View more...</a>
	</div>
</div>

<?php
ArticleList('Roses 2011 Articles', $others, true);
?>
