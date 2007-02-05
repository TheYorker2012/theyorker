<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Messages.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Library for managing messages.
 */
 
// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

/// Message class.
class Message extends FramesView
{
	function __construct($Class, $Message = '')
	{
		if (is_array($Class)) {
			$Message = $Class['text'];
			$Class = $Class['class'];
		}
		parent::__construct('general/message.php');
		$this->mDataArray['class'] = $Class;
		$this->mDataArray['text'] = $Message;
	}
	
	function ToArray()
	{
		return array(	'class' => $this->mDataArray['class'],
						'text'  => $this->mDataArray['text']);
	}
}

/// Messages library class.
class Messages
{
}

?>