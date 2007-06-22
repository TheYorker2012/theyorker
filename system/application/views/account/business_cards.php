<div id="RightColumn">
	<h2>Whats This?</h2>
	<p><?php echo $bcards_about; ?></p>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>My Business Cards</h2>
		<div>
		<?php
		if(empty($cards)) {
		?>
		<p><b>You do not have any buisness cards listed in the directory.</b></p>
		<?php
		} else {
			foreach ($cards as $business_card) {
			echo "<h4>Card for ".$business_card['organisation']."</h4>";
				$this->load->view('directory/business_card',array(
					'business_card' => $business_card,
					'editmode' => true,
					'url' => '/account/bcardsedit/'.$business_card['id'],
				));
			}
		}
		?>
		</div>
	</div>
</div>
