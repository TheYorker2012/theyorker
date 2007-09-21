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
<?php
	echo('		<form class="form" name="members_invite_form" action="'.$target.'" method="POST">'."\n");
?>
			<fieldset>
				<label for='invite_list'>Invite List:</label>
				<textarea name="invite_list" rows="10" cols="50"><?php echo $default_list; ?></textarea>
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
		<?php //echo $what_to_do; ?>
<?php
	echo('		<form class="form" name="members_invite_form" action="'.$target.'" method="POST">'."\n");
?>
			<fieldset>
				<label for='invite_list_failures'>List of INCORRECT email addresses:</label>
				<textarea name="invite_list_failures" rows="10" cols="50"><?php echo(implode("\r", $failures)); ?></textarea>
				<label for='invite_list_valids'>List of CORRECT email addresses:</label>
				<textarea name="invite_list_valids" rows="10" cols="50"><?php echo(implode("\r", $valids)); ?></textarea>
				<input type='submit' class='button' name='members_invite_button' value='Continue'>
			</fieldset>
		</form>
	</div>

	<?php } elseif ($State == 3) { ?>
	<div class="BlueBox">
		<h2>Send Invitations</h2>
		<p>
			The following list of emails are either already members or are already invited.
		</p>
		<div class="ArticleBox">
			<table width="100%">
				<thead>
					<tr>
						<th>
							Username
						</th>
					</tr>
				</thead>
				<tbody>
<?php
	$alternate = 1;
	foreach ($existing as $existing_user)
	{
		echo('					<tr class="tr'.$alternate.'">'."\n");
		echo('						<td>'."\n");
		echo('							'.$existing_user."\n");
		echo('						</td>'."\n");
		echo('					</tr>'."\n");
		$alternate == 1 ? $alternate = 2 : $alternate = 1;
	}
?>
				</tbody>
			</table>
		</div>
		<p>
			You have entered the following list of emails, please uninvite any you wish not to send an invitation to and then click the Finish button.
		</p>
<?php
	echo('		<form class="form" name="members_invite_form" action="'.$target.'" method="POST">'."\n");
?>
		<div class="ArticleBox">
			<table width="100%">
				<thead>
					<tr>
						<th>
							Username
						</th>
						<th>
							Invite
						</th>
					</tr>
				</thead>
				<tbody>
<?php
	$alternate = 1;
	foreach ($inviting as $invite_user)
	{
		echo('					<tr class="tr'.$alternate.'">'."\n");
		echo('						<td>'."\n");
		echo('							'.$invite_user."\n");
		echo('						</td>'."\n");
		echo('						<td>'."\n");
		echo('							<input type="checkbox" id="user'.$invite_user.'" name="invite['.$invite_user.']" checked="checked" />'."\n");
		echo('						</td>'."\n");
		echo('					</tr>'."\n");
		$alternate == 1 ? $alternate = 2 : $alternate = 1;
	}
?>
				</tbody>
			</table>
		</div>
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

<?php

echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');

?>
