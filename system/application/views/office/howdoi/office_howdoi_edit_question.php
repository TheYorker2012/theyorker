<div class="RightToolbar">
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>?</b> <a href='#'>Questions</a> that are waiting to be published.
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>?</b> <a href='#'>Suggestions</a> that require attention.
		</div>
	</div>
	<h4>Revisions (Latest First)</h4>
	<div class="Entry">
		<?php
		if (count($article['revisions']) > 0)
		{
			$first_hr = FALSE;
			foreach ($article['revisions'] as $revision)
			{
				if ($first_hr == FALSE)
					$first_hr = TRUE;
				else
					echo '<hr>';
				$revisiontime = strtotime($revision['updated']);
				$revisiontimeformat = date('F jS Y', $revisiontime).' at '.date('g.i A', $revisiontime);
				echo '<a href="/office/howdoi/editquestion/'.$parameters['article_id'].'/'.$revision['id'].'">"'.$revision['title'].'"</a>';
				if ($revision['id'] == $article['header']['live_content'])
				{
					echo '<br /><span class="orange">(Published';
					if ($revision['id'] == $article['displayrevision']['id'])
						echo ', Displayed';
					echo ')</span>';
				}
				elseif ($revision['id'] == $article['displayrevision']['id'])
					echo '<br /><span class="orange">(Displayed)</span>';
				echo '<br />by '.$revision['username'].'<br />on '.$revisiontimeformat;
			}
		}
		else
			echo 'No Revisions Yet.';
		?>
	</div>
</div>

<div class="grey_box">
	<h2>edit question</h2>
	<form class="form">
		<fieldset>
		<?php
			echo '<label for="a_question">Question:</label>
			<input type="text" name="a_question" value="';
			if ($article['displayrevision'] != FALSE)
				echo $article['displayrevision']['heading'];
			echo '" /><br />
			<label for="a_category">Category:</label>';
		?>
			<select name="a_category">
				<?php
					foreach ($categories as $category_id => $category)
					{
						echo '<option value="'.$category_id.'"';
						if ($category_id == $article['header']['content_type'])
							echo ' selected';
						echo '>'.$category['name'].'</option>';
						//echo '<option selected>Opening Times</option>';
					}
				?>
			</select><br />
			<label for="a_answer">Answer:</label>
			<?php
				if ($article['displayrevision'] != FALSE)
					echo '<textarea name="a_answer" rows="5" cols="30" />'.$article['displayrevision']['wikitext'].'</textarea><br />';
				else
					echo '<textarea name="a_answer" rows="5" cols="30" /></textarea><br />'
			?>
			<input type="submit" value="Save" class="button" name="r_submit_save" />
		</fieldset>
	</form>
</div>
<div class="blue_box">
	<h2>publish options</h2>
	<form class="form">
	To publish the question now, click publish now
		<fieldset>
			<input type="submit" value="Publish Now" class="button" />
		</fieldset>
	Or to publish at a later date...
		<fieldset>
			<label for"a_publishdate">Publish On (dd/mm/yyyy):</label>
			<input type="text" name="a_publishdate" />
			<input type="submit" value="Publish Then" class="button" />
		</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>


