<div id="RightColumn">
	<h2 class="first">Links</h2>
	<div class="Entry">
		<a href="/charity">Back To Charity</a>
	</div>
</div>

<div id="MainColumn">

	<?php
	if (isset($sections['progress_reports']['entries']))
	{
		echo '<div class="BlueBox">';
		echo '<h2>'.$sections['progress_reports']['title'].'</h2>';
		foreach ($sections['progress_reports']['entries'] as $pr_entry)
		{
			echo '<h5><span class="orange">'.$pr_entry['date'].'</span></h5>';
			echo $pr_entry['text'].'<br /><br />';
		}
		echo '</div>';
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
