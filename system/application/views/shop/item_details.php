<div id="RightColumn">
	<h2 class="first">
		Options
	</h2>
	<div class="Entry">
		<a href="/shop/checkout/">Checkout</a>
	</div>
	<h2 class="first">
		Current Basket (&pound;43.50)
	</h2>
	<div class="Entry">
		2 x Christmas Club D - Adult (&pound;4.50)
	</div>
	<div class="Entry">
		1 x  Yorker Hoodie - M, Custom Name (&pound;20.00)
	</div>
	<div class="Entry">
		1 x  Yorker Hoodie - XL (&pound;19.00)
	</div>
</div>
<div id="MainColumn">
	<div id="HomeBanner">
		<?php 
		//$this->homepage_boxes->print_homepage_banner($banner);
		?>
	</div>
	<div class="BlueBox">
		<h2>
			<?php echo($item['name']); ?>
		</h2>
		<table border="0" width="100%">
			<tbody>
				<tr>
					<td width="20%" valign="top">
						<a href="/shop/item/<?php echo($item['id']); ?>"><img src="http://ecx.images-amazon.com/images/I/31qa-xPHWNL._AA115_.jpg" height="115" width="115" title="GSBP" alt="GSBP"  /></a>
					</td>
					<td width="80%" valign="top">
						<table border="0" width="100%">
							<tbody>
<?php if($item['event_date'] > 0) { ?>
								<tr>
									<td>
										<?php echo($item['event_date_string']); ?>
									</td>
								</tr>
<?php } ?>
								<tr>
									<td>
										<?php echo($item['description']); ?>
									</td>
								</tr>
								<tr>
									<td>
										<strong>Price: </strong> <?php echo($item['price_string']); ?><br /><strong>Availability: </strong> Limited<br />
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td width="20%" valign="top">
						<form id="add_to_basket_form" method="post" class="form">
							<fieldset>
								<input type="hidden" name="r_item_id" id="r_item_id" value="<?php echo($item['id']); ?>" />
								<label for="a_quantity">Quantity:</label>
								<select id="a_quantity" name="a_quantity">
<?php
	for($i=1; $i<=$item['max_per_user']; $i++)
	{
?>
									<option value="<?php echo($i); ?>"><?php echo($i); ?></option>
<?php
	}
?>
								</select>
<?php
	foreach($item['customisations'] as $customisation)
	{
?>
								<label for="a_customisation[<?php echo($customisation['id']); ?>]"><?php echo($customisation['name']); ?>: </label>
								<select id="a_customisation[<?php echo($customisation['id']); ?>]" name="a_customisation[<?php echo($customisation['id']); ?>]">
<?php
	foreach($customisation['options'] as $option)
	{
?>
									<option value="<?php echo($option['id']); ?>"><?php echo($option['name']); ?> - <?php echo($option['price_string']); ?></option>
<?php
	}
?>
								</select>
<?php
	}
?>
							</fieldset>
							<fieldset>
								<input class="button" type="submit" name="r_submit_add" id="r_submit_add" value="Add To Basket" />
							</fieldset>
						</form>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<?php
/*
	echo('<div class="BlueBox"><pre>');
	print_r($data);
	echo('</pre></div>');
*/
?>

</div>