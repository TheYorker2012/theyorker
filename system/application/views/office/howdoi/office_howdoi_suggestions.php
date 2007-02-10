<div class="RightToolbar">
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Questions</a> that are waiting to be published.
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b><?php echo $counts['suggestions']; ?></b> <a href='#'>Suggestions</a> that require attention.
		</div>
	</div>
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
					<a href="#">[edit]</a> <a href="#">[remove]</a>
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
			<select name="a_category">
			<option value="'.$categoryparentid.'">Unassigned</option>';
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

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>