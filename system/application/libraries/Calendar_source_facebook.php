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
	
	/// Fedge the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	protected function _FetchEvents(&$Data)
	{
		$CI = & get_instance();
		
		if (!$CI->facebook->InUse()) return;
		
		try {
			// Get events in the range.
			$events = $CI->facebook->Client->events_get(
				$CI->facebook->Uid,
				null,
				$this->mRange[0],
				$this->mRange[1],
				null
			);
			
			$user_id = $CI->facebook->Client->users_getLoggedInUser();
			if (!empty($events)) {
				foreach ($events as $event) {
					// get the list of members, so we can see if the user is attending.
					$members = $CI->facebook->Client->events_getMembers($event['eid']);
					$attending = NULL;
					if (is_array($members['attending']) && in_array($user_id, $members['attending'])) {
						$attending = TRUE;
					} elseif (is_array($members['declined']) && in_array($user_id, $members['declined'])) {
						$attending = FALSE;
					} else {
						$attending = NULL;
					}
					/*echo '<pre align="left">';
					print_r($event);
					print_r($members);
					echo '</pre>';*/
					
					$event_obj = & $Data->NewEvent();
					$occurrence = & $Data->NewOccurrence($event_obj);
					$event_obj->SourceEventId = 'e'.$event['eid'];
					$event_obj->Name = $event['name'];
					$event_obj->Description = $event['description'];
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
			
			// Get friends with birthdays in the range.
			$birthdays = $CI->facebook->Client->fql_query(
				'SELECT uid, name, birthday, profile_update_time, pic FROM user '.
				//'WHERE uid = '.$user_id
				'WHERE uid IN (SELECT uid2 FROM friend WHERE uid1 = '.$user_id.') '.
				'OR uid = '.$user_id
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
							$event_obj->Description = '';
							$event_obj->LastUpdate = (int)$birthday['profile_update_time'];
							if (!empty($birthday['pic'])) {
								$event_obj->Image = $birthday['pic'];
							}
							$occurrence->SourceOccurrenceId = 'bd'.$birthday['uid'].'.'.$start_age;
							$occurrence->LocationDescription = '';
							$occurrence->StartTime = new Academic_time($dob);
							$occurrence->EndTime = $occurrence->StartTime->Adjust('1day');
							$occurrence->TimeAssociated = FALSE;
							$occurrence->UserAttending = NULL;
							unset($occurrence);
							unset($event_obj);
						}
						$dob = strtotime('1year', $dob);
						++$start_age;
					}
				}
			}
			
			/*
			echo('<pre align="left">');
			print_r($birthdays);
			echo('</pre>');
			//*/
		} catch (FacebookRestClientException $ex) {
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