<?php
	function PrintSectionTableContents($header_name, $data)
	{
		echo('	<b>'.xml_escape($header_name).'</b>');
		echo('	<div class="ArticleBox">'."\n");
		echo('		<table>'."\n");
		echo('			<thead>'."\n");
		echo('				<tr>'."\n");
		echo('					<th style="width:50%;">'."\n");
		echo('						Name'."\n");
		echo('					</th>'."\n");
		echo('					<th style="width:20%;">'."\n");
		echo('						By'."\n");
		echo('					</th>'."\n");
		echo('					<th style="width:30%;text-align:right;">'."\n");
		echo('						Date'."\n");
		echo('					</th>'."\n");
		echo('				</tr>'."\n");
		echo('			</thead>'."\n");
		echo('			<tbody>'."\n");
		$alternate = 1;
		foreach ($data as $section)
		{
			$dateformatted = date('d/m/y @ H:i', $section['publish']);
			echo('				<tr class="tr'.$alternate.'">'."\n");
			echo('					<td>'."\n");
			echo('						<a href="/office/howdoi/editquestion/'.$section['id'].'">'.xml_escape($section['heading']).'</a>'."\n");
			echo('					</td>'."\n");
			echo('					<td>'."\n");
			echo('						'.xml_escape($section['user_firstname'].' '.$section['user_surname'])."\n");
			echo('					</td>'."\n");
			echo('					<td style="text-align:right;">'."\n");
			echo('						'.$dateformatted."\n");
			echo('					</td>'."\n");
			echo('				</tr>'."\n");
			$alternate == 1 ? $alternate = 2 : $alternate = 1;
		}
		echo('			</tbody>'."\n");
		echo('		</table>'."\n");
		echo('	</div>'."\n");
	}
?>

<div id="RightColumn">
	<h2 class="first">Areas for Attention</h2>
	<div class="Entry">
		<ul>
		<?php
		if ($status_count['unpublished'] > 0)
		{
			if ($status_count['unpublished'] == 1)
			{
				echo('<li><b>'.$status_count['unpublished'].'</b> <a href="/office/howdoi/published/">Question</a> is waiting to be published.</li>');
			}else{
				echo('<li><b>'.$status_count['unpublished'].'</b> <a href="/office/howdoi/published/">Questions</a> are waiting to be published.</li>');
			}
		}
		if ($status_count['requests'] > 0)
		{
			if ($status_count['requests'] == 1)
			{
				echo('<li><b>'.$status_count['requests'].'</b> <a href="/office/howdoi/requests/">Request</a> requires an answer.</li>');
			}else{
				echo('<li><b>'.$status_count['requests'].'</b> <a href="/office/howdoi/requests/">Requests</a> require answers.</li>');
			}
		}
		if ($status_count['suggestions'] > 0)
		{
			if ($status_count['suggestions'] == 1)
			{
				echo('<li>There is <b>'.$status_count['suggestions'].'</b> <a href="/office/howdoi/suggestions/">Suggestion</a>.</li>');
			}else{
				echo('<li>There are <b>'.$status_count['suggestions'].'</b> <a href="/office/howdoi/suggestions/">Suggestions</a>.</li>');
			}
		}
		?>
		</ul>
	</div>

	<?php
	if (count($user['writer']['requested']) > 0)
	{
		echo('<h2>Write Requests</h2>');
		echo('<div class="Entry">');
		echo('<ul>');
		foreach ($user['writer']['requested'] as $requested)
		{
			echo('<li><a href="/office/howdoi/editquestion/'.$requested['id'].'">'.xml_escape($requested['title']).'</a></li>');
		}
		echo('</ul>');
		echo('</div>');
	}
	if (count($user['writer']['accepted']) > 0)
	{
		echo('<h2>Accepted Requests</h2>');
		echo('<div class="Entry">');
		echo('<ul>');
		foreach ($user['writer']['accepted'] as $accepted)
		{
			echo('<li><a href="/office/howdoi/editquestion/'.$accepted['id'].'">'.xml_escape($accepted['title']).'</a></li>');
		}
		echo('</ul>');
		echo('</div>');
	}
	?>
</div>
<div id="MainColumn">
	<?php
	$hr_first = FALSE; //no hr is drawn at the top but between categories
	echo('<div class="BlueBox">');
  	echo('<h2>All Questions</h2>');
	if (count($categories) > 0)
	{
		foreach ($categories as $category)
		{
			if ($hr_first == FALSE)
				$hr_first = TRUE;
			else
				echo('<hr />');
			echo('<h5>'.xml_escape($category['name']).'</h5>');
			$br_first = FALSE; //no br is drawn after the category name
			if (count($category['unpublished']) > 0)
			{
				PrintSectionTableContents('To be published', $category['unpublished']);
			}
			if (count($category['published']) > 0)
			{
				PrintSectionTableContents('Published', $category['published']);
			}
			if (count($category['pulled']) > 0)
			{
				PrintSectionTableContents('Pulled', $category['pulled']);
			}
		}
	}
	echo('</div>');
	?>
</div>
