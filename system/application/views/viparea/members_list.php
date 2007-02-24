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
	<?php foreach ($members as $membership) {?>
	<tr>
		<td><?php echo $membership['firstname']; ?></td>
		<td><?php echo $membership['surname']; ?></td>
		<?php if (NULL !== $membership['email']) { ?>
			<td><a href='mailto:<?php echo $membership['email'];?>'><?php echo $membership['email']; ?></a></td>
		<?php } else {?>
			<td>unavailable</td>
		<?php } ?>
		<td><?php echo $membership['paid']; ?></td>
		<td><?php echo $membership['on_mailing_list']; ?></td>
		<td>0</td>
		<td><?php echo $membership['vip']; ?></td>
		<td><a href='<?php echo vip_url('members/info/'.$membership['user_id']); ?>'>Edit</a></td>
	</tr>
	<?php } ?>
	</table>	

</div>