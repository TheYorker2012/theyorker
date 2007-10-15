<?php

/**
 * @file libraries/Notifications.php
 * @brief For notification classes.
 * @author James Hogan (jh559)
 *
 * A notification is an event that requires the users attention.
 */

$CI = & get_instance();
$CI->load->library('frames');

/// A notification view class
abstract class Notification extends FramesView
{
	/// Construct the notification using notification type information.
	/**
	 * @param $TypeInfo &array Associative array of type information:
	 *  'class','summary','description','actions'
	 */
	function __construct(& $TypeInfo, $Keys)
	{
		parent::__construct('calendar/notification');
		$this->mDataArray['Type'] = &$TypeInfo;
		$this->mDataArray['Keys'] = $Keys;
		$this->mDataArray['Custom'] = '';
	}
	
	/// Get the notification's identifying keys.
	function GetKeys()
	{
		return $this->mDataArray['Keys'];
	}
	
	/// Find if an action is valid for this notification.
	function ValidAction($Action)
	{
		return isset($this->mDataArray['Type']['actions'][$Action]);
	}
	
	/// Perform an action on this notification.
	/**
	 * @param $Action string Action key.
	 * @return array Messages in the usual message array format.
	 */
	abstract function PerformAction($Action);
}

/// Main library class.
class Notifications
{
	/// Static list of notification types.
	static $Types = array();
	
	/// Register a loaded notification type.
	static function RegisterNotificationType($Name)
	{
		self::$Types[$Name] = true;
	}
	
	/// Find whether a notification type is loaded.
	static function NotificationTypeRegistered($Name)
	{
		return isset(self::$Types[$Name]);
	}
	
	/// Create a new notification by type, checking its valid!
	/**
	 * @param $TypeId string The registered type id of the notification type.
	 * @param $Keys array[string/int] List of identifying keys.
	 * @return $TypeId,NULL New object of type typeid or NULL if not valid.
	 */
	static function Create($TypeId, $Keys, $Extra = NULL)
	{
		if (self::NotificationTypeRegistered($TypeId)) {
			return new $TypeId($Keys, $Extra);
		} else {
			return NULL;
		}
	}
	
	/// Check for notification actions and perform them.
	/**
	 * @return Array of messages to be printed.
	 */
	static function CheckNotificationActions()
	{
		$messages = array();
		if (isset($_POST['calnot']) &&
			isset($_POST['calnot']['type']) &&
			isset($_POST['calnot']['keys']))
		{
			// Something relating to notifications
			$type = $_POST['calnot']['type'];
			$keys = $_POST['calnot']['keys'];
			if (isset($_POST['calnot']['action'])) {
				// An action has been taken on a notification.
				$action = $_POST['calnot']['action'];
				$notification = Notifications::Create($type, $keys);
				if (NULL !== $notification) {
					if ($notification->ValidAction($action)) {
						$messages = $notification->PerformAction($action);
					} else {
						$messages['error'][] = 'Unrecognised notification action: '.htmlentities($action, ENT_QUOTES, 'utf-8');
					}
				} else {
					$messages['error'][] = 'Unrecognised notification type: '.htmlentities($type, ENT_QUOTES, 'utf-8');
				}
			}
		}
		return $messages;
	}
}


?>