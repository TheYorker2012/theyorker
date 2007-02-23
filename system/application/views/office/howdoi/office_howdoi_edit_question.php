<div class="RightToolbar">
	<?php
	if ($article['hasarticlerequest'] == 'requested')
	{
		echo '<h4>Areas for Attention</h4>
			You have been requested to answer this question.
			<form class="form" action="/office/howdoi/writermodify" method="post" >
				<fieldset>
					<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
					<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
					<input type="hidden" name="r_userid" id="r_userid" value="'.$user['id'].'" />
					<input type="submit" value="Accept" class="button" name="r_submit_accept" />
					<input type="submit" value="Decline" class="button" name="r_submit_decline" />
				</fieldset>
			</form>';
	}
	if ($article['hasarticlerequest'] == 'accepted')
	{
		echo '<h4>Areas for Attention</h4>
			If you no longer wish to answer this question.
			<form class="form" action="/office/howdoi/writermodify" method="post" >
				<fieldset>
					<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
					<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
					<input type="hidden" name="r_userid" id="r_userid" value="'.$user['id'].'" />
					<input type="submit" value="Decline" class="button" name="r_submit_decline" />
				</fieldset>
			</form>';
	}
	if ($article['header']['status'] != 'pulled')
	{
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
					echo '<br />by '.$revision['username'].'<br />on '.$dateformatted;
				}
			}
			else
				echo 'No Revisions ... Yet.';
		echo '</div>';
	}
	?>
</div>

<?php
if ($article['header']['status'] == 'suggestion')
{
	echo '<div class="blue_box">';
	echo '<h2>suggestion info</h2>';
	echo '<b>Title: </b>'.$article['header']['requesttitle'].'<br />
			<b>Description: </b>'.$article['header']['requestdescription'].'<br />
			</div>';
}
?>

<?php
if ($article['header']['status'] == 'request')
{
	echo '<div class="blue_box">';
	echo '<h2>request info</h2>';
	echo '<b>Title: </b>'.$article['header']['requesttitle'].'<br />
		<b>Description: </b>'.$article['header']['requestdescription'].'<br />';
	if ($user['officetype'] != 'Low')
	{
		echo '<a href="/office/howdoi/editrequest/'.$parameters['article_id'].'">[modify and assign]</a>';
	}
	echo '</div>';
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
if ($article['header']['status'] == 'pulled')
{
	echo '<div class="grey_box">
	<h2>pulled question</h2>
		<b>Question:</b> '.$article['displayrevision']['heading'].'<br />
		<b>Answer:</b> '.$article['displayrevision']['wikitext'].'<br />
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
					<input type="submit" value="Delete" class="button" name="r_submit_reject" />
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
			Or delete the request
				<fieldset>
					<input type="submit" value="Delete" class="button" name="r_submit_rejectrequest" />
				</fieldset>
			</form>
		</div>';
	}
	else if ($article['header']['status'] == 'published')
	{
		echo '<div class="blue_box">
			<h2>options</h2>';
			foreach ($article['revisions'] as $revision)
			{
				if ($revision['id'] == $article['header']['live_content'])
					$publishdate = $revision['updated'];
			}
			$revisiondate = $article['displayrevision']['updated'];
			if ($revisiondate < $publishdate)
			{
				echo '<div class="information_box"><span class="orange">Warning!</span><br />this is an old revision, it may be missing some current content.</div><br />';
			}
			echo '<form class="form" action="/office/howdoi/questionmodify" method="post" >
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
				<input type="hidden" name="r_revisionid" id="r_revisionid" value="'.$parameters['revision_id'].'" />';
			if ($article['header']['live_content'] != $parameters['revision_id'])
			{
			echo 'To publish the question now, click publish now
				<fieldset>
					<input type="submit" value="Publish Now" class="button" name="r_submit_publishnow" />
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
					<input type="submit" value="Pull Question" class="button" name="r_submit_pull" />
				</fieldset>
			</form>
		</div>';
	}else if ($article['header']['status'] == 'pulled')
	{
		echo '<div class="blue_box">
			<h2>options</h2>
			<form class="form" action="/office/howdoi/questionmodify" method="post" >
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
				<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >
				<input type="hidden" name="r_revisionid" value="'.$article['displayrevision']['id'].'" >
   Convert this pulled question back to a request.
				<fieldset>
					<input type="submit" value="Convert" class="button" name="r_submit_makerequest" />
				</fieldset>
			Or publish this question now.
				<fieldset>
					<input type="submit" value="Publish Now" class="button" name="r_submit_publishnow" />
				</fieldset>
   Or delete the question.
				<fieldset>
					<input type="submit" value="Delete" class="button" name="r_submit_rejectpulled" />
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


