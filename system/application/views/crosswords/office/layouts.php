<?php
/**
 * @file views/crosswords/office/layouts.php
 * @param $Permissions array[string => bool]
 *  - 'layout_add'
 *  - 'layout_edit'
 * @param $Layouts array[int => array('id', 'name', 'description')]
 */
?>

<div>
<?php
if ($Permissions['layout_add']) {
	?><div class="BlueBox"><?php
	?><ul><?php
	?><li><a href="<?php echo(site_url('office/crosswords/layouts/add')); ?>">Add a New Layout</a></li><?php
	?></ul><?php
	?></div><?php
}
foreach ($Layouts as $id => $layout) {
	?><div class="BlueBox"><?php
	?><h2><?php echo(xml_escape($layout['name'])); ?></h2><?php
	?><p><?php echo(nl2br(xml_escape($layout['description']))); ?></p><?php
	if ($Permissions['layout_edit']) {
		?><ul><?php
		?><li><a href="<?php echo(site_url('office/crosswords/layouts/'.(int)$id)); ?>">Edit This Layout</a></li><?php
		?></ul><?php
	}
	?></div><?php
}
?>
</div>
