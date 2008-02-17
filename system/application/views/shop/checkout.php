<div id="RightColumn">
	<h2 class="first">Options</h2>
	<div class="Entry">
		<a href="/shop/checkout/">Checkout</a>
	</div>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
	</div>
	<div class="BlueBox">
		<h2>Current Basket</h2>
	</div>
	<div>
	<?php foreach ($items as $item) { ?>
		<p><?php echo $item ?></p>
	<?php } ?>
	</div>
</div>