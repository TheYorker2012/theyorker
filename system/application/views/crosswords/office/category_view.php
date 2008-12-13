<?php
/**
 * @file views/crosswords/office/category_view.php
 * @param $Permissions array[string => bool]
 * @param $Category array of category data
 *  - name
 *  - short_name
 *  - default_width
 *  - default_height
 *  - default_layout_id
 *  - default_has_normal_clues
 *  - default_has_cryptic_clues
 */
?>

<div class="BlueBox">
	<ul>
<?php
	if ($Permissions['category_edit']) {
		?><li><a href="<?php echo(site_url('office/crosswords/cats/'.(int)$Category['id'].'/edit').'?ret='.xml_escape(urlencode($PostAction))); ?>">Edit this category</a></li><?php
	}
	if ($Permissions['categories_index']) {
		?><li><a href="<?php echo(site_url('office/crosswords/cats')); ?>">Return to crosswords categories</a></li><?php
	}
?>
	</ul>
</div>
