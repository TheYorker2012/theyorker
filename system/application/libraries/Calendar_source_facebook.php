<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_source_facebook.php
 * @brief Calendar source for facebook events.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library calendar_backend)
 *
 * Event source class for obtaining facebook events using the facebook api.
 *
 * @version 18-04-2007 James Hogan (jh559)
 *	- Created.
 */

/// Calendar source for facebook events.
class CalendarSourceFacebook extends CalendarSource
{
	/// Default constructor.
	function __construct($SourceId)
	{
		parent::__construct();
		
		$this->SetSourceId($SourceId);
		$this->mName = 'Facebook';
		//$this->mCapabilities[] = 'attend';
	}
	
	protected function ProfileUrl($uid)
	{
		return 'http://www.facebook.com/profile.php?id='.$uid;
	}
	
	protected function EventUrl($eid)
	{
		return 'http://www.facebook.com/event.php?eid='.$eid;
	}
	
	/// Fetch the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 * @param $Event identifier Source event identitier.
	 */
	protected function _FetchEvent(&$Data, $Event)
	{
		$CI = & get_instance();
		if (!$CI->facebook->InUse()) return;
		
		if ('e' === substr($Event, 0, 1)) {
			$Event = substr($Event, 1);
			if (is_numeric($Event)) {
				$this->GetEvents($Data, (int)$Event);
			}
		} elseif ('bd' === substr($Event, 0, 2)) {
			$Event = substr($Event, 2);
			list($uid, $age) = explode('.', $Event);
			if (is_numeric($uid) && is_numeric($age)) {
				$this->GetBirthdays($Data, (int)$uid, (int)$age);
			}
		}
	}
	
	/// Fedge the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	protected function _FetchEvents(&$Data)
	{
		if ($this->GroupEnabled('event')) {
			$CI = & get_instance();
			if (!$CI->facebook->InUse()) return;
			
			$this->GetEvents($Data);
			$this->GetBirthdays($Data);
		}
	}
	
	protected function GetEvents(&$Data, $EventId = NULL)
	{
		$CI = & get_instance();
		
		try {
			// Get events in the range.
			if (NULL === $EventId) {
				$event_range = $this->mEventRange;
			} else {
				$event_range = array(NULL, NULL);
			}
			$events = $CI->facebook->Client->events_get(
				$CI->facebook->Uid,
				$EventId,
				$event_range[0],
				$event_range[1],
				null
			);
			
			if (!empty($events)) {
				foreach ($events as $event) {
					// get the list of members, so we can see if the user is attending.
					$members = $CI->facebook->Client->events_getMembers($event['eid']);
					$attending = NULL;
					if (is_array($members['attending']) && in_array($CI->facebook->Uid, $members['attending'])) {
						$attending = TRUE;
						if (!$this->GroupEnabled('rsvp')) {
							continue;
						}
					} elseif (is_array($members['declined']) && in_array($CI->facebook->Uid, $members['declined'])) {
						$attending = FALSE;
						if (!$this->GroupEnabled('hide')) {
							continue;
						}
					} else {
						$attending = NULL;
						if (!$this->GroupEnabled('show')) {
							continue;
						}
					}
					/*echo '<pre align="left">';
					print_r($event);
					print_r($members);
					echo '</pre>';*/
					
					$event_obj = & $Data->NewEvent();
					$occurrence = & $Data->NewOccurrence($event_obj);
					$event_obj->SourceEventId = 'e'.$event['eid'];
					$event_obj->Name = $event['name'];
					$event_obj->Description = str_replace("\n",'<br />'."\n", $event['description']);
					$event_obj->Description .= '<br /><a href="'.$this->EventUrl($event['eid']).'" target="_blank">This event on Facebook</a>';
					$event_obj->LastUpdate = $event['update_time'];
					if (!empty($event['pic'])) {
						$event_obj->Image = $event['pic'];
					}
					$occurrence->SourceOccurrenceId = $event_obj->SourceEventId;
					$occurrence->LocationDescription = $event['location'];
					$occurrence->StartTime = new Academic_time((int)$event['start_time']);
					$occurrence->EndTime = new Academic_time((int)$event['end_time']);
					$occurrence->TimeAssociated = TRUE;
					$occurrence->UserAttending = $attending;
					unset($occurrence);
					unset($event_obj);
				}
			}
		} catch (FacebookRestClientException $ex) {
			$CI->facebook->HandleException($ex);
		}
	}
	
	function GetBirthdays(&$Data, $UserId = NULL, $Age = NULL)
	{
		$CI = & get_instance();
		
		$event_range = $this->mEventRange;
		if (NULL === $range[0]) {
			$event_range[0] = time();
		}
		if (NULL === $range[1]) {
			$event_range[1] = strtotime('+1year');
		}
		
		try {
			// Get friends with birthdays in the range.
			$query = 'SELECT uid, name, birthday, profile_update_time, pic FROM user '.
				'WHERE (uid IN (SELECT uid2 FROM friend WHERE uid1 = '.$CI->facebook->Uid.') '.
				'OR uid = '.$CI->facebook->Uid.')';
			$specific = (	NULL !== $UserId &&
							NULL !== $Age &&
							$Age >= 0	);
			if ($specific) {
				$query .= ' AND uid = '.$UserId;
			} else {
				$year = date('Y', $event_range[0]);
			}
			$birthdays = $CI->facebook->Client->fql_query($query);
			
			$yesterday = strtotime('-1day',$event_range[0]);
			foreach ($birthdays as $birthday) {
				if (preg_match('/([A-Z][a-z]+ \d\d?, )(\d\d\d\d)/', $birthday['birthday'], $matches)) {
					if ($specific) {
						$year = $matches[2] + $Age;
						if ($year >= 1970 && $year < 2037) {
							$start_age = $Age;
							$dob = strtotime($matches[1].$year);
							$event_range[1] = $dob+1;
							$yesterday = $dob;
						} else {
							continue;
						}
					} else {
						$start_age = $year - $matches[2];
						$dob = strtotime($matches[1].$year);
					}
					
					while ($dob < $event_range[1]) {
						if ($dob >= $yesterday) {
							$event_obj = & $Data->NewEvent();
							$occurrence = & $Data->NewOccurrence($event_obj);
							$event_obj->SourceEventId = 'bd'.$birthday['uid'].'.'.$start_age;
							$event_obj->Name = 'Birthday '.$start_age.': '.$birthday['name'];
							$event_obj->Description = '<a href="'.$this->ProfileUrl($birthday['uid']).'" target="_blank">'.$birthday['name'].'\'s profile</a>';
							$event_obj->LastUpdate = (int)$birthday['profile_update_time'];
							if (!empty($birthday['pic'])) {
								$event_obj->Image = $birthday['pic'];
							}
							$occurrence->SourceOccurrenceId = $event_obj->SourceEventId;
							$occurrence->LocationDescription = '';
							$occurrence->StartTime = new Academic_time($dob);
							$occurrence->EndTime = $occurrence->StartTime->Adjust('1day');
							$occurrence->TimeAssociated = FALSE;
							$occurrence->UserAttending = 0;
							unset($occurrence);
							unset($event_obj);
						}
						$dob = strtotime('1year', $dob);
						++$start_age;
					}
				}
			}
		} catch (FacebookRestClientException $ex) {
			$CI->facebook->HandleException($ex);
		}
	}
	
	/// Get list of known attendees.
	/**
	 * @param $Occurrence Occurrence identifier.
	 * @return array Attendees, defined by fields:
	 *	- 'name' string Name of attendee.
	 *	- 'link' string URL about user.
	 *	- 'entity_id' int Entity id if known.
	 *	- 'attend' bool,NULL TRUE for attending, FALSE for not attending, NULL for maybe.
	 */
	function GetOccurrenceAttendanceList($Occurrence)
	{
		if ('e' === substr($Occurrence, 0, 1)) {
			$event = substr($Occurrence, 1);
			if (is_numeric($event)) {
				// get the list of members.
				$CI = & get_instance();
				try {
					$fb_attendings = $CI->facebook->Client->fql_query(
						'SELECT uid, rsvp_status '.
						'FROM event_member '.
						'WHERE eid = '.(int)$event
					);
					$fb_attendees = $CI->facebook->Client->fql_query(
						'SELECT uid, name '.
						'FROM user '.
						'WHERE uid IN (SELECT uid, rsvp_status '.
									'FROM event_member '.
									'WHERE eid = '.(int)$event.')'
					);
					$members = $CI->facebook->Client->events_getMembers((int)$event);
					$attendees = array();
					foreach ($fb_attendees as $attendee) {
						$attendees[(int)$attendee['uid']] = array(
							'name' => $attendee['name'],
							'link' => $this->ProfileUrl($attendee['uid']),
						);
					}
					foreach ($fb_attendings as $attending) {
						$attendees[(int)$attending['uid']]['attend'] = $attending['rsvp_status'];
					}
					return array_values($attendees);
				} catch (FacebookRestClientException $ex) {
					$CI->facebook->HandleException($ex);
				}
			}
		}
		return parent::GetOccurrenceAttendanceList($Occurrence);
	}
}



/// Dummy class
class Calendar_source_facebook
{
	/// Default constructor.
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->library('facebook');
		
		if ($CI->facebook->InUse()) {
			$CI->facebook->Connect();
		}
	}
}

?>