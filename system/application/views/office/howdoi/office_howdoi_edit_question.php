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

<?php
if (($article['header']['status'] == 'suggestion') or ($article['header']['status'] == 'request'))
{
	echo '<div class="blue_box">';
	if ($article['header']['status'] == 'suggestion')
		echo '<h2>suggestion info</h2>';
	else if ($article['header']['status'] == 'request')
		echo '<h2>request info</h2>';
	if ($user['officetype'] == 'Low')
	{
		echo '<b>Title: </b>'.$article['header']['requesttitle'].'<br />
			<b>Description: </b>'.$article['header']['requestdescription'].'<br />
			</div>';
	}
	else
	{
		echo '<form class="form" action="/office/howdoi/questionmodify" method="post" >
				<fieldset>
					<input type="hidden" name="r_status" value="'.$article['header']['status'].'" >
					<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
					<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
					<label for"a_title">Title:</label>
					<input type="text" name="a_title" value="'.$article['header']['requesttitle'].'" />
					<label for="a_category">Category:</label>
					<select name="a_category">';
					foreach ($categories as $category_id => $category)
					{
						echo '<option value="'.$category_id.'"';
						if ($category_id == $article['header']['content_type'])
							echo ' selected';
						echo '>'.$category['name'].'</option>';
						//echo '<option selected>Opening Times</option>';
					}
					echo '</select><br />
					<label for"a_description">Description:</label>
					<textarea name="a_description" rows="5" cols="30" />'.$article['header']['requestdescription'].'</textarea>
					<input type="submit" value="Modify" class="button" name="r_submit_modify" />
				</fieldset>
			</form>
		</div>';
	}
}
?>

<?php
if (($article['header']['status'] == 'request') or ($article['header']['status'] == 'published'))
{
	echo '<div class="grey_box">
	<h2>edit question</h2>
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
				echo '<textarea name="a_answer" rows="5" cols="30" />'.$article['displayrevision']['wikitext'].'</textarea><br />';
			else
				echo '<textarea name="a_answer" rows="5" cols="30" /></textarea><br />';
			echo '<input type="submit" value="Save" class="button" name="r_submit_save" />
		</fieldset>
	</form>
</div>';
}
?>

<?php
if ($user['officetype'] != 'Low')
{
	if ($article['header']['status'] == 'suggestion')
	{
		echo '<div class="blue_box">
			<h2>options</h2>
			<form class="form" action="/office/howdoi/questionmodify" method="post" >
			Please reject or accept the suggestion (accepting converts this to a request).
				<fieldset>
					<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
					<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
					<label for"a_title">Title:</label>
					<input type="text" name="a_title" />
					<label for="a_category">Category:</label>
					<select name="a_category">';
					foreach ($categories as $category_id => $category)
					{
						echo '<option value="'.$category_id.'"';
						if ($category_id == $article['header']['content_type'])
							echo ' selected';
						echo '>'.$category['name'].'</option>';
						//echo '<option selected>Opening Times</option>';
					}
					echo '</select><br />
					<label for"a_description">Description:</label>
					<textarea name="a_description" rows="5" cols="30" /></textarea>
					<label for"a_deadline">Deadline (yy-mm-dd h:m):</label>
					<input type="text" name="a_deadline" value="'.date('y-m-d H:i').'" />
					<input type="submit" value="Accept" class="button" name="r_submit_accept" />
					<input type="submit" value="Reject" class="button" name="r_submit_reject" />
				</fieldset>
			</form>
		</div>';
	}
	else if ($article['header']['status'] == 'request')
	{
		echo '<div class="blue_box">
			<h2>options</h2>
			<form class="form" action="/office/howdoi/questionmodify" method="post" >
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<input type="hidden" name="r_revisionid" id="r_revisionid" value="'.$parameters['revision_id'].'" />
				<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
			To publish the question now, click publish now
				<fieldset>
					<input type="submit" value="Publish Now" class="button" name="r_submit_publishnow" />
				</fieldset>
			Or to publish at a later date...
				<fieldset>
					<label for"a_publishdate">Publish On (yy-mm-dd h:m):</label>
					<input type="text" name="a_publishdate" value="'.date('y-m-d H:i').'" />
					<input type="submit" value="Publish Then" class="button" name="r_submit_publishon" />
				</fieldset>
			Or reject the request
				<fieldset>
					<input type="submit" value="Reject" class="button" name="r_submit_reject" />
				</fieldset>
			</form>
		</div>';
	}
	else if ($article['header']['status'] == 'published')
	{
		echo '<div class="blue_box">
			<h2>options</h2>
			<form class="form" action="/office/howdoi/questionmodify" method="post" >
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
				<input type="hidden" name="r_revisionid" id="r_revisionid" value="'.$parameters['revision_id'].'" />';
			if ($article['header']['live_content'] != $parameters['revision_id'])
			{
			echo 'To publish the question now, click publish now
				<fieldset>
					<input type="submit" value="Publish Now" class="button" name="r_submit_publishnow" />
				</fieldset>
			Or to publish at a later date...
				<fieldset>
					<label for"a_publishdate">Publish On (yy-mm-dd h:m):</label>
					<input type="text" name="a_publishdate" value="'.date('y-m-d H:i').'" />
					<input type="submit" value="Publish Then" class="button" name="r_submit_publishon" />
				</fieldset>';
			}
			echo '<fieldset>
					<label for="a_category">Category:</label>
					<select name="a_category">';
					foreach ($categories as $category_id => $category)
					{
						echo '<option value="'.$category_id.'"';
						if ($category_id == $article['header']['content_type'])
							echo ' selected';
						echo '>'.$category['name'].'</option>';
					}
					echo '</select><br />
					<input type="submit" value="Set Category" class="button" name="r_submit_category" />
				</fieldset>
				<fieldset>
					<label for="r_submit_pull">Pull This Question:</label>
					<input type="submit" value="Pull Article" class="button" name="r_submit_pull" />
				</fieldset>
			</form>
		</div>';
	}
}
?>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>


