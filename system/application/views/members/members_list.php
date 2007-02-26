<?php
/**
 * @file members_list.php
 * @brief Show a list of all the members and allow filtering.
 */
?>
<div class='blue_box'>
	<h2>members</h2>
	<form class="form" method="post" action="" name="member_select_form" id="member_select_form">
		<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<tr style="background-color: #eee">
			<th></th>
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
			<td align="center">
				<input type="checkbox" name="selected_tbl[]"
					value="user<?php echo $membership['user_id']; ?>"
					id="user<?php echo $membership['user_id']; ?>" /></td>
			<td><label for="user<?php echo $membership['user_id']; ?>"><?php echo $membership['firstname']; ?></label></td>
			<td><label for="user<?php echo $membership['user_id']; ?>"><?php echo $membership['surname']; ?></label></td>
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
	</form>

</div>