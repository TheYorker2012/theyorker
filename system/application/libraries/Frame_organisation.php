<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_organisation.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Extension of public frame.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frame_public');

/// Main organisation frame library class.
class Frame_organisation extends Frame_public
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct();
		$this->SetView('frames/organisation_frame.php');
	}
}

?>