<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<?php
	if ($user['officetype'] != 'Low')
	{
		if ($campaign['status'] == "unpublished")
		{
			echo('<div class="BlueBox">'."\n");
			echo('	<h2>publish options</h2>'."\n");
			echo('	<p>set the campaign to be published on the next voting round.</p>'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Publish" class="button" name="r_submit_set_to_future" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('	<p>Or expire this campaign.</p>'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Expire" class="button" name="r_submit_set_to_expired" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('</div>'."\n");
		}
		else if ($campaign['status'] == "future")
		{
			echo('<div class="BlueBox">'."\n");
			echo('	<h2>publish options</h2>'."\n");
			echo('	<p>remove this campaign from being published on the next voting round.</p>'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Unpublish" class="button" name="r_submit_set_to_unpublished" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('	<p>Or expire this campaign.</p>'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Expire" class="button" name="r_submit_set_to_expired" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('</div>'."\n");
		}
		else if ($campaign['status'] == "live")
		{
			echo('<div class="BlueBox">'."\n");
			echo('	<h2>publish options</h2>'."\n");
			echo('	<p>pull (expire) this campaign.</p>'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Expire" class="button" name="r_submit_set_to_expired" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('</div>'."\n");
		}
		else if ($campaign['status'] == "expired")
		{
			echo('<div class="BlueBox">'."\n");
			echo('	<h2>publish options</h2>'."\n");
			echo('	<p>delete this campaign.</p>'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Delete" class="button" name="r_submit_set_to_deleted" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('	<p>Or make it an unpublished campaign.</p>'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Make Unpublished" class="button" name="r_submit_set_to_unpublished" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('</div>'."\n");
		}
	}
	?>
	<a href="/office/campaign/">Back To Campaign Index</a>
</div>
