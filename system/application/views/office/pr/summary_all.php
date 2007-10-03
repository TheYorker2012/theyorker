<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="/wizard/organisation/">Wizard</a>
	</div>
</div>

<div class="blue_box">
	<h2>summary - all assigned organisations</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>
						Organisation
						<a href="/office/pr/summaryall/org/asc"><img src="/images/icons/bullet_arrow_down.png" /></a>
						<a href="/office/pr/summaryall/org/desc"><img src="/images/icons/bullet_arrow_up.png" /></a>
					<th>
						Priority
						<a href="/office/pr/summaryall/pri/asc"><img src="/images/icons/bullet_arrow_down.png" /></a>
						<a href="/office/pr/summaryall/pri/desc"><img src="/images/icons/bullet_arrow_up.png" /></a>
					</th>
					<th>
						Rep
						<a href="/office/pr/summaryall/rep/asc"><img src="/images/icons/bullet_arrow_down.png" /></a>
						<a href="/office/pr/summaryall/rep/desc"><img src="/images/icons/bullet_arrow_up.png" /></a>
					</th>
					<th>
						Rating
					</th>
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
		echo('						'.$org['user_firstname'].' '.$org['user_surname']."\n");
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