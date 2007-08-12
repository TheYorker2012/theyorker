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
/**
 * This is the class that gets sent to the view to get loaded.
 */
class Message extends FramesView
{
	/// Primary constructor.
	/**
	 * @param $Class
	 *	- string Message type.
	 *	- array Values in array as returned by ToArray.
	 * @param $Message string message text.
	 */
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
	
	/// Return the message in the form of an array readable by __construct.
	/**
	 * @return array In a format that can be stored in sessions and accepted
	 *	by __construct.
	 */
	function ToArray()
	{
		return array(	'class' => $this->mDataArray['class'],
						'text' => $this->mDataArray['text']	);
	}
}

/// Messages library class.
/**
 * Manages message queue and across sessions.
 */
class Messages
{
	/// array Messages in queue
	protected $mMessages;
	
	/// Default constructor
	function __construct()
	{
		$this->mMessages = array();
		
		if (array_key_exists('messages',$_SESSION)) {
			foreach ($_SESSION['messages'] as $message) {
				$message = new Message($message);
				$this->mMessages[] = $message;
			}
		}
		// The messages in the session are cleared at load time.
	}
	
	/// Add a message to the queue.
	/**
	 * @param $Type string/Message Message type or Message class.
	 * @param $Message string Message (if $Type isn't Message class).
	 * @param $Persistent bool Whether the message should be shown on the next
	 *	page if this page is never displayed.
	 */
	function AddMessage($Type, $Message = '', $Persistent = TRUE)
	{
		if (is_string($Type)) {
			$message = new Message($Type,$Message);
		} else {
			$message = $Type;
		}
		$this->mMessages[] = $message;
		
		if ($Persistent) {
			// Also store in session
			if (!array_key_exists('messages',$_SESSION)) {
				$_SESSION['messages'] = array();
			}
			$_SESSION['messages'][] = $message->ToArray();
		}
	}
	
	/// Add multiple messages to the queue.
	/**
	 * @param $Messages array[array($message[, $type, [$persistent]])] Messages.
	 * @param $DefaultType string
	 * @param $DefaultPersistence bool
	 */
	function AddMessages($Messages, $DefaultType = 'information', $DefaultPersistence = TRUE)
	{
		assert('is_array($Messages)');
		foreach (array('error', 'warning', 'success', 'information') as $type) {
			if (array_key_exists($type, $Messages)) {
				// Handle if the array is first indexed by type
				foreach ($Messages[$type] as $message) {
					if (!is_array($message)) {
						$message = array($message);
					}
					$args = count($message);
					if ($args === 1) {
						$this->AddMessage($type, $message[0], $DefaultPersistence);
					} elseif ($args > 1) {
						$this->AddMessage($type, $message[0], $message[1]);
					}
				}
			}
		}
		foreach ($Messages as $key => $message) {
			if (is_int($key)) {
				if (!is_array($message)) {
					$message = array($message);
				}
				$args = count($message);
				if ($args === 1) {
					$this->AddMessage($DefaultType, $message[0], $DefaultPersistence);
				} elseif ($args === 2) {
					$this->AddMessage($message[1], $message[0], $DefaultPersistence);
				} elseif ($args > 2) {
					$this->AddMessage($message[1], $message[0], $message[2]);
				}
			}
		}
	}
	
	/// Add a variable dump in a message to the queue.
	/**
	 * @param $Name string Name of variable.
	 * @param $Variable var Variable to dump.
	 */
	function AddDumpMessage($Name,$Variable)
	{
		$CI = &get_instance();
		$this->AddMessage('information', '<h4>Variable: ' . $Name . ' in '.$CI->uri->uri_string().'</h4>'.
			'<pre>'.htmlentities(var_export($Variable,true), ENT_QUOTES, 'UTF-8').'</pre>');
	}

	/// Clear the queue of persistent messages.
	function ClearQueue()
	{
		if (array_key_exists('messages',$_SESSION)) {
			unset($_SESSION['messages']);
		}
		$this->mMessages = array();
	}
	
	/// Get the number of messages.
	/**
	 * @return int Number of messages queued.
	 */
	function NumMessages()
	{
		return count($this->mMessages['messages']);
	}
	
	/// Get the messages array
	/**
	 * @return array of Message Queues messages.
	 */
	function GetMessages()
	{
		return $this->mMessages;
	}
	
}

?>