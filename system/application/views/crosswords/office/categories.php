<?php
/**
 * @file views/crosswords/office/categories.php
 * @param $Permissions array[string => bool]
 *  - 'category_add'
 * @param $Categories array[int => array('name', 'short_name')]
 */
?>

<div>
<?php
if ($Permissions['category_add']) {
	?><div class="BlueBox"><?php
	?><ul><?php
	?><li><a href="<?php echo(site_url('office/crosswords/cats/add')); ?>">Add a new category</a></li><?php
	?></ul><?php
	?></div><?php
}
foreach ($Categories as $id => $category) {
	?><div class="BlueBox"><?php
	?><h2><?php echo(xml_escape($category['name'])); ?></h2><?php
	?></div><?php
}
?>
</div>
