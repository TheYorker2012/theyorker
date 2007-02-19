<div class='RightToolbar'>
	<h4><?php echo $sidebar_about['title']; ?></h4>
	<div class='Entry'>
		<?php echo $sidebar_about['text']; ?>
	</div>
	<h4><?php echo $sidebar_what_now['title']; ?></h4>
	<div class='Entry'>
		<?php echo $sidebar_what_now['text']; ?>
	</div>
</div>

<div class='blue_box'>
	<h2><?php echo $current_campaigns['title']; ?></h2>
	<?php echo $current_campaigns['text']; ?><br />
	<table width="100%">
		<?php
		$total_votes = 0;
                foreach ($campaign_list as $campaigns)
		{
			$total_votes += $campaigns['votes'];
		}
                foreach ($campaign_list as $key => $campaigns)
		{
			if ($total_votes == 0)
				$percentage = 0;
			else {
				$percentage = $campaigns['votes']/$total_votes*100;
				$divpercentage = $percentage*0.76;
				/* If anyone has a better idea so the div doesn't go off the end feel free to change it.
				 * But I believe this works fine. --rr512
				 */
			}
			echo '<tr>
				<td>
				<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b>
				</td><td style="width:40%; border: thin solid teal;">
				<div style="float: left; width: '.$divpercentage.'%; background-color: teal;">&nbsp</div>
				<div stlye="float: right;">&nbsp;'.round($percentage).'%</div>
				</td>
				</tr>';
		}
		?>
	</table>
	<br />
</div>

<?php
if ($user == TRUE)
{

	echo '<div class="grey_box">';
	echo '<h2>'.$vote_campaigns['title'].'</h2>';
	echo $vote_campaigns['text'];
	echo '<div class="CampaignVoteAlign">';
	echo '<form class="form">';
        foreach ($campaign_list as $key => $campaigns)
	{
		if ($user['vote_id'] == $key)
		{
			echo '<form name="withdrawform" action="/campaign/withdrawvote" method="POST" class="form">
					<fieldset>
						<input type="hidden" name="a_campaignid" value="'.$key.'" />
						<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
						<label for="campaignname">'.$campaigns['name'].':</label>
						<input type="submit" value="Withdraw Vote" class="button" style="float: left" name="r_withdrawvote" />
					</fieldset>
				</form>';
		}
		else
		{
			echo '<form name="voteform" action="/campaign/castvote" method="POST" class="form">
					<fieldset>
						<input type="hidden" name="a_campaignid" value="'.$key.'" />
						<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
						<label for="campaignname">'.$campaigns['name'].':</label>
						<input type="submit" value="Vote" class="button" style="float: left" name="r_castvote" />
					</fieldset>
				</form>';
		}
	}
	echo '</form>';
	echo '</div>';
	echo '</div>';

}
?>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>
