<?php

	if ($user['officetype'] != 'Low')
	{
		echo('<div class="RightToolbar">'."\n");
		echo('	<h4>Quick Links</h4>'."\n");
		echo('	<div class="Entry">'."\n");
		echo('		a link'."\n");
		echo('	</div>'."\n");
		echo('</div>'."\n");

		if ($campaign['is_petition'] == TRUE)
		{
			echo('<div class="blue_box">'."\n");
			echo('	<h2>options</h2>'."\n");
			if ($campaign['future_campaign_count'] >= 1)
			{
				echo('	end petition and start voting on campaigns set to future.'."\n");
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
				echo('	can\'t end this petition as there are none ready to be voted on.'."\n");
			}
			echo('</div>'."\n");
		}
		else
		{
			echo('<div class="blue_box">'."\n");
			echo('	<h2>options</h2>'."\n");
			if ($campaign['can_start_petition'] == TRUE)
			{
				echo('	start a petition with the campaign that has most votes.'."\n");
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
					echo('	start voting on future campaigns.'."\n");
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
					echo('	campaign voting is level, therefore can\'t start a petition.'."\n");
			}
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