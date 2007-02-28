<?php
	/// Draw a branch of the tree of teams
	function DoTeam($team, $in_list = TRUE)
	{
		if ($in_list) {
			echo '<LI>';
		}
		echo '<input type="checkbox" name="filter_team_'.$team['id'].'" value="'.$team['id'].'" />';
		echo '<a href="">';
		echo $team['name'];
		if (isset($team['notices'])) {
			echo ' ('.count($team['notices']).' notices)';
		}
		echo '</a>';
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
	
	if (!empty($teams['subteams'])) {
		// Draw the tree of teams
		foreach ($teams['subteams'] as $team) {
			if (!DoTeam($team, FALSE)) {
				echo '<br />';
			}
		}
	}
?>

<PRE>$organisation=<?php var_dump($organisation); ?></PRE>
<PRE>$teams=<?php var_dump($teams); ?></PRE>
<PRE>$notices=<?php var_dump($notices); ?></PRE>