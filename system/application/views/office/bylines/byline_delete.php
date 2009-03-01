<div id="RightColumn">
	<h2 class="first"><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">

<?php $this->load->view('/office/bylines/byline', $byline_info); ?>
	<div style="float:left;width:100%;margin-bottom:0.5em;">
		<b>Owner:</b>
		<a href="/office/bylines/user/<?php echo(($byline_info['business_card_user_entity_id'] == NULL) ? '-1' : $byline_info['business_card_user_entity_id']); ?>">
			<?php if (($byline_info['user_firstname'] == NULL) && ($byline_info['user_surname'] == NULL)) {
				echo('GLOBAL');
			} else {
				echo(xml_escape($byline_info['user_firstname'] . ' ' . $byline_info['user_surname']));
			} ?>
		</a>
		<b>Team:</b> <?php echo(xml_escape($byline_info['business_card_group_name'])); ?>
		<b>Status:</b> <?php echo(($byline_info['business_card_approved']) ? '<span style="color:darkgreen">Approved</span>' : '<span style="color:red">Pending</span>'); ?>
		<br />
		<b>Display:</b> <?php echo(date('d/m/y', $byline_info['business_card_start_date']) . ' - ' . date('d/m/y', $byline_info['business_card_end_date'])); ?>
		<br />
		<?php if ($byline_info['business_card_about_us']) echo('<span style="color:red"><b>ABOUT US PAGE</b></span>'); ?>
		<div class="clear"></div>
	</div>

	<div class="BlueBox">
		<h2>delete byline</h2>

		<div>
			Deleting this byline will mean that you will no longer be able to use to write
			any further articles and it will not appear in the about us section of the site.
			Are you sure you wish to continue and delete this byline?
		</div>

		<form action="/office/bylines/delete_byline/<?php echo($byline_info['business_card_id']); ?>/" method="post">
			<fieldset>
				<input type="submit" name="delete_no" id="delete_no" value="Cancel" class="button" />
				<input type="submit" name="delete_yes" id="delete_yes" value="Delete" class="button" />
			</fieldset>
		</form>
	</div>

</div>