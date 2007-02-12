<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>

</div>
<div class='blue_box'>
	<h2>members</h2>
	
	<table border="1">
	<tr>
		<td><strong>Forename</strong></td>
		<td><strong>Surname</strong></td>
		<td><strong>Email</strong></td>
		<td><strong>Paid</strong></td>
		<td><strong>Email?</strong></td>
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
		<a href='/viparea/members/view/<?php echo $this->user_auth->organisationLogin; ?>/delete/<?php if(!empty($editmember['id'])){echo $editmember['id'];} ?>'>Delete Member</a>
		<br />
		<label for='member_name'>Name:</label>
		<input type='text' name='member_name' value='<?php echo $member[0]['firstname'] ?>'/>
		<br />
		<label for='member_email'>Email:</label>
		<input type='text' name='member_email' value='<?php echo $member[0]['email'] ?>'/>
		<br />
		<label for='member_phone'>Phone:</label>
		<input type='text' name='member_phone' value='<?php if(!empty($editmember['phone'])){echo $editmember['phone'];} ?>'/>
		<br />
		<input type='checkbox' name='member_paid' value='1' 
		<?php if($member[0]['paid']){echo "checked";} ?>> Paid<br />
		<input type='checkbox' name='member_mailing_list' value='1' 
		<?php if($member[0]['if_email']){echo "checked";} ?>> On Mailing List<br />
		<input type='checkbox' name='member_reply' value='1' 
		<?php if($member[0]['paid']){echo "checked";} ?>> Awaiting Reply<br />
		<input type='checkbox' name='member_vip' value='1' 
		<?php if($member[0]['vip']){echo "checked";} ?>> VIP<br />
		<input type='checkbox' name='member_drive' value='1' <?php if(!empty($editmember['member_drive'])){echo "selected";} ?>> Can Drive<br />
		<label for='member_update'></label>
		<input name='member_update' type='submit' value='Update' class='button' />
	</fieldset>
</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>