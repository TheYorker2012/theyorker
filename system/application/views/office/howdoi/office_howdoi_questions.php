<div class="RightToolbar">
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b><?php echo ($status_count['requests'] + $status_count['unpublished']); ?></b> <a href='#'>Questions</a> that are waiting to be published.
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b><?php echo $status_count['suggestions']; ?></b> <a href='#'>Suggestions</a> that require attention.
		</div>
	</div>
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
			if (count($category['suggestions']) > 0)
			{
				if ($br_first == FALSE)
					$br_first = TRUE;
				else
					echo '<br />';
				echo '<b>Suggestions</b>';
				foreach ($category['suggestions'] as $suggestion)
				{
					echo '<br /><span class="orange">'.$suggestion['title'].'</span>
						<span class="grey">(asked by '.$suggestion['username'].')</span>
						<a href="/office/howdoi/editquestion/'.$suggestion['id'].'">[edit]</a> <a href="#">[remove]</a>';
				}
			}
			if (count($category['requests']) > 0)
			{
				if ($br_first == FALSE)
					$br_first = TRUE;
				else
					echo '<br />';
				echo '<b>Requests</b>';
				foreach ($category['requests'] as $request)
				{
					$deadline = strtotime($request['deadline']);
					$deadlineformat = date('F jS Y', $deadline).' at '.date('g.i A', $deadline);
					echo '<br /><span class="orange">'.$request['title'].'</span>
						<span class="grey">(approved by '.$request['editorname'].')</span>
						<br />deadline: '.$deadlineformat.'
						<a href="/office/howdoi/editquestion/'.$request['id'].'">[edit]</a> <a href="#">[remove]</a>';
				}
			}
			if (count($category['unpublished']) > 0)
			{
				if ($br_first == FALSE)
					$br_first = TRUE;
				else
					echo '<br />';
				echo '<b>Unpublished</b>';
				foreach ($category['unpublished'] as $unpublished)
				{
					$publish = strtotime($unpublished['publish']);
					$publishformat = date('F jS Y', $publish).' at '.date('g.i A', $publish);
					echo '<br /><span class="orange">'.$unpublished['heading'].'</span>
						<span class="grey">(published by '.$unpublished['editorname'].')</span>
						<br />being published on: '.$publishformat.'
						<a href="/office/howdoi/editquestion/'.$unpublished['id'].'">[edit]</a> <a href="#">[remove]</a>';
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
					$publish = strtotime($published['publish']);
					$publishformat = date('F jS Y', $publish).' at '.date('g.i A', $publish);
					echo '<br /><span class="orange">'.$published['heading'].'</span>
						<span class="grey">(published by '.$published['editorname'].')</span>
						<br />published on: '.$publishformat.'
						<a href="/office/howdoi/editquestion/'.$published['id'].'">[edit]</a> <a href="#">[remove]</a>';
				}
			}
		}
	}
	echo '</div>';
?>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
