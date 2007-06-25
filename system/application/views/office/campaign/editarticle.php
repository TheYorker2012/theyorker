<div class="RightToolbar">
	<?php
		echo '<h4>Revisions (Latest First)</h4>
		<div class="Entry">';
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
					echo '<a href="/office/campaign/editarticle/'.$parameters['campaign_id'].'/'.$revision['id'].'">"'.$revision['title'].'"</a>';
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

<?php
	echo '<div class="blue_box">';
	echo '<h2>request info</h2>';
	echo '<b>Title: </b>'.$article['header']['requesttitle'].'<br />
		<b>Description: </b>'.$article['header']['requestdescription'].'<br />';
	if ($user['officetype'] != 'Low')
	{
		echo '<a href="/office/howdoi/editrequest/'.$parameters['article_id'].'">[modify and assign]</a>';
	}
	echo '</div>';
	echo '<div class="blue_box">
	<h2>edit article</h2>
	<form class="form" action="/office/howdoi/questionmodify" method="post" >
		<fieldset>
			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
			<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
			<label for="a_question">Question:</label>
			<input type="text" name="a_question" value="';
			if ($article['displayrevision'] != FALSE)
				echo $article['displayrevision']['heading'];
			echo '" /><br />
			<label for="a_answer">Answer:</label>';
			if ($article['displayrevision'] != FALSE)
				echo '<textarea name="a_answer" rows="10" cols="56" />'.$article['displayrevision']['wikitext'].'</textarea><br />';
			else
				echo '<textarea name="a_answer" rows="10" cols="56" /></textarea><br />';
			echo '<input type="submit" value="Save" class="button" name="r_submit_save" />
		</fieldset>
	</form>
</div>';
?>

<?php
echo('<pre><div class=BlueBox>');
print_r($data);
echo('</div></pre>');
?>