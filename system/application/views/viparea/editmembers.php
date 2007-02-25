<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>

</div>
<?php //$this->load->view('viparea/members_list', array('organisation' => $organisation));?>
<div class='blue_box'>
	<div style="text-align: center;"><h2>Membership - <?php echo $membership['firstname'] ?></h2></div>
	<a href="<?php echo vip_url('members/cards/filter/user/'.$membership['user_id'].'');?>">Member's Business Card</a>
	<br />
	<a href='/viparea/members/unsubscribe/<?php echo $membership['user_id']; ?>'>Unsubscribe Member</a>
	<br />
	<P>These links as icons?</P>
	
	<H3>Member Details</H3>
	<form class='form'>
		<fieldset>
			<label for='member_name'>Name:</label>
			<input style="border: 0px;" type='text' readonly='readonly' name='member_name' value='<?php echo $membership['firstname'] ?>'/>
			<br />
			<label for='member_surname'>Surname:</label>
			<input style="border: 0px;" type='text' readonly='readonly' name='member_surname' value='<?php echo $membership['surname'] ?>'/>
			<br />
			<label for='member_nick'>Nickname:</label>
			<input style="border: 0px;" type='text' readonly='readonly' name='member_nick' value='<?php echo $membership['nickname'] ?>'/>
			<br />
			<?php if (NULL !== $membership['email']) { ?>
				<label for='member_email'>Email:</label>
				<input style="border: 0px;" type='text' readonly='readonly' name='member_email' value='<?php echo $membership['email'] ?>'/>
				<br />
			<?php } ?>
			<label for='member_gender'>Gender:</label>
			<input style="border: 0px;" type='text' readonly='readonly' name='member_gender' value='<?php echo $membership['gender'] ?>'/>
			<br />
			<label for='member_enrol_year'>Enrolled Year:</label>
			<input style="border: 0px;" type='text' readonly='readonly' name='member_enrol_year' value='<?php echo $membership['enrol_year'] ?>'/>
			<br />	<br />
		</fieldset>
	</form>
	
	<H3>Membership Information</H3>
	<form action="<?php echo vip_url('members/info/'.$membership['user_id']);?>" class='form' method='POST'>
		<fieldset>
			<P>On Mailing List:<?php echo $membership['on_mailing_list'] ? 'Yes' : 'No'; ?></P>
			<label for='member_paid'>Paid:</label>
			<input style="border: 0px;" type='checkbox' name='member_paid' value='1' <?php if($membership['paid']){echo 'checked';} ?>>
			<label for='member_vip'>VIP Member:</label>
			<input style="border: 0px;" type='checkbox' name='member_vip' value='1' <?php if($membership['vip']){echo 'checked';} ?>>
			<input name='member_update' type='submit' value='Update' class='button' />
		</fieldset>
	</form>
</div>
<a href='<?php echo vip_url('members/list'); ?>'>Back to Member Management.</a>