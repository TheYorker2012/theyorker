<?php
/**
 * @file views/office/howdoi/office_howdoi_requests.php
 * @author Richard Ingle <ri504@york.ac.uk>
 * @todo cut down on the number of large echos
 * @param $status_count
 * @param $categories
 * @param $permissions[$key => bool]
 *  - make_suggestion
 *  - make_request
 */
?>
<div class="RightToolbar">
	<?php
	if (count($user['writer']['requested']) > 0)
	{
		echo('<h4>Write Requests</h4>');
		foreach ($user['writer']['requested'] as $requested)
		{
			echo('<a href="/office/howdoi/editquestion/'.$requested['id'].'">'.xml_escape($requested['title']).'</a><br />');
		}
		echo('<br />');
	}
	if (count($user['writer']['accepted']) > 0)
	{
		echo('<h4>Accepted Requests</h4>');
		foreach ($user['writer']['accepted'] as $accepted)
		{
			echo('<a href="/office/howdoi/editquestion/'.$accepted['id'].'">'.xml_escape($accepted['title']).'</a><br />');
		}
		echo('<br />');
	}
	echo '<h4>Areas for Attention</h4>
	<div class="Entry">';
		if ($status_count['unpublished'] > 0)
		{
			echo('<div class="information_box">');
			if ($status_count['unpublished'] == 1)
				echo('There is <b>'.$status_count['unpublished'].'</b> <a href="/office/howdoi/published/">Question</a> that is waiting to be published.');
			else
				echo('There are <b>'.$status_count['unpublished'].'</b> <a href="/office/howdoi/published/">Questions</a> that are waiting to be published.');
			echo('</div>');
		}
		if ($status_count['requests'] > 0)
		{
			echo('<div class="information_box">');
			if ($status_count['requests'] == 1)
				echo('There is <b>'.$status_count['requests'].'</b> <a href="/office/howdoi/requests/">Request</a> that requires an answer.');
			else
				echo('There are <b>'.$status_count['requests'].'</b> <a href="/office/howdoi/requests/">Requests</a> that require answers.');
			echo('</div>');
		}
		if ($status_count['suggestions'] > 0)
		{
			echo('<div class="information_box">');
			if ($status_count['suggestions'] == 1)
				echo('There is <b>'.$status_count['suggestions'].'</b> <a href="/office/howdoi/suggestions/">Suggestion</a> that requires attention.');
			else
				echo('There are <b>'.$status_count['suggestions'].'</b> <a href="/office/howdoi/suggestions/">Suggestions</a> that require attention.');
			echo('</div>');
		}

	echo('</div>');
	?>
</div>

<?php
	echo('<div class="blue_box" id="view_suggestions">'."\n");
	echo('	<h2>requests</h2>'."\n");
	$first = FALSE;
	foreach ($categories as $category_id => $category)
	{
		if ($first == FALSE)
			$first = TRUE;
		else
			echo('	<hr />'."\n");
		echo('	<h5>'.xml_escape($category['name']).'</h5>'."\n");
		if (count($category['requests']) > 0)
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
			foreach ($category['requests'] as $request)
			{
				$dateformatted = date('d/m/y @ H:i', $request['created']);
				echo('				<tr class="tr'.$alternate.'">'."\n");
				echo('					<td>'."\n");
				echo('						<a href="/office/howdoi/editquestion/'.$request['id'].'">'.xml_escape($request['title']).'</a>'."\n");
				echo('					</td>'."\n");
				echo('					<td>'."\n");
				foreach ($request['reporters'] as $key => $reporter)
				{
					echo('						'.xml_escape($reporter['name'])."\n");
					echo('						<br />'."\n");
				}
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

<?php
if ($permissions['make_request'])
{
?>
<div class="blue_box">
	<h2>make a request</h2>
	<form class="form" action="/office/howdoi/suggestionmodify" method="post" >
		<fieldset>
			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="<?php echo(xml_escape($_SERVER['REQUEST_URI'])); ?>" />
			<label for="a_question">Question: </label>
			<input type="text" name="a_question" />
			<label for="a_description">Description: </label>
			<textarea name="a_description" cols="30" rows="5"></textarea>
			<label for="a_category">Category: </label>
			<select name="a_category">
			<?php
			foreach ($categories as $category_id => $category)
			{
				?>
				<option value="<?php echo(xml_escape($category['codename'])); ?>"><?php echo(xml_escape($category['name'])); ?></option>
				<?php
			}
			?>
			</select>
			<label for="a_deadline">Deadline (yy-mm-dd h:m):</label>
			<input type="text" name="a_deadline" value="<?php echo(date('y-m-d H:i')); ?>" />
			<input type="submit" class="button" value="Ask" name="r_submit_request" />
		</fieldset>
	</form>
</div>
<?php
}
?>
