<div class='RightToolbar'>
	<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
	<P><A HREF="<?php echo vip_url('members/list/not/confirmed'); ?>">List invited users</A></P>
	
	<P>you have 100 members (of which 20 have paid) and 200 invited (of which 2 have paid)</P>
</div>

<?php if ($State == 1) { ?>
<div class="<?php echo alternator('blue','grey'); ?>_box">
	<H2>Block Invite</H2>
	<P>
		Use this to invite lots of users by copying in the usernames into this box.
		You can also put p and v at the end to indicate paid and vip.
	</P>
	<?php echo $what_to_do; ?>
	<form name='members_invite_form' action='2' method='POST' class='form'>
		<fieldset>
			<label for='invite_list'>Invite List:</label>
			<textarea name="invite_list" class="full" rows="10"><?php echo $default_list; ?></textarea>
			<input type='submit' class='button' name='members_invite_button' value='Continue'>
		</fieldset>
	</form>
</div>

<?php } elseif ($State == 2) { ?>
<div class="<?php echo alternator('blue','grey'); ?>_box">
	<H2>Errors in Block Invite</H2>
	<P>
		There were mistakes in your input.
		Please look though and correct them
	</P>
	<P>
		81 users selected for invitation.
	</P>
	<?php //echo $what_to_do; ?>
	<form name='members_invite_form' action='3' method='POST' class='form'>
		<fieldset>
			<table width="100%">
			<tr>
				<th></th>
				<td>...</td>
			</tr>
			<tr>
				<th>30</th>
				<td>jh559</td>
			</tr>
			<tr>
				<th>31</th>
				<td>nse500 v</td>
			</tr>
			<tr>
				<th>32</th>
				<td colspan="3"><textarea name="invite_list" class="full" rows="3">jh559nse500</textarea></td>
			</tr>
			<tr>
				<th>33</th>
				<td>jh559 p</td>
			</tr>
			<tr>
				<th>34</th>
				<td>nse500 pv</td>
			</tr>
			<tr>
				<th></th>
				<td>...</td>
			</tr>
			</table>
			<input type='submit' class='button' name='members_invite_button' value='Continue'>
		</fieldset>
	</form>
</div>

<?php } elseif ($State == 3) { ?>
<div class="<?php echo alternator('blue','grey'); ?>_box">
	<H2>Unrecognised Emails in Block Invite</H2>
	<P>
		The following emails which you specified don't exists.
		You can use this section to correct them.
	</P>
	<P>
		81 users selected for invitation.
	</P>
	<form class="form" action="4">
	<table width="100%">
		<tr>
			<th></th>
			<th>Username</th>
			<th>Paid</th>
			<th>VIP</th>
			<th>Remove from invite list</th>
		</tr>
		<tr>
			<th>15</th>
			<td><input value="jh559" /></td>
			<td><input type="checkbox" checked /> Paid</td>
			<td><input type="checkbox" /> VIP</td>
			<td><a href="#">remove</a></td>
		</tr>
		<tr>
			<th>43</th>
			<td><input value="jh559" /></td>
			<td><input type="checkbox" checked /> Paid</td>
			<td><input type="checkbox" /> VIP</td>
			<td><a href="#">remove</a></td>
		</tr>
	</table>
	<fieldset>
		<input type="submit" class="button" value="Continue" />
	</fieldset>
	</form>
</div>

<?php } elseif ($State == 4) { ?>
<div class="<?php echo alternator('blue','grey'); ?>_box">
	<H2>Invitation List</H2>
	<P>
		The following users which you specified were validated.
		You may now choose which of them have paid membership and which should
		be given VIP status (same as you, beware!)
	</P>
	<P>
		The Invite button below will invite this list of users.
	</P>
	<P>
		81 users selected for invitation.
	</P>
	<form class="form" action="1">
	<table width="100%">
		<tr>
			<th></th>
			<th>Username</th>
			<th>Name</th>
			<th>Status</th>
			<th>Paid</th>
			<th>VIP</th>
			<th>Remove</th>
		</tr>
		<tr>
			<th>43</th>
			<td>jh559</td>
			<td>James Hogan</td>
			<td>already a member</td>
			<td><input type="checkbox" checked /> Paid</td>
			<td><input type="checkbox" /> VIP</td>
			<td><a href="#">remove</a></td>
		</tr>
		<tr>
			<th>44</th>
			<td>jh559</td>
			<td>James Hogan</td>
			<td>invited</td>
			<td><input type="checkbox" checked /> Paid</td>
			<td><input type="checkbox" /> VIP</td>
			<td><a href="#">remove</a></td>
		</tr>
		<tr>
			<th>45</th>
			<td>jh559</td>
			<td>James Hogan</td>
			<td>not invited</td>
			<td><input type="checkbox" checked /> Paid</td>
			<td><input type="checkbox" /> VIP</td>
			<td><a href="#">remove</a></td>
		</tr>
	</table>
	<fieldset>
		<input type="submit" class="button" value="Invite" />
	</fieldset>
	</form>
</div>
<?php } ?>
<?php /*
<div class="<?php echo alternator('blue','grey'); ?>_box">
	<H2>Recently invited users</H2>
	<P>
		The following users have recently been invited.
	</P>
	<table width="100%">
		<tr>
			<th>Username</th>
			<th>Name</th>
			<th>Invite status</th>
		</tr>
		<tr>
			<td>jh559</td>
			<td><a href="#">James Hogan</a></td>
			<td>accepted</td>
		</tr>
		<tr>
			<td>jh559</td>
			<td><a href="#">James Hogan</a></td>
			<td>pending</td>
		</tr>
		<tr>
			<td>jh559</td>
			<td><a href="#">James Hogan</a></td>
			<td>rejected</td>
		</tr>
	</table>
</div>
*/ ?>

<a href='<?php echo vip_url('members/list'); ?>'>Back to Member Management.</a>