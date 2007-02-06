<div class='RightToolbar'>
	<h4><?php echo $sidebar_vote['title']; ?></h4>
	<?php
	if ($user == TRUE)
	{
		if ($user['vote_id'] == FALSE)
		{
			echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sidebar_vote['newvote']);
			echo '	<form id="form1" name="voteform" action="/campaign/castvote" method="POST" class="form">
					<fieldset>
						<input type="hidden" name="a_campaignid" value="'.$parameters['campaign'].'" />
						<input type="hidden" name="r_redirecturl" value="'.str_replace("/index.php/", "", $_SERVER['PHP_SELF']).'" />
						<input type="submit" value="Vote" class="button" name="r_castvote" />
					</fieldset>
				</form>';
		}
		else if ($user['vote_id'] == $parameters['campaign'])
		{
			echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sidebar_vote['withdrawvote']);
			echo '	<form id="form1" name="withdrawform" action="/campaign/withdrawvote" method="POST" class="form">
					<fieldset>
						<input type="hidden" name="r_redirecturl" value="'.str_replace("/index.php/", "", $_SERVER['PHP_SELF']).'" />
						<input type="submit" value="Withdraw" class="button" name="r_withdrawvote" />
					</fieldset>
				</form>';
		}
		else
		{
			echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sidebar_vote['changevote']);
			echo '	<form id="form1" name="voteform" action="/campaign/castvote" method="POST" class="form">
					<fieldset>
						<input type="hidden" name="a_campaignid" value="'.$parameters['campaign'].'" />
						<input type="hidden" name="r_redirecturl" value="'.str_replace("/index.php/", "", $_SERVER['PHP_SELF']).'" />
						<input type="submit" value="Vote" class="button" name="r_changevote" />
					</fieldset>
				</form>';
		}
	}
	else
		echo $sidebar_vote['not_logged_in'];
	?>
	<br />

	<h4><?php echo $sidebar_other_campaigns['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($campaign_list as $key => $campaigns)
	{
		if ($key != $selected_campaign)
			echo '<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b><br />';
	};
	?>
	</p>
	
	<h4><?php echo $sidebar_more['title']; ?></h4>
	<?php echo $sidebar_more['text']; ?>
	
	<h4><?php echo $sidebar_related['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($article['related_articles'] as $related_articles)
	{
		echo '<b><a href="http://www.google.com/">'.$related_articles['heading'].'</a></b><br />';
	};
	?>
	</p>

	<h4><?php echo $sidebar_external['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($article['links'] as $links)
	{
		echo '<b><a href="'.$links['url'].'">'.$links['name'].'</a></b><br />';
	};
	?>
	</p>
	
    	<h4><?php echo $sidebar_comments['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">#TODO</p>
</div>

<div class='grey_box'>
	<h2><?php echo $article['heading']; ?></h2>
	<span class="black"><?php echo $article['text']; ?></span>
</div>

<?php
	foreach ($article['fact_boxes'] as $fact_box)
	{
		echo '<div class=\'blue_box\'>';
		echo '<h2>'.$fact_box['title'].'</h2>';
		echo $fact_box['wikitext'];
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
