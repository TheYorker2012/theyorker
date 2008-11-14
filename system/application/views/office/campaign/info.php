<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<ul>
		<li><a href="/office/campaign/editarticle/<?php echo($campaign['id']); ?>">Article</a></li>
		<li><a href="/office/campaign/editreports/<?php echo($campaign['id']); ?>">Progress Reports</a></li>
	</ul>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>campaign info</h2>
		<form class="form" action="/office/campaign/campaignmodify" method="post" >
			<fieldset>
				<input type="hidden" name="a_campaignid" value="<?php echo($campaign['id']); ?>" />
				<input type="hidden" name="r_redirecturl" value="<?php echo($_SERVER['REQUEST_URI']); ?>" />
				<label for="a_name">Name:</label>
				<input type="text" name="a_name" size="50" value="<?php echo(xml_escape($campaign['name'])); ?>" />
			</fieldset>
			<fieldset>
				<input type="submit" value="Set Campaign Name" class="submit" name="r_submit_set_campaign_name" />
			</fieldset>
		</form>
	</div>
	<a href="/office/campaign/">Back To Campaign Index</a>
</div>