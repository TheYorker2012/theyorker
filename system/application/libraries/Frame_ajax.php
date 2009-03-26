<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_ajax.php
 * @author James Hogan (james_hogan@theyorker.co.uk)
 * @brief AJAX XML frame.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');
$CI->load->library('messages');

/// Standardized AJAX frame library class.
class Frame_ajax extends FramesFrame
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('frames/ajax_frame.php');
		$this->mDataArray['errors'] = array();
		$this->mDataArray['xmlContent'] = array('_tag' => 'null');
	}

	/// Add an error to display.
	function Error($error)
	{
		$this->mDataArray['errors'][] = $error;
	}

	/// Set xml data structure.
	function SetXml($root)
	{
		$this->mDataArray['xmlContent'] = $root;
	}
	
	/// Load the frame.
	function Load()
	{
		$CI = &get_instance();
		
		// write the output
		parent::Load();
		unset($this->mDataArray['messages']);
		$CI->messages->ClearQueue();
	}
}

?>
