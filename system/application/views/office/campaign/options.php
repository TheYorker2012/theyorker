<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
<?php
if ($campaign['is_petition'] == TRUE)
{
	echo('<div class="BlueBox">'."\n");
	echo('	<h2>options</h2>'."\n");
	if ($campaign['future_campaign_count'] >= 1)
	{
		echo('<p>End petition and start voting on campaigns set to future.</p>'."\n");
		echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
		echo('		</fieldset>'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="submit" value="End" class="button" name="r_submit_end" />'."\n");
		echo('		</fieldset>'."\n");
		echo('	</form>'."\n");
	}
	else
	{
		echo('<p>Can\'t end this petition as there are none ready to be voted on.</p>'."\n");
	}
	echo('</div>'."\n");
}
else
{
	echo('<div class="BlueBox">'."\n");
	echo('	<h2>options</h2>'."\n");
	if ($campaign['can_start_petition'] == TRUE)
	{
		echo('<p>Start a petition with the campaign that has most votes.</p>'."\n");
		echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
		echo('		</fieldset>'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="submit" value="Start" class="button" name="r_submit_start" />'."\n");
		echo('		</fieldset>'."\n");
		echo('	</form>'."\n");
	}
	else
	{
		if (($campaign['live_campaign_count'] == 0) && ($campaign['future_campaign_count'] >= 1))
		{
			//thinks votes are level but there isn't anything
			echo('<p>start voting on future campaigns.</p>'."\n");
			echo('	<form class="form" action="/office/campaign/campaignmodify" method="post" >'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
			echo('		</fieldset>'."\n");
			echo('		<fieldset>'."\n");
			echo('			<input type="submit" value="Start Voting" class="button" name="r_submit_end" />'."\n");
			echo('		</fieldset>'."\n");
			echo('	</form>'."\n");
		}
		else
			echo('<p>campaign voting is level, therefore can\'t start a petition.</p>'."\n");
	}
	echo('</div>'."\n");
}
?>
</div>