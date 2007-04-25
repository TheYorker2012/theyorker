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
	
	/// Fetch the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 * @param $Event identifier Source event identitier.
	 */
	protected function _FetchEvent(&$Data, $Event)
	{
		$CI = & get_instance();
		if (!$CI->facebook->InUse()) return;
		
		$this->GetEvents($Data, $Event);
	}
	
	/// Fedge the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	protected function _FetchEvents(&$Data)
	{
		$CI = & get_instance();
		if (!$CI->facebook->InUse()) return;
		
		$this->GetEvents($Data);
		$this->GetBirthdays($Data);
	}
	
	protected function GetEvents(&$Data, $EventId = NULL)
	{
		$CI = & get_instance();
		
		try {
			// Get events in the range.
			if (NULL === $EventId) {
				$range = $this->mRange;
			} else {
				$range = array(NULL, NULL);
			}
			$events = $CI->facebook->Client->events_get(
				$CI->facebook->Uid,
				$EventId,
				$range[0],
				$range[1],
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
					$event_obj->Description .= '<br /><a href="http://www.facebook.com/event.php?eid='.$event['eid'].'" target="_blank">This event on Facebook</a>';
					$event_obj->LastUpdate = $event['update_time'];
					if (!empty($event['pic'])) {
						$event_obj->Image = $event['pic'];
					}
					$occurrence->SourceOccurrenceId = $event['eid'];
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
			var_dump($ex->getMessage());
			$CI->facebook->HandleException($ex);
		}
	}
	
	function GetBirthdays(&$Data)
	{
		$CI = & get_instance();
		
		try {
			
			// Get friends with birthdays in the range.
			$birthdays = $CI->facebook->Client->fql_query(
				'SELECT uid, name, birthday, profile_update_time, pic FROM user '.
				'WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = '.$CI->facebook->Uid.') '.
				'OR uid = '.$CI->facebook->Uid
			);
			
			$year = date('Y', $this->mRange[0]);
			$yesterday = strtotime('-1day',$this->mRange[0]);
			foreach ($birthdays as $birthday) {
				if (preg_match('/([A-Z][a-z]+ \d\d?, )(\d\d\d\d)/', $birthday['birthday'], $matches)) {
					$start_age = $year - $matches[2];
					$dob = strtotime($matches[1].$year);
					
					while ($dob < $this->mRange[1]) {
						if ($dob >= $yesterday) {
							$event_obj = & $Data->NewEvent();
							$occurrence = & $Data->NewOccurrence($event_obj);
							$event_obj->SourceEventId = 'bd'.$birthday['uid'];
							$event_obj->Name = 'Birthday '.$start_age.': '.$birthday['name'];
							$event_obj->Description = '<a href="http://www.facebook.com/profile.php?id='.$birthday['uid'].'" target="_blank">'.$birthday['name'].'\'s profile</a>';
							$event_obj->LastUpdate = (int)$birthday['profile_update_time'];
							if (!empty($birthday['pic'])) {
								$event_obj->Image = $birthday['pic'];
							}
							$occurrence->SourceOccurrenceId = 'bd'.$birthday['uid'].'.'.$start_age;
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
			var_dump($ex->getMessage());
			$CI->facebook->HandleException($ex);
		}
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