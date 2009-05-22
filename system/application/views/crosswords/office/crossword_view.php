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

$links = array();
$links['back to category'] = site_url('office/crosswords/cats/'.(int)$Crossword['category_id']);
if ($Permissions['modify']) {
	$links['edit this crossword'] = site_url('office/crosswords/crossword/'.(int)$Crossword['id'].'/edit');
}
if ($Permissions['stats_basic']) {
	$links['view statistics for this crossword'] = site_url('office/crosswords/crossword/'.(int)$Crossword['id'].'/stats');
}
if ($Crossword['published']) {
	$links['view crossword on public site'] = site_url('crosswords/'.(int)$Crossword['id']);
}

// class="crosswordEdit" so that clues aren't crossed out when complete:
?><div class="crosswordEdit"><?php
	$this->load->view('crosswords/crossword', array(
		'Crossword' => &$Crossword,
		'Winners' => null,
		'Grid' => &$Grid,
		'LoggedIn' => null,
		'Paths' => array(),
		'Tips' => &$Tips,
		'Comments' => null,
		'Links' => $links,
		'ShareUrl' => null,
	));
?></div><?php

?>
