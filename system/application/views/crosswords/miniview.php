<?php

/**
 * @file views/crosswords/miniview.php
 * @author James Hogan <james_hogan@theyorker.co.uk>
 *
 * @param $Latest array of latest crosswords
 * @param $Next array with next crossword to be published
 */

if (!empty($Next))
{
	$next_pub = new Academic_time($Next[0]['publication']);
	$next_pub_text = $next_pub->Format('l').' week '.$next_pub->AcademicWeek().' at '.$next_pub->Format('H:i');
}
if (empty($Latest))
{
	?><img alt="" src="<?php echo(site_url('images/crosswords/xw.png')); ?>" /><?php
	if (!empty($Next))
	{
		?><div class="Date"><?php
		echo('first online crossword will be published '.xml_escape($next_pub_text));
		?></div><?php
	}
	?><div><strong>online crosswords coming soon</strong></div><?php
	?><div><?php
		?>be one of the first to complete a crossword online and have your name on the winner list!<?php
	?></div><?php
}
else
{
	if (!empty($Next))
	{
		?><div class="Date"><?php
		echo('next online crossword will be published '.xml_escape($next_pub_text));
		?></div><?php
	}
	foreach ($Latest as $crossword)
	{
		$pub = new Academic_time($crossword['publication']);
		$pub_text = $pub->Format('D').' week '.$pub->AcademicWeek().' '.$pub->Format('H:i');
		?><div style="width:50%;float:left;"><?php
			// Icon and category
			?><a href="<?php echo(site_url('crosswords/'.$crossword['id'])); ?>"><?php
				?><img alt="" src="<?php echo(site_url('crosswords/'.$crossword['id'].'/preview')); ?>" /><?php
				echo(xml_escape($crossword['category_name']));
			?></a><?php

			// Date
			?><div class="Date"><?php
				echo(xml_escape($pub_text));
			?></div><?php

			// Author
			if (count($crossword['author_fullnames']) > 0) {
				?><em>by <?php echo(xml_escape(join(', ', $crossword['author_fullnames']))); ?></em><?php
			}

			// How many winners?
			?><div><?php
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
			?></div><?php

			?><div class="clear"></div><?php
		?></div><?php
	}
}
?>
