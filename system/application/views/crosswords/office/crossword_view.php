<?php
/**
 * @file views/crosswords/office/crossword_view.php
 * @param $Permissions array[string => bool] including:
 *	- 'modify'
 *	- 'stats_basic'
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Grid
 * @param $Tips
 */
?>

<div class="BlueBox">
	<ul>
<?php
	?><li><a href="<?php echo(xml_escape(site_url('office/crosswords/cats/'.(int)$Crossword['category_id']))); ?>">Back to category</a></li><?php
	if ($Permissions['modify']) {
		?><li><a href="<?php echo(xml_escape(site_url('office/crosswords/crossword/'.(int)$Crossword['id'].'/edit'))); ?>">Edit this crossword</a></li><?php
	}
	if ($Permissions['stats_basic']) {
		?><li><a href="<?php echo(xml_escape(site_url('office/crosswords/crossword/'.(int)$Crossword['id'].'/stats'))); ?>">View statistics for this crossword</a></li><?php
	}
	if ($Crossword['published']) {
		?><li><a href="<?php echo(xml_escape(site_url('crosswords/'.(int)$Crossword['id']))); ?>">View crossword on public site</a></li><?php
	}
?>
	</ul>
</div>

<?php

// So that clues aren't crossed out when complete:
?><div class="crosswordEdit"><?php
	$this->load->view('crosswords/crossword', array(
		'Crossword' => &$Crossword,
		'Winners' => null,
		'Grid' => &$Grid,
		'LoggedIn' => null,
		'Paths' => array(),
		'Tips' => &$Tips,
		'Comments' => null,
	));
?></div><?php

?>
