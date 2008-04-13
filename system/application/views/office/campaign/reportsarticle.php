<div id="RightColumn">
	<h2 class="first">Revisions (Latest First)</h2>
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
					echo('		<hr>'."\n");
				$dateformatted = date('F jS Y', $revision['updated']).' at '.date('g.i A', $revision['updated']);
				echo('		<a href="/office/campaign/editprogressreport/'.$parameters['campaign_id'].'/'.$parameters['prarticle_id'].'/'.$revision['id'].'">'.$dateformatted.'</a>'."\n");
				if ($revision['id'] == $article['header']['live_content'])
				{
					echo('		<br /><span class="orange">(Published');
					if ($revision['id'] == $article['displayrevision']['id'])
						echo(', Displayed');
					echo(')</span>'."\n");
				}
				elseif ($revision['id'] == $article['displayrevision']['id'])
					echo('		<br /><span class="orange">(Displayed)</span>'."\n");
				echo('		<br />by '.xml_escape($revision['username'])."\n");
			}
		}
		else
		{
			echo('No Revisions ... Yet.'."\n");
		}
		?>
	</div>
</div>

<div id="MainColumn">
	<?php
	//main - edit article
	echo('<div class="BlueBox">'."\n");
	echo('	<h2>edit progress report</h2>'."\n");
	echo('	<form class="form" action="/office/campaign/articlemodify" method="post" >'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('			<input type="hidden" name="r_articleid" value="'.$parameters['prarticle_id'].'" />'."\n");
	echo('			<input type="hidden" name="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<label for="a_report">Report:</label>'."\n");
	if ($parameters['revision_id'] != NULL)
		echo('			<textarea name="a_report" rows="10" cols="50">'.xml_escape($article['displayrevision']['wikitext']).'</textarea><br />'."\n");
	else
		echo('			<textarea name="a_report" rows="10" cols="50"></textarea><br />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="submit" value="Save" class="button" name="r_submit_pr_save" />'."\n");
	echo('		</fieldset>'."\n");
	echo('	</form>'."\n");
	echo('</div>'."\n");
	
	//main - options
	if ($user['officetype'] != 'Low')
	{
		echo('<div class="BlueBox">'."\n");
		echo('	<h2>options</h2>'."\n");
		echo('	<form class="form" action="/office/campaign/articlemodify" method="post" >'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
		echo('			<input type="hidden" name="r_revisionid" id="r_revisionid" value="'.$parameters['revision_id'].'" />'."\n");
		echo('			<input type="hidden" name="r_articleid" value="'.$parameters['prarticle_id'].'" />'."\n");
		echo('			<input type="hidden" name="r_date_set" value="'.$article['header']['publish_date'].'" />'."\n");
		echo('			<input type="hidden" name="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
		echo('		</fieldset>'."\n");
		if ($parameters['revision_id'] != NULL) //if a revision exists
		{
			if ($article['displayrevision']['id'] != $article['header']['live_content'])
			{
				echo('		Make this revision the published revision for this progress report.'."\n");
				echo('		<fieldset>'."\n");
				echo('			<input type="submit" value="Publish" class="button" name="r_submit_pr_publish" />'."\n");
				echo('		</fieldset>'."\n");
			}
			else
			{
				echo('		Set this revision to not be the default for this progress report.'."\n");
				echo('		<fieldset>'."\n");
				echo('			<input type="submit" value="Unpublish" class="button" name="r_submit_pr_unpublish" />'."\n");
				echo('		</fieldset>'."\n");
			}
		}
		echo('		Set the date for the progress report.'."\n");
		echo('		<fieldset>'."\n");
		echo('			<label for="a_date">Date:</label>'."\n");
		echo('			<input type="text" name="a_date" size="20" value="');
		echo($article['header']['publish_date']);
		echo('" /><br />'."\n");
		echo('		</fieldset>'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="submit" value="Set Date" class="button" name="r_submit_pr_date" />'."\n");
		echo('		</fieldset>'."\n");
		echo('		Delete this progress report.');
		echo('		<fieldset>'."\n");
		echo('			<input type="submit" value="Delete" class="button" name="r_submit_pr_delete" />'."\n");
		echo('		</fieldset>'."\n");
		echo('	</form>'."\n");
		echo('</div>'."\n");
	}
	?>
	<a href="/office/campaign/editreports/<?php echo($parameters['campaign_id']); ?>">Back To Progress Reports</a>
</div>