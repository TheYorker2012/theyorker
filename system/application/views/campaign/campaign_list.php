<div class='RightToolbar'>
	<h4><?php echo $sidebar_about['title']; ?></h4>
	<div class='Entry'>
		<?php echo $sidebar_about['text']; ?>
	</div>
	<h4><?php echo $sidebar_how['title']; ?></h4>
	<div class='Entry'>
		<?php echo $sidebar_how['text']; ?>
	</div>
</div>

<div class='blue_box'>
	<h2><?php echo $current_campaigns['title']; ?></h2>
	<?php echo $current_campaigns['text']; ?>
	<h2><?php echo $votes['title']; ?></h2>
	<?php echo $votes['text']; ?>
	<table width="100%">
		<?php
                foreach ($campaign_list as $key => $campaigns)
		{
			$divpercentage = $campaigns['percentage']*0.76;
			/* If anyone has a better idea so the div doesn't go off the end feel free to change it.
			 * But I believe this works fine. --rr512
			 */
			echo '<tr>
				<td>
				<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b>
				</td><td style="width:40%; border: thin solid #ff6a00;">
				<div style="float: left; width: '.$divpercentage.'%; background-color: #ff6a00;">&nbsp</div>
				<div stlye="float: right;">&nbsp;'.round($campaigns['percentage']).'%</div>
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
	echo '<table width="100%">';
        foreach ($campaign_list as $key => $campaigns)
	{
		if ($user['vote_id'] == $key)
		{
			echo '<tr>
				<td>
					<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b>
				</td>
				<td style="width:40%; float: right">
					<form name="withdrawform" action="/campaign/withdrawvote" method="POST" class="form">
						<fieldset>
							<input type="hidden" name="a_campaignid" value="'.$key.'" />
 	                        			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
							<input type="submit" value="Withdraw Vote" class="button" name="withdrawvote" />
						</fieldset>
					</form>
				</td>
			</tr>';
		}
		else
		{
			echo '<tr>
				<td>
					<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b>
				</td>
				<td style="width:40%; float: right">
					<form name="voteform" action="/campaign/castvote" method="POST" class="form">
						<fieldset>
							<input type="hidden" name="a_campaignid" value="'.$key.'" />
 	                        			<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
							<input type="submit" value="Vote" class="button" name="r_castvote" />
						</fieldset>
					</form>
				</td>
			</tr>';
		}
	}
	echo '</table>';
	echo '</div>';
}
?>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
