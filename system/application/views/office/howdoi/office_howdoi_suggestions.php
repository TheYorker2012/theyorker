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
</div>

<div id="MainColumn">
	<?php
	echo('<div class="BlueBox" id="view_suggestions">'."\n");
	echo('	<h2>suggestions</h2>'."\n");
	$first = FALSE;
	foreach ($categories as $category_id => $category)
	{
		if ($first == FALSE)
			$first = TRUE;
		else
			echo('	<hr />'."\n");
		echo('	<h5>'.xml_escape($category['name']).'</h5>'."\n");
		
		if (count($category['suggestions']) > 0)
		{
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
			foreach ($category['suggestions'] as $suggestion)
			{
				$dateformatted = date('d/m/y @ H:i', $suggestion['created']);
				echo('				<tr class="tr'.$alternate.'">'."\n");
				echo('					<td>'."\n");
				echo('						<a href="/office/howdoi/editquestion/'.$suggestion['id'].'">'.xml_escape($suggestion['title']).'</a>'."\n");
				echo('					</td>'."\n");
				echo('					<td>'."\n");
				echo('						'.xml_escape($suggestion['username'])."\n");
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
	}
	echo('</div>');
	?>

	<div class="BlueBox">
		<h2>make a suggestion</h2>
		<?php
		echo('<form class="form" action="/office/howdoi/suggestionmodify" method="post" >
			<fieldset class="form">
				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<label for="a_question">Question: </label>
				<input type="text" name="a_question" />
				<label for="a_description">Description: </label>
				<textarea name="a_description" cols="30" rows="5"></textarea>
				<label for="a_category">Category: </label>
				<select name="a_category" >');
		foreach ($categories as $category_id => $category)
		{
			echo('<option value="'.xml_escape($category['codename']).'">'.xml_escape($category['name']).'</option>');
		}
		echo('</select>
				<input type="submit" class="button" value="Ask" name="r_submit_ask" />
			</fieldset>
		</form>');
		?>
	</div>
</div>
