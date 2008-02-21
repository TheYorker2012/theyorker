<?php include('sidebar.php'); ?>

<div id="MainColumn">
	<div id="HomeBanner">
	</div>
	<div class="BlueBox">
		<h2>Your Basket</h2>
		<form method="POST"
			action="https://sandbox.google.com/checkout/cws/v2/Merchant/572321583992745/checkoutForm"
			accept-charset="utf-8">
			<input type="hidden" name="_charset_"/>
			<?php foreach ($basket['items'] as $i => $item) { ?>
				<?php $i += 1; /* Google Checkout used 1-based indexing */ ?>
				<input type="hidden" name="item_name_<?php echo($i); ?>" value="<?php echo(xml_escape($item['item_name'])); ?>"/>
				<input type="hidden" name="item_description_<?php echo($i); ?>" value="<?php echo(xml_escape($item['item_description'])); ?>"/>
				<input type="hidden" name="item_quantity_<?php echo($i); ?>" value="<?php echo($item['quantity']); ?>"/>
				<input type="hidden" name="item_price_<?php echo($i); ?>" value="<?php echo($item['item_price']); ?>"/>
				<input type="hidden" name="item_currency_<?php echo($i); ?>" value="GBP"/>
				<p>
					<?php echo($item['price_string']); ?>
					&mdash;
					<?php echo($item['quantity']); ?>
					x
					<?php echo(xml_escape($item['item_name'])); ?> (<?php echo(xml_escape($item['cust_string'])); ?>)
				</p>
			<?php } ?>
			<input type="image" name="Google Checkout" alt="Fast checkout through Google"
				src="http://checkout.google.com/buttons/checkout.gif?merchant_id=572321583992745&amp;w=180&amp;h=46&amp;style=white&amp;variant=text&amp;loc=en_GB"
				height="46" width="180"/>
		</form>
	</div>
	<!--
		
		<?php print_r($basket); ?>
		
	-->
</div>

