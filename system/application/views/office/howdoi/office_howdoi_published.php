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
	$hr_first = FALSE; //no hr is drawn at the top but between categories
	echo '<div class="blue_box">';
  	echo '<h2>All Questions</h2>';
	if (count($categories) > 0)
	{
		foreach ($categories as $category)
		{
			if ($hr_first == FALSE)
				$hr_first = TRUE;
			else
				echo '<hr />';
			echo '<h5>'.$category['name'].'</h5>';
			$br_first = FALSE; //no br is drawn after the category name
			if (count($category['unpublished']) > 0)
			{
				if ($br_first == FALSE)
					$br_first = TRUE;
				else
					echo '<br />';
				echo '<b>To be published</b>';
				foreach ($category['unpublished'] as $unpublished)
				{
					$dateformatted = date('F jS Y', $unpublished['publish']).' at '.date('g.i A', $unpublished['publish']);
					echo '<br /><span class="orange">'.$unpublished['heading'].'</span>
						<span class="grey">(published by '.$unpublished['editorname'].')</span>
						<br />being published on: '.$dateformatted.'
						<br /><a href="/office/howdoi/editquestion/'.$unpublished['id'].'">[edit]</a>';
				}
			}
			if (count($category['published']) > 0)
			{
				if ($br_first == FALSE)
					$br_first = TRUE;
				else
					echo '<br />';
				echo '<b>Published</b>';
				foreach ($category['published'] as $published)
				{
					$dateformatted = date('F jS Y', $published['publish']).' at '.date('g.i A', $published['publish']);
					echo '<br /><span class="orange">'.$published['heading'].'</span>
						<span class="grey">(published by '.$published['editorname'].')</span>
						<br />published on: '.$dateformatted.'
						<br /><a href="/office/howdoi/editquestion/'.$published['id'].'">[edit]</a>';
				}
			}
			if (count($category['pulled']) > 0)
			{
				if ($br_first == FALSE)
					$br_first = TRUE;
				else
					echo '<br />';
				echo '<b>Pulled</b>';
				foreach ($category['pulled'] as $pulled)
				{
					$dateformatted = date('F jS Y', $pulled['publish']).' at '.date('g.i A', $pulled['publish']);
					echo '<br /><span class="orange">'.$pulled['heading'].'</span>
						<span class="grey">(pulled by '.$pulled['editorname'].')</span>
						<br />pulled on: '.$dateformatted.'
						<br /><a href="/office/howdoi/editquestion/'.$pulled['id'].'">[edit]</a>';
				}
			}
		}
	}
	echo '</div>';
?>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>
