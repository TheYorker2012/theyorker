<div class='RightToolbar'>
	<h4><?php echo $sidebar_about['title']; ?></h4>
	<div class='Entry'>
		<a href='/news/article/2'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
		<?php echo $sidebar_about['text']; ?>
	</div>
	<h4><?php echo $sidebar_what_now['title']; ?></h4>
	<div class='Entry'>
		<a href='/news/article/2'><img src='/images/prototype/news/thumb2.jpg' alt='Some Spy' title='Some Spy' /></a>
		<?php echo $sidebar_what_now['text']; ?>
	</div>
</div>

<div class='blue_box'>
	<h2><?php echo $current_campaigns['title']; ?></h2>
	<?php echo $current_campaigns['text']; ?><br /><br />
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
			else
				$percentage = $campaigns['votes']/$total_votes*100;
			echo '<tr>
				<td>
				<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b>
				</td><td style="width:40%; border: thin solid teal;">
				<div style="float: left; width: '.$percentage.'%; background-color: teal;">&nbsp</div>
				<div stlye="float: right;">&nbsp;'.round($percentage).'%</div>
				</td>
				</tr>';
		}
		?>
	</table>
	<br />
	<?php echo $current_campaigns['deadline_text']; ?>
</div>

<div class='grey_box'>
	<h2><?php echo $vote_campaigns['title']; ?></h2>
	<?php echo $vote_campaigns['text']; ?>
	<form id='form1' name='form1' action='#' method='POST'>
	</form>
	<form id='form1' name='form1' action='#' method='POST' class='form'>
	<table width="80%">
	<?php
	/*
                foreach ($campaign_list as $campaigns)
                {
		echo '<tr><td style="text-align: right;">'.$campaigns['name'];
		echo '</td><td><fieldset style="display: inline;"><input type="submit" name="addorgform_addbutton" value="Vote" class="button" /></fieldset><br /></td></tr>';
	}
	*/
	?>
	<br />
	<?php
                foreach ($campaign_list as $key => $campaigns)
                {
			//echo '<tr><td style="text-align: right;">'.$campaigns['name'];
			if ($user == TRUE)
			{
				if ($user['vote_id'] == $key)
				{
					echo '<form name="withdrawform" action="/campaign/withdrawvote" method="POST" class="form">
							<fieldset>
								<input type="hidden" name="a_campaignid" value="'.$key.'" />
								<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
								<label for="campaignname">'.$campaigns['name'].':</label>
								<input type="submit" value="Withdraw Vote" class="button" name="r_withdrawvote" />
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
								<input type="submit" value="Vote" class="button" name="r_castvote" />
							</fieldset>
						</form>';
				}
			echo '<br /><br />';
			}
			//else
			//	echo $sidebar_vote['not_logged_in'];
		}
	?>
	</table>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>