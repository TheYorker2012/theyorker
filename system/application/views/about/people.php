<script type="text/javascript">
function changeYear () {
	var newyear = document.getElementById('year_change');
	window.location = "/about/theteam/<?php echo($selected_team); ?>/" + newyear.options[newyear.selectedIndex].value + "/";
}
</script>

<div class="BlueBox">
	<h2><?php echo($intro_heading); ?></h2>
	<?php echo($intro_text); ?>
</div>

<div class="BlueBox">
	<div style="float:right;text-align:right">
		Viewing year:
		<select id="year_change" size="1" onchange="changeYear()" onblur="changeYear()">
<?php for ($i = $last_year; $i >= $first_year; $i--) {
	echo('			<option value="' . $i . '"' . (($selected_year == $i) ? ' selected="selected"' : '') . '>' . $i . '-' . ($i+1) . '</option>'."\n");
} ?>
		</select>
	</div>

	<h2>Teams</h2>

	<ul>
<?php foreach ($byline_teams as $team) {
	echo('		<li>'."\n");
	if ($team['business_card_group_id'] == $selected_team)
		echo('			<b>'."\n");
	echo('			<a href="/about/theteam/' . $team['business_card_group_id'] . '/' . $selected_year . '/">'."\n");
	echo('				' . $team['business_card_group_name'] . "\n");
	echo('			</a>'."\n");
	echo('			(' . $team['business_card_group_count'] . ' ' . (($team['business_card_group_count'] == 1) ? 'person' : 'people') . ')'."\n");
	if ($team['business_card_group_id'] == $selected_team)
		echo('			</b>'."\n");
	echo('		</li>'."\n");
} ?>
	</ul>
</div>

<?php
if ($selected_team > 0) {
	foreach ($bylines as $byline) {
		$byline['archive_link'] = true;
		$this->load->view('/office/bylines/byline', $byline);
	}
} ?>