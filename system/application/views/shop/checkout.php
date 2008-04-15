<?php include('sidebar.php'); ?>

<div id="MainColumn">
	<div id="HomeBanner">
	</div>
	<div class="BlueBox">
		<h2>Your Basket</h2>
		
		<?php foreach ($basket['items'] as $item) { ?>
			<p>
				<?php echo(xml_escape($item['price_string'])); ?>
				&mdash;
				<?php echo($item['quantity']); ?>
				&times;
				<?php echo(xml_escape($item['item_name'])); ?> (<?php echo(xml_escape($item['cust_string'])); ?>)
			</p>
		<?php } ?>
		
		<?php echo $cart->CheckoutButtonCode('SMALL'); ?>
	</div>
</div>

