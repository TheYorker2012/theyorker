<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>

</div>
<div class='blue_box'>
	<h2>members</h2>
	
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
<div class='grey_box'>
<div style="text-align: center;"><h2>add member</h2></div>
can you add a member?? surely they subscribe somewhere else?
<form action='/viparea/members/add' class='form' method='POST'>
	<fieldset>
		<label for='member_name'>Name:</label>
		<input type='text' name='member_name' />
		<br />
		<label for='member_nick'>Nickname:</label>
		<input type='text' name='member_nick' />
		<br />
		<label for='member_email'>Email:</label>
		<input type='text' name='member_email' />
		<br />
		<label for='member_gender'>Gender:</label>
		<input type='text' name='member_gender' />
		<br />
		<label for='member_enrol_year'>Enrolled Year:</label>
		<input type='text' name='member_enrol_year' />
		<br />	<br />					
		<input style="border: 0px;" type='checkbox' name='member_paid' value='1'> Paid<br />
		<input style="border: 0px;" type='checkbox' name='member_mailing_list' value='1'> On Mailing List<br />
		<input style="border: 0px;" type='checkbox' name='member_reply' value='1'> Awaiting Reply<br />
		<input style="border: 0px;" type='checkbox' name='member_vip' value='1'> VIP<br />
		<label for='member_update'></label>
		<input name='member_update' type='submit' value='Add' class='button' />
	</fieldset>
</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>