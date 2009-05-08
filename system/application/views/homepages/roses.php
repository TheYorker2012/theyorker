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
		<a href="/news/<?php echo($liveblog[0]['headline']); ?>">latest updates</a>
	</div>
	<?php
	foreach ($latest as $l) {
		echo('<div>');
		$cache = str_replace('//medium//', '/small/', $l['cache']);
		$cache = str_replace('//large//', '/small/', $cache);
		echo($cache);
		echo('</div>');
	}
	?>
</div>

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

<div class="FlexiBox Box23">
	<div class="ArticleListTitle">
		Fixtures &amp; Results
	</div>
	<table style="width:100%">
		<tr>
			<th>Start</th>
			<th>Sport</th>
			<th>Event</th>
			<th>Venue</th>
			<th>Points</th>
			<th>Score</th>
			<th>Winner</th>
		</tr>
		<?php foreach ($events as $event) { ?>
		<tr>
			<td><?php echo(xml_escape(date('D H:i', strtotime($event['event_time'])))); ?></td>
			<td><?php echo(xml_escape($event['event_sport'])); ?></td>
			<td><?php echo(xml_escape($event['event_name'])); ?></td>
			<td><?php echo(xml_escape($event['event_venue'])); ?></td>
			<td><?php echo(xml_escape($event['event_points'])); ?></td>
			<?php if ($event['event_score_time'] !== NULL) { ?>
				<td>
					<?php
					if ($event['event_york_score'] > 0 && $event['event_lancaster_score'] > 0) {
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
</div>

<?php
ArticleList('Roses 2009 Articles', $others, true);
?>
