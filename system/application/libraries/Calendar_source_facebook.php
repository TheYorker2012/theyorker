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
		//$this->mCapabilities[] = 'rsvp';
		$this->mCapabilities[] = 'refer';
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
			// Get events for the next few weeks.
			$events = $CI->facebook->Client->events_get(
				$CI->facebook->Uid,
				null,
				$this->mRange[0],
				$this->mRange[1],
				null
			);
			
			/*
			echo('<pre align="left">');
			print_r($events);
			echo('</pre>');
			//*/
			
			if (!empty($events)) {
				foreach ($events as $event) {
					// get the list of members, so we can see if the user is attending.
					$members = $CI->facebook->Client->events_getMembers($event['eid']);
					$user_id = $CI->facebook->Client->users_getLoggedInUser();
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
					$event_obj->SourceEventId = $event['eid'];
					$event_obj->Name = $event['name'];
					$event_obj->Description = $event['description'];
					$event_obj->LastUpdate = $event['update_time'];
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
			$CI->facebook->HandleException($ex);
		}
		
		/*if (isset($events[0]))
		{
			$first_event_eid = $events[0]['eid'];
			$event_members = $client->events_getMembers($events[0]['eid']);
			$event_count = count($event_members['attending']);
		}*/
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