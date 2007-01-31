<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_public.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Shortcut library for using the public frame.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');
$CI->load->library('messages');
$CI->load->library('frame_navbar');
$CI->load->model('pages_model');

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
 *	$calendar_view = $this->frames->view('calendar/calendar', $data);
 *	
 *	// Set up the public frame
 *	$this->frame_public->SetTitle($page_title);
 *	$this->frame_public->SetExtraHead($extra_head);
 *	$this->frame_public->SetContent($calendar_view);
 *	
 *	// Load the public frame view (which will load the content view)
 *	$this->frame_public->Load();
 * @endcode
 */
class Frame_public extends FrameNavbar
{
	/// Has the title been set yet
	private $mTitleSet;
	
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::__construct('frames/public_frame.php');
		
		$this->mDataArray['messages'] = array();
		$this->mDataArray['description'] = '';
		$this->mDataArray['keywords'] = '';
		$this->mTitleSet = FALSE;
		
		$this->mDataArray['login'] = array(
				'logged_in' => FALSE,
			);
		$CI = &get_instance();
		$CI->load->library('user_auth');
		if ($CI->user_auth->isLoggedIn) {
			$this->mDataArray['login']['logged_in'] = TRUE;
			$this->mDataArray['login']['username'] = $entity_id = $CI->user_auth->username;
		}
	}
	
	/**
	 * @brief Set the page title.
	 * @param $Title string Page title.
	 */
	function SetTitle($Title)
	{
		$this->SetData('title', $Title);
		$this->mTitleSet = TRUE;
	}
	
	/**
	 * @brief Set the page title parameters.
	 * @param $Parameters array[string=>string] Array of parameters to be
	 *	replaced.
	 */
	function SetTitleParameters($Parameters = array())
	{
		$CI = &get_instance();
		$this->SetData('title', $CI->pages_model->GetTitle($Parameters));
		$this->mTitleSet = TRUE;
	}
	
	/// Add keywords to the page.
	/**
	 * @param $Keywords string Comma seperated keywords.
	 */
	function AddKeywords($Keywords)
	{
		if (!empty($this->mDataArray['keywords'])) {
			$this->mDataArray['keywords'] .= ',';
		}
		$this->mDataArray['keywords'] .= $Keywords;
	}
	
	/// Add description to the page.
	/**
	 * @param $Description string Description.
	 */
	function AddDescription($Description)
	{
		if (!empty($this->mDataArray['description'])) {
			$this->mDataArray['description'] .= '. ';
		}
		$this->mDataArray['description'] .= $Description;
	}
	
	/**
	 * @brief Set the extra code to go in the page header.
	 * @param $ExtraHead string Extra code to go in the page header.
	 */
	function SetExtraHead($ExtraHead)
	{
		$this->SetData('extra_head', $ExtraHead);
	}
	
	/// Add a message to the page.
	/**
	 * @param $Message FramesView Message class (see Messages.php library).
	 */
	function AddMessage($Message)
	{
		$this->mDataArray['messages'][] = $Message;
	}
	
	/// Get the number of messages.
	function NumMessages()
	{
		return count($this->mDataArray['messages']);
	}
	
	/// Load the frame.
	function Load()
	{
		$CI = &get_instance();
		if ($CI->pages_model->PageCodeSet()) {
			if (!$this->mTitleSet) {
				$this->SetTitleParameters();
			}
			$this->AddDescription($CI->pages_model->GetDescription());
			$this->AddKeywords($CI->pages_model->GetKeywords());
		}
		parent::Load();
	}
}

?>