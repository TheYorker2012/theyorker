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
 * @param $SelfUri string URI of current page.
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
		?><h2><?php
			?><a href="<?php echo(site_url('office/crosswords/tips/'.(int)$category['id'])); ?>"><?php
				echo(xml_escape($category['name']));
			?></a><?php
		?></h2><?php
		?><p><?php
			echo(nl2br(xml_escape($category['description'])));
		?></p><?php
		?><ul><?php
			if ($Permissions['tip_cat_view']) {
				?><li><?php
					?><a href="<?php echo(site_url('office/crosswords/tips/'.(int)$category['id'])); ?>"><?php
						?>View this tip category<?php
					?></a><?php
				?></li><?php
			}
			if ($Permissions['tip_cat_edit']) {
				?><li><?php
					?><a href="<?php echo(xml_escape(site_url('office/crosswords/tips/'.(int)$category['id']).'/edit?ret='.urlencode($SelfUri))); ?>"><?php
						?>Edit this tip category<?php
					?></a><?php
				?></li><?php
			}
		?></ul><?php
	?></div><?php
}
?>
</div>
