<div id="RightColumn">
	<h2 class="first">
		Quick Links
	</h2>
	<div class="Entry">
		<a href="/office/">Office Home</a>
	</div>
</div>
<div class="blue_box">
	<h2>poll listing</h2>
	<div class="ArticleBox">
		<table>
			<thead>
				<tr>
					<th style="width:55%;">
						Question
					</th>
					<th style="width:15%;">
						Running?
					</th>
					<th style="width:15%;">
						Displayed?
					</th>
				</tr>
			</thead>
			<tbody>
<?php
	$alternate = 1;
	foreach ($poll_list as $poll)
	{
		echo('				<tr class="tr'.$alternate.'">'."\n");
		echo('					<td>'."\n");
		echo('						<a href="/office/polls/edit/'.$poll['id'].'">'.$poll['question'].'</a>'."\n");
		echo('					</td>'."\n");
		echo('					<td style="text-align:right;">'."\n");
		$poll['is_running'] ? $result = "Yes" : $result = "No" ;
		echo('						'.$result."\n");
		echo('					</td>'."\n");
		echo('					<td style="text-align:right;">'."\n");
		$poll['is_displayed'] ? $result = "Yes" : $result = "No" ;
		echo('						'.$result."\n");
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