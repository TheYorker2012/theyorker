<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="#">Wizard</a>
	</div>
</div>

<div class="BlueBox">
	<h2>suggestions</h2>
	<p>
		This page contains a list of all organisations which have been suggested to the yorker and need verifying before being accepted and assigned to a rep. Click an organisation name for more information and options to act on this suggestion.
	</p>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Suggested By</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
<?php
	$alternate = 1;
	foreach($orgs as $org)
	{
		$date_text = date('jS F Y' , time($org['suggested_time']));
		echo('				<tr class="tr'.$alternate.'">'."\n");
		echo('					<td>'."\n");
		echo('						<a href="/office/pr/info/'.$org['org_dir_entry_name'].'">'.$org['org_name'].'</a>'."\n");
		echo('					</td>'."\n");
		echo('					<td>'."\n");
		echo('						'.$org['user_firstname'].' '.$org['user_surname'].''."\n");
		echo('					</td>'."\n");
		echo('					<td>'."\n");
		echo('						'.$date_text.''."\n");
		echo('					</td>'."\n");
		echo('				</tr>'."\n");
		$alternate == 1 ? $alternate = 2 : $alternate = 1;
	}
?>
			</tbody>
		</table>
	</div>
</div>

<!--
<div class="BlueBox">
	<h2>options</h2>
	<div class="Entry">
		Any option you choose here applies to all ticked organisations above.
	</div>
	<div class="Entry">
		Reject all ticked suggestions
		<form class="form" action="/office/pr/suggestionmodify" method="post">
			<fieldset>
				<input type="submit" value="Reject Suggestion" class="button" name="r_submit_reject" />
			</fieldset>
		</form>
	</div>
	<br />
	<div class="Entry">
		Accept all ticked suggestions and place them in the unassigned pool
		<form class="form" action="/office/pr/suggestionmodify" method="post">
			<fieldset>
				<input type="submit" value="Accept To Pool" class="button" name="r_submit_accept_unnassigned" />
			</fieldset>
		</form>
	</div>
	<br />
	<div class="Entry">
		Accept all ticked suggestions and assign them to someone
		<form class="form" action="/office/pr/suggestionmodify" method="post">
			<fieldset>
				<select name="a_assign_to">
				<optgroup label="Assign To:">
<?php
/*
	foreach($office_users as $office_user)
	{
		echo('					<option value="'.$office_user['id'].'">to '.$office_user['firstname'].' '.$office_user['surname'].'</option>'."\n");
	}
	*/
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
-->

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
