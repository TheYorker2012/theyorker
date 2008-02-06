<?php
	//sidebar
	echo('<div class="RightToolbar">'."\n");
	echo('	<h4>Quick Links</h4>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		<a href="/office/campaign/">Back To Campaign Index</a>'."\n");
	echo('	</div>'."\n");
	echo('	<h4>Preview</h4>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		Preview how displayed revision will look on the public site. Remember to save any changes first.'."\n");
	echo('		<form class="form" action="/campaign/details/'.$parameters['campaign_id'].'" method="post" >'."\n");
	echo('			<fieldset>'."\n");
	echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('				<input type="hidden" name="r_revisionid" value="'.$parameters['revision_id'].'" />'."\n");
	echo('			</fieldset>'."\n");
	echo('			<fieldset>'."\n");
	echo('				<input type="submit" value="Preview Details Page" class="button" name="r_submit_preview_details" />'."\n");
	echo('			</fieldset>'."\n");
	echo('		</form>'."\n");
	echo('		<br />'."\n");
	echo('		<form class="form" action="/campaign/" method="post" >'."\n");
	echo('			<fieldset>'."\n");
	echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('				<input type="hidden" name="r_revisionid" value="'.$parameters['revision_id'].'" />'."\n");
	echo('				<input type="hidden" name="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
	echo('			</fieldset>'."\n");
	echo('			<fieldset>'."\n");
	echo('				<input type="submit" value="Preview Petition Page" class="button" name="r_submit_preview_petition" />'."\n");
	echo('			</fieldset>'."\n");
	echo('		</form>'."\n");
	echo('	</div>'."\n");
	echo('	<h4>Revisions (Latest First)</h4>'."\n");
	echo('	<div class="Entry">'."\n");
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
			echo('		<a href="/office/campaign/editarticle/'.$parameters['campaign_id'].'/'.$revision['id'].'">"'.xml_escape($revision['title']).'"</a>'."\n");
			if ($revision['id'] == $article['header']['live_content'])
			{
				echo('		<br /><span class="orange">(Published');
				if ($revision['id'] == $article['displayrevision']['id'])
					echo(', Displayed');
				echo(')</span>'."\n");
			}
			elseif ($revision['id'] == $article['displayrevision']['id'])
				echo('		<br /><span class="orange">(Displayed)</span>'."\n");
			echo('		<br />by '.xml_escape($revision['username']).'<br />on '.$dateformatted."\n");
		}
	}
	else
		echo('No Revisions ... Yet.'."\n");
	echo('	</div>'."\n");
	echo('</div>'."\n");

	//main - request info
	echo('<div class="blue_box">'."\n");
	echo('	<h2>request info</h2>'."\n");
	echo('	<b>Title: </b>'.xml_escape($article['header']['requesttitle']).'<br />'."\n");
	echo('	<b>Description: </b>'.xml_escape($article['header']['requestdescription']).'<br />'."\n");
	if ($user['officetype'] != 'Low')
	{
		echo('<a href="/office/campaign/editrequest/'.$parameters['campaign_id'].'">[modify]</a>'."\n");
	}
	echo('</div>'."\n");
	
	//main - edit article
	echo('<div class="blue_box">'."\n");
	echo('	<h2>edit article</h2>'."\n");
	echo('	<form class="form" action="/office/campaign/articlemodify" method="post" >'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('			<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" />'."\n");
	echo('			<input type="hidden" name="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<label for="a_question">Heading:</label>'."\n");
	echo('			<input type="text" name="a_question" id="a_question" size="60" value="');
	if ($parameters['revision_id'] != NULL)
		echo(xml_escape($article['displayrevision']['heading']));
	echo('" /><br />'."\n");
	echo('			<label for="a_answer">Article:</label>'."\n");
	if ($parameters['revision_id'] != NULL)
		echo('			<textarea name="a_answer" id="a_answer" rows="10" cols="56">'.xml_escape($article['displayrevision']['wikitext']).'</textarea><br />'."\n");
	else
		echo('			<textarea name="a_answer" id="a_answer" rows="10" cols="56"></textarea><br />'."\n");
	echo('			<label for="a_facts_title">Fact Box Title:</label>'."\n");
	echo('			<input type="text" name="a_facts_title" id="a_facts_title" size="60" value="');
	if ($parameters['revision_id'] != NULL)
		echo(xml_escape($article['displayrevision']['fact_box']['title']));
	echo('" />'."\n");
	echo('			<label for="a_facts">Facts:</label>'."\n");
	if ($parameters['revision_id'] != NULL)
		echo('			<textarea name="a_facts" id="a_facts" rows="5" cols="56">'.xml_escape($article['displayrevision']['fact_box']['wikitext']).'</textarea><br />'."\n");
	else
		echo('			<textarea name="a_facts" id="a_facts" rows="5" cols="56"></textarea><br />'."\n");
	echo('		</fieldset>'."\n");
	echo('		<fieldset>'."\n");
	echo('			<input type="submit" value="Save" class="button" name="r_submit_save" />'."\n");
	echo('		</fieldset>'."\n");
	echo('	</form>'."\n");
	echo('</div>'."\n");
	
	//main - options
	if ($user['officetype'] != 'Low')
	{
		if ($parameters['revision_id'] != NULL) //if a revision exists
		{
			echo('<div class="blue_box">'."\n");
			echo('	<h2>options</h2>'."\n");
			echo('	<form class="form" action="/office/campaign/articlemodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_revisionid" id="r_revisionid" value="'.$parameters['revision_id'].'" />'."\n");
			echo('			<input type="hidden" name="r_articleid" value="'.$parameters['article_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			if ($article['displayrevision']['id'] != $article['header']['live_content'])
			{
				echo('		Make this revision the published revision for this campaign.'."\n");
				echo('		<fieldset>'."\n");
				echo('			<input type="submit" value="Publish" class="button" name="r_submit_publish" />'."\n");
				echo('		</fieldset>'."\n");
			}
			else
			{
				echo('		Set this revision to not be the default for this campaign.'."\n");
				echo('		<fieldset>'."\n");
				echo('			<input type="submit" value="Unpublish" class="button" name="r_submit_unpublish" />'."\n");
				echo('		</fieldset>'."\n");
			}
			echo('	</form>'."\n");
			echo('</div>'."\n");
		}
	}
?>