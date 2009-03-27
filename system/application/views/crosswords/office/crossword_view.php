<?php
/**
 * @file views/crosswords/office/crossword_view.php
 * @param $Permissions array[string => bool] including:
 *	- 'modify'
 *	- 'stats_basic'
 * @param $Crossword array of crossword information:
 *	- 'id'
 */
?>

<div class="BlueBox">
	<h2>crossword</h2>
	<ul>
<?php
	?><li><a href="<?php echo(xml_escape(site_url('office/crosswords/cats/'.(int)$Crossword['category_id']))); ?>">Back to category</a></li><?php
	if ($Permissions['modify']) {
		?><li><a href="<?php echo(xml_escape(site_url('office/crosswords/crossword/'.(int)$Crossword['id'].'/edit'))); ?>">Edit this crossword</a></li><?php
	}
	if ($Permissions['stats_basic']) {
		?><li><a href="<?php echo(xml_escape(site_url('office/crosswords/crossword/'.(int)$Crossword['id'].'/stats'))); ?>">View statistics for this crossword</a></li><?php
	}
?>
	</ul>
</div>

