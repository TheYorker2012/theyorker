<?php
/**
 * @file views/crosswords/office/tip_view.php
 * @param $Permissions array[string => bool]
 * @param $Category array of tip category data
 *	- id
 *  - name
 *  - description
 * @param $Tips CrosswordTipsList
 * @param $PostAction
 */
?>

<div class="BlueBox">
	<ul>
<?php
	if ($Permissions['tip_cat_edit']) {
		?><li><a href="<?php echo(site_url('office/crosswords/tips/'.(int)$Category['id'].'/edit').'?ret='.xml_escape(urlencode($PostAction))); ?>">Edit this tip category</a></li><?php
	}
	if ($Permissions['tips_index']) {
		?><li><a href="<?php echo(site_url('office/crosswords/tips')); ?>">Return to tip categories</a></li><?php
	}
?>
	</ul>
</div>

<div class="BlueBox">
	<h2>Tips</h2>

	<?php $Tips->Load(); ?>
</div>
