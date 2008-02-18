<?php include('sidebar.php'); ?>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo(xml_escape($category['name'])); ?></h2>
<?php
	foreach ($items as $item) {
?>
		<h3><a href="/shop/item/<?php echo(xml_escape($item['id']).'/'.xml_escape($uri_trail)); ?>"><?php echo(xml_escape($item['name'])); ?></a></h3>
<?php
		include('item_blurb.php');
	}
?>
	</div>
</div>

