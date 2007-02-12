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
	<a href='/viparea/members/view/<?php echo $this->user_auth->organisationLogin; ?>/add'>Add a member.</a>
	<table border="1">
	<tr>
		<td><strong>Forename</strong></td>
		<td><strong>Surname</strong></td>
		<td><strong>Email</strong></td>
		<td><strong>Paid</strong></td>
		<td><strong>E?</strong></td>
		<td><strong>Conf</strong></td>
		<td><strong>VIP</strong></td>
		<td><strong>Edit</strong></td>
	</tr>
	<?php for($i=0;$i<count($organisation);$i++) {?>
	<tr>
		<td><?php echo $organisation[$i]['firstname']; ?></td>
		<td><?php echo $organisation[$i]['firstname']; ?></td>
		<td><a href='mailto:<?php echo $organisation[$i]['email'];?>'><?php echo $organisation[$i]['email']; ?></a></td>
		<td><?php echo $organisation[$i]['paid']; ?></td>
		<td><?php echo $organisation[$i]['if_email']; ?></td>
		<td><?php echo $organisation[$i]['confirmed']; ?></td>
		<td><?php echo $organisation[$i]['vip']; ?></td>
		<td><a href='/viparea/members/edit/<?php echo $organisation[$i]['id']; ?>'>Edit</a></td>
	</tr>
	<?php } ?>
	</table>	

</div>
<a href='/viparea/'>Back to the vip area.</a>