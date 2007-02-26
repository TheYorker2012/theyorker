<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<?php
	/// Draw a branch of the tree of teams
	function DoTeam($team, $in_list = TRUE)
	{
		if ($in_list) {
			echo '<LI>';
		}
		echo '<input type="checkbox" name="filter_team_'.$team['id'].'" value="'.$team['id'].'" />';
		echo $team['name'];
		if (!empty($team['subteams'])) {
			echo '<UL>';
			foreach ($team['subteams'] as $subteam) {
				DoTeam($subteam);
			}
			echo '</UL>';
		}
		if ($in_list) {
			echo '</LI>';
		}
		return count($team['subteams']);
	}
	
	if (!empty($organisation['subteams'])) {
		// Draw the tree of teams
		foreach ($organisation['subteams'] as $team) {
			if (!DoTeam($team, FALSE)) {
				echo '<br />';
			}
		}
	}
?>
<a href='<?php echo vip_url(); ?>'>Back to the vip area.</a>