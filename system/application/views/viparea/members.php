<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
<h4>Filter by details</h4>
<input type='checkbox' name='filter_paid' value='1'> Paid<br />
<input type='checkbox' name='filter_mailing_list' value='1'> On Mailing List<br />
<input type='checkbox' name='filter_reply' value='1'> Awaiting Reply<br />
<input type='checkbox' name='filter_vip' value='1'> VIP<br />
<input type='checkbox' name='filter_phone' value='1'> Phone Number<br />
<input type='checkbox' name='filter_drive' value='1'> Can Drive<br />
<h4>Filter by team</h4>
<input type='checkbox' name='filter_team_1' value='1'> Team 1<br />
<input type='checkbox' name='filter_team_2' value='1'> Team 2<br />
<input type='checkbox' name='filter_team_3' value='1'> Team 3<br />
<input type='checkbox' name='filter_team_4' value='1'> Team 4<br />
</div>
<a href='<?php echo vip_url('members/invite'); ?>'>Invite members to join</a>
<?php $this->load->view('viparea/members_list');?>
<a href='<?php echo vip_url(); ?>'>Back to the vip area.</a>