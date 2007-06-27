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
	
	/// array[stream id] For including additional specific streams.
	protected $mStreams = array();
	
	/// array[source id] For including additional specific streams.
	protected $mEvents = array();
	
	/// string Special mysql condition.
	protected $mSpecialCondition = FALSE;
	
	/// array Categories cache.
	protected $mCategoriesCache = NULL;
	
	/// Default constructor.
	function __construct($SourceId)
	{
		parent::__construct();
		
		$this->mQuery = new EventOccurrenceQuery();
		
		$this->SetSourceId($SourceId);
		$this->mName = 'Yorker';
		
		$CI = & get_instance();
		if (!$CI->events_model->IsReadOnly()) {
			$this->mCapabilities[] = 'create';
		}
		$this->mCapabilities[] = 'attend';
		
		$this->mGroups['streams'] = FALSE;
	}
	
	/// Add included stream.
	/**
	 * @param $StreamId integer/array Feed id(s).
	 * @param $EnableStreams bool Whether to enable inclusions.
	 */
	function IncludeStream($StreamId, $EnableStreams = FALSE)
	{
		if (is_array($StreamId)) {
			$this->mStreams = array_merge($this->mStreams, $FeedId);
		} else {
			$this->mStreams[] = $StreamId;
		}
		if ($EnableStreams) {
			$this->EnableGroup('streams');
		}
	}
	
	/// Clear included streams.
	/**
	 * @param $DisableStreams bool Whether to disable streams.
	 */
	function ClearStreams($DisableStreams = FALSE)
	{
		$this->mInclusions = array();
		if ($DisableStreams) {
			$this->DisableGroup('streams');
		}
	}
	
	/// Set the special condition.
	/**
	 * @param $Condition string SQL condition.
	 */
	function SetSpecialCondition($Condition = FALSE)
	{
		$this->mSpecialCondition = $Condition;
	}
	
	/// Get all allowed categories.
	/**
	 * @return array[name => array], NULL, TRUE.
	 *	- NULL if categories are not supported
	 *	- TRUE if all categories are allowed.
	 */
	function GetAllCategories()
	{
		if (NULL === $this->mCategoriesCache) {
			$CI = & get_instance();
			// Get categories and reindex by name
			$categories = $CI->events_model->CategoriesGet();
			foreach ($categories as $category) {
				$this->mCategoriesCache[$category['name']] = $category;
			}
		}
		return $this->mCategoriesCache;
	}
	
	/// Fetch the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 * @param $Event identifier Source event identitier.
	 */
	protected function _FetchEvent(&$Data, $Event)
	{
		$CI = & get_instance();
		$this->TransformEventData(
			$Data,
			$this->MainQuery(
				$this->GetFields(),
				'('.$this->mQuery->ExpressionOwned().
				' OR ('.$this->mQuery->ExpressionSubscribed().
					' AND '.$this->mQuery->ExpressionPublic().'))'.
				' AND events.event_id = '.$CI->db->escape($Event)
			)
		);
	}
	
	/// Fetch the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	protected function _FetchEvents(&$Data)
	{
		$this->TransformEventData(
			$Data,
			$this->MainQuery(
				$this->GetFields(),
				$this->ProduceWhereClause()
			)
		);
	}
	
	protected function GetFields()
	{
		$fields =
			'event_occurrences.event_occurrence_id						AS occurrence_id,'.
			'event_occurrences.event_occurrence_state					AS state,'.
			'event_occurrences.event_occurrence_active_occurrence_id	AS active,'.
			'UNIX_TIMESTAMP(event_occurrences.event_occurrence_start_time) AS start,'.
			'UNIX_TIMESTAMP(event_occurrences.event_occurrence_end_time) AS end,'.
			'event_occurrences.event_occurrence_time_associated			AS time_associated,'.
			'event_occurrences.event_occurrence_location_name			AS location,'.
			'event_occurrences.event_occurrence_ends_late				AS ends_late,'.
			
			// show on calendar if date associated and attending=TRUE or NULL
			$this->mQuery->ExpressionShowOnCalendar().' AS show_on_calendar,'.
			
			'UNIX_TIMESTAMP(event_occurrence_users.event_occurrence_user_timestamp) AS user_last_update,'.
			'event_occurrence_users.event_occurrence_user_attending	AS user_attending,'.
			'event_occurrence_users.event_occurrence_user_todo		AS user_todo,'.
			'event_occurrence_users.event_occurrence_user_progress	AS user_progress,'.
			
			'events.event_id	AS event_id,'.
			'events.event_todo	AS event_todo,'.
			'event_types.event_type_name		AS category_name,'.
			'event_types.event_type_colour_hex	AS category_colour,'.
			'events.event_name			AS name,'.
			'events.event_description	AS description,'.
			//'events.event_blurb AS blurb,'.
			'events.event_time_associated			AS event_time_associated,'.
			'UNIX_TIMESTAMP(events.event_timestamp)	AS last_update,'.
			$this->mQuery->ExpressionSubscribed().'	AS subscribed,'.
			$this->mQuery->ExpressionOwned().'		AS owned,'.
			
			'organisations.organisation_entity_id	AS org_id,'.
			'organisations.organisation_name		AS org_name,'.
			'organisations.organisation_directory_entry_name AS org_shortname';
		
		if ($this->mGroups['todo']) {
			$fields .= ','.
				// show on todo if user todo=TRUE or (NULL AND todo)
				$this->mQuery->ExpressionShowOnTodo().' AS show_on_todo,'.
				// effective start and end of todo if forced into one (from now until the beginning of the event)
				'UNIX_TIMESTAMP('.$this->mQuery->ExpressionTodoStart().') AS todo_start,'.
				'UNIX_TIMESTAMP('.$this->mQuery->ExpressionTodoEnd().') AS todo_end';
		}
		
		return $fields;
	}
	
	protected function TransformEventData(&$Data, $DbData)
	{
		// Go through and sort the database data into objects.
		$events = array();
		$occurrences = array();
		$organisations = array();
		foreach ($DbData as $row) {
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
					$event->ReadOnly = FALSE;
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
				$occurrence->State = $row['state'];
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
					$organisation->YorkerOrganisationId = $org_id;
					$organisation->Name = $row['org_name'];
					$organisation->ShortName = $row['org_shortname'];
				}
				$events[$event_id]->AddOrganisation($organisations[$org_id]);
			}
		}
	}
	
	/// Retrieve specified fields of event occurrences.
	/**
	 * @param $Fields string Fields to select
	 * @return array Results from db query.
	 */
	function MainQuery($Fields, $Where)
	{
		/// @todo Optimise main event query to avoid left joins amap
		$sql = '
		SELECT '.$Fields.' FROM event_occurrences
		INNER JOIN events
			ON	event_occurrences.event_occurrence_event_id = events.event_id
			AND	(events.event_deleted = 0 || event_occurrence_state = "cancelled")
		LEFT JOIN event_types
			ON	events.event_type_id = event_types.event_type_id
		LEFT JOIN event_entities
			ON	event_entities.event_entity_event_id = events.event_id
		LEFT JOIN organisations
			ON	organisations.organisation_entity_id
					IN (event_entities.event_entity_entity_id, events.event_organizer_entity_id)
		LEFT JOIN subscriptions
			ON	subscriptions.subscription_organisation_entity_id
					IN (event_entities.event_entity_entity_id, events.event_organizer_entity_id)
			AND	subscriptions.subscription_user_entity_id	= '.$this->mQuery->GetEntityId().'
			AND	subscriptions.subscription_calendar = TRUE
		LEFT JOIN event_occurrence_users
			ON	event_occurrence_users.event_occurrence_user_event_occurrence_id
					= event_occurrences.event_occurrence_id
			AND	event_occurrence_users.event_occurrence_user_user_entity_id
					= '.$this->mQuery->GetEntityId().'
		LEFT JOIN event_occurrences AS active_occurrence
			ON	event_occurrences.event_occurrence_active_occurrence_id
					= active_occurrence.event_occurrence_id';
		
		$sql .= ' WHERE '.$Where.
				' ORDER BY event_occurrences.event_occurrence_start_time';
		
		// Try it out
		$CI = & get_instance();
		return $CI->db->query($sql)->result_array();
	}
	
	/// Produce the where part of the sql statement.
	/**
	 * @return string SQL where clause.
	 */
	protected function ProduceWhereClause()
	{
		$CI = & get_instance();
		
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
			
			if ($this->mGroups['streams'] && count($this->mStreams) > 0) {
				$streams = array();
				foreach ($this->mStreams as $stream_id) {
					$streams[] = $CI->db->escape($stream_id);
				}
				$streams = implode(',', $streams);
				$included = '(events.event_organizer_entity_id IN ('.$streams.') OR '.
							' event_entities.event_entity_event_id IN ('.$streams.'))';
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
		
		// User's attendance status
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
		
		// The category of the event
		$category_predicates = array();
		foreach ($this->mCategories as $category => $enabled) {
			if (!$enabled) {
				$category_predicates[] = 'event_types.event_type_name <> '.$CI->db->escape($category);
			}
		}
		if (count($category_predicates) > 0) {
			$category = '('.implode(' AND ',$category_predicates).')';
		} else {
			$category = 'TRUE';
		}
		
		// Full text search
		if (is_string($this->mSearchPhrase)) {
			$search = 'MATCH (events.event_name, events.event_description, events.event_blurb'/*, organisations.organisation_name*/.') AGAINST ('.$CI->db->escape($this->mSearchPhrase).')';
		} else {
			$search = 'TRUE';
		}
		
		$filters = '('.$state.' AND '.$visibility.' AND '.$category.' AND '.$search.')';
		
		// DATE RANGE ----------------------------------------------------------
		$ranges = array();
		if ($this->mGroups['event']) {
			$ranges[] = $this->mQuery->ExpressionDateRange($this->mEventRange);
		}
		if ($this->mGroups['todo']) {
			$ranges[] = $this->mQuery->ExpressionTodoRange($this->mTodoRange);
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
		
		return implode(' AND ', $conditions);
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
	
	/// Create an event.
	/**
	 * @param $Event CalendarEvent event information.
	 * @return array Array of messages.
	 */
	function CreateEvent($Event)
	{
		/// @todo Make this function work with a CalendarEvent object.
		$messages = array();
		$CI = & get_instance();
		try {
			$results = $CI->events_model->EventCreate($Event);
		} catch (Exception $e) {
			$messages['error'][] = $e->getMessage();
		}
		return $messages;
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
		if (is_numeric($Occurrence)) {
			$CI = & get_instance();
			$attendees = $CI->events_model->GetOccurrenceRsvp($Occurrence);
			foreach ($attendees as $key => $value) {
				$attendees[$key] = array(
					'name' => $value['firstname'] . ' ' . $value['surname'],
					'attend' => TRUE
				);
			}
			return $attendees;
		} else {
			return parent::GetOcurrenceAttendanceList($Occurrence);
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