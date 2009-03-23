<div id="RightColumn">
	<h2 class="first"><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo((($user_info['user_firstname'] == NULL) && ($user_info['user_surname'] == NULL)) ? 'global' : 'user'); ?> bylines</h2>

		<div>
			The following list of bylines are those belonging to <b><?php echo((($user_info['user_firstname'] == NULL) && ($user_info['user_surname'] == NULL)) ? 'EVERYONE' : xml_escape($user_info['user_firstname'] . ' ' . $user_info['user_surname'])); ?></b>.
		</div>
	</div>

<?php foreach ($bylines as $byline) {
	$this->load->view('/office/bylines/byline', $byline); ?>
	<div style="float:left;width:100%;margin-bottom:0.5em;">
		<span style="float:right">
			<b>Ops:</b>
			<a href="/office/bylines/view_byline/<?php echo($byline['business_card_id']); ?>/">
				<img src="/images/prototype/news/edit.png" alt="Edit" title="Edit" />
			</a>
			<a href="/office/bylines/delete_byline/<?php echo($byline['business_card_id']); ?>">
				<img src="/images/prototype/news/delete.png" alt="Delete" title="Delete" />
			</a>
			<br />
<?php if ((isset($default_byline)) && ($default_byline == $byline['business_card_id'])) {
	echo('<b>DEFAULT</b>');
} elseif ($this->user_auth->entityId == $user_id) { ?>
			<a href="/office/bylines/setdefault/<?php echo($byline['business_card_id']); ?>/">
				Make Default
			</a>
<?php } ?>
		</span>

		<b>Team:</b> <?php echo(xml_escape($byline['business_card_group_name'])); ?>
		<b>Status:</b> <?php echo(($byline['business_card_approved']) ? '<span style="color:darkgreen">Approved</span>' : '<span style="color:red">Pending</span>'); ?>
		<br />
		<b>Display:</b> <?php echo(date('d/m/y', $byline['business_card_start_date']) . ' - ' . date('d/m/y', $byline['business_card_end_date'])); ?>
		<br />
		<?php if ($byline['business_card_about_us']) echo('<span style="color:red"><b>ABOUT US PAGE</b></span>'); ?>
		<div class="clear"></div>
	</div>
<?php } ?>

</div>