<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($main_text); ?>
	</div>
</div>
<div id="MainColumn">
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