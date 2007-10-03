<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="/wizard/organisation/">Wizard</a>
	</div>
</div>

<div class="blue_box">
	<h2>rep summary</h2>
	<div id="ArticleBox">
		Rep Business Card Here (with edit if Rep).<br />
		Rep Rating: 75%<br />
		<table>
			<thead>
				<tr>
					<th>Organisation</th>
					<th>Priority</th>
					<th>Rating</th>
				</tr>
			</thead>
			<tbody>
<?php
	$alternate = 1;
	foreach($orgs as $org)
	{
		echo('				<tr class="tr'.$alternate.'">'."\n");
		echo('					<td>'."\n");
		echo('						<a href="/office/pr/summaryorg/'.$org['org_dir_entry_name'].'">'.$org['org_name'].'</a>'."\n");
		echo('					</td>'."\n");
		echo('					<td>'."\n");
		echo('						'.$org['org_priority']."\n");
		echo('					</td>'."\n");
		echo('					<td>'."\n");
		echo('						100%'."\n");
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