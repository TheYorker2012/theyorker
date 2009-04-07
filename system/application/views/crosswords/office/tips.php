<?php
/**
 * @file views/crosswords/office/tips.php
 * @param $Permissions array[string => bool]
 *  - 'index'
 *  - 'tips_index'
 *  - 'tip_cat_add'
 *  - 'tip_cat_view'
 *  - 'tip_cat_edit'
 * @param $Categories array
 */
?>

<div>
	<div class="BlueBox">
		<ul>
<?php
		if ($Permissions['tip_cat_add']) {
			?><li><a href="<?php echo(site_url('office/crosswords/tips/add')); ?>">Add a new tip category</a></li><?php
		}
		if ($Permissions['index']) {
			?><li><a href="<?php echo(site_url('office/crosswords')); ?>">Return to crosswords management</a></li><?php
		}
?>
		</ul>
	</div>
<?php
foreach ($Categories as $category) {
	?><div class="BlueBox"><?php
	?><h2><?php echo(xml_escape($category['name'])); ?></h2><?php
	?><ul><?php
	if ($Permissions['tips_cat_view']) {
		?><li><a href="<?php echo(site_url('office/crosswords/tips/'.(int)$id)); ?>">View this tip category</a></li><?php
	}
	if ($Permissions['tips_cat_edit']) {
		?><li><a href="<?php echo(site_url('office/crosswords/tips/'.(int)$id).'/edit'); ?>">Edit this tip category</a></li><?php
	}
	?></ul><?php
	?></div><?php
}
?>
</div>
