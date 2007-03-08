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
	<form class="form" action="/office/charity/domodify" method="post" >
		<fieldset>
			<?php echo '<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'; ?>
			<?php echo '<input type="hidden" name="r_charityid" value="'.$charity['id'].'" >'; ?>
			<label for="a_heading">Heading:</label>
			<?php echo '<input type="text" name="a_heading" value="'.$article['displayrevision']['heading'].'" /><br />'; ?>
			<label for="a_content">Description:</label>
			<?php echo '<textarea name="a_content" rows="15" cols="30" />'.$article['displayrevision']['wikitext'].'</textarea><br />'; ?>
			<input type="submit" value="Save" class="button" name="r_submit_articlesave" />
		</fieldset>
	</form>
</div>

<?php
if (($article['displayrevision']['id'] != $article['header']['live_content']) and ($user['officetype'] != 'Low'))
{
	echo '
	<div class="blue_box">
		<h2>options</h2>
		<form class="form" action="/office/charity/domodify" method="post" >
			<fieldset>
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<input type="hidden" name="r_charityid" value="'.$charity['id'].'" >
				<input type="hidden" name="r_revisionid" value="'.$article['displayrevision']['id'].'" >
				<input type="submit" value="Publish" class="button" name="r_submit_articlepublish" />
			</fieldset>
		</form>
	</div>
	';
}
?>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
