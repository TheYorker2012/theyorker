<div class="RightToolbar">
	<h4>Quick Links</h4>
	<div class="Entry">
		<a href="/office/pr/suggestions/">Back To Suggestions</a>
	</div>
</div>

<div class="blue_box">
	<h2>brief information about the organisation</h2>
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

<div class="blue_box">
	<h2>options</h2>
	<div class="Entry">
		Reject this suggestion
		<form class="form" action="/office/pr/suggestionmodify" method="post">
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
		<form class="form" action="/office/pr/suggestionmodify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<input type="submit" value="Accept To Pool" class="button" name="r_submit_accept_unnassigned" />
			</fieldset>
		</form>
	</div>
	<br />
	<div class="Entry">
		Accept this suggestion and assign it to someone
		<form class="form" action="/office/pr/suggestionmodify" method="post">
			<fieldset>
<?php echo('				<input type="hidden" name="r_direntryname" value="'.$organisation['shortname'].'" />'."\n"); ?>
			</fieldset>
			<fieldset>
				<select name="a_assign_to">
				<optgroup label="Assign To:">
<?php
	foreach($users as $user)
	{
		echo('					<option value="'.$user['id'].'">to '.$user['firstname'].' '.$user['surname'].'</option>'."\n");
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

echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');

?>