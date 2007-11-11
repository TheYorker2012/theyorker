<div id="RightColumn">
	<h2 class="first"><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>pending bylines</h2>

		<div>
			The following is a list of all the bylines that are new or have been changed
			and therefore need reviewing by an editor before they will be allowed to be
			visible on the live site.
		</div>
	</div>

<?php foreach ($bylines as $byline) {
	$this->load->view('/office/bylines/byline', $byline); ?>
	<div>
		<span style="float:right">
			<b>Ops:</b>
			<a href="/office/bylines/view_byline/<?php echo($byline['business_card_id']); ?>/">
				<img src="/images/prototype/news/edit.png" alt="Edit" title="Edit" />
			</a>
			<a href="/office/bylines/delete_byline/<?php echo($byline['business_card_id']); ?>">
				<img src="/images/prototype/news/delete.png" alt="Delete" title="Delete" />
			</a>
		</span>

		<b>Owner:</b>
		<a href="/office/bylines/user/<?php echo(($byline['business_card_user_entity_id'] == NULL) ? '-1' : $byline['business_card_user_entity_id']); ?>">
			<?php if (($byline['user_firstname'] == NULL) && ($byline['user_surname'] == NULL)) {
				echo('GLOBAL');
			} else {
				echo($byline['user_firstname'] . ' ' . $byline['user_surname']);
			} ?>
		</a>
		<b>Team:</b> <?php echo($byline['business_card_group_name']); ?>
		<br />
		<b>Display:</b> <?php echo(date('d/m/y', $byline['business_card_start_date']) . ' - ' . date('d/m/y', $byline['business_card_end_date'])); ?>
		<br />
		<?php if ($byline['business_card_about_us']) echo('<span style="color:red"><b>ABOUT US PAGE ONLY</b></span>'); ?>
		<div class="clear"></div>
	</div>
<?php } ?>

</div>