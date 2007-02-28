<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
<h4>Filter by details</h4>
<input type='checkbox' name='filter_paid' value='1'> Paid<br />
<input type='checkbox' name='filter_mailing_list' value='1'> On Mailing List<br />
<input type='checkbox' name='filter_reply' value='1'> Awaiting Reply<br />
<input type='checkbox' name='filter_vip' value='1'> VIP<br />
<?php
	/// Draw a branch of the tree of teams
	function EchoTeamFilters($team, $in_list = TRUE)
	{
		if ($in_list) {
			echo '<LI>';
		}
		echo '<input type="checkbox" name="filter_team_'.$team['id'].'" value="'.$team['id'].'" />';
		echo $team['name'];
		if (!empty($team['subteams'])) {
			echo '<UL>';
			foreach ($team['subteams'] as $subteam) {
				EchoTeamFilters($subteam);
			}
			echo '</UL>';
		}
		if ($in_list) {
			echo '</LI>';
		}
		return count($team['subteams']);
	}
	
	if (!empty($organisation['subteams'])) {
		echo '<h4>Filter by team</h4>';
		// Draw the tree of teams
		foreach ($organisation['subteams'] as $team) {
			if (!EchoTeamFilters($team, FALSE)) {
				echo '<br />';
			}
		}
	}
?>
</div>
<a href='<?php echo vip_url('members/invite'); ?>'>Invite members to join</a>
<?php $this->load->view('members/members_list');?>
<a href='<?php echo vip_url(); ?>'>Back to the vip area.</a>