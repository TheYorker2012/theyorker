<?php include('sidebar.php'); ?>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($category['name']); ?></h2>
<?php
	foreach ($items as $item) {
?>
		<h3><a href="/shop/item/<?php echo($item['id']); ?>"><?php echo($item['name']); ?></a></h3>
<?php
		include('item_description.php');
	}
?>
	</div>
</div>

