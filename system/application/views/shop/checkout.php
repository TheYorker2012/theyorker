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
		<h2>Your Basket</h2>
		<form method="POST"
			action="https://sandbox.google.com/checkout/cws/v2/Merchant/642636134744714/checkout"
			accept-charset="utf-8">
			<input type="hidden" name="_charset_"/>
			<?php foreach ($basket['items'] as $i => $item) { ?>
				<?php $i += 1; /* Google Checkout used 1-based indexing */ ?>
				<input type="hidden" name="item_name_<?php echo($i); ?>" value="<?php echo(xml_escape($item['details']['name'])); ?>"/>
				<input type="hidden" name="item_description_<?php echo($i); ?>" value="<?php echo(xml_escape($item['details']['description'])); ?>"/>
				<input type="hidden" name="item_quantity_<?php echo($i); ?>" value="<?php echo($item['quantity']); ?>"/>
				<input type="hidden" name="item_price_<?php echo($i); ?>" value="3.99"/>
				<input type="hidden" name="item_currency_<?php echo($i); ?>" value="GBP"/>
				<p><?php echo($item['quantity']); ?> x <?php echo(xml_escape($item['details']['name'])); ?></p>
			<?php } ?>
			<input type="image" name="Google Checkout" alt="Fast checkout through Google"
				src="http://checkout.google.com/buttons/checkout.gif?merchant_id=642636134744714&amp;w=180&amp;h=46&amp;style=white&amp;variant=text&amp;loc=en_GB"
				height="46" width="180"/>
		</form>
	</div>
</div>

