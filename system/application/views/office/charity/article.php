<div class="RightToolbar">
	<h4>Quick Links</h4>
	<div class="Entry">
		<a href="/office/charity/">Back To Charity List</a>
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
					echo('		<hr />'."\n");
				$dateformatted = date('F jS Y', $revision['updated']).' at '.date('g.i A', $revision['updated']);
				echo('		<a href="/office/charity/editarticle/'.$parameters['charity_id'].'/'.$revision['id'].'">"'.$revision['title'].'"</a>'."\n");
				if ($revision['id'] == $article['header']['live_content'])
				{
					echo('		<br /><span class="orange">(Published');
					if ($revision['id'] == $article['displayrevision']['id'])
						echo(', Displayed');
					echo(')</span>'."\n");
				}
				elseif ($revision['id'] == $article['displayrevision']['id'])
					echo('		<br /><span class="orange">(Displayed)</span>'."\n");
				echo('		<br />by '.$revision['username'].'<br />on '.$dateformatted."\n");
			}
		}
		else
			echo('		No Revisions ... Yet.'."\n");
	echo('	</div>'."\n");
?>
</div>

<div class="blue_box">
	<h2>request info</h2>
	<b>Title: </b><?php echo $article['header']['requesttitle']; ?><br />
	<b>Description: </b><?php echo $article['header']['requestdescription']; ?><br />
<?php
	if ($user['officetype'] != 'Low')
	{
		echo('	<a href="/office/charity/editrequest/'.$parameters['charity_id'].'">[modify]</a>'."\n");
	}
?>
</div>

<div class="blue_box">
	<h2>edit charity</h2>
	<form class="form" action="/office/charity/domodify" method="post" >
		<fieldset>
			<?php echo('<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n"); ?>
			<?php echo('<input type="hidden" name="r_charityid" value="'.$parameters['charity_id'].'" />'."\n"); ?>
			<label for="a_heading">Heading:</label>
			<?php echo('<input type="text" name="a_heading" id="a_heading" size="60" value="'.$article['displayrevision']['heading'].'" /><br />'."\n"); ?>
			<label for="a_content">Description:</label>
			<?php echo('<textarea name="a_content" id="a_content" rows="10" cols="56">'.$article['displayrevision']['wikitext'].'</textarea><br />'."\n"); ?>
			<input type="submit" value="Save" class="button" name="r_submit_article_save" />
		</fieldset>
	</form>
</div>

<?php
	//main - options
	if ($user['officetype'] != 'Low')
	{
		if ($parameters['revision_id'] != NULL) //if a revision exists
		{
			echo('<div class="blue_box">'."\n");
			echo('	<h2>options</h2>'."\n");
			echo('	<form class="form" action="/office/charity/domodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_revisionid" id="r_revisionid" value="'.$parameters['revision_id'].'" />'."\n");
			echo('			<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" >'."\n");
			echo('		</fieldset>'."\n");
			if ($article['displayrevision']['id'] != $article['header']['live_content'])
			{
				echo('		Make this revision the published revision for this campaign.'."\n");
				echo('		<fieldset>'."\n");
				echo('			<input type="submit" value="Publish" class="button" name="r_submit_article_publish" />'."\n");
				echo('		</fieldset>'."\n");
			}
			else
			{
				echo('		Set this revision to not be the default for this campaign.'."\n");
				echo('		<fieldset>'."\n");
				echo('			<input type="submit" value="Unpublish" class="button" name="r_submit_article_unpublish" />'."\n");
				echo('		</fieldset>'."\n");
			}
			echo('	</form>'."\n");
			echo('</div>'."\n");
		}
	}
?>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
