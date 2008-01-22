<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file polls.php
 * @brief Library for assembling poll data and displaying polls.
 * @author Richard Ingle (ri504@york.ac.uk)
 */
 
class Polls_view
{
	function print_sidebar_poll_voting($poll_data, $poll_option_data) {
		echo('  <h2>Poll</h2>'."\n");
		echo('	<form class="form" action="'.$_SERVER['REQUEST_URI'].'" method="post">'."\n");
		echo('  	<div class="Entry">'."\n");
		echo('		<fieldset>'."\n");
		echo('		'.$poll_data['question'].' ('.$poll_option_data['vote_count'].' votes)'."\n");
		if (count($poll_option_data['choices']) > 0) {
			foreach ($poll_option_data['choices'] as $choice) {
				if ($poll_option_data['vote_count'] > 0)
				{
					$percentage = round($choice['votes']/$poll_option_data['vote_count']*100);
				}
				else
				{
					$percentage = 0;
				}
				echo('		<input type="radio" id="poll_vote" name="poll_vote" value="'.$choice['id'].'">'.$choice['name'].' - '.$percentage.'%<br />'."\n");
			}
		}
		echo('		</fieldset>'."\n");
		echo('		<fieldset>'."\n");
		echo('			<input type="submit" name="submit_vote" value="Vote" />'."\n");
		echo('			<input type="submit" name="submit_no_vote" value="Show Results" />'."\n");
		echo('		</fieldset>'."\n");
		echo('  	</div>'."\n");
		echo('  </form>'."\n");
	}
	
	function print_sidebar_poll_no_voting($poll_data, $poll_option_data) {
		echo('  <h2>Poll</h2>'."\n");
		echo('  	<div class="Entry">'."\n");
		echo('		'.$poll_data['question'].' ('.$poll_option_data['vote_count'].' votes)'."\n");
		if (count($poll_option_data['choices']) > 0) {
			foreach ($poll_option_data['choices'] as $choice) {
				if ($poll_option_data['vote_count'] > 0)
				{
					$percentage = round($choice['votes']/$poll_option_data['vote_count']*100);
				}
				else
				{
					$percentage = 0;
				}
				echo('		'.$choice['name'].' - '.$percentage.'%<br />'."\n");
			}
		}
		echo('  	</div>'."\n");
	}
	
	function print_sidebar_poll_login_to_vote($poll_data) {
		echo('  <h2>Poll</h2>'."\n");
		echo('  <div class="Entry">'."\n");
		echo('		'.$poll_data['question']."\n");
		echo('  </div>'."\n");
		echo('  <div class="Entry">'."\n");
		echo('		You must login to vote.'."\n");
		echo('  </div>'."\n");
	}
	
}
?>
