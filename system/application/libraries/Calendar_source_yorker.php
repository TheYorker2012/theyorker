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
	
	/// bool Whether todo list items are enabled.
	protected $mEnableTodo;
	
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
	
	/// Enable todo list items.
	function EnableTodo($Enable = TRUE)
	{
		$this->mEnableTodo = $Enable;
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
		$filter->SetFlag('todo', $this->mEnableTodo);
		
		$CI = & get_instance();
		
		$query = new EventOccurrenceQuery();
		
		$fields = array(
			'occurrence_id'		=> 'event_occurrences.event_occurrence_id',
			'state'				=> 'event_occurrences.event_occurrence_state',
			'active'			=> 'event_occurrences.event_occurrence_active_occurrence_id',
			'start'				=> 'UNIX_TIMESTAMP(event_occurrences.event_occurrence_start_time)',
			'end'				=> 'UNIX_TIMESTAMP(event_occurrences.event_occurrence_end_time)',
			'time_associated'	=> 'event_occurrences.event_occurrence_time_associated',
			'location'			=> 'event_occurrences.event_occurrence_location_name',
			'ends_late'			=> 'event_occurrences.event_occurrence_ends_late',
			
			// show on calendar if date associated and attending=TRUE or NULL
			'show_on_calendar'	=> $query->ExpressionShowOnCalendar(),
			
			'user_last_update'	=> 'UNIX_TIMESTAMP(event_occurrence_users.event_occurrence_user_timestamp)',
			'user_attending'	=> 'event_occurrence_users.event_occurrence_user_attending',
			'user_todo'			=> 'event_occurrence_users.event_occurrence_user_todo',
			'user_progress'		=> 'event_occurrence_users.event_occurrence_user_progress',
			
			'event_id'			=> 'events.event_id',
			'event_todo'		=> 'events.event_todo',
			'category_name'		=> 'event_types.event_type_name',
			'category_colour'	=> 'event_types.event_type_colour_hex',
			'name'				=> 'events.event_name',
			'description'		=> 'events.event_description',
			//'blurb'				=> 'events.event_blurb',
			'event_time_associated'	=> 'events.event_time_associated',
			'last_update'		=> 'UNIX_TIMESTAMP(events.event_timestamp)',
			'subscribed'		=> 'event_subscriptions.event_subscription_user_entity_id IS NOT NULL',
			'owned'				=> 'event_entities.event_entity_entity_id = '.$CI->events_model->GetActiveEntityId(),
			
			'org_id'			=> 'organisations.organisation_entity_id',
			'org_name'			=> 'organisations.organisation_name',
			'org_shortname'		=> 'organisations.organisation_directory_entry_name',
		);
		
		if ($this->mEnableTodo) {
			// show on todo if user todo=TRUE or (NULL AND todo)
			$fields['show_on_todo']	= $query->ExpressionShowOnTodo();
			// effective start and end of todo if forced into one (from now until the beginning of the event)
			$fields['todo_start']	= $query->ExpressionTodoStart();
			$fields['todo_end']		= $query->ExpressionTodoEnd();
		}
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
				$event->TimeAssociated = $row['event_time_associated'];
			}
			if (!array_key_exists($occurrence_id, $occurrences)) {
				$occurrence = $occurrences[$occurrence_id] = & $Data->NewOccurrence($events[$event_id]);
				$occurrence->SourceOccurrenceId = $occurrence_id;
				/// @todo Active occurrence
				if (NULL !== $row['start']) {
					$occurrence->StartTime = new Academic_time((int)$row['start']);
				}
				if (NULL !== $row['end']) {
					$occurrence->EndTime = new Academic_time((int)$row['end']);
				}
				$occurrence->TimeAssociated = $row['time_associated'];
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
				//$occurrence->Todo = (bool)$row['todo'];
				$occurrence->DisplayOnCalendar = (NULL !== $row['show_on_calendar'] && (bool)$row['show_on_calendar']);
				if ($this->mEnableTodo) {
					$occurrence->DisplayOnTodo = (NULL !== $row['show_on_todo'] && (bool)$row['show_on_todo']);
					if (NULL !== $row['todo_start']) {
						$occurrence->TodoStartTime = new Academic_time($row['todo_start']);
					}
					if (NULL !== $row['todo_end']) {
						$occurrence->TodoEndTime = new Academic_time($row['todo_end']);
					}
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