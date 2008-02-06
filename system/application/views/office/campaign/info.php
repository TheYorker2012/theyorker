<div class="RightToolbar">
	<h4>Quick Links</h4>
	<?php
	echo('<a href="/office/campaign/editarticle/'.$campaign['id'].'">Article</a><br/ >');
	echo('<a href="/office/campaign/editreports/'.$campaign['id'].'">Progress Reports</a><br/ >');
	echo('<br/ >');
	?>
</div>

<div class="blue_box">
	<h2>campaign info</h2>
	<form class="form" action="/office/campaign/campaignmodify" method="post" >
		<fieldset>
			<input type="hidden" name="a_campaignid" value="<?php echo($campaign['id']); ?>" />
			<input type="hidden" name="r_redirecturl" value="<?php echo($_SERVER['REQUEST_URI']); ?>" />
			<label for="a_name">Name:</label>
			<input type="text" name="a_name" size="60" value="<?php echo(xml_escape($campaign['name'])); ?>" />
		</fieldset>
		<fieldset>
			<input type="submit" value="Set Campaign Name" class="submit" name="r_submit_set_campaign_name" />
		</fieldset>
	</form>
</div>
