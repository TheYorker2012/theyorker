<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<p>For the main organisation and each team:</p>
		<ul>
			<li>list of current VIPs, name, username </li>
			<li>link to user</li>
			<li>revoke VIP for any other than the current user</li>
		</ul>
	</div>
<?php
	
	
	/// Draw a branch of the tree of teams
	function DoTeam($team, $in_list = TRUE)
	{
		if ($in_list) {
			echo '<LI>';
		}
		echo '<input type="checkbox" name="filter_team_'.$team['id'].'" value="'.$team['id'].'" />';
		echo '<a href="'.vip_url('members/teams/'.$team['id']).'">'.$team['name'].'</a>';
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
<?php $this->load->view('members/members_list');?>
<a href='<?php echo vip_url('members/list'); ?>'>Back to Member Management.</a>
