<div class="RightToolbar">
	<h4>Quick Links</h4>
	<?php
	echo '<a href="/office/charity/edit/'.$charity['id'].'">Edit</a><br/ >';
	echo '<a href="/office/charity/progressreports/'.$charity['id'].'">Progress Reports</a><br/ >';
	echo '<br/ >';
	echo '<h4>Revisions (Latest First)</h4>';
	echo '<div class="Entry">';
		if (count($article['revisions']) > 0)
		{
			$first_hr = FALSE;
			foreach ($article['revisions'] as $revision)
			{
				if ($first_hr == FALSE)
					$first_hr = TRUE;
				else
					echo '<hr>';
				$dateformatted = date('F jS Y', $revision['updated']).' at '.date('g.i A', $revision['updated']);
				echo '<a href="/office/charity/article/'.$charity['id'].'/'.$revision['id'].'">"'.$revision['title'].'"</a>';
				if ($revision['id'] == $article['header']['live_content'])
				{
					echo '<br /><span class="orange">(Published';
					if ($revision['id'] == $article['displayrevision']['id'])
						echo ', Displayed';
					echo ')</span>';
				}
				elseif ($revision['id'] == $article['displayrevision']['id'])
					echo '<br /><span class="orange">(Displayed)</span>';
				echo '<br />by '.$revision['username'].'<br />on '.$dateformatted;
			}
		}
		else
			echo 'No Revisions ... Yet.';
	echo '</div>';
	?>
</div>

<div class="grey_box">
	<h2>edit charity</h2>
	<form class="form" action="/charity/howdoi/#" method="post" >
		<fieldset>
			<label for="a_question">Heading:</label>
			<?php echo '<input type="text" name="a_question" value="'.$article['displayrevision']['heading'].'" /><br />'; ?>
			<label for="a_answer">Description:</label>
			<?php echo '<textarea name="a_answer" rows="15" cols="30" />'.$article['displayrevision']['wikitext'].'</textarea><br />'; ?>
			<input type="submit" value="Save" class="button" name="r_submit_save" />
		</fieldset>
	</form>
</div>

<!--
<div class="blue_box">
	<h2>latest progress report</h2>
	<b>Date:</b> Saturday, 3rd February 2007<br />
	<b>Details:</b> This is a test for a random charity progress report.<br />
	<a href="/office/charity/#">[Modify]</a>
</div>

<div class="grey_box">
	<h2>old progress reports</h2>
	<b>02/02/2007</b>: Jeff ate a salad at some... <a href="/office/charity/#">[Modify]</a><br />
	<b>24/01/2007</b>: Our first protest began... <a href="/office/charity/#">[Modify]</a><br />
</div>
-->

<div class="blue_box">
	<h2>options</h2>
	<form class="form" action="/office/charity/#" method="post" >
		<fieldset>
			<input type="submit" value="Publish" class="button" name="r_submit_publish" />
		</fieldset>
		<fieldset>
			<input type="submit" value="Unpublish" class="button" name="r_submit_unpublish" />
		</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
