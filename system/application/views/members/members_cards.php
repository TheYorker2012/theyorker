<div class='RightToolbar'>
	<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div style="width: 420px; margin: 0px; padding-right: 3px; ">
<?php
if (empty($business_cards)) {
?>
	<p>
		No matching business cards found.
	</p>
<?php
} else {
	foreach ($business_cards as $business_card) {
		$this->load->view('directory/business_card', array(
			'business_card' => $business_card,
			'editmode' => TRUE,
		));
	}
}
?>
<a href='<?php echo vip_url('members/list'); ?>'>Back to Member Management.</a>
</div>