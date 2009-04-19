<?php
/**
 * @file views/crosswords/office/layouts.php
 * @param $Permissions array[string => bool]
 *  - 'index'
 *  - 'layout_add'
 *  - 'layout_edit'
 * @param $Layouts array[int => array('id', 'name', 'description')]
 */
?>

<div>
	<div class="BlueBox">
		<ul>
<?php
		if ($Permissions['layout_add']) {
			?><li><a href="<?php echo(site_url('office/crosswords/layouts/add')); ?>">Add a New Layout</a></li><?php
		}
		if ($Permissions['index']) {
			?><li><a href="<?php echo(site_url('office/crosswords')); ?>">Return to crosswords management</a></li><?php
		}
?>
		</ul>
	</div>
<?php
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
