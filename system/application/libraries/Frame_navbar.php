<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_navbar.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Shortcut library for using navbar frames.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');
$CI->load->library('navigation_bar');

/// Navbar frame library class.
/**
 * Automatically loads the Frames library.
 */
class FrameNavbar extends FramesFrame
{
	/// Default constructor.
	function __construct($ViewFile)
	{
		parent::__construct($ViewFile);
		$navbar = new NavigationBar();
		$this->SetContent($navbar,'navbar');
	}
	
	/// Set the page in use (determines the navigation bar highlighting).
	/**
	 * @param $Page string Page name.
	 */
	function SetPage($Page)
	{
		$this->mDataArray['content']['navbar']->SetSelected($Page);
	}
	
	/// Get the navbar
	function GetNavbar()
	{
		return $this->mDataArray['content']['navbar'];
	}
}

class Frame_navbar
{
}

?>