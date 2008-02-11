<div class="RightToolbar">
	<h4>Quick Links</h4>
	<div class="Entry">
<?php
if ($status == 'suggestion')
{
?>
		<a href="/office/pr/suggestions/">Back To Suggestions</a>
<?php
}
else if ($status == 'unassigned')
{
?>
		<a href="/office/pr/unnassigned/">Back To Unassigned</a>
<?php
}
else if ($status == 'pending')
{
?>
		<a href="/office/pr/pending/">Back To Pending</a>
<?php
}
?>
	</div>
</div>

<?php
	if ($status == 'suggestion')
	{
?>
<div class="blue_box">
	<h2>suggestee information</h2>
	<div class="Entry">
<?php
	echo('		<b>Name:</b> '.xml_escape($suggestor['name'])."\n");
	echo('		<br />'."\n");
	echo('		<b>Email:</b> <a href="mailto:'.xml_escape($suggestor['email']).'">'.xml_escape($suggestor['email']).'</a>'."\n");
	echo('		<br />'."\n");
	echo('		<b>Position:</b> '.xml_escape($suggestor['position'])."\n");
	echo('		<br />'."\n");
	echo('		<b>Notes:</b> '.xml_escape($suggestor['notes'])."\n");
	echo('		<br />'."\n");
?>
	</div>
</div>
<?php
	}
?>

<div class="blue_box">
	<h2>brief information</h2>
	<div class="Entry">
<?php
	echo('		Please click <a href="/office/pr/org/'.$organisation['shortname'].'/directory/information">here</a> to go to the full directory entry.'."\n");
	echo('		<br /><br />'."\n");
	echo('		<b>Name:</b> '.xml_escape($organisation['name'])."\n");
	echo('		<br />'."\n");
	echo('		<b>Type:</b> '.xml_escape($organisation['type'])."\n");
	echo('		<br /><br />'."\n");
	echo('		<b>Description:</b> '.xml_escape($organisation['description'])."\n");
	echo('		<br />'."\n");
	echo('		<b>Website:</b> <a href="'.xml_escape($organisation['website']).'">'.xml_escape($organisation['website']).'</a>'."\n");
	echo('		<br />'."\n");
	echo('		<b>Email:</b> <a href="mailto:'.xml_escape($organisation['email_address']).'">'.xml_escape($organisation['email_address']).'</a>'."\n");
	echo('		<br />'."\n");
?>
	</div>
</div>

<?php
if ($status == 'suggestion')
{
	if ($user['officetype'] != 'Low')
	{
?>
<div class="blue_box">
	<h2>options</h2>
	<div class="Entry">
		Reject this suggestion
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Reject Suggestion" class="button" name="r_submit_reject" />
			</fieldset>
		</form>
	</div>
	<br />
	<div class="Entry">
		Accept this suggestion and place it in the unassigned pool
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Accept To Pool" class="button" name="r_submit_accept_unnassigned" />
			</fieldset>
		</form>
	</div>
	<br />
	<div class="Entry">
		Accept this suggestion and assign it to someone
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<select name="a_assign_to">
				<optgroup label="Assign To:">
<?php
	foreach($office_users as $office_user)
	{
		echo('					<option value="'.$office_user['id'].'">to '.xml_escape($office_user['firstname'].' '.$office_user['surname']).'</option>'."\n");
	}
?>
				</optgroup>
				</select>
			</fieldset>
			<fieldset>
				<input type="submit" value="Accept And Assign" class="button" name="r_submit_accept_assign" />
			</fieldset>
		</form>
	</div>
</div>
<?php
	}
}
else if ($status == 'unassigned')
{
	if ($user['officetype'] != 'Low')
	{
?>
<?php /*
<div class="blue_box">
	<h2>options (user request - for testing only)</h2>
	This is for testing only. It makes a rep request to be the organisations rep
	<div class="Entry">
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<select name="a_assign_to">
				<optgroup label="Assign To:">
<?php
	//foreach($office_users as $office_user)
	//{
	//	echo('					<option value="'.$office_user['id'].'">to '.xml_escape($office_user['firstname'].' '.$office_user['surname']).'</option>'."\n");
	//}
?>
				</optgroup>
				</select>
			</fieldset>
			<fieldset>
				<input type="submit" value="Force Rep Request" class="button" name="r_submit_testing_assign" />
			</fieldset>
		</form>
	</div>
</div>
*/ ?>
<div class="blue_box">
	<h2>options (as an editor)</h2>
	<div class="Entry">
		Delete this organisation
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Delete Organisation" class="button" name="r_submit_delete" />
			</fieldset>
		</form>
	</div>
	<br />
<?php
if (count($reps) > 0)
{
?>
	<div class="Entry">
		The following reps have requested to look after this organisation
<?php
	$alternate = 1;
	foreach($reps as $rep)
	{
		echo('		<form class="form" action="/office/pr/modify" method="post">'."\n");
		echo('			<fieldset>'."\n");
		echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n");
		echo('				<input type="hidden" name="r_userid" value="'.$rep['user_id'].'" />'."\n");
		echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
		echo('			</fieldset>'."\n");
		echo('			<fieldset>'."\n");
		echo('				<label for="r_submit_accept_rep'.$rep['user_id'].'">'.xml_escape($rep['user_firstname'].' '.$rep['user_surname']).'</label>'."\n");
		echo('				<input type="submit" value="Accept" class="button" name="r_submit_accept_rep" id="r_submit_accept_rep'.$rep['user_id'].'" />'."\n");
		echo('				<input type="submit" value="Reject" class="button" name="r_submit_reject_rep" />'."\n");
		echo('			</fieldset>'."\n");
		echo('		</form>'."\n");
		$alternate == 1 ? $alternate = 2 : $alternate = 1;
	}
?>
	</div>
	<br />
<?php
}
?>
	<div class="Entry">
		OR request a different rep to look after this organisation
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<select name="a_assign_to">
				<optgroup label="Assign To:">
<?php
	foreach($office_users as $office_user)
	{
		echo('					<option value="'.$office_user['id'].'">to '.xml_escape($office_user['firstname'].' '.$office_user['surname']).'</option>'."\n");
	}
?>
				</optgroup>
				</select>
			</fieldset>
			<fieldset>
				<input type="submit" value="Request Rep" class="button" name="r_submit_officer_request_rep" />
			</fieldset>
		</form>
	</div>
</div>
<?php
	}
?>
<div class="blue_box">
<?php
	if ($user['officetype'] == 'Low')
		echo('		<h2>options</h2>'."\n");
	else
		echo('		<h2>options (as a writer)</h2>'."\n");
?>
	<div class="Entry">
		Request to be rep for this organisation
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Request" class="button" name="r_submit_request_rep" />
			</fieldset>
		</form>
	</div>
	<br />
	<div class="Entry">
		Withdraw your request to be rep for this organisation
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Withdraw" class="button" name="r_submit_withdraw_rep" />
			</fieldset>
		</form>
	</div>
</div>
<?php
}
else if ($status == 'pending')
{
	if ($user['officetype'] != 'Low')
	{
?>
<div class="blue_box">
	<h2>options (as an editor)</h2>
	<div class="Entry">
<?php echo('		'.xml_escape($rep['user_firstname'].' '.$rep['user_surname']).' has been asked to look after this organisation'."\n"); ?>
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_userid" value="'.$rep['user_id'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Withdraw Request" class="button" name="r_submit_withdraw_request" />
			</fieldset>
		</form>
	</div>
</div>
<?php
	}
?>
<?php
	//only if the current user is the pending subscription user
	if ($rep['user_id'] == $this->user_auth->entityId)
	{
?>
<div class="blue_box">
<?php
		if ($user['officetype'] == 'Low')
			echo('		<h2>options</h2>'."\n");
		else
			echo('		<h2>options (as a writer)</h2>'."\n");
?>
	<div class="Entry">
		Accept the request from the editor to be the rep for this organisation
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_userid" value="'.$rep['user_id'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Accept" class="button" name="r_submit_accept_request" />
			</fieldset>
		</form>
	</div>
	<br />
	<div class="Entry">
		Reject the request from the editor to be the rep for this organisation
		<form class="form" action="/office/pr/modify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
<?php echo('				<input type="hidden" name="r_userid" value="'.$rep['user_id'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Reject" class="button" name="r_submit_reject_request" />
			</fieldset>
		</form>
	</div>
</div>
<?php
	}
}
else if ($status == 'assigned')
{
?>
<div class="blue_box">
	<h2>assigned</h2>
	<p>filler</p>
</div>
<?php
}
?>
