<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_messages.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Shortcut library for using the messages frame.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

/// Message frame library class.
/**
 * Automatically loads the Frames library.
 */
class Frame_messages extends FramesFrame
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('frames/messages_frame.php');
		
		$this->mDataArray['messages'] = array();
	}
	
	/// Add a message.
	/**
	 * @param $Text string The text of the message.
	 *
	 * @todo Consider what other parameters a message may require, e.g:
	 *	- Type code: operation failed, invalid parameter, permission denied
	 */
	function AddMessage($Text)
	{
		$this->mDataArray['messages'][] = array(
				'text' => $Text
			);
	}
	
	/// Get the number of messages.
	function NumMessages()
	{
		return count($this->mDataArray['messages']);
	}
	
	/// Use code igniter to load the content view.
	/**
	 * If there are no messages to be displayed, the messages frame will not be
	 *	used, its content will be loaded directly.
	 */
	function Load()
	{
		if ($this->NumMessages() === 0) {
			$this->mDataArray['content'][0]->Load();
		} else {
			parent::Load();
		}
	}
}

?>