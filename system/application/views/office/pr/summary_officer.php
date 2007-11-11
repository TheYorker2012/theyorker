<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="/wizard/organisation/">Wizard</a>
	</div>
	<div class="Entry">
		<a href="/office/pr/summaryall/">All Organisations</a>
	</div>
</div>

<div class="blue_box">
	<h2>officer summary</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>PR Rep</th>
					<th>Rating</th>
				</tr>
			</thead>
			<tbody>
<?php
	$alternate = 1;
	foreach($reps as $rep)
	{
		echo('				<tr class="tr'.$alternate.'">'."\n");
		echo('					<td>'."\n");
		echo('						<a href="/office/pr/summaryrep/'.$rep['user_id'].'">'.$rep['user_firstname'].' '.$rep['user_surname'].'</a>'."\n");
		echo('					</td>'."\n");
		echo('					<td>'."\n");
		echo('						10%'."\n");
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