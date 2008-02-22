<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Polls_view.php
 * @brief Library for assembling poll data and displaying polls.
 * @author James Hogan (james_hogan@theyorker.co.uk)
 */

// Library dependencies
get_instance()->load->library('frames');

/// Box for voting to go in side bar.
class PollsVoteBox extends FramesView
{
	public $data;
	public $option_data;
	public $user_voted;
	public $show_results;
	
	function __construct($poll_data, $poll_option_data,
	                     $user_voted, $show_results)
	{
		parent::__construct('polls/vote_box');
		$this->data = $poll_data;
		$this->option_data = $poll_option_data;
		$this->user_voted = $user_voted;
		$this->show_results = $show_results;
	}
}

class Polls_view
{
}

?>
