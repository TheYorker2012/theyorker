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

/// Success message class
class SuccessMsg extends Message
{
	function __construct($Title = '', $Message = '')
	{
		parent::__construct($Title, $Message);
		$this->SetMessageClass('success');
	}
}

/// Information message class
class InformationMsg extends Message
{
	function __construct($Title = '', $Message = '')
	{
		parent::__construct($Title, $Message);
		$this->SetMessageClass('information');
	}
}

/// Warning message class
class WarningMsg extends Message
{
	function __construct($Title = '', $Message = '')
	{
		parent::__construct($Title, $Message);
		$this->SetMessageClass('warning');
	}
}

/// Error message class.
class ErrorMsg extends Message
{
	function __construct($Title = '', $Message = '')
	{
		parent::__construct($Title, $Message);
		$this->SetMessageClass('error');
	}
}

/// Messages library class.
class Messages
{
}

?>