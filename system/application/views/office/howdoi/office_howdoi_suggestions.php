<div class="RightToolbar">
	<?php
	echo '<h4>Areas for Attention</h4>
	<div class="Entry">';
		if ($status_count['unpublished'] > 0)
			echo '<div class="information_box">
			There are <b>'.$status_count['unpublished'].'</b> <a href="/office/howdoi/published/">Questions</a> that are waiting to be published.
		</div>';
		if ($status_count['requests'] > 0)
		echo '<div class="information_box">
			There are <b>'.$status_count['requests'].'</b> <a href="/office/howdoi/requests/">Requests</a> that require answers.
		</div>';
		if ($status_count['suggestions'] > 0)
		echo '<div class="information_box">
			There are <b>'.$status_count['suggestions'].'</b> <a href="/office/howdoi/suggestions/">Suggestions</a> that require attention.
		</div>';

	echo '</div>';
	?>
</div>
<div class="blue_box">
	<h2>suggestions</h2>
<?php
	$first = FALSE;
	foreach ($categories as $category_id => $category)
	{
		if (count($category['suggestions']) > 0)
		{
			if ($first == FALSE)
				$first = TRUE;
			else
				echo '<hr />';
			echo '<h5>'.$category['name'].'</h5>';
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
?>
</div>

<div class="grey_box">

	<h2>make a suggestion</h2>
	<?php
	echo '<form class="form" action="/office/howdoi/suggestionmodify" method="post" >
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
				echo '<option value="'.$category_id.'">'.$category['name'].'</option>';
			}
			echo '</select>
			<input type="submit" class="button" value="Ask" name="r_submit_ask" />
		</fieldset>
	</form>';
	?>

</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>