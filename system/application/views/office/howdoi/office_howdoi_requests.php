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
	<h2>requests</h2>
<?php
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
				echo '<br /><span class="orange">'.$request['title'].'</span><br />
					<span class="grey">(asked by '.$request['suggestionusername'].', accepted by '.$request['editorname'].')</span>	<br />
					'.$request['description'].'<br />
					<a href="/office/howdoi/editquestion/'.$request['id'].'">[edit]</a>
					<br />';
			}
		}
	}
?>
</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>