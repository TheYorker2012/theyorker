<?php include('sidebar.php'); ?>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php 
		//$this->homepage_boxes->print_homepage_banner($banner);
		?>
	</div>
	<div class="BlueBox">
		<h2><?php echo($item['name']); ?></h2>
		<?php include('item_description.php'); ?>
		<form id="add_to_basket_form" method="post" class="form">
			<fieldset>
				<input type="hidden" name="r_item_id" id="r_item_id" value="<?php echo($item['id']); ?>" />
				<label for="a_quantity">Quantity:</label>
				<select id="a_quantity" name="a_quantity">
<?php
for ($i = 1; $i <= $item['max_per_user']; $i++) {
?>
					<option value="<?php echo($i); ?>"><?php echo($i); ?></option>
<?php
}
?>
				</select>
<?php
foreach ($item['customisations'] as $customisation) {
?>
				<label for="a_customisation[<?php echo($customisation['id']); ?>]"><?php echo($customisation['name']); ?>: </label>
				<select id="a_customisation[<?php echo($customisation['id']); ?>]" name="a_customisation[<?php echo($customisation['id']); ?>]">
<?php
foreach ($customisation['options'] as $option) {
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
	</div>
</div>

