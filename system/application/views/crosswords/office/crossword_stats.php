<?php
/**
 * @file views/crosswords/office/crossword_stats.php
 * @param $Permissions array[string => bool] including:
 *	- 'modify'
 *	- 'stats_basic'
 * @param $Crossword array of crossword information:
 *	- 'id'
 * @param $Stats array of stats
 * @param $StatLabels array of stats labels
 */

$links = array();
$links['back to category'] = site_url('office/crosswords/cats/'.(int)$Crossword['category_id']);
if ($Permissions['modify']) {
	$links['edit this crossword'] = site_url('office/crosswords/crossword/'.(int)$Crossword['id'].'/edit');
}
$links['view this crossword'] = site_url('office/crosswords/crossword/'.(int)$Crossword['id']);
if ($Crossword['published']) {
	$links['view crossword on public site'] = site_url('crosswords/'.(int)$Crossword['id']);
}

?><div class="BlueBox"><?php
	?><h2><?php
		if ($Crossword['publication'] !== null) {
			$pub = new Academic_time($Crossword['publication']);
			echo($pub->Format('D ').$pub->AcademicTermNameUnique().' week '.$pub->AcademicWeek());
		}
		else {
			?>unscheduled<?php
		}
	?> Statistics</h2><?php

	if (!empty($links)) {
		?><ul><?php
			// Main links
			foreach ($links as $label => $url) {
				?><li><a href="<?php echo(xml_escape($url)); ?>"><?php
					echo(xml_escape($label));
				?></a></li><?php
			}
		?></ul><?php
	}

	?><ul><?php
	foreach ($Stats as $label => $value)
	{
		if (isset($StatLabels[$label]))
		{
			?><li><?php
			echo(xml_escape("$value - $StatLabels[$label]"));
			?></li><?php
		}
	}
	?></ul><?php
?></div><?php

?>
