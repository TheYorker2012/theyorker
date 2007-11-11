<div id="RightColumn">
	<h2 class="first"><?php echo($sidebar_about['title']); ?></h2>
	<div class="Entry">
		<?php echo($sidebar_about['text']); ?>
	</div>

	<h2><?php echo($sidebar_how['title']); ?></h2>
	<div class="Entry">
		<?php echo($sidebar_how['text']); ?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($current_campaigns['title']); ?></h2>
		<?php echo($current_campaigns['text']); ?>
	</div>
	<div class="BlueBox">
		<h2><?php echo($votes['title']); ?></h2>
<?php
echo('		'.$votes['text']);
foreach ($campaign_list as $key => $campaigns) {
	$percentage = round($campaigns['percentage']);
	echo('		<div class="CampaignBox">'."\n");
	echo('			<div class="ProgressBar">'."\n");
	echo('				<div class="ProgressInner" style="width: '.$percentage.'%">');
	if ($percentage <= 50) {
		echo('&nbsp;</div>&nbsp;'.$percentage.'%'."\n");
	} else {
		echo('&nbsp;'.$percentage.'%</div>'."\n");
	}
	echo('			</div>'."\n");
	echo('			<a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a>'."\n");
	echo('		</div>'."\n");
}
?>
	</div>

<?php
if ($user == TRUE) {
	echo('	<div class="BlueBox">'."\n");
	echo('		<h2>'.$vote_campaigns['title'].'</h2>'."\n");
	echo('		'.$vote_campaigns['text']);
        foreach ($campaign_list as $key => $campaigns) {
		if ($user['vote_id'] == $key) {
?>
		<form action="/campaign/withdrawvote" method="post" class="voteform">
			<fieldset>
				<input type="submit" value="Withdraw Vote" class="button" name="r_castvote" />
				<a href="<?php echo(site_url('campaign/details/').'/'.$key); ?>"><?php echo($campaigns['name']) ?></a>
				<input type="hidden" name="a_campaignid" value="<?php echo($key); ?>" />
				<input type="hidden" name="r_redirecturl" value="<?php echo($_SERVER['REQUEST_URI']); ?>" />
			</fieldset>
		</form>
<?php
		} else {
?>
		<form action="/campaign/castvote" method="post" class="voteform">
			<fieldset>
				<input type="submit" value="Vote" class="button" name="r_castvote" />
				<a href="<?php echo(site_url('campaign/details/').'/'.$key); ?>"><?php echo($campaigns['name']) ?></a>
				<input type="hidden" name="a_campaignid" value="<?php echo($key); ?>" />
				<input type="hidden" name="r_redirecturl" value="<?php echo($_SERVER['REQUEST_URI']); ?>" />
			</fieldset>
		</form>
<?php
		}
	}
	echo '	</div>';
}
?>

</div>
