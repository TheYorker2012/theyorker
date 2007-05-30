<div id="RightColumn">
<?php if ($State == 4) { ?>
	<h2 class="first">Jump to</h2>
	<div class="Entry">
	<p>
		<ul>
			<li><a href="#list2">Existing members</a> (2 people)</li>
			<li><a href="#list2">Invite list</a> (3 people)</li>
		</ul>
	</p>
	</div>
<?php } ?>
	<h2 <?php if($State != 4) { echo('class="first"'); }?> >What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
		<p><a href="<?php echo vip_url('members/list/not/confirmed'); ?>">List invited users</a></p>
		<p>You have 100 members (of which 20 have paid) and 200 invited (of which 2 have paid)</p>
	</div>
</div>

<div id="MainColumn">
	<?php if ($State == 1) { ?>
	<div class="BlueBox">
		<h2>Block Invite</h2>
		<p>
			Use this to invite lots of users by copying in the usernames into this box.
			You can also put p and v at the end to indicate paid and vip.
		</p>
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
	<div class="BlueBox">
		<h2>Errors in Block Invite</h2>
		<p>
			There were syntactic errors in your input.
			Please look though and correct them
		</p>
		<p>
			81 users selected for invitation.
		</p>
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
	<div class="BlueBox">
		<h2>Unrecognised Emails in Block Invite</h2>
		<p>
			The following emails which you specified don't exists.
			You can use this section to correct them.
		</p>
		<p>
			81 users selected for invitation.
		</p>
		<form class="form" action="4">
		<table width="100%">
			<tr>
				<th></th>
				<th>Username</th>
				<th>Paid</th>
				<th>Remove from list</th>
			</tr>
			<tr>
				<th>15</th>
				<td><input value="jh559" /></td>
				<td><input type="checkbox" checked /></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" /></td>
			</tr>
			<tr>
				<th>43</th>
				<td><input value="jh559" /></td>
				<td><input type="checkbox" checked /></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" /></td>
			</tr>
		</table>
		<fieldset>
			<input type="submit" class="button" value="Continue" />
		</fieldset>
		</form>
	</div>

	<?php } elseif ($State == 4) { ?>
	<form class="form" action="1">
	<div id="save_box" class="<?php echo alternator('blue','grey'); ?>_box">
		<H2>Save changes and Invite</H2>
		<P>This will save changes to existing members and invite users in the invite list.</P>
		<fieldset>
			<input type="submit" class="button" value="Save and Invite" />
		</fieldset>
	</div>
	<div class="BlueBox">
		<h2>Existing Members</h2>
		<p>
			The following users are already members.
			You may edit their paid status' here if you wish.
		</p>
		<p>Members with a <img src="/images/prototype/members/paid.png" alt="Yes" /> are already set as paying members</p>
		<table width="100%">
			<tr>
				<th></th>
				<th>Username</th>
				<th>Name</th>
				<th></th>
				<th>Paid</th>
				<th>Update</th>
			</tr>
			
			<tr>
				<th>43</th>
				<td>jh559</td>
				<td>James Hogan</td>
				<td><img src="/images/prototype/members/paid.png" alt="Yes" /></td>
				<td><input type="checkbox" /><font color="#FF0000"><strong><big>!</big></strong></font></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" checked /></td>
			</tr>
			<tr>
				<th>43</th>
				<td>jh559</td>
				<td>James Hogan</td>
				<td></td>
				<td><input type="checkbox" /></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" checked /></td>
			</tr>
			<tr>
				<th>43</th>
				<td>jh559</td>
				<td>James Hogan</td>
				<td></font></td>
				<td><input type="checkbox" checked /><font color="#FF0000"><strong><big>!</big></strong></font></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" checked /></td>
			</tr>
			<tr>
				<th>43</th>
				<td>jh559</td>
				<td>James Hogan</td>
				<td><img src="/images/prototype/members/paid.png" alt="Yes" /></td>
				<td><input type="checkbox" checked /></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" checked /></td>
			</tr>
		</table>
		<p><a href="#save_box">Back to Top</a></p>
	</div>
	<div class="BlueBox">
		<h2>Invite List</h2>
		<p>
			The following users have been validated and are ready to be invited.
			You may now choose which of them have paid membership.
		</p>
		<table width="100%">
			</tr>
			<tr>
				<th></th>
				<th>Username</th>
				<th>Name</th>
				<th></th>
				<th>Paid</th>
				<th>Invite</th>
			</tr>
			
			<tr>
				<th>43</th>
				<td>jh559</td>
				<td>James Hogan</td>
				<td></td>
				<td><input type="checkbox" checked /></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" checked /></td>
			</tr>
			
			<tr>
				<th>43</th>
				<td>jh559</td>
				<td>James Hogan</td>
				<td></td>
				<td><input type="checkbox" /></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" checked /></td>
			</tr>
			<tr>
				<th>44</th>
				<td>jh559</td>
				<td>James Hogan</td>
				<td></td>
				<td><input type="checkbox" checked /></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" checked /></td>
			</tr>
			<tr>
				<th>45</th>
				<td>jh559</td>
				<td>James Hogan</td>
				<td></td>
				<td><input type="checkbox" /></td>
				<td><input type="checkbox" name="inviteesConfirmed[]"
						value="yeh"
						id="somethinghere" checked /></td>
			</tr>
		</table>
		<P><A href="#save_box">Back to Top</A></P>
	</div>
	</form>
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
</div>
