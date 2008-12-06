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
				xml_escape($Extra['name']).'</a></strong><br />'.
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

/// A notification for when an event is submitted to an open calendar.
class NotificationEventSubmission extends Notification
{
	/// The notification type information.
	public static $notTypeInfo = array(
		'id'			=> 'NotificationEventSubmission',
		'class'			=> 'warning',
		'summary'		=> 'Event Submission',
		'description'	=> 'An event has been submitted for inclusion on this calendar:',
		'actions'		=> array(
			'Accept'		=> 'Add the event to this calendar',
			'Reject'		=> 'Reject the event',
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
			$CI = & get_instance();
			$this->SetData('Custom',
				'<p>Summary: <strong><a href="'.site_url($Extra['link']). $CI->uri->uri_string().'">'.
				xml_escape($Extra['name']).'</a></strong>'
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
		$CI = & get_instance();
		$keys = $this->GetKeys();
		switch ($Action) {
			case 'Accept':
				$rows_affected = $CI->events_model->AcceptEventSubmission($keys['eventid']);
				if ($rows_affected == 0) {
					$CI->messages->AddMessage('error', 'Submission could not be accepted.');
				}
				else {
					$CI->messages->AddMessage('success', 'Event has been added to calendar.');
				}
				break;

			case 'Reject':
				$rows_affected = $CI->events_model->RejectEventSubmission($keys['eventid']);
				if ($rows_affected == 0) {
					$CI->messages->AddMessage('error', 'Submission could not be rejected.');
				}
				else {
					$CI->messages->AddMessage('success', 'Submission has been rejected.');
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
Notifications::RegisterNotificationType(NotificationEventSubmission::$notTypeInfo['id']);

/// Dummy class.
class Calendar_notifications
{
	/// Get calendar notifications for the current user.
	static function GetNotifications($paths)
	{
		$CI = & get_instance();
		$notifications = array();

		$occurrence_alerts = $CI->events_model->GetOccurrenceAlerts();
		foreach ($occurrence_alerts as $occurrence) {
			$occurrence['link'] = $paths->OccurrenceRawInfo(0, $occurrence['event_id'], $occurrence['occurrence_id']);
			$notifications[] = Notifications::Create('NotificationCancelledEvent',array(
				'userid' => $CI->events_model->GetActiveEntityId(),
				'occid' => $occurrence['occurrence_id'],
			), $occurrence);
		}

		$event_submissions = $CI->events_model->GetEventSubmissions();
		foreach ($event_submissions as $event_submission) {
			$event_submission['link'] = $paths->EventRawInfo(0, $event_submission['event_id']);
			$notifications[] = Notifications::Create('NotificationEventSubmission', array(
				'userid' => $CI->events_model->GetActiveEntityId(),
				'eventid' => $event_submission['event_id'],
			), $event_submission);
		}

		return $notifications;
	}
}

?>
