<?php
/**
 * @file views/crosswords/office/categories.php
 * @param $Permissions array[string => bool]
 *  - 'index'
 *  - 'category_add'
 *  - 'category_view'
 *  - 'category_edit'
 * @param $Categories array[int => array('name', 'short_name'..)]
 */
?>

<div>
	<div class="BlueBox">
		<ul>
<?php
		if ($Permissions['category_add']) {
			?><li><a href="<?php echo(site_url('office/crosswords/cats/add')); ?>">Add a new category</a></li><?php
		}
		if ($Permissions['index']) {
			?><li><a href="<?php echo(site_url('office/crosswords')); ?>">Return to crosswords management</a></li><?php
		}
?>
		</ul>
	</div>
<?php
foreach ($Categories as $id => $category) {
	?><div class="BlueBox"><?php
	?><h2><?php echo(xml_escape($category['name'])); ?></h2><?php
	?><ul><?php
	if ($Permissions['category_view']) {
		?><li><a href="<?php echo(site_url('office/crosswords/cats/'.(int)$id)); ?>">View This Category</a></li><?php
	}
	if ($Permissions['category_edit']) {
		?><li><a href="<?php echo(site_url('office/crosswords/cats/'.(int)$id).'/edit'); ?>">Edit This Category</a></li><?php
	}
	?></ul><?php
	?></div><?php
}
?>
</div>
