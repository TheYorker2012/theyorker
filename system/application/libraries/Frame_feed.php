<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_feed.php
 * @author James Hogan (james_hogan@theyorker.co.uk)
 * @brief Feed frame.
 */

// Load the Feeds helper
$CI = &get_instance();
$CI->load->helper('feeds');

/// Standardized feed frame library class.
class Frame_feed extends FeedView
{
	// So toplinks can be "set"
	function SetData($a = null, $b = null)
	{
	}

	function SetFeedTitle($Title)
	{
		$this->Channel()->SetTitle($Title);
	}
}

?>
