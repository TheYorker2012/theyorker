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
abstract class Message extends FramesView
{
	function __construct($Title = '', $Message = '')
	{
		parent::__construct('general/message.php');
		$this->SetMessageClass('message');
		$this->SetMessageType($Title);
		$this->SetMessageDescription($Message);
	}
	
	function SetMessageClass($MessageClass)
	{
		$this->SetData('class', $MessageClass);
	}
	
	function SetMessageType($MessageType, $Parameters = '')
	{
		$this->SetData('type', $MessageType . ': ' . $Parameters);
	}
	
	function SetMessageDescription($MessageDescription)
	{
		$this->SetData('text', $MessageDescription);
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


/// Some input was invalid.
class InputInvalidMsg extends ErrorMsg
{
	function __construct($Parameters, $Description = '')
	{
		parent::__construct();
		$this->SetMessageType('Invalid input', $Parameters);
		$this->SetMessageDescription('There was a problem with the input. ' . $Description);
	}
}

/// Some input was missing.
class InputMissingMsg extends ErrorMsg
{
	function __construct($Parameters, $Description = '')
	{
		parent::__construct();
		$this->SetMessageType('Missing input', $Parameters);
		$this->SetMessageDescription('The specified fields are required. ' . $Description);
	}
}

/// Specified page not found.
class PageNotFoundMsg extends WarningMsg
{
	function __construct()
	{
		parent::__construct();
		$this->SetMessageType('Not Found');
		$this->SetMessageDescription('The specified page was not found.');
	}
}

/// Don't have permission.
class PermissionDeniedMsg extends ErrorMsg
{
	function __construct()
	{
		parent::__construct();
		$this->SetMessageType('Permission denied');
		$this->SetMessageDescription('You don\'t have permission to use this page.');
	}
}

/// Being logged in is required.
class LoginRequiredMsg extends InformationMsg
{
	function __construct()
	{
		parent::__construct();
		$this->SetMessageType('Login required');
		$this->SetMessageDescription('You need to log in to be able to use this page.');
	}
}

/// Messages library class.
class Messages
{
}

?>