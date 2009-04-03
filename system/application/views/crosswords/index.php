<?php
/**
 * @file views/crosswords/index.php
 * @author James Hogan <james_hogan@theyorker.co.uk>
 * @param $Categories array of categories.
 * @param $Search array of categories.
 */
?>
<div class="BlueBox">
	<h2>welcome to The Yorker crosswords</h2>
	<?php
	$Search->Load();
	?>
</div>
<div class="HalfColumns">
<?php
	foreach ($Categories as $category) {
		if (count($category['latest'])+count($category['next']) == 0) {
			continue;
		}
		?><div class="Column"><?php
		?><div class="BlueBox"><?php
			if (count($category['next']) > 0) {
				$pub = new Academic_time($category['next'][0]['publication']);
				?><div class="crossword_note"><?php
				echo('next: '.$pub->Format('D').' week '.$pub->AcademicWeek().$pub->Format(' H:i'));
				?></div><?php
			}
			if (count($category['latest']) > 0) {
				?><div class="crossword_note"><?php
					?><a href="<?php echo(site_url('crosswords/'.$category['short_name'].'/archive')); ?>">archive</a><?php
				?></div><?php
			}
			?><h2><a href="<?php echo(site_url('crosswords/'.$category['short_name'])); ?>"><?php
				echo(xml_escape($category['name']));
			?></a></h2><?php

			foreach ($category['latest'] as $crossword) {
				$pub = new Academic_time($crossword['publication']);
				?><div class="crossword_title"><?php
					?><a href="<?php echo(site_url('crosswords/'.$crossword['id'])); ?>"><?php
					echo($pub->Format('D ').$pub->AcademicTermNameUnique().' week '.$pub->AcademicWeek());
					?></a><?php
				?></div><?php
				?><div class="crossword_note">not attempted</div><?php
				if (count($crossword['authors']) > 0) {
					?><em>by <?php echo(xml_escape(join(',', $crossword['authors']))); ?></em><?php
				}
				$max_winners = $crossword['winners'];
				if ($max_winners > 0) {
					$winners_so_far = (int)$crossword['winners_so_far'];
					if ($crossword['expired']) {
						?><em><?php echo(($winners_so_far==0) ? 'no' : $winners_so_far) ?> medals awarded</em><?php
					}
					else {
						?><em><?php echo($winners_so_far) ?> of <?php echo((int)$max_winners); ?> medals awarded</em><?php
					}
				}
			}
		?></div><?php
		?></div><?php
	}
?>
</div>
