<div class="RightToolbar">
	<?php
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
	echo '<div class="blue_box" id="view_suggestions">';
	echo '<h2>suggestions</h2>';
	$first = FALSE;
	foreach ($categories as $category_id => $category)
	{
		if ($first == FALSE)
			$first = TRUE;
		else
			echo '<hr />';
		echo '<h5>'.$category['name'].'</h5>';

		if (count($category['suggestions']) > 0)
		{
			foreach ($category['suggestions'] as $suggestion)
			{
				echo '<br /><span class="orange">'.$suggestion['title'].'</span>
					<span class="grey">(asked by '.$suggestion['username'].')</span><br />
					'.$suggestion['description'].'<br />
					<a href="/office/howdoi/editquestion/'.$suggestion['id'].'">[edit]</a>
					<br />';
			}
		}
	}
	echo '</div>';
?>

<div class="grey_box">

	<h2>make a suggestion</h2>
	<?php
	echo '<form class="form" action="/office/howdoi/suggestionmodify" method="post" >
		<fieldset class="form">
			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
			<label for="a_question">Question: </label>
			<input type="text" name="a_question" />
			<label for="a_description">Description: </label>
			<textarea name="a_description" cols="30" rows="5"></textarea>
			<label for="a_category">Category: </label>
			<select name="a_category" >';
			foreach ($categories as $category_id => $category)
			{
				echo '<option value="'.$category['codename'].'">'.$category['name'].'</option>';
			}
			echo '</select>
			<input type="submit" class="button" value="Ask" name="r_submit_ask" />
		</fieldset>
	</form>';
	?>

</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>