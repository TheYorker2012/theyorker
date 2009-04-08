<?php
/**
 * @file views/crosswords/tip_cat_view.php
 * @param $Category array of tip category data
 *	- id
 *  - name
 *  - description
 * @param $Tips CrosswordTipsList
 * @param $PostAction
 */
?>

<div class="BlueBox">
	<h2><?php echo(xml_escape($Category['name'])); ?></h2>

	<?php $Tips->Load(); ?>
</div>
