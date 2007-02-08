<div class="RightToolbar">
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Questions</a> that are waiting to be published.
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Suggestions</a> that require attention.
		</div>
	</div>
</div>

<?php
	echo '<div class="blue_box">';
  	echo '<h2>All Questions</h2>';
	if (count($categories) > 0)
	{
		foreach ($categories as $category)
		{
			echo '<h5>'.$category['name'].'</h5>';
			if (count($category['questions']) > 0)
			{
				foreach ($category['questions'] as $question)
				{
					$publish_time = strtotime($question['heading']['publish_date']);
					echo $question['revision'][0]['heading'];
					if ($question['heading']['status'] == 1)
						echo ' <b>(Awaiting Publishing)</b>';
					else if ($question['heading']['status'] == 2)
						echo ' <i>(To be published on '.date('F jS Y', $publish_time).' at '.date('g.i A', $publish_time).')</i>';
					echo '<br />';
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
