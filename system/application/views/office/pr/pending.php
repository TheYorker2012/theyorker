<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="/wizard/organisation/">Wizard</a>
	</div>
</div>

<div class="blue_box">
	<h2>pending</h2>
	<p>
		This table contains a list of all organisations which have been accepted and are waiting for the editor rep request to be accepted. Click an organisation name for more information and to accept if necessary.
	</p>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>PR Rep</th>
				</tr>
			</thead>
			<tbody>
<?php
	$alternate = 1;
	foreach($pending_orgs as $org)
	{
		echo('				<tr class="tr'.$alternate.'">'."\n");
		echo('					<td>'."\n");
		echo('						<a href="/office/pr/info/'.$org['org_dir_entry_name'].'">'.$org['org_name'].'</a>'."\n");
		echo('					</td>'."\n");
		echo('					<td>'."\n");
		echo('						'.$org['user_firstname'].' '.$org['user_surname']."\n");
		echo('					</td>'."\n");
		echo('				</tr>'."\n");
		$alternate == 1 ? $alternate = 2 : $alternate = 1;
	}
?>
			</tbody>
		</table>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
