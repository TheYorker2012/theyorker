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
	/// EventOccurrenceQuery Query object.
	protected $mQuery;
	
	/// string Special mysql condition.
	protected $mSpecialCondition = FALSE;
	
	/// Default constructor.
	function __construct($SourceId)
	{
		parent::__construct();
		
		$this->mQuery = new EventOccurrenceQuery();
		
		$this->SetSourceId($SourceId);
		$this->mName = 'Yorker events';
		$this->mCapabilities[] = 'attend';
	}
	
	/// Set the special condition.
	/**
	 * @param $Condition string SQL condition.
	 */
	function SetSpecialCondition($Condition = FALSE)
	{
		$this->mSpecialCondition = $Condition;
	}
	
	/// Fedge the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	protected function _FetchEvents(&$Data)
	{
		
		$CI = & get_instance();
		
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
			'show_on_calendar'	=> $this->mQuery->ExpressionShowOnCalendar(),
			
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
			'subscribed'		=> $this->mQuery->ExpressionSubscribed(),
			'owned'				=> $this->mQuery->ExpressionOwned(),
			
			'org_id'			=> 'organisations.organisation_entity_id',
			'org_name'			=> 'organisations.organisation_name',
			'org_shortname'		=> 'organisations.organisation_directory_entry_name',
		);
		
		if ($this->mGroups['todo']) {
			// show on todo if user todo=TRUE or (NULL AND todo)
			$fields['show_on_todo']	= $this->mQuery->ExpressionShowOnTodo();
			// effective start and end of todo if forced into one (from now until the beginning of the event)
			$fields['todo_start']	= $this->mQuery->ExpressionTodoStart();
			$fields['todo_end']		= $this->mQuery->ExpressionTodoEnd();
		}
		$db_data = $this->MainQuery($fields);
		
		/*
		echo('<span align="left">');
		var_dump($db_data);
		echo('</span>');
		//*/
		
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
				if ($row['owned']) {
					$event->UserStatus = 'owner';
				} elseif ($row['subscribed']) {
					$event->UserStatus = 'subscriber';
				}
			} else {
				$event = $events[$event_id];
				if ($row['owned']) {
					$event->UserStatus = 'owner';
				} elseif ($row['subscribed'] && 'none' === $event->UserStatus) {
					$event->UserStatus = 'subscriber';
				}
			}
			if (!array_key_exists($occurrence_id, $occurrences)) {
				$occurrence = & $Data->NewOccurrence($events[$event_id]);
				$occurrences[$occurrence_id] = & $occurrence;
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
				if (NULL !== $row['user_attending']) {
					$attending = (bool)$row['user_attending'];
				} else {
					$attending = NULL;
				}
				$occurrence->UserAttending = $attending;
				if (NULL !== $row['user_progress']) {
					$occurrence->UserProgress = (int)$row['user_progress'];
				}
				//$occurrence->Todo = (bool)$row['todo'];
				$occurrence->DisplayOnCalendar = (NULL !== $row['show_on_calendar'] && (bool)$row['show_on_calendar']);
				if ($this->mGroups['todo']) {
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
	
	/// Retrieve specified fields of event occurrences.
	/**
	 * @param $Fields array of aliases to select expressions (field names).
	 *	e.g. array('name' => 'events.event_name')
	 * @return array Results from db query.
	 */
	function MainQuery($Fields)
	{
		// MAIN QUERY ----------------------------------------------------------
		/*
			owned:
				occurrence.event.owners.id=me
				OR subscription.vip=1
			subscribed:
				occurrence.event.entities.subscribers.id=me
				event_subscription.interested
			inclusions:
				occurrence.event.entities.id=inclusion
				
			own OR (public AND (subscribed OR inclusion))
		*/
		
		$FieldStrings = array();
		foreach ($Fields as $Alias => $Expression) {
			if (is_string($Alias)) {
				$FieldStrings[] = $Expression.' AS '.$Alias;
			} else {
				$FieldStrings[] = $Expression;
			}
		}
		
		/// @todo Optimise main event query to avoid left joins amap
		$bind_data = array();
		$sql = '
			SELECT '.implode(',',$FieldStrings).' FROM event_occurrences
			INNER JOIN events
				ON	event_occurrences.event_occurrence_event_id = events.event_id
				AND	(events.event_deleted = 0 || event_occurrence_state = "cancelled")
			LEFT JOIN event_types
				ON	events.event_type_id = event_types.event_type_id
			LEFT JOIN event_entities
				ON	event_entities.event_entity_event_id = events.event_id
			LEFT JOIN organisations
				ON	organisations.organisation_entity_id
						= event_entities.event_entity_entity_id
			LEFT JOIN subscriptions
				ON	subscriptions.subscription_organisation_entity_id
						= event_entities.event_entity_entity_id
				AND	subscriptions.subscription_user_entity_id	= ?
				AND	subscriptions.subscription_calendar = TRUE
			LEFT JOIN event_occurrence_users
				ON	event_occurrence_users.event_occurrence_user_event_occurrence_id
						= event_occurrences.event_occurrence_id
				AND	event_occurrence_users.event_occurrence_user_user_entity_id
						= ?
			LEFT JOIN event_occurrences AS active_occurrence
				ON	event_occurrences.event_occurrence_active_occurrence_id
						= active_occurrence.event_occurrence_id';
		$bind_data[] = $this->mQuery->GetEntityId();
		$bind_data[] = $this->mQuery->GetEntityId();
		
		// SOURCES -------------------------------------------------------------
		
		if ($this->mGroups['owned']) {
			$own = $this->mQuery->ExpressionOwned();
		} else {
			$own = '0';
		}
		
		$public = $this->mQuery->ExpressionPublic();
		
		if ($this->mGroups['all']) {
			$public_sources = '';
		} else {
			if ($this->mGroups['subscribed']) {
				$subscribed = $this->mQuery->ExpressionSubscribed();
			} else {
				$subscribed = '0';
			}
			
			if ($this->mGroups['inclusions'] && count($this->mInclusions) > 0) {
				$includes = array();
				foreach ($this->mInclusions as $inclusion) {
					$includes[] = 'event_entities.event_entity_event_id=?';
					$bind_data[] = $inclusion;
				}
				$included = '('.implode(' AND ', $includes).')';
			} else {
				$included = '0';
			}
			
			$public_sources = ' AND ('.$subscribed.' OR '.$included.')';
		}
		
		$sources = '('.$own.' OR ('.$public.$public_sources.'))';
		
		// FILTERS -------------------------------------------------------------
		
		static $occurrence_states = array(
				'private' => array('draft','movedraft','trashed'),
				'active' => array('published'),
				'inactive' => array('cancelled'),
			);
		$state_predicates = array();
		foreach ($occurrence_states as $filter => $states) {
			if ($this->mGroups[$filter]) {
				foreach ($states as $state) {
					$state_predicates[] = 'event_occurrences.event_occurrence_state=\''.$state.'\'';
				}
			}
		}
		if (count($state_predicates) > 0) {
			$state = '('.implode(' OR ',$state_predicates).')';
		} else {
			$state = '0';
		}
		
		$visibility_predicates = array();
		if ($this->mGroups['hide'] && $this->mGroups['show'] && $this->mGroups['rsvp']) {
			$visibility_predicates[] = 'TRUE';
		} else {
			if ($this->mGroups['hide']) {
				$visibility_predicates[] = $this->mQuery->ExpressionVisibilityHidden();
			}
			if ($this->mGroups['show']) {
				$visibility_predicates[] = $this->mQuery->ExpressionVisibilityNormal();
			}
			if ($this->mGroups['rsvp']) {
				$visibility_predicates[] = $this->mQuery->ExpressionVisibilityRsvp();
			}
		}
		if (count($visibility_predicates) > 0) {
			$visibility = '('.implode(' OR ',$visibility_predicates).')';
		} else {
			$visibility = '0';
		}
		
		$filters = '('.$state.' AND '.$visibility.')';
		
		// DATE RANGE ----------------------------------------------------------
		$ranges = array();
		if ($this->mGroups['event']) {
			$ranges[] = $this->mQuery->ExpressionDateRange($this->mRange);
		}
		if ($this->mGroups['todo']) {
			$ranges[] = $this->mQuery->ExpressionTodoRange($this->mRange);
		}
		
		
		// SPECIAL CONDITION ---------------------------------------------------
		$conditions = array(
			'('.implode(' OR ',$ranges).')',
			$sources,
			$filters,
		);
		
		if (FALSE !== $this->mSpecialCondition) {
			$conditions[] = '('.$this->mSpecialCondition.')';
		}
		
		// WHERE CLAUSE --------------------------------------------------------
		
		$sql .= ' WHERE '.implode(' AND ',$conditions).'';
		
		$sql .= ' ORDER BY event_occurrences.event_occurrence_start_time';
		
		// Try it out
		$CI = &get_instance();
		$query = $CI->db->query($sql,$bind_data);
		return $query->result_array();
	}
	
	// MAKING CHANGES **********************************************************
	
	/// Set the user's attending status on an occurrence.
	/**
	 * @param $OccurrenceId Occurrence identifier.
	 * @param $Attending bool,NULL Whether attending.
	 * @return array Array of messages.
	 */
	function AttendingOccurrence($OccurrenceId, $Attending)
	{
		$messages = array();
		if (is_numeric($OccurrenceId)) {
			$CI = & get_instance();
			$result = $CI->events_model->SetOccurrenceUserAttending((int)$OccurrenceId, $Attending);
			if (!$result) {
				$messages['error'][] = 'The attendance status could not be set.';
			}
		} else {
			$messages['error'][] = 'The occurrence identifier was invalid.';
		}
		return $messages;
	}
	
	/// Delete an event.
	/**
	 * @param $EventId Event identifier.
	 * @return array Array of messages.
	 */
	function DeleteEvent($EventId)
	{
		$messages = array();
		if (is_numeric($EventId)) {
			$CI = & get_instance();
			$result = $CI->events_model->EventDelete((int)$EventId);
			if (!$result) {
				$messages['error'][] = 'The event could not be deleted.';
			}
		} else {
			$messages['error'][] = 'The event identifier was invalid.';
		}
		return $messages;
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