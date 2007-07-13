<?php
	//sidebar
	echo('<div class="RightToolbar">'."\n");
	echo('	<h4>Quick Links</h4>'."\n");
	echo('	<div class="Entry">'."\n");
	echo('		<a href="/office/campaign/">Back To Campaign Index</a>'."\n");
	echo('	</div>'."\n");
	echo('</div>'."\n");
	
	//main - request info
	if ($user['officetype'] != 'Low')
	{
		if ($campaign['status'] == "unpublished")
		{
			echo('<div class="blue_box">'."\n");
			echo('	<h2>publish options</h2>'."\n");
			echo('	set the campaign to be published on the next voting round.'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Publish" class="button" name="r_submit_set_to_future" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('	Or expire this campaign.'."\n");
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
			echo('<div class="blue_box">'."\n");
			echo('	<h2>publish options</h2>'."\n");
			echo('	remove this campaign from being published on the next voting round.'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Unpublish" class="button" name="r_submit_set_to_unpublished" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('	Or expire this campaign.'."\n");
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
			echo('<div class="blue_box">'."\n");
			echo('	<h2>publish options</h2>'."\n");
			echo('	pull (expire) this campaign.'."\n");
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
			echo('<div class="blue_box">'."\n");
			echo('	<h2>publish options</h2>'."\n");
			echo('	delete this campaign.'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('			<input type="hidden" name="r_campaignid" id="r_campaignid" value="'.$parameters['campaign_id'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Delete" class="button" name="r_submit_set_to_deleted" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
			echo('	Or make it an unpublished campaign.'."\n");
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
