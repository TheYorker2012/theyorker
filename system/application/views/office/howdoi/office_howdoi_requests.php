<div class="RightToolbar">
	<?php
	if (count($user['writer']['requested']) > 0)
	{
		echo '<h4>Write Requests</h4>';
		foreach ($user['writer']['requested'] as $requested)
		{
			echo '<a href="/office/howdoi/editquestion/'.$requested['id'].'">'.$requested['title'].'</a><br />';
		}
		echo '<br />';
	}
	if (count($user['writer']['accepted']) > 0)
	{
		echo '<h4>Accepted Requests</h4>';
		foreach ($user['writer']['accepted'] as $accepted)
		{
			echo '<a href="/office/howdoi/editquestion/'.$accepted['id'].'">'.$accepted['title'].'</a><br />';
		}
		echo '<br />';
	}
	echo '<h4>Areas for Attention</h4>
	<div class="Entry">';
		if ($status_count['unpublished'] > 0)
		{
			echo '<div class="information_box">';
			if ($status_count['unpublished'] == 1)
				echo 'There is <b>'.$status_count['unpublished'].'</b> <a href="/office/howdoi/published/">Question</a> that is waiting to be published.';
			else
				echo 'There are <b>'.$status_count['unpublished'].'</b> <a href="/office/howdoi/published/">Questions</a> that are waiting to be published.';
			echo '</div>';
		}
		if ($status_count['requests'] > 0)
		{
			echo '<div class="information_box">';
			if ($status_count['requests'] == 1)
				echo 'There is <b>'.$status_count['requests'].'</b> <a href="/office/howdoi/requests/">Request</a> that requires an answer.';
			else
				echo 'There are <b>'.$status_count['requests'].'</b> <a href="/office/howdoi/requests/">Requests</a> that require answers.';
			echo '</div>';
		}
		if ($status_count['suggestions'] > 0)
		{
			echo '<div class="information_box">';
			if ($status_count['suggestions'] == 1)
				echo 'There is <b>'.$status_count['suggestions'].'</b> <a href="/office/howdoi/suggestions/">Suggestion</a> that requires attention.';
			else
				echo 'There are <b>'.$status_count['suggestions'].'</b> <a href="/office/howdoi/suggestions/">Suggestions</a> that require attention.';
			echo '</div>';
		}

	echo '</div>';
	?>
</div>

<?php
	if ($status_count['requests'] > 0)
	{
		echo '<div class="blue_box">
			<h2>requests</h2>';
		$first = FALSE;
		foreach ($categories as $category_id => $category)
		{
			if (count($category['requests']) > 0)
			{
				if ($first == FALSE)
					$first = TRUE;
				else
					echo '<hr />';
				echo '<h5>'.$category['name'].'</h5>';
				foreach ($category['requests'] as $request)
				{
					$dateformatted = date('F jS Y', $request['deadline']).' at '.date('g.i A', $request['deadline']);
					echo '<br /><span class="orange">'.$request['title'].'</span><br />
						deadline on: '.$dateformatted.'<br />
						<span class="grey">(asked by '.$request['suggestionusername'].', approved by '.$request['editorname'].')</span>	<br />
						'.$request['description'].'<br />
						<a href="/office/howdoi/editquestion/'.$request['id'].'">[edit]</a>';
					if ($user['officetype'] != 'Low')
						echo ' or <a href="/office/howdoi/editrequest/'.$request['id'].'">[modify and assign]</a>';
					echo '<br />';
				}
			}
		}
		echo '</div>';
	}
?>

<?php
if ($user['officetype'] != 'Low')
{
	echo '<div class="grey_box">
		<h2>make a request</h2>
		<form class="form" action="/office/howdoi/suggestionmodify" method="post" >
			<fieldset>
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<label for="a_question">Question: </label>
				<input type="text" name="a_question" />
				<label for="a_description">Description: </label>
				<textarea name="a_description" cols="30" rows="5"></textarea>
				<label for="a_category">Category: </label>
				<select name="a_category">';
				foreach ($categories as $category_id => $category)
				{
					echo '<option value="'.$category['codename'].'">'.$category['name'].'</option>';
				}
				echo '</select>
				<label for="a_deadline">Deadline (yy-mm-dd h:m):</label>
				<input type="text" name="a_deadline" value="'.date('y-m-d H:i').'" />
				<input type="submit" class="button" value="Ask" name="r_submit_request" />
			</fieldset>
		</form>
	</div>';
}
?>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>
