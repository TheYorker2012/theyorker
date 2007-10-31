<div id="RightColumn">
	<h2 class="first">Your Rep</h2>
	<div class="Entry">
<?php
	if ($rep['has_rep'] == true) {
		echo('		<p>'."\n");
		echo('			Your rep is: '.$rep['firstname'].' '.$rep['surname']."\n");
		echo('		</p>'."\n");
	}
	else {
		echo('		<p>'."\n");
		echo('			You have no dedicated rep, so our pr officers '.$rep['name'].' are looking after you.'."\n");
		echo('		</p>'."\n");
	}
?>
		<p>
<?php
	echo('			<a href="'.vip_url('contactpr').'">Contact Your Rep</a>'."\n");
?>

		</p>
	</div>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<img src="/images/vip_banner.jpg" title="VIP Area" width="392" height="100" alt="VIP Area" />
	</div>
	
	<div class="BlueBox">
		<h2>welcome</h2>
		<?php echo($main_text); ?>
	</div>
</div>
