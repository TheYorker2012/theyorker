<?php

/**
 * @file libraries/Calendar_notifications.php
 * @brief Calendar notification classes.
 * @author James Hogan (jh559)
 */

$CI = & get_instance();
$CI->load->library('notifications');
$CI->load->model('calendar/events_model');

/// A notification for when an event the user is attending is cancelled.
class NotificationCancelledEvent extends Notification
{
	/// The notification type information.
	public static $notTypeInfo = array(
		'id'			=> 'NotificationCancelledEvent',
		'class'			=> 'error',
		'summary'		=> 'Cancelled Event',
		'description'	=> 'An event which you are attending has been cancelled:',
		'actions'		=> array(
			'Dismiss'		=> 'Dismiss this notification',
		),
	);
	
	/// Default constructor
	/**
	 * @param $Keys array with keys 'userid', 'occid'.
	 */
	function __construct($Keys, $Extra = NULL)
	{
		parent::__construct(self::$notTypeInfo, $Keys);
		
		if (NULL !== $Extra) {
			$start = new Academic_time($Extra['start_time']);
			$when = $start->Format('%D');
			if ($Extra['time_associated']) {
				$when = $start->Format('%T').", $when";
			}
			$CI = & get_instance();
			$this->SetData('Custom',
				'<p>Summary: <strong><a href="'.site_url($Extra['link']). $CI->uri->uri_string().'">'.
				htmlentities($Extra['name'], ENT_QUOTES, 'utf-8').'</a></strong><br />'.
				'<div class="Date" style="display:inline;">'.$when.'</div></p>'
			);
		}
	}
	
	/// Perform an action on this notification.
	/**
	 * @note The 'Dismiss' @a $Action sets the attendence to the occurrence to "no".
	 */
	function PerformAction($Action)
	{
		$messages = array();
		switch ($Action) {
			case 'Dismiss':
				// Check the keys exist.
				$keys = $this->GetKeys();
				if (isset($keys['occid']) && isset($keys['userid'])) {
					$CI = & get_instance();
					if ($keys['userid'] == $CI->events_model->GetActiveEntityId()) {
						$changes = $CI->events_model->DismissCancelledOccurrence($keys['occid']);
						if (!$changes) {
							$messages['information'][] = 'Notification already dismissed.';
						} else {
							$messages['success'][] = 'Notification dismissed.';
						}
					} else {
						$messages['error'][] = 'Notification refers to a different user and could not be dismissed.';
					}
				} else {
					$messages['error'][] = 'Could not dismiss notification, Incomplete identifier.';
				}
				break;
				
			default:
				$messages['error'][] = 'Invalid action on notification.';
				break;
		}
		return $messages;
	}
}
// Register this notification type.
Notifications::RegisterNotificationType(NotificationCancelledEvent::$notTypeInfo['id']);

/// Dummy class.
class Calendar_notifications
{
	/// Get calendar notifications for the current user.
	static function GetNotifications($paths)
	{
		$CI = & get_instance();
		$occurrence_alerts = $CI->events_model->GetOccurrenceAlerts();
		$notifications = array();
		foreach ($occurrence_alerts as $occurrence) {
			$occurrence['link'] = $paths->OccurrenceRawInfo(0, $occurrence['event_id'], $occurrence['occurrence_id']);
			$notifications[] = Notifications::Create('NotificationCancelledEvent',array(
				'userid' => $CI->events_model->GetActiveEntityId(),
				'occid' => $occurrence['occurrence_id'],
			), $occurrence);
		}
		return $notifications;
	}
}

?>