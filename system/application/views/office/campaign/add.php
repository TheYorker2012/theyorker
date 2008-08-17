<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>add a new campaign</h2>
		<form class="form" action="/office/campaign/campaignmodify" method="post" >
			<fieldset>
				<label for="r_campaign_name">Name:</label>
				<input type="text" name="a_campaign_name" id="r_campaign_name" value="" size="30" />
			</fieldset>
			<fieldset>
				<input type="submit" value="Add" class="button" name="r_submit_add_campaign" />
			</fieldset>
		</form>
	</div>
</div>