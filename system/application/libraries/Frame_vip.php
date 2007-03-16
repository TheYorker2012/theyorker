<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_vip.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Extension of public frame.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frame_public');

/// Main vip frame library class.
class Frame_vip extends Frame_public
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct();
		$this->SetView('frames/vip_frame.php');
		
		// Set the data about the vip to display in the frame.
		$CI = &get_instance();
		$this->SetData('vipinfo', array(
			'name'			=> $CI->user_auth->firstname . ' ' . $CI->user_auth->surname,
			'organisation'	=> VipOrganisationName(TRUE),
		));
	}
}

?>