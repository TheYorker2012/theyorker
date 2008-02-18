<?php include('sidebar.php'); ?>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>Shop Categories</h2>
		<ul>
<?php
	foreach ($categories as $category) {
?>
			<li><a href="/shop/view/<?php echo($category['id']); ?>/"><?php echo($category['name']); ?></a></li>
<?php
	}
?>
		</ul>
	</div>
</div>

