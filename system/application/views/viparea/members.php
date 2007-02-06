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
<input type='checkbox' name='filter_phone' value='1'> Phone Number<br />
<input type='checkbox' name='filter_drive' value='1'> Can Drive<br />
<h4>Filter by team</h4>
<input type='checkbox' name='filter_team_1' value='1'> Team 1<br />
<input type='checkbox' name='filter_team_2' value='1'> Team 2<br />
<input type='checkbox' name='filter_team_3' value='1'> Team 3<br />
<input type='checkbox' name='filter_team_4' value='1'> Team 4<br />
</div>
<div class='blue_box'>
	<h2>members</h2>
	<a href='/viparea/members/view/<?php echo $organisation; ?>/add'>Add a member.</a>
	<table>
	<tr>
		<td><strong>Forename</strong></td>
		<td><strong>Surname</strong></td>
		<td><strong>Email</strong></td>
		<td><strong>£</strong></td>
		<td><strong>@</strong></td>
		<td><strong>R</strong></td>
		<td><strong>V</strong></td>
		<td><strong>Edit</strong></td>
	</tr>
	<?php foreach ($members as $member) {?>
	<tr>
		<td><?php echo $member['forename']; ?></td>
		<td><?php echo $member['sirname']; ?></td>
		<td><a href='mailto:<?php echo $member['email']; ?>'><?php echo $member['email']; ?></a></td>
		<td><?php echo $member['paid']; ?></td>
		<td><?php echo $member['mailing']; ?></td>
		<td><?php echo $member['awaiting_reply']; ?></td>
		<td><?php echo $member['vip']; ?></td>
		<td><a href='/viparea/members/view/<?php echo $organisation; ?>/edit/<?php echo $member['id']; ?>'>E</a></td>
	</tr>
	<?php } ?>
	</table>
</div>
<div class='grey_box'>
<h2>edit member</h2>
<form action='/viparea/members/view/<?php echo $organisation; ?>/edit' class='form' method='POST'>
	<fieldset>
		<label for='member_name'>Name:</label>
		<input type='text' name='member_name' value='<?php if(!empty($editmember['name'])){echo $editmember['name'];} ?>'/>
		<br />
		<label for='member_group'>Group:</label>
		<select name='member_group'>
		<?php
		foreach ($teams as $team) {
		?>
		<option value='<?php echo $team['id'] ?>' <?php if(!empty($editmember['team_id'])){if ($team['id']==$editmember['team_id']) echo 'SELECTED';}?>><?php echo $team['name'] ?></option>
		<?php
		}
		?>
		</select>
		<br />
		<label for='member_email'>Email:</label>
		<input type='text' name='member_email' value='<?php if(!empty($editmember['email'])){echo $editmember['email'];} ?>'/>
		<br />
		<label for='member_phone'>Phone:</label>
		<input type='text' name='member_phone' value='<?php if(!empty($editmember['phone'])){echo $editmember['phone'];} ?>'/>
		<br />
		<input type='checkbox' name='member_paid' value='1' <?php if(!empty($editmember['member_paid'])){echo "selected";} ?>> Paid<br />
		<input type='checkbox' name='member_mailing_list' value='1' <?php if(!empty($editmember['member_mailing_list'])){echo "selected";} ?>> On Mailing List<br />
		<input type='checkbox' name='member_reply' value='1' <?php if(!empty($editmember['member_reply'])){echo "selected";} ?>> Awaiting Reply<br />
		<input type='checkbox' name='member_vip' value='1' <?php if(!empty($editmember['member_vip'])){echo "selected";} ?>> VIP<br />
		<input type='checkbox' name='member_drive' value='1' <?php if(!empty($editmember['member_drive'])){echo "selected";} ?>> Can Drive<br />
		<label for='member_update'></label>
		<input name='member_update' type='submit' value='Update' class='button' />
	</fieldset>
</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>