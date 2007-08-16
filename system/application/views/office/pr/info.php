<div class="RightToolbar">
	<h4>Quick Links</h4>
	<div class="Entry">
		<a href="/office/pr/suggestions/">Back To Suggestions</a>
	</div>
</div>

<div class="blue_box">
	<h2>brief information</h2>
	<div class="Entry">
<?php
	echo('	Please click <a href="/office/pr/org/'.$organisation['shortname'].'/directory/information">here</a> to go to the full directory entry.'."\n");
	echo('<br /><br />'."\n");
	echo('<b>Name:</b> '.$organisation['name']."\n");
	echo('<br />'."\n");
	echo('<b>Type:</b> '.$organisation['type']."\n");
	echo('<br />'."\n");
	echo('<b>Description:</b> '.$organisation['description']."\n");
	echo('<br />'."\n");
	echo('<b>Website:</b> <a href="'.$organisation['website'].'">'.$organisation['website'].'</a>'."\n");
	echo('<br />'."\n");
	echo('<b>Email:</b> <a href="mailto:'.$organisation['email_address'].'">'.$organisation['email_address'].'</a>'."\n");
	echo('<br />'."\n");
?>
	</div>
</div>

<?php
if ($status == 'suggestion')
{
?>
<div class="blue_box">
	<h2>options (suggestion editor)</h2>
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
		echo('					<option value="'.$office_user['id'].'">to '.$office_user['firstname'].' '.$office_user['surname'].'</option>'."\n");
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
else if ($status == 'unassigned')
{
?>
<div class="blue_box">
	<h2>options (unassigned editor)</h2>
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
		echo('				<label for="r_submit_accept_rep'.$rep['user_id'].'">'.$rep['user_firstname'].' '.$rep['user_surname'].'</label>'."\n");
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
		echo('					<option value="'.$office_user['id'].'">to '.$office_user['firstname'].' '.$office_user['surname'].'</option>'."\n");
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

<div class="blue_box">
	<h2>options (unassigned writer)</h2>
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
?>
<div class="blue_box">
	<h2>options (pending editor)</h2>
	<div class="Entry">
<?php echo('		'.$rep['user_firstname'].' '.$rep['user_surname'].' has been asked to look after this organisation'."\n"); ?>
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

<div class="blue_box">
	<h2>options (pending writer)</h2>
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
?>

<?php

echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');

?>