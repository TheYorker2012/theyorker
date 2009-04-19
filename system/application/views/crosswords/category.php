<?php
/**
 * @file views/crosswords/category.php
 * @author James Hogan <james_hogan@theyorker.co.uk>
 * @param $Category category.
 * @param $Links array
 */

?><div class="BlueBox"><?php
	if (count($Category['next']) > 0) {
		$pub = new Academic_time($Category['next'][0]['publication']);
		?><div class="crossword_note"><?php
		echo('next: '.$pub->Format('D').' week '.$pub->AcademicWeek().$pub->Format(' H:i'));
		?></div><?php
	}
	if (count($Category['latest']) > 0) {
		?><div class="crossword_note"><?php
			?><a href="<?php echo(site_url('crosswords/'.$Category['short_name'].'/archive')); ?>">archive</a><?php
		?></div><?php
	}
	?><h2><?php echo(xml_escape($Category['name'])); ?></h2><?php

	if (!empty($Links)) {
		?><ul><?php
			// Main links
			foreach ($Links as $label => $url) {
				?><li><a href="<?php echo(xml_escape($url)); ?>"><?php
					echo(xml_escape($label));
				?></a></li><?php
			}
		?></ul><?php
	}

	foreach ($Category['latest'] as $crossword) {
		$pub = new Academic_time($crossword['publication']);
		?><div style="clear: both"></div><?php
		?><div class="crossword_preview"><?php
			?><a href="<?php echo(site_url('crosswords/'.$crossword['id'])); ?>"><?php
				?><img alt="" src="<?php echo(site_url('images/crosswords/xw.png')); ?>" /><?php
			?></a><?php
		?></div><?php
		?><div class="crossword_title"><?php
			?><a href="<?php echo(site_url('crosswords/'.$crossword['id'])); ?>"><?php
			echo($pub->Format('D ').$pub->AcademicTermNameUnique().' week '.$pub->AcademicWeek());
			?></a><?php
		?></div><?php
		if (false) {
			?><div class="crossword_note">not attempted</div><?php
		}
		if (count($crossword['author_fullnames']) > 0) {
			?><em>by <?php echo(xml_escape(join(', ', $crossword['author_fullnames']))); ?></em><?php
		}
		$max_winners = $crossword['winners'];
		if ($max_winners > 0) {
			$winners_so_far = (int)$crossword['winners_so_far'];
			if ($crossword['expired']) {
				$medals = ($winners_so_far != 1 ? 'medals' : 'medal');
				?><em><?php
					echo(($winners_so_far==0) ? 'no' : $winners_so_far);
					echo(" $medals awarded");
				?></em><?php
			}
			else {
				$medals = ($max_winners != 1 ? 'medals' : 'medal');
				?><em><?php
					echo("$winners_so_far of $max_winners $medals awarded");
				?></em><?php
			}
		}
	}
?></div><?php
?>
