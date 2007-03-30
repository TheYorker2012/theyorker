<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_source_yorker.php
 * @brief Calendar source for Yorker events.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library calendar_backend)
 * @pre loaded(library academic_calendar)
 *
 * Event source class for obtaining yorker events.
 *
 * @version 28-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Calendar source for yorker events.
class CalendarSourceYorker extends CalendarSource
{
	/// EventOccurrenceFilter Occurrence filter.
	protected $mOccurrenceFilter;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct();
		
		$this->mName = 'Yorker events';
		$this->mCapabilities[] = 'rsvp';
		$this->mCapabilities[] = 'refer';
		
		$this->SetOccurrenceFilter();
	}
	
	/// Set the event occurrence filter to use.
	/**
	 * @param $Filter EventOccurrenceFilter Event filter object
	 *	(A value of NULL means use a default filter)
	 */
	function SetOccurrenceFilter($Filter = NULL)
	{
		$this->mOccurrenceFilter = $Filter;
	}
	
	/// Fedge the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	protected function _FetchEvents(&$Data)
	{
		// Get the occurrences.
		$filter = $this->mOccurrenceFilter;
		if (NULL === $filter) {
			$filter = new EventOccurrenceFilter();
		}
		$filter->SetRange($this->mStartTime,$this->mEndTime);
		
		$CI = & get_instance();
		
		$fields = array(
			'occurrence_id'		=> 'event_occurrences.event_occurrence_id',
			'state'				=> 'event_occurrences.event_occurrence_state',
			'active'			=> 'event_occurrences.event_occurrence_active_occurrence_id',
			'all_day'			=> 'event_occurrences.event_occurrence_all_day',
			'start'				=> 'UNIX_TIMESTAMP(event_occurrences.event_occurrence_start_time)',
			'end'				=> 'UNIX_TIMESTAMP(event_occurrences.event_occurrence_end_time)',
			'location'			=> 'event_occurrences.event_occurrence_location_name',
			'ends_late'			=> 'event_occurrences.event_occurrence_ends_late',
			'user_last_update'	=> 'UNIX_TIMESTAMP(event_occurrence_users.event_occurrence_user_timestamp)',
			'user_attending'	=> 'event_occurrence_users.event_occurrence_user_state',
			'user_progress'		=> 'event_occurrence_users.event_occurrence_user_progress',
			
			'event_id'			=> 'events.event_id',
			'category_name'		=> 'event_types.event_type_name',
			'category_colour'	=> 'event_types.event_type_colour_hex',
			'name'				=> 'events.event_name',
			'description'		=> 'events.event_description',
			//'blurb'				=> 'events.event_blurb',
			'last_update'		=> 'UNIX_TIMESTAMP(events.event_timestamp)',
			'subscribed'		=> 'event_subscriptions.event_subscription_user_entity_id IS NOT NULL',
			'owned'				=> 'event_entities.event_entity_entity_id = '.$CI->events_model->GetActiveEntityId(),
			
			'org_id'			=> 'organisations.organisation_entity_id',
			'org_name'			=> 'organisations.organisation_name',
			'org_shortname'		=> 'organisations.organisation_directory_entry_name',
		);
		$db_data = $filter->GenerateOccurrences($fields);
		
		// Go through and sort the database data into objects.
		$events = array();
		$occurrences = array();
		$organisations = array();
		foreach ($db_data as $row) {
			$event_id = (int)$row['event_id'];
			$occurrence_id = (int)$row['occurrence_id'];
			
			// Create new events and occurrences if necessary.
			if (!array_key_exists($event_id, $events)) {
				$event = $events[$event_id] = $Data->NewEvent();
				$event->SourceEventId = $event_id;
				$event->Category = $row['category_name'];
				$event->Name = $row['name'];
				$event->Description = $row['description'];
				$event->LastUpdate = $row['last_update'];
			}
			if (!array_key_exists($occurrence_id, $occurrences)) {
				$occurrence = $occurrences[$occurrence_id] = $Data->NewOccurrence($events[$event_id]);
				$occurrence->SourceOccurrenceId = $occurrence_id;
				/// @todo Active occurrence
				$occurrence->TimeAssociated = !$row['all_day'];
				if (NULL !== $row['start']) {
					$occurrence->StartTime = new Academic_time((int)$row['start']);
				}
				if (NULL !== $row['end']) {
					$occurrence->EndTime = new Academic_time((int)$row['end']);
				}
				$occurrence->LocationDescription = $row['location'];
				/// @todo location link
				if ($row['ends_late']) {
					$occurrence->SpecialTags[] = 'ends_late';
				}
				if (NULL !== $row['user_last_update']) {
					$occurrence->UserLastUpdate = (int)$row['user_last_update'];
				}
				$occurrence->UserAttending = $row['user_attending'];
				if (NULL !== $row['user_progress']) {
					$occurrence->UserProgress = (int)$row['user_progress'];
				}
			}
			
			// Adjust the events user status if necessary.
			if ($row['owned'] && $events[$event_id]->UserStatus !== 'owned') {
				$events[$event_id]->UserStatus = 'owned';
			} elseif ($row['subscribed'] && $events[$event_id]->UserStatus === 'none') {
				$events[$event_id]->UserStatus = 'subscribed';
			}
			
			// Create new organisation if necessary.
			if (NULL !== $row['org_id']) {
				$org_id = (int)$row['org_id'];
				if (!array_key_exists($org_id, $organisations)) {
					$organisation = $organisations[$org_id] = $Data->NewOrganisation();
					$organisation->SourceOrganisationId = $org_id;
					$organisation->Name = $row['org_name'];
					$organisation->ShortName = $row['org_shortname'];
				}
				$events[$event_id]->AddOrganisation($organisations[$org_id]);
			}
		}
		
	}
}



/// Dummy class
class Calendar_source_yorker
{
	/// Default constructor.
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->model('calendar/events_model');
	}
}

?>