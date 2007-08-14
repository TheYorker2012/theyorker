<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="#">Wizard</a>
	</div>
</div>

<div class="BlueBox">
	<h2>unnassigned</h2>
	<p>
		This table contains a list of all organisations which have been accepted but do not have a rep assigned to them. Click an organisation name for more information and options to become its rep.
	</p>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>PR Rep(s)</th>
				</tr>
			</thead>
			<tbody>
<?php
	$alternate = 1;
	$while_reps = 0;
	foreach($unassigned_orgs as $org)
	{
		echo('				<tr class="tr'.$alternate.'">'."\n");
		echo('					<td>'."\n");
		echo('						<a href="/office/pr/info/'.$org['org_dir_entry_name'].'">'.$org['org_name'].'</a>'."\n");
		echo('					</td>'."\n");
		echo('					<td>'."\n");
		//make sure it stays within the array then if parent id matches current org id
		$no_reps = TRUE;
		while (($while_reps < count($reps)) && ($reps[$while_reps]['org_id'] == $org['org_id']))
		{
			echo('						'.$reps[$while_reps]['user_firstname'].' '.$reps[$while_reps]['user_surname'].'<br />'."\n");
			$while_reps++;
			$no_reps = FALSE;
		}
		if ($no_reps)
		{
			echo('						None');
		}
		echo('					</td>'."\n");
		echo('				</tr>'."\n");
		$alternate == 1 ? $alternate = 2 : $alternate = 1;
	}
?>
			</tbody>
		</table>
	</div>
</div>

<div class="BlueBox">
	<h2>Pending</h2>
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


<pre>
<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
</pre>
