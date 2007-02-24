<div class='RightToolbar'>
	<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<?php
foreach ($business_cards as $business_card) {
	$this->load->view('directory/business_card', array(
		'business_card' => $business_card,
		'editmode' => TRUE,
	));
}
?>
<a href='<?php echo vip_url('members/list'); ?>'>Back to Member Management.</a>