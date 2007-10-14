<div id="RightColumn">
	<h2 class="first">Quick Links</h4>
	<div class="entry">
		<a href="/office/vipmanager">Back To VIP List</a>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>user details</h2>
		<form class="form">
			<fieldset>
				<label for="member_name">Name:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_name" id="member_name" value="<?php echo($membership['firstname']); ?>" />
				<label for="member_surname">Surname:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_surname" value="<?php echo($membership['surname']); ?>" />
				<label for="member_nick">Nickname:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_nick" value="<?php echo($membership['nickname']); ?>" />
				<label for="member_email">Email:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_email" value="<?php echo($membership['email']); ?>" />
				<label for="member_email">Phone:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_phone" value="<?php echo(htmlentities($membership['phone_number'])); ?>" />
				<label for="member_gender">Gender:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_gender" value="<?php echo($membership['gender']); ?>" />
				<label for="member_enrol_year">Enrolled Year:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_enrol_year" value="<?php echo($membership['enrol_year']); ?>" />
			</fieldset>
		</form>
	</div>
	<div class="BlueBox">
		<h2>organisation details</h2>
		<form action="<?php echo($_SERVER['REQUEST_URI']);?>" class="form" method="post">
			<fieldset>
				<label for="member_status">Status:</label>
				<table id="a_send_to" style="padding: 5px;">
					<tbody>
<?php if (isset($membership['vip']) && $membership['vip']) { ?>
						<tr>
							<td>
								VIP
								<small><a href="/office/vipmanager/demote/<?php echo($membership['user_id']); ?>/<?php echo($membership['organisation_entity_id']); ?>" onclick="return confirm('Are you sure you want to DEMOTE &quot;<?php echo($membership['firstname']); ?> <?php echo($membership['surname']); ?>&quot; of &quot;<?php echo($membership['organisation_name']); ?>&quot;? No confirmation e-mail will be sent, and the member will be silently rejected. He will have to reapply for VIP status if you cock this up.');"><img src="/images/prototype/members/no9.png" alt="DEMOTE Member" title="DEMOTE Member" /></a></small>
							</td>
						</tr>
<?php } elseif (isset($membership['vip_requested']) && $membership['vip_requested']) { ?>
						<tr>
							<td>
								Requested
								<small><a href="/office/vipmanager/promote/<?php echo($membership['user_id']); ?>/<?php echo($membership['organisation_entity_id']); ?>" onclick="return confirm('Are you sure you want to promote &quot;<?php echo($membership['firstname']); ?> <?php echo($membership['surname']); ?>&quot; of &quot;<?php echo($membership['organisation_name']); ?>&quot; to VIP status?');"><img src="/images/prototype/members/vip_requested.png" alt="Requested VIP Access: Click to Promote" title="Requested VIP Access: Click to Promote" /></a></small>
								<small><a href="/office/vipmanager/demote/<?php echo($membership['user_id']); ?>/<?php echo($membership['organisation_entity_id']); ?>" onclick="return confirm('Are you sure you want to DEMOTE &quot;<?php echo($membership['firstname']); ?> <?php echo($membership['surname']); ?>&quot; of &quot;<?php echo($membership['organisation_name']); ?>&quot;? No confirmation e-mail will be sent, and the member will be silently rejected. He will have to reapply for VIP status if you cock this up.');"><img src="/images/prototype/members/no9.png" alt="DEMOTE Member" title="DEMOTE Member" /></a></small>
							</td>
						</tr>
<?php } ?>
					</tbody>
				</table>
				<label for="member_org_name">Name:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_org_name" id="member_org_name" value="<?php echo($membership['organisation_name']); ?>" />
				<label for="member_org_name">Position:</label>
				<input style="border: 0px;" type="text" readonly="readonly" name="member_org_position" id="member_org_position" value="<?php echo(htmlentities($membership['position'])); ?>" />
				<!--
				<label for="member_paid">Paid:</label>
				<input style="border: 0px;" type="checkbox" name="member_paid" value="1" <?php //if($membership['paid']) { echo('checked="checked"'); } ?> />
				-->
			</fieldset>
		</form>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>