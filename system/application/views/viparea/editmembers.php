<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>

</div>
<?php //$this->load->view('viparea/members_list', array('organisation' => $organisation));?>
<div class='blue_box'>
	<div style="text-align: center;"><h2>Membership - <?php echo $membership['firstname'] ?></h2></div>
	<form action="<?php echo vip_url('members/info/'.$member_entity_id.'/post');?>" class='form' method='POST'>
		<fieldset>
			<a href="<?php echo vip_url('members/info/'.$member_entity_id.'/cards');?>">Member's Business Card</a>
			<br />
			<a href='/viparea/members/unsubscribe/<?php echo $member_entity_id; ?>'>Unsubscribe Member</a>
			<br />
			<P>These links as icons?</P>
			
			<H3>Member Details</H3>
			<label for='member_name'>Name:</label>
			<input style="border: 0px;" type='text' readonly='readonly' name='member_name' value='<?php echo $membership['firstname'] ?>'/>
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
			
			<H3>Membership Information</H3>
			<input style="border: 0px;" type='checkbox' name='member_paid' value='1'
			<?php if($membership['paid']){echo "checked";} ?>> Paid<br />
			<input style="border: 0px;" type='checkbox' name='member_mailing_list' value='1'
			<?php if($membership['on_mailing_list']){echo "checked";} ?>> On Mailing List<br />
			<input style="border: 0px;" type='checkbox' name='member_reply' value='1'
			<?php if($membership['vip']){echo "checked";} ?>> VIP<br />
			<label for='member_update'></label>
			<input name='member_update' type='submit' value='Update' class='button' />
		</fieldset>
	</form>
</div>
<a href='<?php echo vip_url(); ?>'>Back to the vip area.</a>