<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="#">Wizard</a>
	</div>
</div>
<?php
	if ($user['officetype'] == 'Admin' ||
		$user['officetype'] == 'High')
	{
?>
<div class="BlueBox">
	<h2>Suggestions</h2>
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
		echo('					<td><input type="checkbox" /><a href="/office/pr/suggestion/'.$org['org_dir_entry_name'].'">'.$org['org_name'].'</a></td>'."\n");
		echo('					<td>'.$org['user_firstname'].' '.$org['user_surname'].'</td>'."\n");
		echo('					<td>'.$date_text.'</td>'."\n");
		echo('				</tr>'."\n");
		$alternate == 1 ? $alternate = 2 : $alternate = 1;
	}
?>
			</tbody>
		</table>
	</div>
</div>



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
?>
<?php
	if ($user['officetype'] == 'Low')
	{
?>
<div class="grey_box">
	<h2>Suggestions</h2>
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
				<tr class="tr1">
					<td><a href="#">Toffs V2.0</a></td>
					<td>Jamie Hogan</td>
					<td>25th March 07</td>
				</tr>
				<tr class="tr2">
					<td><a href="#">So Cheesy</a></td>
					<td>Christine Travis</td>
					<td>24th March 07</td>
				</tr>
				<tr class="tr1">
					<td><a href="#">Blue Bicycle</a></td>
					<td>Joe Shelley</td>
					<td>24th March 07</td>
				</tr>
				<tr class="tr2">
					<td><a href="#">Vue Cinema</a></td>
					<td>Nicola Evans</td>
					<td>23rd March 07</td>
				</tr>
				<tr class="tr1">
					<td><a href="#">So Cheesy</a></td>
					<td>Daniella Ashby</td>
					<td>21st March 07</td>
				</tr>
			</tbody>
		</table>
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
