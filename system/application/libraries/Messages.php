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
	function __construct($Class, $Message)
	{
		parent::__construct('general/message.php');
		$this->SetData('class', $Class);
		$this->SetData('text', $Message);
	}
}

/// Messages library class.
class Messages
{
}

?>