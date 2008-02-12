<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file polls.php
 * @brief Library for assembling poll data and displaying polls.
 * @author Richard Ingle (ri504@york.ac.uk)
 */
 
class Polls_view
{
	function print_sidebar_poll_voting($poll_data, $poll_option_data, $show_results) {
		echo('  <h2>Poll</h2>'."\n");
		echo('	<form class="form" action="'.$_SERVER['REQUEST_URI'].'" method="post">'."\n");
		echo('  	<div class="Entry">'."\n");
		echo('		<fieldset>'."\n");
		echo('			'.xml_escape($poll_data['question']));
		if ($show_results) {
			echo(' ('.$poll_option_data['vote_count'].' votes)');
		}
		echo("\n");
		if (count($poll_option_data['choices']) > 0) {
			foreach ($poll_option_data['choices'] as $choice) {
				if ($show_results)
				{
					if ($poll_option_data['vote_count'] > 0)
					{
						$percentage = round($choice['votes']/$poll_option_data['vote_count']*100);
					}
					else
					{
						$percentage = 0;
					}
					$percentage = ' - '.$percentage.'%';
				}
				else
				{
					$percentage = '';
				}
				echo('		<label>'."\n");
				echo('			<input class="checkbox" type="radio" name="poll_vote" value="'.$choice['id'].'" />'."\n");
				echo('			'.xml_escape($choice['name']).$percentage."\n");
				echo('		</label>'."\n");
			}
		}
		echo('		</fieldset>'."\n");
		echo('		<fieldset>'."\n");
		if (!$show_results) {
			echo('			<input class="button" type="submit" name="submit_results" value="Results" />'."\n");
		}
		echo('			<input class="button" type="submit" name="submit_vote" value="Vote" />'."\n");
		echo('		</fieldset>'."\n");
		echo('  	</div>'."\n");
		echo('  </form>'."\n");
	}
	
	function print_sidebar_poll_no_voting($poll_data, $poll_option_data) {
		echo('  <h2>Poll</h2>'."\n");
		echo('  	<div class="Entry">'."\n");
		echo('		'.xml_escape($poll_data['question']).' ('.$poll_option_data['vote_count'].' votes)'."\n");
		if (count($poll_option_data['choices']) > 0) {
			echo('		<ul>'."\n");
			foreach ($poll_option_data['choices'] as $choice) {
				if ($poll_option_data['vote_count'] > 0)
				{
					$percentage = round($choice['votes']/$poll_option_data['vote_count']*100);
				}
				else
				{
					$percentage = 0;
				}
				$percentage = ' - '.$percentage.'%';
				echo('			<li>'.xml_escape($choice['name']).$percentage.'</li>'."\n");
			}
			echo('		</ul>'."\n");
		}
		echo('  	</div>'."\n");
	}
	
	function print_sidebar_poll_login_to_vote($poll_data) {
		echo('  <h2>Poll</h2>'."\n");
		echo('  <div class="Entry">'."\n");
		echo('		'.xml_escape($poll_data['question'])."\n");
		echo('  </div>'."\n");
		echo('  <div class="Entry">'."\n");
		echo('		You must login to vote.'."\n");
		echo('  </div>'."\n");
	}
	
}
?>
