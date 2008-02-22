<div id="RightColumn">
	<h2 <?php if($State != 4) { echo('class="first"'); }?> >What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
</div>

<div id="MainColumn">
	<?php if ($State == 1) { ?>
	<div class="BlueBox">
		<h2>Block Invite</h2>
		<p>
			Use this to invite lots of users by copying in the usernames into this box.
		</p>
		<?php echo($what_to_do); ?>
<?php
	echo('		<form class="form" action="'.$target.'" method="post">'."\n");
?>
			<fieldset>
				<label for='invite_list'>Invite List:</label>
				<textarea name="invite_list" rows="10" cols="50"><?php echo($default_list); ?></textarea>
				<input type='submit' class='button' name='members_invite_button' value='Continue'>
			</fieldset>
		</form>
	</div>

	<?php } elseif ($State == 2) { ?>
	<div class="BlueBox">
		<h2>Errors in Block Invite</h2>
		<p>
			There are a number of email addresses that do not appear to be York University emails.
			Please check over the errors and then click continue.
		</p>
		<?php //echo $what_to_do; ?>
<?php
	echo('		<form class="form" action="'.$target.'" method="post">'."\n");
?>
			<fieldset>
				<label for='invite_list_failures'>List of INCORRECT email addresses:</label>
				<textarea name="invite_list_failures" rows="10" cols="50"><?php echo(xml_escape(implode("\r", $failures))); ?></textarea>
				<label for='invite_list_valids'>List of CORRECT email addresses:</label>
				<textarea name="invite_list_valids" rows="10" cols="50"><?php echo(xml_escape(implode("\r", $valids))); ?></textarea>
			</fieldset>
			<fieldset>
				<input type='submit' class='button' name='members_invite_button' value='Continue'>
			</fieldset>
		</form>
	</div>

	<?php } elseif ($State == 3) { ?>
	<div class="BlueBox">
		<h2>Send Invitations</h2>
<?php
	if (count($existing) > 0) {
?>
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
<?php
	}
?>
		<p>
			You have entered the following list of emails, please uninvite any you wish not to send an invitation to and then click the Finish button.
		</p>
<?php
	echo('		<form class="form" action="'.$target.'" method="post">'."\n");
?>
			<fieldset>
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
		echo('							<tr class="tr'.$alternate.'">'."\n");
		echo('								<td>'."\n");
		echo('									'.$invite_user."\n");
		echo('								</td>'."\n");
		echo('								<td>'."\n");
		echo('									<input type="checkbox" id="user'.$invite_user.'" name="invite['.$invite_user.']" checked="checked" />'."\n");
		echo('								</td>'."\n");
		echo('							</tr>'."\n");
		$alternate == 1 ? $alternate = 2 : $alternate = 1;
	}
?>
						</tbody>
					</table>
				</div>
			</fieldset>
			<fieldset>
				<input type="submit" class="button" name="confirm_invite_button" value="Confirm Invites" />
			</fieldset>
		</form>
	</div>
</div>
<?php
	}
?>
