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
<div style="text-align: center;"><h2>edit member - <?php echo $member[0]['nickname'] ?></h2></div>
<form action='/viparea/members/view/<?php echo $this->user_auth->organisationLogin; ?>/edit' class='form' method='POST'>
	<fieldset>
		<a href='/viparea/members/unsubscribe/<?php echo $member[0]['id']; ?>'>Unsubscribe Member</a>
		<br />
		surely this link ^^ should be somewhere up top - will have to re-arrange the table in the blue box somehow
		<br />
		<label for='member_name'>Name:</label>
		<input style="border: 0px;" type='text' readonly='readonly' name='member_name' value='<?php echo $member[0]['firstname'] ?>'/>
		<br />
		<label for='member_nick'>Nickname:</label>
		<input style="border: 0px;" type='text' readonly='readonly' name='member_nick' value='<?php echo $member[0]['nickname'] ?>'/>
		<br />
		<label for='member_email'>Email:</label>
		<input style="border: 0px;" type='text' readonly='readonly' name='member_email' value='<?php echo $member[0]['email'] ?>'/>
		<br />
		<label for='member_gender'>Gender:</label>
		<input style="border: 0px;" type='text' readonly='readonly' name='member_gender' value='<?php echo $member[0]['gender'] ?>'/>
		<br />
		<label for='member_enrol_year'>Enrolled Year:</label>
		<input style="border: 0px;" type='text' readonly='readonly' name='member_enrol_year' value='<?php echo $member[0]['enrol_year'] ?>'/>
		<br />	<br />					
		<input style="border: 0px;" type='checkbox' name='member_paid' value='1' 
		<?php if($member[0]['paid']){echo "checked";} ?>> Paid<br />
		<input style="border: 0px;" type='checkbox' name='member_mailing_list' value='1' 
		<?php if($member[0]['if_email']){echo "checked";} ?>> On Mailing List<br />
		<input style="border: 0px;" type='checkbox' name='member_reply' value='1' 
		<?php if(!$member[0]['confirmed']){echo "checked";} ?>> Awaiting Reply<br />
		<input style="border: 0px;" type='checkbox' name='member_vip' value='1' 
		<?php if($member[0]['vip']){echo "checked";} ?>> VIP<br />
		<label for='member_update'></label>
		<input name='member_update' type='submit' value='Update' class='button' />
	</fieldset>
</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>