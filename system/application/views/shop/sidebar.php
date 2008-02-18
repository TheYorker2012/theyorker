<div id="RightColumn">
	<h2 class="first">Options</h2>
	<div class="Entry">
		<a href="/shop/checkout/">Checkout</a>
	</div>

	<h2>Current Basket (<?php echo($basket['price_string']); ?>)</h2>
<?php
	foreach ($basket['items'] as $basket_item) {
?>
	<div class="Entry">
		<?php echo($basket_item['quantity']); ?> x
		<?php echo($basket_item['item_name']); ?> -
		<?php echo($basket_item['cust_string']); ?>
		(<?php echo($basket_item['price_string']); ?>)
	</div>
<?php
	}
?>
</div>

