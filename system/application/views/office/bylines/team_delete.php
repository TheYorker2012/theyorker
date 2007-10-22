<div id="RightColumn">
	<h2 class="first"><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">

	<div class="BlueBox">
		<h2>delete byline team: <?php echo($team_info['business_card_group_name']); ?></h2>

		<div>
			Are you sure you want to delete this byline team?
		</div>

		<form action="/office/bylines/delete_team/<?php echo($team_info['business_card_group_id']); ?>/" method="post">
			<fieldset>
				<input type="submit" name="delete_no" id="delete_no" value="Cancel" class="button" />
				<input type="submit" name="delete_yes" id="delete_yes" value="Delete" class="button" />
			</fieldset>
		</form>
	</div>

</div>