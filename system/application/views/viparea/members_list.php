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