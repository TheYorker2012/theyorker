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
	<a href='<?php echo vip_url('members/invite'); ?>'>Invite members to join</a>.
	<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
	<tr style="background-color: #eee">
		<th>Forename</th>
		<th>Surname</th>
		<th>Email</th>
		<th>Paid</th>
		<th>E?</th>
		<th>Conf</th>
		<th>VIP</th>
		<th>Edit</th>
	</tr>
	<?php for($i=0;$i<count($organisation);$i++) {?>
	<tr>
		<td><?php echo $organisation[$i]['firstname']; ?></td>
		<td><?php echo $organisation[$i]['surname']; ?></td>
		<td><a href='mailto:<?php echo $organisation[$i]['email'];?>'><?php echo $organisation[$i]['email']; ?></a></td>
		<td><?php echo $organisation[$i]['paid']; ?></td>
		<td><?php echo $organisation[$i]['if_email']; ?></td>
		<td><?php echo $organisation[$i]['confirmed']; ?></td>
		<td><?php echo $organisation[$i]['vip']; ?></td>
		<td><a href='<?php echo vip_url('members/info/'.$organisation[$i]['id']); ?>'>Edit</a></td>
	</tr>
	<?php } ?>
	</table>	

</div>
<a href='<?php echo vip_url(); ?>'>Back to the vip area.</a>