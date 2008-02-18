<div id="RightColumn">
	<h2 class="first">Options</h2>
	<div class="Entry">
		<a href="/shop/checkout/">Go To Checkout</a>
	</div>
	<div class="Entry">
		<a href="/shop/">Go To Shop Home</a>
	</div>
<?php
	if (isset($uri_trail_back)) {
?>
	<div class="Entry">
		<a href="/<?php echo(xml_escape($uri_trail_back)); ?>">Go To Category Home</a>
	</div>
<?php
	}
?>

	<h2>Current Basket (<?php echo(xml_escape($basket['price_string'])); ?>)</h2>
<?php
	if (count($basket['items']) == 0)
	{
?>
	<div class="Entry">
		No Items In Basket.
	</div>
<?php
	}
	else
	{
		foreach ($basket['items'] as $basket_item) {
?>
	<div class="Entry">
		<?php echo(xml_escape($basket_item['quantity'])); ?> x
		<a href="/shop/item/<?php echo(xml_escape($basket_item['item_id'])); ?>"><?php echo(xml_escape($basket_item['item_name'])); ?></a> -
		<?php echo(xml_escape($basket_item['cust_string'])); ?>
		(<?php echo(xml_escape($basket_item['price_string'])); ?>)
		<a href="/shop/itemcount/inc/<?php echo(xml_escape($basket_item['order_item_id']).'/'.xml_escape($uri_trail)); ?>">(+)</a>
		<a href="/shop/itemcount/dec/<?php echo(xml_escape($basket_item['order_item_id']).'/'.xml_escape($uri_trail)); ?>">(-)</a>
	</div>
<?php
		}
	}
?>
</div>

