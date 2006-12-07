<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_public.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Shortcut library for using the public frame.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

/**
 * @brief Main public frame library class.
 *
 * Automatically loads the Frames library.
 *
 * Load the library from the controller constructor:
 * @code
 *	// Load the public frame
 *	$this->load->library('frame_public');
 * @endcode
 *
 * You can then refer to it as $this->frame_public in order to SetContent().
 * The view can then be loaded using Load() (including the content view
 *	specified using SetContent()).
 *
 * Example of usage from a controller function:
 * @code
 *	// Set up the subview
 *	$listings_view = $this->frames->view('listings/listings', $data);
 *	
 *	// Set up the public frame
 *	$this->frame_public->SetExtraHead($extra_head);
 *	$this->frame_public->SetContent($listings_view);
 *	
 *	// Load the public frame view (which will load the content view)
 *	$this->frame_public->Load();
 * @endcode
 */
class Frame_public extends FramesFrame
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::__construct('frames/public_frame.php');
	}
	
	/**
	 * @brief Set the extra code to go in the page header.
	 * @param $ExtraHead string Extra code to go in the page header.
	 */
	function SetExtraHead($ExtraHead)
	{
		$this->SetData('extra_head', $ExtraHead);
	}
}

?>