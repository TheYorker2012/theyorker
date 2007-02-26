<?php
	/// Do option list teams
	function DoTeam($team, $selected, $depth = 0)
	{
		echo '<option value="'.$team['id'].'"'.(($team['id'] == $selected)?' SELECTED':'').'>'.
			str_repeat('+ ',$depth).$team['name'].'</option>';
		if (!empty($team['subteams'])) {
			foreach ($team['subteams'] as $subteam) {
				DoTeam($subteam, $selected, $depth + 1);
			}
		}
	}
?>

<div class='RightToolbar'>
	<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div style="width: 420px; margin: 0px; padding-right: 3px; ">
<?php echo $what_to_do; ?>
<form name='members_invite_form' action='<?php echo $target; ?>' method='POST' class='form'>
	<fieldset>
		<?php /* Shouldn't invite to teams from here, should invite to organisation then add to team
		<label for="invite_team">Team to invite to:</label>
		<select name="invite_team">
		<?php DoTeam($organisation, $default_team); ?>
		</select><br/> */ ?>
		<label for='invite_list'>Invite List:</label>
		<textarea name="invite_list" class="full" rows="10"><?php echo $default_list; ?></textarea>
		<input type='submit' class='button' name='members_invite_button' value='Invite Members'>
	</fieldset>
</form>
<a href='<?php echo vip_url('members/list'); ?>'>Back to Member Management.</a>
</div>