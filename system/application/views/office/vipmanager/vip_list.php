<?php
/**
 * @file vip_list.php
 * @brief Show a list of all the vips in all organisations
 */
?>

<div class="BlueBox">
	<h2>VIPs</h2>

	<p>This is a list of the VIPs in every organisation. VIP requests will appear at the top of the list.</p>

	<form class="form" method="post" action="<?php echo $target; ?>" id="member_select_form">

		<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2" width="100%">
		<tr style="background-color: #eee">
			<th align="center">
				<input type="checkbox" name="members_selected[]"
					value="userSelectAllNone"
					id="userSelectAllNone" /></th>
			<th>Forename</th>
			<th>Surname</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Organisation</th>
			<th>Position</th>
			<th align="center">Paid</th>
			<th align="center">VIP</th>
			<th>New</th>
			<th>X</th>
		</tr>
		<?php

		foreach ($members as $membership) {
		?>
		<tr>
			<td align="center">
				<input type="checkbox" name="members_selected[]"
					value="user<?php echo $membership['user_id']; ?>"
					id="user<?php echo $membership['user_id']; ?>" /></td>
			<td><a href='<?php echo '/office/vipmanager/info/'.$membership['user_id']; ?>'><?php echo $membership['firstname']; ?></a></td>
			<td><a href='<?php echo '/office/vipmanager/info/'.$membership['user_id']; ?>'><?php echo $membership['surname']; ?></a></td>
			<td><?php if (NULL !== $membership['email']) { ?>
				<a href='mailto:<?php echo $membership['email'];?>@york.ac.uk'><?php echo $membership['username']; ?></a>
			<?php } else {?>
				<?php echo $membership['username']; ?>
			<?php } ?></td>
			<td><?php echo $membership['phone_number']; ?></td>
			<td><a href='/office/reviews/<?php echo $membership['organisation_codename']; ?>'><?php echo $membership['organisation_name']; ?></a></td>
			<td><?php echo $membership['position']; ?></td>
			<td align="center"><?php if (isset($membership['paid']) && $membership['paid']) { ?>
				<img src="/images/prototype/members/paid.png" alt="Yes" />
			<?php } ?></td>
			<td align="center">
				<?php if (isset($membership['vip']) && $membership['vip']) { ?>
					<img src="/images/prototype/members/vip.png" alt="VIP" title="VIP" />
				<?php } elseif (isset($membership['vip_requested']) && $membership['vip_requested']) { ?>
					<a href="/office/vipmanager/promote/<?php echo $membership['user_id'];?>/<?php echo $membership['organisation_entity_id'];?>" onclick="return confirm('Are you sure you want to promote &quot;<?php echo $membership['firstname']; ?> <?php echo $membership['surname']; ?>&quot; of &quot;<?php echo $membership['organisation_name']; ?>&quot; to VIP status?');"><img src="/images/prototype/members/vip_requested.png" alt="Requested VIP Access: Click to Promote" title="Requested VIP Access: Click to Promote" /></a>
				<?php } ?>
				</td>
			<td align="center"><?php if (isset($membership['organisation_needs_approval']) && $membership['organisation_needs_approval']) { ?>
				<a href='/office/pr/org/<?php echo $membership['organisation_codename']; ?>/directory/information'><img src="/images/prototype/members/new.png" alt="Organisation Requires Approval" /></a>
			<?php } ?></td>
			<td align="center">
					<a href="/office/vipmanager/demote/<?php echo $membership['user_id'];?>/<?php echo $membership['organisation_entity_id'];?>" onclick="return confirm('Are you sure you want to DEMOTE &quot;<?php echo $membership['firstname']; ?> <?php echo $membership['surname']; ?>&quot; of &quot;<?php echo $membership['organisation_name']; ?>&quot;? No confirmation e-mail will be sent, and the member will be silently rejected. He will have to reapply for VIP status if you cock this up.');"><img src="/images/prototype/members/no9.png" alt="DEMOTE Member" title="DEMOTE Member" /></a>
				</td>
		</tr>
		<?php } ?>
		</table>
	</form>
</div>
