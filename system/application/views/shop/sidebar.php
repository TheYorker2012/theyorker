<div id="RightColumn">
	<h2 class="first">Options</h2>
	<div class="Entry">
		<a href="/shop/checkout/">Checkout</a>
	</div>

	<h2>Current Basket (<?php echo(xml_escape($basket['price_string'])); ?>)</h2>
<?php
	if (count($basket['items']) == 0)
	{
?>
	<div class="Entry">
		No Items In Basket
	</div>
<?php
	}
	else
	{
		foreach ($basket['items'] as $basket_item) {
?>
	<div class="Entry">
		<?php echo(xml_escape($basket_item['quantity'])); ?> x
		<?php echo(xml_escape($basket_item['item_name'])); ?> -
		<?php echo(xml_escape($basket_item['cust_string'])); ?>
		(<?php echo(xml_escape($basket_item['price_string'])); ?>)
	</div>
<?php
		}
	}
?>
</div>

