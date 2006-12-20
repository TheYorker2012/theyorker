<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_directory.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Shortcut library for using the directory frame.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

/// Directory frame library class.
/**
 * Automatically loads the Frames library.
 *
 * Load the library from the controller constructor (you'll probably want to
 *	load frame_public as well:
 * @code
 *	// Load the public frame
 *	$this->load->library('frame_directory');
 * @endcode
 *
 * You can then refer to it as $this->frame_directory in order to SetContent().
 * The view can then be loaded using Load() (including the content view
 *	specified using SetContent()) or by setting it as content in another frame
 *	such as frame_public and loading that.
 *
 * Example of usage from a controller function:
 * @code
 *	// Set up the directory view
 *	$directory_view = $this->frames->view('directory/directory', $data);
 *	
 *	// Set up the directory frame
 *	$this->frame_directory->SetPage('members');
 *	$this->frame_directory->SetOrganisation('The Yorker');
 *	$this->frame_directory->SetContent($directory_view);
 *	
 *	// Set up the public frame
 *	$this->frame_public->SetTitle($page_title);
 *	$this->frame_public->SetExtraHead($extra_head);
 *	$this->frame_public->SetContent($this->frame_directory);
 *	
 *	// Load the public frame view (which will load the directory frame which
 *	// will load the contents)
 *	$this->frame_public->Load();
 * @endcode
 */
class Frame_directory extends FramesFrame
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('directory/directory_frame.php');
	}
	
	/// Set the page in use (determines the navigation bar highlighting).
	/**
	 * @param $Page string Page name.
	 */
	function SetPage($Page)
	{
		$this->SetData('page', $Page);
	}
	
	/// Set the organisation data.
	/**
	 * @param $OrganisationData array Organisation data with the following fields:
	 *	- 'name'
	 */
	function SetOrganisation($OrganisationData)
	{
		$this->SetData('organisation', $OrganisationData);
	}
}

?>