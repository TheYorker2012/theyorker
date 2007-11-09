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
	/// disable adverts for the navbar frame
	protected $mHasAdverts = FALSE;
	
	/// Default constructor.
	function __construct($ViewFile)
	{
		parent::__construct($ViewFile);
	}
	
	/// Ensure that the navbar exists
	private function CheckNavbar()
	{
		if (!isset($this->mDataArray['content']['navbar'])) {
			$navbar = new NavigationBar();
			$this->SetContent($navbar,'navbar');
		}
	}
	
	/// Set the page in use (determines the navigation bar highlighting).
	/**
	 * @param $Page string Page name.
	 * @param $Name int Optional identifier.
	 */
	function SetPage($Page, $Name = NULL)
	{
		if (NULL === $Name) {
			$this->CheckNavbar();
			$this->mDataArray['content']['navbar']->SetSelected($Page);
		} elseif (isset($this->mDataArray['content']['navbars'][$Name])) {
			$this->mDataArray['content']['navbars'][$Name]->SetSelected($Page);
		}
	}
	
	/// Get the navbar
	/**
	 * @param $Name int Optional identifier.
	 * @return NavigationBar The navbar object.
	 * @note if the navbar does not exist it is created.
	 */
	function GetNavbar($Name = NULL)
	{
		if (NULL === $Name) {
			$this->CheckNavbar();
			return $this->mDataArray['content']['navbar'];
		} elseif (isset($this->mDataArray['content']['navbars'][$Name])) {
			return $this->mDataArray['content']['navbars'][$Name];
		} else {
			return $this->AppendNewNavbar($Name);
		}
	}
	
	/// Create a new navbar and append to navbar list.
	/**
	 * @param $Name int Optional identifier.
	 *	If @a $Name is unspecified, the next available integer id will be used.
	 * @return NavigationBar The navbar object
	 */
	function AppendNewNavbar($Name = NULL)
	{
		$new_navbar = new NavigationBar();
		if (NULL === $Name) {
			$this->mDataArray['content']['navbars'][] = & $new_navbar;
		} else {
			$this->mDataArray['content']['navbars'][$Name] = & $new_navbar;
		}
		return $new_navbar;
	}
	
}

class Frame_navbar
{
}

?>