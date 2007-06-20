<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_facebook.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Extension of public frame for facebook app.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frame_public');

/// Main office frame library class.
class Frame_facebook extends Frame_public
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct();
		$this->SetView('frames/facebook_frame.php');
	}
}

?>