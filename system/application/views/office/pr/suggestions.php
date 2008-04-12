<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
	<h2>Quick Links</h2>
	<div class="Entry">
		<ul>
			<li><a href="/wizard/organisation/">Organisations wizard</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>suggestions</h2>
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
						echo('						<a href="/office/pr/info/'.$org['org_dir_entry_name'].'">'.xml_escape($org['org_name']).'</a>'."\n");
						echo('					</td>'."\n");
						echo('					<td>'."\n");
						echo('						'.xml_escape($org['user_name'])."\n");
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
</div>

<?php /*
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
*/ ?>
