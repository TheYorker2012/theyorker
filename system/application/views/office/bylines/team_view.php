<div id="RightColumn">
	<h2 class="first"><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo(xml_escape($team_info['business_card_group_name'])); ?></h2>

		<form action="/office/bylines/view_team/<?php echo($team_info['business_card_group_id']); ?>/" method="post">
			<fieldset>
				<label for="team_name">Team Name:</label>
				<input type="text" name="team_name" id="team_name" value="<?php echo(xml_escape($team_info['business_card_group_name'])); ?>" />
				<input type="submit" name="rename_team" id="rename_team" value="Rename" class="button" />
			</fieldset>
		</form>
	</div>

<?php foreach ($bylines as $byline) {
	$this->load->view('/office/bylines/byline', $byline); ?>
	<div style="float:left">
		<span style="float:right">
			<b>Order:</b>
			<a href="/office/bylines/order_byline/<?php echo($byline['business_card_id']); ?>/up/">
				<img src="/images/prototype/members/sortdesc.png" alt="Move Up" title="Move Up" />
			</a>
			<a href="/office/bylines/order_byline/<?php echo($byline['business_card_id']); ?>/down/">
				<img src="/images/prototype/members/sortasc.png" alt="Move Down" title="Move Down" />
			</a>
			<b>Ops:</b>
			<a href="/office/bylines/view_byline/<?php echo($byline['business_card_id']); ?>/">
				<img src="/images/prototype/news/edit.png" alt="Edit" title="Edit" />
			</a>
			<a href="/office/bylines/delete_byline/<?php echo($byline['business_card_id']); ?>/">
				<img src="/images/prototype/news/delete.png" alt="Delete" title="Delete" />
			</a>
		</span>

		<b>Owner:</b>
		<a href="/office/bylines/user/<?php echo(($byline['business_card_user_entity_id'] == NULL) ? '-1' : $byline['business_card_user_entity_id']); ?>">
			<?php if (($byline['user_firstname'] == NULL) && ($byline['user_surname'] == NULL)) {
				echo('GLOBAL');
			} else {
				echo(xml_escape($byline['user_firstname'] . ' ' . $byline['user_surname']));
			} ?>
		</a>
		<b>Status:</b> <?php echo(($byline['business_card_approved']) ? '<span style="color:darkgreen">Approved</span>' : '<span style="color:red">Pending</span>'); ?>
		<br />
		<b>Display:</b> <?php echo(date('d/m/y', $byline['business_card_start_date']) . ' - ' . date('d/m/y', $byline['business_card_end_date'])); ?>
		<br />
		<?php if ($byline['business_card_about_us']) echo('<span style="color:red"><b>ABOUT US PAGE</b></span>'); ?>
		<div class="clear"></div>
	</div>
<?php } ?>

</div>