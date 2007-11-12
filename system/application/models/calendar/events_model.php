<?php

/// Class for creating parts of queries.
class EventOccurrenceQuery
{
	/// Entity id of relevent user/organisation.
	protected $mEntityId;

	/// Default constructor
	function __construct()
	{
		$CI = &get_instance();
		$this->mEntityId = $CI->events_model->GetActiveEntityId();
	}

	/// Get the current entity id.
	function GetEntityId()
	{
		return $this->mEntityId;
	}

	/// Produce an SQL expression for all and only public events.
	function ExpressionPublic()
	{
		return	'(event_occurrences.event_occurrence_state IN (\'published\',\'cancelled\'))';
	}

	/// Produce an SQL expression for all and only owned events.
	function ExpressionOwned($EntityId = FALSE)
	{
		if (FALSE === $EntityId) {
			$EntityId = $this->mEntityId;
		}
		return	'(events.event_organiser_entity_id = ' . $EntityId . ' OR
				(	event_entities.event_entity_entity_id = ' . $EntityId . '
				AND	event_entities.event_entity_confirmed = 1
				AND	event_entities.event_entity_relationship = \'own\'))';
	}

	/// Produce an SQL expression for all and only rsvp'd events.
	function ExpressionVisibilityRsvp()
	{
		return	'event_occurrence_users.event_occurrence_user_attending=TRUE';
	}

	/// Produce an SQL expression for all and only hidden events.
	function ExpressionVisibilityHidden()
	{
		return	'event_occurrence_users.event_occurrence_user_attending=FALSE';
	}

	/// Produce an SQL expression for all and only normal visibility events.
	function ExpressionVisibilityNormal()
	{
		return	'event_occurrence_users.event_occurrence_user_attending IS NULL';
	}

	/// Produce an SQL expression for all and only subscribed events.
	/**
	 * @note Left joined to default_event_subscription
	 *	on subscription user = 0, organisation matches event, event_subscription_calendar
	 * @note Left joined to event_subscriptions
	 *	on user matches, organisation matches event, either value of event_subscription_calendar.
	 */
	function ExpressionSubscribed($EntityId = FALSE)
	{
		if (FALSE === $EntityId) {
			$EntityId = $this->mEntityId;
		}
		return	'(	events.event_organiser_entity_id = ' . $EntityId . '
				OR	(	event_entities.event_entity_entity_id = ' . $EntityId . '
					AND	event_entities.event_entity_confirmed = 1
					AND	(	event_entities.event_entity_relationship IN (\'own\',\'subscribe\')))
				OR	(	IF(	event_subscriptions.event_subscription_user_entity_id IS NOT NULL,
							event_subscriptions.event_subscription_calendar,
							default_event_subscription.event_subscription_calendar IS NOT NULL
								AND default_event_subscription.event_subscription_calendar)
					AND	'.$this->ExpressionPublic().')
				OR '.$this->ExpressionVisibilityRsvp().')';
	}

	/// Produce an SQL expression for whether an occurrence should be on a users calendar.
	function ExpressionShowOnCalendar()
	{
		return 'event_occurrences.event_occurrence_end_time IS NOT NULL';
	}

	/// Produce an SQL expression for whether an occurrence should be on a users todo list.
	function ExpressionShowOnTodo()
	{
		return 'event_occurrence_users.event_occurrence_user_todo = TRUE '.
				'OR (	event_occurrence_users.event_occurrence_user_todo IS NULL'.
				'	AND	events.event_todo)';
	}

	/// Produce an SQL expression for the effective todo start time mysql timestamp.
	function ExpressionTodoStart()
	{
		return 'IF(event_occurrence_users.event_occurrence_user_todo = TRUE '.
				'	AND events.event_todo = FALSE,'.
				'CURRENT_TIMESTAMP,'.
				'event_occurrences.event_occurrence_start_time)';
	}

	/// Produce an SQL expression for the effective todo end time mysql timestamp.
	function ExpressionTodoEnd()
	{
		return 'IF(event_occurrence_users.event_occurrence_user_todo = TRUE '.
				'	AND events.event_todo = FALSE,'.
				'event_occurrences.event_occurrence_start_time,'.
				'event_occurrences.event_occurrence_end_time)';
	}

	/// Produce an SQL expression for all and only occurrences in a range of time.
	function ExpressionDateRange($Range)
	{
		assert('is_array($Range)');
		assert('is_int($Range[0]) || NULL === $Range[0]');
		assert('is_int($Range[1]) || NULL === $Range[1]');

		$conditions = array($this->ExpressionShowOnCalendar());
		if (NULL !== $Range[0]) {
			$conditions[] = 'event_occurrences.event_occurrence_end_time >
								FROM_UNIXTIME('.$Range[0].')';
		}
		if (NULL !== $Range[1]) {
			$conditions[] = 'event_occurrences.event_occurrence_start_time <
								FROM_UNIXTIME('.$Range[1].')';
		}
		if (count($conditions) <= 1) {
			return 'TRUE';
		} else {
			// take care of the special case when an event of zero length covers a boundary
			if (NULL !== $Range[0]) {
				$zero_len_at_beginning =
					'(event_occurrences.event_occurrence_start_time = FROM_UNIXTIME('.$Range[0].') AND '.
					'event_occurrences.event_occurrence_end_time = FROM_UNIXTIME('.$Range[0].'))';
			}
			return '(('.implode(' AND ',$conditions).') OR '.$zero_len_at_beginning.')';
		}
	}

	/// Produce an SQL expression for all and only todos in a range of time.
	function ExpressionTodoRange($Range)
	{
		assert('is_array($Range)');
		assert('is_int($Range[0]) || NULL === $Range[0]');
		assert('is_int($Range[1]) || NULL === $Range[1]');

		$conditions = array($this->ExpressionShowOnTodo());
		if (NULL !== $Range[0]) {
			$conditions[] = '(	event_occurrence_users.event_occurrence_user_progress IS NULL '.
							'OR	event_occurrence_users.event_occurrence_user_progress < 100 '.
							'OR	DATE_ADD(event_occurrence_users.event_occurrence_user_timestamp, INTERVAL 1 WEEK) > FROM_UNIXTIME('.$Range[0].'))';
		}
		if (NULL !== $Range[1]) {
			$conditions[] = 'FROM_UNIXTIME('.$Range[1].') > '.$this->ExpressionTodoStart();
		}
		if (empty($conditions)) {
			return 'TRUE';
		} else {
			return '('.implode(' AND ',$conditions).')';
		}
	}

	/*
		cancelled?
			active_id?
				active.state=published?
					>rescheduled
				active.state=cancelled
					>cancelled
				else
					>postponed
			else
				>cancelled
		else
			>$state
	 */

	/// Produce an SQL expression for whether the object is cancelled.cancelled.
	/**
	 * @param $ActiveAlias string Alias of active occurrence used in check.
	 * @return string SQL boolean expression.
	 */
	function ExpressionPublicCancelled($ActiveAlias = 'active_occurrence')
	{
		return '(event_occurrences.event_occurrence_state = \'cancelled\' ' .
				'AND (event_occurrences.event_occurrence_active_occurrence_id IS NULL
					OR '.$ActiveAlias.'.event_occurrence_state = \'cancelled\'))';
	}

	/// Produce an SQL expression for whether the object is cancelled.postponed.
	/**
	 * @param $ActiveAlias string Alias of active occurrence used in check.
	 * @return string SQL boolean expression.
	 */
	function ExpressionPublicPostponed($ActiveAlias = 'active_occurrence')
	{
		return '(event_occurrences.event_occurrence_state = \'cancelled\' ' .
				'AND event_occurrences.event_occurrence_active_occurrence_id IS NOT NULL '.
				'AND '.$ActiveAlias.'.event_occurrence_state != \'published\''.
				'AND '.$ActiveAlias.'.event_occurrence_state != \'cancelled\')';
	}

	/// Produce an SQL expression for whether the object is cancelled.rescheduled.
	/**
	 * @param $ActiveAlias string Alias of active occurrence used in check.
	 * @return string SQL boolean expression.
	 */
	function ExpressionPublicRescheduled($ActiveAlias = 'active_occurrence')
	{
		return '(event_occurrences.event_occurrence_state = \'cancelled\' ' .
				'AND event_occurrences.event_occurrence_active_occurrence_id IS NOT NULL '.
				'AND '.$ActiveAlias.'.event_occurrence_state = \'published\')';
	}

	/// Produce an SQL expression for the public state of an object.
	/**
	 * @param $ActiveAlias string Alias of active occurrence used in expression.
	 * @return string SQL string expression.
	 */
	function ExpressionPublicState($ActiveAlias = 'active_occurrence')
	{
		return
		'IF(event_occurrences.event_occurrence_state = \'cancelled\',' .
			'IF(event_occurrences.event_occurrence_active_occurrence_id IS NULL,' .
				'\'cancelled\',' .
				'IF('.$ActiveAlias.'.event_occurrence_state = \'published\',' .
					'\'rescheduled\',' .
					'IF('.$ActiveAlias.'.event_occurrence_state = \'cancelled\',' .
						'\'cancelled\',' .
						'\'postponed\'))),' .
			'event_occurrences.event_occurrence_state)';
	}
	
	/// Produce an SQL expression for the location name of an occurrence.
	/**
	 * @param $EventAlias string Alias of event used in expression.
	 * @param $OccurrenceAlias string Alias of occurrence used in expression.
	 * @return string SQL string expression.
	 */
	function ExpressionLocationName($EventAlias = 'events', $OccurrenceAlias = 'event_occurrences')
	{
		return "IF ($OccurrenceAlias.event_occurrence_location_name IS NOT NULL,'.
					'$OccurrenceAlias.event_occurrence_location_name,'.
					'$EventAlias.event_location_name)";
	}
}

/// Filter class for retrieving event occurrences.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The purpose of this class is to allow classes which provide a layer of
 *	abstraction over the model (e.g. View_calendar_days) to provide a consistent
 *	filtering interface.
 *
 * always in relation to an entity who is accessing, to determin permission.
 *	occurrence must belong to an event owned by the entity or which is public
 *	event must be owned by an entity and have public occurrences
 *	event must not be deleted
 * sources
 *	owned events
 *	subscribed feeds
 *	extra feeds
 * filters
 *	private? active? inactive?
 *	hidden events? normal events? rsvpd events?
 *	date range
 * @todo Generalise event occurrence filter across calendar sources
 */
class EventOccurrenceFilter extends EventOccurrenceQuery
{
	/// array[bool] For enabling/disabling sources of events.
	protected $mSources = array(
		'owned'      => TRUE,
		'subscribed' => TRUE,
		'inclusions' => FALSE,
		'all'        => FALSE,
	);

	/// array[bool] For filtering sources of events.
	protected $mFilters = array(
		'private'    => TRUE,
		'active'     => TRUE,
		'inactive'   => TRUE,

		'hide'       => FALSE,
		'show'       => TRUE,
		'rsvp'       => TRUE,
	);

	/// array[feed] For including additional sources.
	protected $mInclusions = array();

	/// array[2*timestamp] For filtering by time.
	protected $mRange = array( NULL, NULL );

	/// array[string=>bool] Whether each type of event is enabled (require different range methods).
	protected $mFlags = array(
		'event' => TRUE,
		'todo' => FALSE
	);

	/// string Special mysql condition.
	protected $mSpecialCondition = FALSE;

	/// Retrieve specified fields of event occurrences.
	/**
	 * @param $Fields array of aliases to select expressions (field names).
	 *	e.g. array('name' => 'events.event_name')
	 * @return array Results from db query.
	 */
	function GenerateOccurrences($Fields)
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
				AND	events.event_deleted = 0
			LEFT JOIN event_types
				ON	events.event_type_id = event_types.event_type_id
			LEFT JOIN event_entities
				ON	event_entities.event_entity_event_id = events.event_id
			LEFT JOIN organisations
				ON	organisations.organisation_entity_id
						= event_entities.event_entity_entity_id
			LEFT JOIN event_subscriptions
				ON	event_subscriptions.event_subscription_organisation_entity_id
						= event_entities.event_entity_entity_id
				AND	event_subscriptions.event_subscription_user_entity_id	= ?
				AND	event_subscriptions.event_subscription_calendar = TRUE
			LEFT JOIN event_occurrence_users
				ON	event_occurrence_users.event_occurrence_user_event_occurrence_id
						= event_occurrences.event_occurrence_id
				AND	event_occurrence_users.event_occurrence_user_user_entity_id
						= ?
			LEFT JOIN event_occurrences AS active_occurrence
				ON	event_occurrences.event_occurrence_active_occurrence_id
						= active_occurrence.event_occurrence_id';
		$bind_data[] = $this->mEntityId;
		$bind_data[] = $this->mEntityId;

		// SOURCES -------------------------------------------------------------

		if ($this->mSources['owned']) {
			$own = $this->ExpressionOwned();
		} else {
			$own = '0';
		}

		$public = $this->ExpressionPublic();

		if ($this->mSources['all']) {
			$public_sources = '';
		} else {
			if ($this->mSources['subscribed']) {
				$subscribed = $this->ExpressionSubscribed();
			} else {
				$subscribed = '0';
			}

			if ($this->mSources['inclusions'] && count($this->mInclusions) > 0) {
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
			if ($this->mFilters[$filter]) {
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
		if ($this->mFilters['hide']) {
			$visibility_predicates[] = $this->ExpressionVisibilityHidden();
		}
		if ($this->mFilters['show']) {
			$visibility_predicates[] = $this->ExpressionVisibilityNormal();
		}
		if ($this->mFilters['rsvp']) {
			$visibility_predicates[] = $this->ExpressionVisibilityRsvp();
		}
		if (count($visibility_predicates) > 0) {
			$visibility = '('.implode(' OR ',$visibility_predicates).')';
		} else {
			$visibility = '0';
		}

		$filters = '('.$state.' AND '.$visibility.')';

		// DATE RANGE ----------------------------------------------------------
		$ranges = array();
		if ($this->mFlags['event']) {
			$ranges[] = $this->ExpressionDateRange($this->mRange);
		}
		if ($this->mFlags['todo']) {
			$ranges[] = $this->ExpressionTodoRange($this->mRange);
		}


		// SPECIAL CONDITION ---------------------------------------------------
		$conditions = array('('.implode(' OR ',$ranges).')', $sources, $filters);

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

	/// Enable a source.
	/**
	 * @param $SourceName string Index to $this->mSources.
	 * @return bool Whether successfully enabled.
	 */
	function EnableSource($SourceName)
	{
		if (array_key_exists($SourceName, $this->mSources)) {
			$this->mSources[$SourceName] = TRUE;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/// Disable a source.
	/**
	 * @param $SourceName string Index to $this->mSources.
	 * @return bool Whether successfully disabled.
	 */
	function DisableSource($SourceName)
	{
		if (array_key_exists($SourceName, $this->mSources)) {
			$this->mSources[$SourceName] = FALSE;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/// Enable a filter.
	/**
	 * @param $FilterName string Index to $this->mFilters.
	 * @return bool Whether successfully enabled.
	 */
	function EnableFilter($FilterName)
	{
		if (array_key_exists($FilterName, $this->mFilters)) {
			$this->mFilters[$FilterName] = TRUE;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/// Disable a filter.
	/**
	 * @param $FilterName string Index to $this->mFilters.
	 * @return bool Whether successfully disabled.
	 */
	function DisableFilter($FilterName)
	{
		if (array_key_exists($FilterName, $this->mFilters)) {
			$this->mFilters[$FilterName] = FALSE;
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/// Add inclusion.
	/**
	 * @param $FeedId integer/array Feed id(s).
	 * @param $EnableInclusions bool Whether to enable inclusions.
	 */
	function AddInclusion($FeedId, $EnableInclusions = FALSE)
	{
		if (is_array($FeedId)) {
			$this->mInclusions = array_merge($this->mInclusions, $FeedId);
		} else {
			$this->mInclusions[] = $FeedId;
		}
		if ($EnableInclusions) {
			$this->EnableSource('inclusions');
		}
	}

	/// Clear inclusions.
	/**
	 * @param $DisableInclusions bool Whether to disable inclusions.
	 */
	function ClearInclusions($DisableInclusions = FALSE)
	{
		$this->mInclusions = array();
		if ($DisableInclusions) {
			$this->DisableSource('inclusions');
		}
	}

	/// Set the date range.
	/**
	 * @param $Start timestamp Start time.
	 * @param $End timestamp End time.
	 */
	function SetRange($Start, $End)
	{
		assert('is_int($Start) || FALSE === $Start');
		assert('is_int($End)   || FALSE === $End');
		$this->mRange[0] = $Start;
		$this->mRange[1] = $End;
	}

	/// Set the special condition.
	/**
	 * @param $Condition string SQL condition.
	 */
	function SetSpecialCondition($Condition = FALSE)
	{
		$this->mSpecialCondition = $Condition;
	}

	/// Enable/disable something.
	function SetFlag($FlagName, $Value)
	{
		$this->mFlags[$FlagName] = $Value;
	}

}



/// Model for access to events.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * User operations
 * Org operations
 *		create new event (with subevents/occurrences)
 *		State transitions
 *			Trash a draft
 *			restore a trashed draft
 *			delete (from anything except draft)
 *			publish a draft
 *			cancel an active published
 *			move an active published
 *			restore a cancelled
 *		get RSVP lists
 * Auto operations
 *		generate recurrences
 */
class Events_model extends Model
{
	private $mStates;

	private $mDayInformation;
	private $mOccurrences;

	protected $mOccurrenceFilter;

	/// Entity id of user/organisation.
	/**
	 * @note This should ALWAYS be accessed using GetActiveEntityId()
	 */
	protected $mActiveEntityId;

	/// Type of acting entity.
	protected $mActiveEntityType;

	/// Whether the user is in read only mode.
	protected $mReadOnly;

	/// Message to give to the exception when a user in read only mode tries to write.
	protected static $cReadOnlyMessage = 'Public calendar is read-only. Please log in.';

	protected static $cEntityPublic = 0; ///< $mActiveEntityType, indicates not logged in.
	protected static $cEntityUser   = 1; ///< $mActiveEntityType, indicates logged in.
	protected static $cEntityVip    = 2; ///< $mActiveEntityType, indicates in viparea.

	/// Default constructor
	function __construct()
	{
		parent::Model();

		$this->load->model('calendar/recurrence_model');

		$this->mActiveEntityId = NULL;
		$this->mActiveEntityType = self::$cEntityPublic;

		$this->mStates = array();
		$this->mDayInformation = FALSE;
		$this->mOccurrences = FALSE;
		$this->SetOccurrenceFilter();

		$this->load->library('academic_calendar');

		// So that IsReadOnly will work
		$this->GetActiveEntityId();
	}

	/// Find whether the user has permission to alter the calendar.
	function IsReadOnly()
	{
		return $this->mReadOnly;
	}

	/// Find whether the user has permission to alter the calendar.
	function IsVip()
	{
		return $this->mActiveEntityType === self::$cEntityVip;
	}

	/// Find whether the user is a normal logged in yorker user.
	function IsNormalUser()
	{
		return $this->mActiveEntityType === self::$cEntityUser;
	}

	/// Get the entity id of the active entity.
	function GetActiveEntityId()
	{
		if (NULL === $this->mActiveEntityId) {
			// Get entity id from user_auth library
			$CI = &get_instance();
			if ($CI->user_auth->isLoggedIn) {
				$this->mActiveEntityId = VipOrganisationId();
				if (FALSE === $this->mActiveEntityId) {
					$this->mReadOnly = FALSE;
					$this->mActiveEntityId = $CI->user_auth->entityId;
					$this->mActiveEntityType = self::$cEntityUser;
				} else {
					$this->mReadOnly = !VipLevel('rep');
					$this->mActiveEntityType = self::$cEntityVip;
				}
			} else {
				// Default to an entity id with default events
				$this->mReadOnly = TRUE;
				$this->mActiveEntityId = 0;
				$this->mActiveEntityType = self::$cEntityPublic;
			}
		}
		return $this->mActiveEntityId;
	}

	/// Set whether some option is enabled
	function SetEnabled($StateName, $Enabled = TRUE)
	{
		$this->mStates[$StateName] = $Enabled;
	}

	/// Find if some data is enabled to be retrieved.
	function IsEnabled($StateName, $Default = FALSE)
	{
		if (array_key_exists($StateName,$this->mStates)) {
			return $this->mStates[$StateName];
		} else {
			return $Default;
		}
	}

	/// Set whether day information is enabled.
	function IncludeDayInformation($Enabled)
	{
		$this->SetEnabled('day-info', $Enabled);
	}

	/// Set whether event occurrences are enabled.
	function IncludeOccurrences($Enabled)
	{
		$this->SetEnabled('occurrences-all', $Enabled);
	}

	/// Set the event occurrence filter to use.
	/**
	 * @param $Filter EventOccurrenceFilter Event filter object
	 *	(A value of FALSE means use a default filter)
	 */
	function SetOccurrenceFilter($Filter = NULL)
	{
		$this->mOccurrenceFilter = $Filter;
	}

	/// Retrieve the specified data between certain dates.
	/**
	 * @param $StartTime Academic_time Start time.
	 * @param $EndTime Academic_time End time.
	 * @pre @a $StartTime < @a $EndTime
	 * @return bool Success state:
	 *	- TRUE (Success).
	 *	- FALSE (Failure).
	 */
	function Retrieve($StartTime, $EndTime)
	{
		// Validate input
		if ($StartTime >= $EndTime)
			return FALSE;

		$success = TRUE;

		// Day information
		if ($this->IsEnabled('day-info')) {
			$this->mDayInformation = array();

			// Initialise the day info array
			$current_time = $StartTime->Midnight();
			$current_index = 0;
			while ($current_time->Timestamp() < $EndTime->Timestamp()) {
				$this->mDayInformation[$current_time->Timestamp()] = array(
						'index' => $current_index,
						'date' => $current_time,
						'special_headings' => array(),
					);

				// Onto the next day
				$current_time = $current_time->Adjust('1day');
				++$current_index;
			}
		}

		// Event occurrences
		if ($this->IsEnabled('occurrences-all')) {
			$occurrence_query = new EventOccurrenceQuery();
			$filter = $this->mOccurrenceFilter;
			if (NULL === $filter) {
				$filter = new EventOccurrenceFilter();
			}
			$filter->SetRange($StartTime->Timestamp(),$EndTime->Timestamp());

			$fields = array(
					'ref_id' => 'event_occurrences.event_occurrence_id',
					'name'   => 'events.event_name',
					'start'  => 'UNIX_TIMESTAMP(event_occurrences.event_occurrence_start_time)',
					'end'    => 'UNIX_TIMESTAMP(event_occurrences.event_occurrence_end_time)',
					'system_update_ts' => 'UNIX_TIMESTAMP(events.event_timestamp)',
					//'user_update_ts'   => 'UNIX_TIMESTAMP(events_occurrence_users.event_occurrence_user_timestamp)',
					'blurb'    => 'events.event_blurb',
					'shortloc' => $occurrence_query->ExpressionLocationName(),
					'type'     => 'event_types.event_type_name',
					'state'    => 'event_occurrences.event_occurrence_state',
					'organisation' => 'organisations.organisation_name',
					'organisation_directory' => 'organisations.organisation_directory_entry_name',
					//'',
				);
			$this->mOccurrences = $filter->GenerateOccurrences($fields);
		}

		return $success;
	}

	/// Get the day information retrieved.
	/**
	 * @pre IsEnabled('day-info')
	 * @pre Retrieve has been called successfully.
	 * @return Array of days with related information, indexed by timestamp of
	 *    midnight on the morning of the day.
	 */
	function GetDayInformation()
	{
		return $this->mDayInformation;
	}

	/// Get the event occurrences retrieved.
	/**
	 * @pre IsEnabled('occurrences-all')
	 * @pre Retrieve has been called successfully.
	 * @return Array of occurrences.
	 */
	function GetOccurrences()
	{
		return $this->mOccurrences;
	}



	// SELECTORS
	// organiser
	/// Get information about the RSVP's to the occurrences of an event
	/**
	 * @param $EventId integer Id of event.
	 * @return array Rsvp's to occurrences of event.
	 * @pre Organisation owns the event
	 */
	function GetEventRsvp($EventId)
	{
		$occurrence_query = new EventOccurrenceQuery();

		$sql = '
		SELECT
			event_occurrence_users.event_occurrence_user_event_occurrence_id
									AS occurrence_id,
			users.user_firstname	AS firstname,
			users.user_surname		AS surname,
			users.user_nickname		AS nickname,
			IF(	subscriptions.subscription_organisation_confirmed = TRUE AND
				subscriptions.subscription_user_confirmed = TRUE AND
				subscriptions.subscription_email = TRUE,
				entities.entity_username, NULL)		AS email
		FROM	event_occurrence_users

		INNER JOIN event_occurrences
			ON	event_occurrences.event_occurrence_id
				= event_occurrence_users.event_occurrence_user_event_occurrence_id
			AND	event_occurrences.event_occurrence_event_id = '.$this->db->escape($EventId).'
		INNER JOIN events
			ON	event_occurrence_event_id = event_id
		INNER JOIN users
			On	users.user_entity_id
				= event_occurrence_users.event_occurrence_user_user_entity_id
		INNER JOIN entities
			ON entities.entity_id = users.user_entity_id
		LEFT JOIN event_entities
			ON	event_entities.event_entity_event_id = '.$this->db->escape($EventId).'
		LEFT JOIN subscriptions
			ON	subscriptions.subscription_user_entity_id
				= event_occurrence_users.event_occurrence_user_user_entity_id
			AND	subscriptions.subscription_organisation_entity_id
				= ' . $this->GetActiveEntityId() . '
		WHERE	event_occurrence_users.event_occurrence_user_attending = TRUE
			AND	'.$occurrence_query->ExpressionOwned();

		$query = $this->db->query($sql);

		return $query->result_array();
	}

	/// Get information about the RSVP's to an occurrence
	/**
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return array Rsvp's to occurrence.
	 * @pre Organisation owns the occurrence
	 */
	function GetOccurrenceRsvp($OccurrenceId)
	{
		$occurrence_query = new EventOccurrenceQuery();

		$sql = '
		SELECT
			event_occurrence_users.event_occurrence_user_event_occurrence_id
									AS occurrence_id,
			users.user_firstname	AS firstname,
			users.user_surname		AS surname,
			users.user_nickname		AS nickname,
			IF(	subscriptions.subscription_organisation_confirmed = TRUE AND
				subscriptions.subscription_user_confirmed = TRUE AND
				subscriptions.subscription_email = TRUE,
				entities.entity_username, NULL)		AS email
		FROM	event_occurrence_users
		INNER JOIN event_occurrences
			ON	event_occurrences.event_occurrence_id
				= event_occurrence_users.event_occurrence_user_event_occurrence_id
			AND	event_occurrences.event_occurrence_id = ' . $OccurrenceId . '
		INNER JOIN events
			ON	event_occurrence_event_id = event_id
		INNER JOIN users
			On	users.user_entity_id
				= event_occurrence_users.event_occurrence_user_user_entity_id
		INNER JOIN entities
			ON entities.entity_id = users.user_entity_id
		LEFT JOIN event_entities
			ON	event_entities.event_entity_event_id
				= event_occurrences.event_occurrence_event_id
		LEFT JOIN subscriptions
			ON	subscriptions.subscription_user_entity_id
				= event_occurrence_users.event_occurrence_user_user_entity_id
			AND	subscriptions.subscription_organisation_entity_id
				= ' . $this->GetActiveEntityId() . '
		LEFT JOIN locations
			ON locations.location_id
				= event_occurrences.event_occurrence_location_id
		WHERE	event_occurrence_users.event_occurrence_user_attending = TRUE
			AND	'.$occurrence_query->ExpressionOwned();

		$query = $this->db->query($sql);

		return $query->result_array();
	}

	/// Get occurrences which have changed so that the user needs informing.
	/**
	 * @return array of occurrences:
	 *  - 'event_id'
	 *  - 'occurrence_id'
	 *  - 'name'
	 *  - 'state'
	 *  - 'start_time'
	 *  - 'start_time'
	 *  - 'time_associated'
	 */
	function GetOccurrenceAlerts()
	{
		$occurrence_query = new EventOccurrenceQuery();
		$sql = '
			SELECT
				event_id,
				event_occurrence_id AS occurrence_id,
				event_name AS name,
				'.$occurrence_query->ExpressionPublicState('event_occurrences').' AS state,
				UNIX_TIMESTAMP(event_occurrence_start_time) AS start_time,
				UNIX_TIMESTAMP(event_occurrence_end_time) AS end_time,
				event_time_associated AS time_associated
			FROM event_occurrence_users
			INNER JOIN event_occurrences
				ON event_occurrence_id = event_occurrence_user_event_occurrence_id
			INNER JOIN events
				ON event_id = event_occurrence_event_id
			WHERE	event_occurrence_user_user_entity_id = '.$this->GetActiveEntityId().'
				AND '.$occurrence_query->ExpressionVisibilityRsvp().'
				AND event_occurrence_state = \'cancelled\'
				AND NOT event_deleted
			ORDER BY event_occurrence_start_time ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	/// Dismiss a cancelled occurrence.
	function DismissCancelledOccurrence($occurrence_id)
	{
		$occurrence_query = new EventOccurrenceQuery();
		$sql = '
			UPDATE	event_occurrence_users
			INNER JOIN event_occurrences
				ON event_occurrence_id = event_occurrence_user_event_occurrence_id
			INNER JOIN events
				ON event_id = event_occurrence_event_id
			SET		event_occurrence_user_attending = NULL
			WHERE	event_occurrence_user_event_occurrence_id = '.$this->db->escape($occurrence_id).'
				AND event_occurrence_user_user_entity_id = '.$this->GetActiveEntityId().'
				AND '.$occurrence_query->ExpressionVisibilityRsvp().'
				AND event_occurrence_state = \'cancelled\'
				AND NOT event_deleted';
		
		$this->db->query($sql);
		return $this->db->affected_rows();
	}

	// TRANSACTIONS
	// organiser
	/// Create preliminary subscriptions for a set of users to a feed.
	/**
	 * @param $EntityId integer Feed id.
	 * @param $Users array Array of email addresses.
	 * @return integer Number of subscriptions created.
	 * @pre Feed @a $EntityId has events enabled.
	 * @post Each user in @a $User without an existing subscription will have
	 *	one set up without user confirmation yet.
	 */
	function FeedSubscribeUsers($EntityId, $Users)
	{
		/// @todo Implement.
	}

	/// Get a list of available event categories.
	/**
	 * @return array[array('id' =>, 'name' =>, 'colour' =>)]
	 */
	function CategoriesGet()
	{
		$this->db->select(
			'event_type_id				AS id,'.
			'event_type_name			AS name,'.
			'event_type_border_colour	AS border_colour,'.
			'event_type_head_colour_hex	AS heading_colour,'.
			'event_type_colour_hex		AS colour,'.
			'event_type_body_image		AS image'
		);
		$category_query = $this->db->Get('event_types');
		$categories = array();
		foreach ($category_query->result_array() as $category) {
			$categories[(int)$category['id']] = $category;
		}
		return $categories;
	}


	/// Get an existing event.
	/**
	 * @param $Fields array of aliases to select expressions (field names).
	 *	e.g. array('name' => 'events.event_name')
	 * @param $EventId
	 *	- integer Event id.
	 *	- FALSE all events.
	 * @param $RecurrenceRule bool Whether to get recurrence rule.
	 * @param $Filter string SQL filter expression.
	 * @param $FilterBind array Bind data associated with @a $Filter.
	 * @return
	 *	- array of Event data arrays.
	 *	- FALSE on failure.
	 */
	function EventsGet($Fields, $EventId = FALSE, $RecurrenceRule = FALSE, $Filter = '', $FilterBind = array())
	{
		/// @todo Tweak to get recurrence rules with new system
		assert(FALSE);
		if ($RecurrenceRule) {
			$Fields[] = $this->recurrence_model->SqlSelectRecurrenceRule();
		}

		$FieldStrings = array();
		foreach ($Fields as $Alias => $Expression) {
			if (is_string($Alias)) {
				$FieldStrings[] = $Expression.' AS '.$Alias;
			} else {
				$FieldStrings[] = $Expression;
			}
		}

		$occurrence_query = new EventOccurrenceQuery();

		$sql = '
			SELECT '.implode(',',$FieldStrings).' FROM events
			LEFT JOIN event_types
				ON	events.event_type_id = event_types.event_type_id
			LEFT JOIN event_entities
				ON	event_entities.event_entity_event_id = events.event_id';
		if ($RecurrenceRule) {
			$sql .= '
			LEFT JOIN recurrence_rules
				ON events.event_recurrence_rule_id
					= recurrence_rules.recurrence_rule_id';
		}

		$bind_data = array();
		$conditions = array(
			'events.event_deleted = 0'
		);
		if (FALSE !== $EventId) {
			$conditions[] = 'events.event_id = ?';
			$bind_data[] = $EventId;
		}
		if (!empty($Filter)) {
			$conditions[] = '('.$Filter.')';
			$bind_data += $FilterBind;
		}
		$sql .= ' WHERE	' . $occurrence_query->ExpressionOwned();
		if (!empty($conditions)) {
			$sql .= ' AND ' . implode(' AND ',$conditions);
		}

		$query = $this->db->query($sql, $bind_data);
		if ($query->num_rows() > 0) {
			$results = $query->result_array();
			if ($RecurrenceRule) {
				foreach ($results as $key => $result) {
					if (NULL !== $results[$key]['recurrence_rule_id']) {
						$results[$key]['event_recurrence_rule'] = new RecurrenceRule($result);
					}
				}
			}
			return $results;
		} else {
			return FALSE;
		}
	}

	/// Generate a unique identifier for an event.
	function GenerateEventUid($EventTime)
	{
		$now = time();
		$nowtime = gmdate('Ymd',$now).'T'.gmdate('His',$now).'Z';
		$eventtime = gmdate('Ymd',$EventTime).'T'.gmdate('His',$EventTime).'Z';

		$result = $nowtime.'-'.sha1($eventtime.'YORKER'.mt_rand().$_SERVER['SERVER_ADDR'].session_id());
		$result .= '@theyorker.co.uk';
		return $result;
	}

	/// Create a new event.
	/**
	 * @param $EventData array Event data. The following are compulsory:
	 *	- 'recur' RecurrenceSet Recurrences.
	 *	- 'location_name'
	 *	- 'occurrences' array['YYYYMMDD' => array['HHMMSS' => array]] recur property overrides.
	 *		Fields are:
	 *		'location_id'
	 *		'location_name'
	 * @return array
	 *	- 'event_id' => new event id.
	 *	- 'occurrences' => number of occurrences generated.
	 * @exception Exception The event could not be created.
	 *
	 * - Uses @a $EventData to create a new event.
	 * - Uses @a $EventData['recur'] to generate occurrences.
	 * - Uses @a $EventData['occurrences'] to set occurrence specific properties.
	 */
	function EventCreate($EventData)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		// if the recurrence rule isn't set set it and force it to be a todo
		if (!array_key_exists('recur', $EventData)) {
			$EventData['recur'] = new RecurrenceSet();
		}
		static $translation = array(
			'name'				=> 'event_name',
			'description'		=> 'event_description',
			'location_name'		=> 'event_location_name',
			/// @pre $EventData['category'] is valid
			'category'			=> 'event_type_id',
			'parent'			=> 'event_parent_id',
			'time_associated'	=> 'event_time_associated',
			'todo'				=> 'event_todo',
		);
		list($start, $end) = $EventData['recur']->GetStartEnd();
		if (NULL === $start) {
			$start = time();
			$EventData['recur']->SetStartEnd($start, $end);
		}
		/// @pre Start must be a timestamp
		assert('is_int($start)');
		/// @pre End must be a timestamp or NULL
		assert('is_int($end) || NULL === $end');
		if (NULL === $end) {
			$EventData['todo'] = TRUE;
		}

		$fields = array(
			'event_organiser_entity_id = '.$this->GetActiveEntityId(),
			'event_uid = "'.$this->GenerateEventUid($start).'"',
		);
		$fields[] = 'event_start = FROM_UNIXTIME('.$start.')';
		if (is_int($end)) {
			$fields[] = 'event_end = FROM_UNIXTIME('.$end.')';
		} else {
			$fields[] = 'event_end = NULL';
		}

		foreach ($translation as $input_name => $field_name) {
			if (array_key_exists($input_name, $EventData)) {
				$fields[] = $field_name.'='.$this->db->escape($EventData[$input_name]);
			}
		}

		// Range to generate occurrences within
		$generate_min = strtotime('today-1month');
		$generate_until = strtotime('today+2year');

		// Generate
		$recurrence_set = $EventData['recur']->Resolve($generate_min, $generate_until);

		// Now go through, making a list
		$occurrences = array();
		foreach ($recurrence_set as $date => $times) {
			foreach ($times as $time => $duration) {
				$occurrence = array();
				if (NULL === $time) {
					$occurrence['start'] = strtotime($date);
				} else {
					$occurrence['start'] = strtotime($date.' '.$time);
				}
				// Handle unending todo
				if (NULL === $duration) {
					$occurrence['end'] = NULL;
				} else {
					$occurrence['end'] = $occurrence['start'] + $duration;
				}
				if (array_key_exists('time_associated', $EventData)) {
					$occurrence['time_associated'] = $EventData['time_associated'];
				}
// 				// Event now has its own location field
// 				if (array_key_exists('location', $EventData)) {
// 					$occurrence['location'] = $EventData['location'];
// 				}
				$occurrence['state'] = 'draft';
				$occurrences[] = $occurrence;
			}
		}

		$fields[] = 'event_recurrence_updated_until = FROM_UNIXTIME('.$generate_until.')';

		// create new event
		$sql_insert = 'INSERT INTO events SET '.implode(',', $fields);
		$this->db->query($sql_insert);
		$affected_rows = $this->db->affected_rows();
		if ($affected_rows == 0) {
			throw new Exception('Event could not be created (1)');
		}
		$event_id = $this->db->insert_id();

		$this->recurrence_model->InsertRecurrenceSet($EventData['recur'], $event_id);

		if (!empty($occurrences)) {
			$num_occurrences = $this->OccurrencesAdd($event_id, $occurrences);
		} else {
			$num_occurrences = 0;
		}

		// Return some information about the created event.
		return array(
			'event_id'		=> $event_id,
			'occurrences'	=> $num_occurrences,
		);
	}

	/// Event delete.
	/**
	 * @param $EventId int Event identifier.
	 * @return Whether successfully deleted.
	 */
	function EventDelete($EventId)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		// Don't need to delete occurrences as if the event is marked as deleted
		// they won't show up from any queries anymore.
		/// @todo BUT then cancelled occurrences don't show up, this needs sorting.
		if (false) {
			// cancel all published occurrences
			$this->OccurrencePublishedCancel($EventId, NULL);
			// delete all unpublished occurrences
			$this->OccurrenceChangeState($EventId, NULL, NULL, 'deleted', array(
				'event_occurrences.event_occurrence_state NOT IN ("published", "cancelled")'
			));
		}

		$occurrence_query = new EventOccurrenceQuery();
		// delete the event
		$delete_sql =
		'UPDATE events
		LEFT JOIN event_entities ON event_id = event_entity_event_id '.
			'AND event_entity_entity_id = '.$occurrence_query->GetEntityId().'
		SET event_deleted = TRUE
		WHERE event_id = '.$this->db->escape($EventId).' AND
			'.$occurrence_query->ExpressionOwned();

		$this->db->query($delete_sql);
		return $this->db->affected_rows();
	}

	/// Alter existing events.
	/**
	 * @param $EventData array Array of Event data arrays.
	 * @return array(
	 *	'events' => events changed,
	 *	'occurrences' => occurrences changed)
	 * @exception Exception The event could not be created.
	 */
	function EventsAlter($EventData)
	{
		/// @pre Each event must have an 'id' element.
		assert('array_key_exists(\'id\',$EventData) && is_int($EventData[\'id\'])');
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}

		$event_id = $EventData['id'];

		static $translation = array(
			'name'				=> 'events.event_name',
			'description'		=> 'events.event_description',
			'location_name'		=> 'events.event_location_name',
			'time_associated'	=> 'events.event_time_associated',
			'category'			=> 'events.event_type_id',
		);
		$fields = array(
			'event_timestamp=NOW()'
		);
		foreach ($translation as $input_name => $field_name) {
			if (array_key_exists($input_name, $EventData)) {
				$fields[] = $field_name.'='.$this->db->escape($EventData[$input_name]);
			}
		}
		if (isset($EventData['recur'])) {
			list($start, $end) = $EventData['recur']->GetStartEnd();
			$fields[] = 'events.event_start=FROM_UNIXTIME('.$this->db->Escape($start).')';
			$fields[] = 'events.event_end=FROM_UNIXTIME('.$this->db->Escape($end).')';
		}
		if (!empty($fields)) {
			$occurrence_query = new EventOccurrenceQuery();
	
			// create new event
			$sql = 'UPDATE events
					LEFT JOIN event_entities
						ON	event_entities.event_entity_event_id = events.event_id
					SET ' . implode(',',$fields) . '
					WHERE ' . $occurrence_query->ExpressionOwned() . ' AND events.event_id = '.$this->db->escape($event_id);
			
	// 		var_dump($sql);
	// 		exit;
			
	// 		$this->db->join('event_entities','event_entities.event_entity_event_id = events.event_id AND ' . $occurrence_query->ExpressionOwned()
	// 		$this->db->set(
	// 		$this->db->where(array('events.event_id' => $event_id));
	// 		$this->db->update('events');
	
			$query = $this->db->query($sql);
			$num_events = $this->db->affected_rows();
		} else {
			$num_events = 0;
		}
		// Only continue if something was succesfully changed in the event as UpdateRecurrenceSet does not check permissions
		if ($num_events && isset($EventData['recur'])) {
			$this->recurrence_model->UpdateRecurrenceSet($EventData['recur'], $event_id);
			$changes = $this->ResolveRecurrenceSetOccurrences($event_id, $EventData['recur']);
			$this->CommitRecurrenceSetOccurrences($event_id, $changes);
		}
		
// 		var_dump($num_events);

		if (array_key_exists('occurrences',$EventData)) {
			$num_occurrences = $this->OccurrencesAlter($event_id, $EventData['occurrences']);
		} else {
			$num_occurrences = 0;
		}

		// Return some information about the created event.
		return array($num_events, $num_occurrences);
	}

	/// Generate occurrences for an event or events.
	/**
	 * @param $Until timestamp Date to generate occurrences up to.
	 * @param $EventId
	 *	- integer Event id.
	 *	- FALSE all events.
	 * @return int Number of occurrences created.
	 *
	 * This should be called periodically with an increased @a $Until.
	 *
	 * This should be called for new recurring events before being published
	 */
	function EventsGenerateRecurrences($Until, $EventId = FALSE)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		// Initial query to get the events
		$bind_data = array();
		$sql_select_events = '
			SELECT
				events.event_id,
				events.event_name,
				'.$this->recurrence_model->SqlSelectRecurrenceRule().'
			FROM	events
			INNER JOIN recurrence_rules
					ON events.event_recurrence_rule_id
						= recurrence_rules.recurrence_rule_id
			WHERE	events.event_deleted = 0
				AND	(	events.event_recurrence_updated_until
							< FROM_UNIXTIME(?)
					OR	events.event_recurrence_updated_until
							IS NULL)';
		$bind_data[] = $Until;
		if (FALSE !== $EventId) {
			$sql_select_events .= '
				AND	event.event_id = ?';
			$bind_data[] = $EventId;
		}

		// Perform the query now
		$query = $this->db->query($sql_select_events, $bind_data);

		// Return values
		$occurrences_created = 0;
		$events_generated = 0;
		$events_mutexed = 0;
		$mutexes_stolen = 0;

		// foreach event
		if ($query->num_rows() > 0) {
			$events = $query->result_array();
			foreach ($events as $event) {
				// Query to set the event recurrence mutex and timestamp
				// Note that affected_rows will only be 1 if
				//	the event exists AND
				//	the mutex isn't already set,
				//		OR timestamp is NULL,
				//		OR timestamp is more than an hour in the past
				// I.E. the mutex is lost after 5 minutes
				$sql_set_mutex = '
					UPDATE	events
					SET		events.event_recurrence_mutex = 1,
							events.event_timestamp = CURRENT_TIMESTAMP()
					WHERE	events.event_id = ?
						AND	(	events.event_recurrence_mutex = 0
							OR	events.event_timestamp = NULL
							OR	events.event_timestamp < DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 5 MINUTE))';
				$set_mutex_bind_data = array($event['event_id']);

				// Query to uset the event recurrence mutex
				// Note that if it is already uset, affected_rows will be 0
				$sql_unset_mutex_failure = '
					UPDATE	events
					SET		events.event_recurrence_mutex = 0
					WHERE	events.event_id = ?';
				$unset_mutex_bind_data_fail = array($event['event_id']);
				$sql_unset_mutex_success = '
					UPDATE	events
					SET		events.event_recurrence_mutex = 0,
							events.event_recurrence_updated_until = FROM_UNIXTIME(?)
					WHERE	events.event_id = ?';
				$unset_mutex_bind_data_success = array($Until, $event['event_id']);

				// Get the mutex
				$this->db->query($sql_set_mutex, $set_mutex_bind_data);
				// if we managed to get the mutex
				if ($this->db->affected_rows() > 0) {
					// get previous update time
					$sql_get_update = '
						SELECT	UNIX_TIMESTAMP(events.event_recurrence_updated_until)
									AS previous_update
						FROM	events
						WHERE	events.event_id = ?';
					$query_get_update = $this->db->query($sql_get_update, $event['event_id']);
					if ($query_get_update->num_rows() == 1) {
						$previous_update = $query_get_update->row()->previous_update;
						if ($previous_update < time()) {
							$previous_update = time();
						}

						// find recurrences between event.until and $Until
						$event['recurrence_rule_offset_minutes'] = 0;
						$recurrence_rule = new RecurrenceRule($event);
						$recurrences = $recurrence_rule->FindTimes($previous_update, $Until);

						if (count($recurrences) > 0) {
							// save occurrences and update event.until to $Until
							$occurrence_data = array();
							foreach ($recurrences as $when => $value) {
								$occurrence_data[] = array(
									'state'				=> 'published',
									'time_associated'	=> TRUE,
									'ends_late'			=> FALSE,
									'start'				=> $when,
									'end'				=> strtotime('+1day',$when),
								);
							}
							//$this->messages->AddDumpMessage('data',$occurrence_data);
							$occurrences_created += $this->OccurrencesAdd(
								$event['event_id'],
								$occurrence_data);
						}

						++$events_generated;

						// Signal the mutex
						$this->db->query($sql_unset_mutex_success, $unset_mutex_bind_data_success);
					} else {
						// Signal the mutex
						$this->db->query($sql_unset_mutex_failure, $unset_mutex_bind_data_fail);
					}

					if ($this->db->affected_rows() === 0) {
						++$mutexes_stolen;
					}
				} else {
					++$events_mutexed;
				}
			}
		}

		return array(
			$occurrences_created,
			$events_generated,
			$events_mutexed,
			$mutexes_stolen
		);
	}

	/// Add occurrences to an event.
	/**
	 * @param $EventId int ID of event to add occurrence to.
	 * @param $OccurrenceData array Array of Occurrence Data arrays.
	 * @return integer Number of occurrences added.
	 */
	function OccurrencesAdd($EventId, $OccurrenceData)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		$occurrence_query = new EventOccurrenceQuery();

		static $translation2 = array(
			'state',
			'location',
			'postcode',
			'time_associated',
			'ends_late',
		);

		// Check the event is owned by this user.
		$this->db->select('COUNT(*)');
		$this->db->from('events');
		$this->db->join('event_entities','event_id = event_entity_event_id','left');
		$this->db->where('event_entity_event_id', $EventId);
		$this->db->where($occurrence_query->ExpressionOwned());
		$query = $this->db->get();
		if ($query->num_rows() > 0) {

			// create each occurrences
			$sql = 'INSERT INTO event_occurrences (
					event_occurrence_event_id,
					event_occurrence_state,
					event_occurrence_location_name,
					event_occurrence_postcode,
					event_occurrence_original_start_time,
					event_occurrence_start_time,
					event_occurrence_end_time,
					event_occurrence_time_associated,
					event_occurrence_ends_late)
				VALUES';
			$first = TRUE;
			foreach ($OccurrenceData as $occurrence) {
				// Ensure state is value
				if (array_key_exists('state', $occurrence)) {
					switch ($occurrence['state']) {
						case 'draft':
						case 'movedraft':
						case 'trashed':
						case 'published':
						case 'cancelled':
						case 'deleted':
							break;
						default:
							unset($occurrence['state']);
							break;
					}
				}

				// Get the values from the input
				$values = array_fill(0, count($translation2), 'DEFAULT');
				foreach ($translation2 as $field_name => $input_name) {
					if (array_key_exists($input_name, $occurrence)) {
						$values[$field_name] = $this->db->escape($occurrence[$input_name]);
					}
				}

				if (!$first)
					$sql .= ',';
				$sql .=	' ('.$EventId.
						','.$values[0].		// state
						','.$values[1].		// location
						','.$values[2];		// postcode
				// original and start time
				if (array_key_exists('start', $occurrence)) {
					$sql .= str_repeat(',FROM_UNIXTIME('.$occurrence['start'].')', 2);
				} else {
					$sql .= str_repeat(',DEFAULT', 2);
				}
				// end time
				if (array_key_exists('end', $occurrence) && NULL !== $occurrence['end']) {
					$sql .= ',FROM_UNIXTIME('.$occurrence['end'].')';
				} else {
					$sql .= ',DEFAULT';
				}
				$sql .= ','.$values[3].		// time associated
						','.$values[4].')'; // ends_late

				$first = FALSE;
			}
			//$this->messages->AddDumpMessage('sql add',$sql);	return 0;
			$query = $this->db->query($sql);
			return $this->db->affected_rows();

		} else {
			return 0;
		}
	}

	/// Edit existing occurrences.
	/**
	 * @param $EventId int ID of event to add occurrence to.
	 * @param $OccurrenceData array Array of Occurrence Data arrays.
	 * @return Number of occurrences changed.
	 * @pre Each occurrence must have an 'id' element.
	 */
	function OccurrencesAlter($EventId, $OccurrenceData)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		$occurrence_query = new EventOccurrenceQuery();

		static $translation = array(
			'location'			=> 'event_occurrences.event_occurrence_location_name',
			'postcode'			=> 'event_occurrences.event_occurrence_postcode',
		);
		$result = 0;
		foreach ($OccurrenceData as $occurrence) {
			$bind_data = array();
			$sets = array();
			foreach ($occurrence as $key => $value) {
				if (array_key_exists($key, $translation)) {
					$sets[] = $translation[$key] . '=?';
					$bind_data[] = $value;
				}
			}
			// start time
			if (array_key_exists('start', $occurrence)) {
				$sets[] = 'event_occurrences.event_occurrence_start_time = FROM_UNIXTIME(?)';
				$bind_data[] = $occurrence['start'];
			}
			// end time
			if (array_key_exists('end', $occurrence)) {
				$sets[] = 'event_occurrences.event_occurrence_end_time = FROM_UNIXTIME(?)';
				$bind_data[] = $occurrence['end'];
			}
			
			if (!empty($sets)) {
				$sql = 'UPDATE event_occurrences
					LEFT JOIN event_entities
						ON	event_entities.event_entity_event_id
							= event_occurrences.event_occurrence_event_id
					SET ' . implode(', ', $sets) . '
					WHERE	event_occurrences.event_occurrence_id = ?
						AND	event_occurrences.event_occurrence_event_id = ?
						AND ' . $occurrence_query->ExpressionOwned();
				$bind_data[] = $occurrence['id'];
				$bind_data[] = $EventId;

				$query = $this->db->query($sql, $bind_data);
				$result += $this->db->affected_rows();
			}
		}
		return $result;
	}

	/// Resolve the differences between the recurrence set of an event and its occurrences.
	/**
	 * @param $EventId int ID of event.
	 * @param $RSet RecurrenceSet The recurrence information.
	 * @return array of change lists. The intention is for this to be passed
	 *  into @a CommitRecurrenceSetOccurrences without change.
	 *  - 'remove'
	 *  - 'move'
	 *  - 'create'
	 *  - 'restore'
	 *
	 * This is pretty clever little function. It figures out the differences
	 * between @a $RSet and the actual occurrences and makes changes to the
	 * occurrences by adding, removing, and (within a day) moving occurrences.
	 */
	function ResolveRecurrenceSetOccurrences($EventId, & $RSet)
	{
		list($start) = $RSet->GetStartEnd();
		$range_start = strtotime('today-1month');
		$range_end = strtotime('today+2years');
		
		// Get all occurrences of the event
		//  id, state
		$occurrence_query = new EventOccurrenceQuery();
		$sql = 'SELECT
				event_occurrence_id		AS id,
				event_occurrence_state	AS state,
				UNIX_TIMESTAMP(event_occurrence_start_time)	AS start_time,
				UNIX_TIMESTAMP(event_occurrence_end_time)	AS end_time
			FROM events
			INNER JOIN event_occurrences
				ON event_occurrence_event_id = event_id
			LEFT JOIN event_entities
				ON	event_entities.event_entity_event_id
						= event_occurrences.event_occurrence_event_id
			WHERE event_id = '.$this->db->Escape($EventId).'
			AND '.$occurrence_query->ExpressionOwned();
		$results = $this->db->query($sql);
		$occurrence_data = $results->result_array();
		
		// Arrays for storing actions chosen by analysis.
		$results = array();
		
		// Reformat occurrence data into time indexing
		$occurrences = array();
		foreach ($occurrence_data as $occurrence) {
			$ts = $occurrence['start_time'];
			$day = date('Ymd', $ts);
			$time = date('His', $ts);
			$state = $occurrence['state'];
			$occurrence['active'] = ($state == 'published' or $state == 'draft' or $state == 'movedraft');
			$occurrence['day'] = $day;
			$occurrence['time'] = $time;
			if (NULL !== $occurrence['end_time']) {
				$occurrence['duration'] = $occurrence['end_time'] - $ts;
			} else {
				$occurrence['duration'] = NULL;
			}
			if (isset($occurrences[$day][$time])) {
				if ($occurrence['active']) {
					if (!$occurrences[$day][$time]['active']) {
						$occurrences[$day][$time] = $occurrence;
					} else {
						// Already an occurrence here, so delete this one
						$results['remove'][] = $occurrence;
					}
				}
			} else {
				$occurrences[$day][$time] = $occurrence;
			}
			
			// If any of the occurrences are greater than the current range_end, extend it
			if (is_numeric($occurrence['start_time']) and $occurrence['start_time'] > $range_end) {
				$range_end = $occurrence['start_time'];
			}
		}
		
		// Get the recurrences from the rule
		$recurrences = $RSet->Resolve($range_start, $range_end);
		
		// First cancel / delete any occurrences which are removed.
		foreach ($occurrences as $day => $times) {
			// If this day is not in $recurrences, cancel
			if (!isset($recurrences[$day])) {
				foreach ($times as $time => $occurrence) {
					// Its gotta be in the future for us to do anything about it
					if ($occurrence['active'] && $occurrence['start_time'] >= $range_start) {
						$results['remove'][] = $occurrence;
					}
				}
			}
			
			// Check that they match
			else {
				// Find the times which differ
				$changed_times = array();
				foreach ($times as $time => $occurrence) {
					$active = $occurrence['active'];
					$willexist = isset($recurrences[$day][$time]);
					if (!$active and $willexist) {
						// an inactive event has been restored
						$results['restore'][] = array($occurrence, $recurrences[$day][$time]);
					} elseif ($active and !$willexist) {
						// an active event has been removed
						$occurrence['new'] = false;
						$changed_times[$time] = $occurrence;
					} elseif ($active and $willexist) {
						if ($occurrence['duration'] !== $recurrences[$day][$time]) {
							$results['move'][] = array(
								$occurrence,
								array(
									'duration' => $recurrences[$day][$time],
									'day' => $day,
									'time' => $time,
								)
							);
						}
					}
				}
				foreach ($recurrences[$day] as $time => $duration) {
					if (!isset($times[$time])) {
						$recurrence_info = array(
							'new' => true,
							'duration' => $duration,
							'day' => $day,
							'time' => $time,
						);
						$changed_times[$time] = $recurrence_info;
					} elseif ($times[$time]['state'] == 'draft') {
						$results['draft'][] = array(
							'day' => $day,
							'time' => $time,
							'duration' => $duration,
						);
					}
				}
				
				// Match together adds and removes to form moves
				// + +- +- +- -+ + +- +
				ksort($changed_times);
				$last_new = NULL;
				$last_occ = NULL;
				foreach ($changed_times as $time => $occurrence) {
					if ($last_new === NULL) {
						// First in sequence
						$last_new = $occurrence['new'];
						$last_occ = $occurrence;
					} elseif ($occurrence['new'] === $last_new) {
						// Another of same newness, take action with last one
						if ($last_new) {
							$results['create'][] = $last_occ;
						} else {
							$results['remove'][] = $last_occ;
						}
						$last_occ = $occurrence;
					} else {
						// Pair this and previous, and restart the sequence
						if ($last_new) {
							$results['move'][] = array($occurrence, $last_occ);
						} else {
							$results['move'][] = array($last_occ, $occurrence);
						}
						$last_new = NULL;
					}
				}
				// Unmatched at end?
				if ($last_new === TRUE) {
					$results['create'][] = $last_occ;
				} elseif ($last_new === FALSE) {
					$results['remove'][] = $last_occ;
				}
			}
		}
		// Create any recurrences which are new.
		foreach ($recurrences as $day => $times) {
			// If this day is not in $occurrences, create
			if (!isset($occurrences[$day])) {
				foreach ($times as $time => $duration) {
					$results['create'][] = array(
						'day' => $day,
						'time' => $time,
						'duration' => $duration,
					);
				}
			}
		}
		// calculate start timestamps of create list.
		if (isset($results['create'])) {
			foreach ($results['create'] as &$recurrence) {
				$recurrence['start_time'] = strtotime($recurrence['day'].' '.$recurrence['time']);
				if (NULL !== $recurrence['duration']) {
					$recurrence['end_time'] = strtotime($recurrence['duration'].' seconds', $recurrence['start_time']);
				} else {
					$recurrence['end_time'] = NULL;
				}
			}
		}
		// calculate final start timestamps of move list.
		if (isset($results['move'])) {
			foreach ($results['move'] as &$info) {
				$recurrence = & $info[1];
				$recurrence['start_time'] = strtotime($recurrence['day'].' '.$recurrence['time']);
				if (NULL !== $recurrence['duration']) {
					$recurrence['end_time'] = strtotime($recurrence['duration'].' seconds', $recurrence['start_time']);
				} else {
					$recurrence['end_time'] = NULL;
				}
			}
		}
		// Return the change information.
		return $results;
	}
	
	/// Tidy the result of ResolveRecurrenceSetOccurrences
	function TidyRecurrenceSetOccurrencesChanges($Changes)
	{
		$confirm_list = array();
		foreach ($Changes as $change_type => $occurrences) {
			if ($change_type == 'remove') {
				foreach ($occurrences as $occurrence) {
					// Split into cancel and delete
					$confirm_type = NULL;
					switch ($occurrence['state']) {
						case 'draft':
						case 'movedraft':
							// delete
							$confirm_type = 'delete';
							break;
						case 'published':
							// cancel
							$confirm_type = 'cancel';
							break;
					}
					if (NULL !== $confirm_type) {
						$confirm_list[$confirm_type][$occurrence['day']] = array(
							'start_time' => $occurrence['start_time'],
							'end_time' => $occurrence['end_time'],
							'duration' => $occurrence['duration'],
							'day' => $occurrence['day'],
							'time' => $occurrence['time'],
						);
					}
				}
			} elseif ($change_type == 'restore') {
				foreach ($occurrences as $info) {
					list($occurrence, $duration) = $info;
					// Split into create and restore
					$confirm_type = NULL;
					switch ($occurrence['state']) {
						case 'deleted':
						case 'trashed':
							// create
							$confirm_type = 'create';
							break;
						case 'cancelled':
							// restore
							$confirm_type = 'restore';
							break;
					}
					if (NULL !== $confirm_type) {
						$confirm_list[$confirm_type][$occurrence['day']] = array(
							'start_time' => $occurrence['start_time'],
							'end_time' => $occurrence['end_time'],
							'duration' => $occurrence['duration'],
							'day' => $occurrence['day'],
							'time' => $occurrence['time'],
						);
					}
					
				}
			} elseif ($change_type == 'move') {
				foreach ($occurrences as $info) {
					list($occurrence, $recurrence) = $info;
					$confirm_list['move'][$occurrence['day']] = array(
						'start_time' => $occurrence['start_time'],
						'end_time' => $occurrence['end_time'],
						'duration' => $occurrence['duration'],
						'day' => $occurrence['day'],
						'time' => $occurrence['time'],
						'new_start_time' => $recurrence['start_time'],
						'new_end_time' => $recurrence['end_time'],
						'new_duration' => $recurrence['duration'],
						'new_day' => $recurrence['day'],
						'new_time' => $recurrence['time'],
					);
					
				}
			} elseif ($change_type == 'draft') {
				foreach ($occurrences as $recurrence) {
					$recurrence['start_time'] = strtotime($recurrence['day'].' '.$recurrence['time']);
					if (NULL !== $recurrence['duration']) {
						$recurrence['end_time'] = strtotime($recurrence['duration'].' seconds', $recurrence['start_time']);
					} else {
						$recurrence['end_time'] = NULL;
					}
					$confirm_list[$change_type][$recurrence['day']] = $recurrence;
				}
			} else {
				// Copy over
				foreach ($occurrences as $info) {
					$confirm_list[$change_type][$info['day']] = $info;
				}
			}
		}
		foreach ($confirm_list as $key => &$values) {
			ksort($values);
		}
		return $confirm_list;
	}
	
	/// Commit changes to occurrences.
	/**
	 * @param $EventId int ID of event.
	 * @param $Changes as returned by @a ResolveRecurrenceSetOccurrences.
	 */
	function CommitRecurrenceSetOccurrences($EventId, $Changes)
	{
		// Now we have the lists of changes:
		// remove
		if (isset($Changes['remove'])) {
			foreach ($Changes['remove'] as $occurrence) {
				switch ($occurrence['state']) {
					case 'draft':
					case 'movedraft':
						// delete
						$this->OccurrenceChangeState($EventId, (int)$occurrence['id'], $occurrence['state'], 'deleted');
						break;
					case 'published':
						// cancel
						$this->OccurrenceChangeState($EventId, (int)$occurrence['id'], $occurrence['state'], 'cancelled');
						break;
				}
			}
		}
		// create
		if (isset($Changes['create'])) {
			foreach ($Changes['create'] as $recurrence) {
				// create occurrences as drafts
				$sql_insert = 'INSERT INTO event_occurrences (
					event_occurrence_event_id,
					event_occurrence_state,
					event_occurrence_location_name,
					event_occurrence_original_start_time,
					event_occurrence_start_time,
					event_occurrence_end_time)
					VALUES ('.$this->db->escape($EventId).',
							"draft",
							DEFAULT,
							FROM_UNIXTIME('.$this->db->escape($recurrence['start_time']).'),
							FROM_UNIXTIME('.$this->db->escape($recurrence['start_time']).'),
							FROM_UNIXTIME('.$this->db->escape($recurrence['end_time']).'))';
				$this->db->query($sql_insert);
			}
		}
		// restore
		if (isset($Changes['restore'])) {
			foreach ($Changes['restore'] as $info) {
				list($occurrence, $duration) = $info;
				switch ($occurrence['state']) {
					case 'deleted':
					case 'trashed':
						// draft
						if (is_numeric($duration)) {
							$this->OccurrenceChangeState($EventId, (int)$occurrence['id'], $occurrence['state'], 'draft', array(),
								'event_occurrence_end_time = DATE_ADD(event_occurrence_start_time, INTERVAL '.(int)$duration.' SECOND)'
							);
						} else {
							$this->OccurrenceChangeState($EventId, (int)$occurrence['id'], $occurrence['state'], 'draft', array(),
								'event_occurrence_end_time = NULL'
							);
						}
						break;
					case 'cancelled':
						// publish
						if (is_numeric($duration)) {
							$this->OccurrenceChangeState($EventId, (int)$occurrence['id'], $occurrence['state'], 'published', array(),
								'event_occurrence_end_time = DATE_ADD(event_occurrence_start_time, INTERVAL '.(int)$duration.' SECOND)'
							);
						} else {
							$this->OccurrenceChangeState($EventId, (int)$occurrence['id'], $occurrence['state'], 'published', array(),
								'event_occurrence_end_time = NULL'
							);
						}
						break;
				}
			}
		}
		// move
		if (isset($Changes['move'])) {
			foreach ($Changes['move'] as $info) {
				list($occurrence, $recurrence) = $info;
				// alter existing occurrences copying times + duration
				$start_time = strtotime($recurrence['day'].' '.$recurrence['time']);
				if (NULL !== $recurrence['duration']) {
					$end_time = strtotime($recurrence['duration'].' seconds', $start_time);
				} else {
					$end_time = NULL;
				}
				$sql = 'UPDATE event_occurrences
				SET event_occurrence_start_time = FROM_UNIXTIME('.$start_time.'),
					event_occurrence_end_time = FROM_UNIXTIME('.$end_time.'),
					event_occurrences.event_occurrence_last_modified = CURRENT_TIMESTAMP()
				WHERE event_occurrence_id = '.(int)$occurrence['id'];
	// 			$this->messages->AddDumpMessage('sql',$sql);
				$this->db->query($sql);
			}
		}
	}

	/// Change the state of an occurrence explicitly.
	/**
	 * @param $EventId integer Id of event occurrence belongs to.
	 * @param $OccurrenceId integer Id of occurrence to change the state of.
	 * @param $OldState string Previous private state.
	 * @param $NewState string New private state.
	 * @param $ExtraConsitions array[string] Additional SQL conditions.
	 * @param $ExtraSets array[string=>string] Addition SQL set statements (NOT ESCAPED).
	 * @return integer Number of changed occurrences.
	 */
	protected function OccurrenceChangeState($EventId, $OccurrenceId, $OldState, $NewState, $ExtraConditions = array(), $ExtraSets = '')
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		$occurrence_query = new EventOccurrenceQuery();

		// change the state to $NewState
		// where the state was $OldState
		$sql = 'UPDATE event_occurrences
			INNER JOIN events
				ON	event_id = event_occurrence_event_id
			LEFT JOIN event_entities
				ON	event_entities.event_entity_event_id
						= event_occurrences.event_occurrence_event_id
			SET		event_occurrences.event_occurrence_state=?,
					event_occurrences.event_occurrence_last_modified=CURRENT_TIMESTAMP()';
		if ($ExtraSets != '') {
			$sql .= ", $ExtraSets";
		}
		$sql .= ' WHERE	event_occurrences.event_occurrence_event_id=?
				AND	'.$occurrence_query->ExpressionOwned();
		$bind_data = array($NewState, $EventId);
		if (FALSE !== $OccurrenceId) {
			$sql .= ' AND	event_occurrences.event_occurrence_id=?';
			$bind_data[] = $OccurrenceId;
		}
		if (is_string($OldState)) {
			$sql .= ' AND	event_occurrences.event_occurrence_state=?';
			$bind_data[] = $OldState;
		}
		foreach ($ExtraConditions as $ExtraCondition) {
			$sql .= ' AND ('.$ExtraCondition.')';
		}
		$this->db->query($sql, $bind_data);
		return $this->db->affected_rows();
	}

	/// Change the state of an occurrence explicitly.
	/**
	 * @param $EventId integer Id of event occurrence belongs to.
	 * @param $OccurrenceId array[integer] Ids of occurrence to change the state of.
	 * @param $OldStates array(string) Previous private state.
	 * @param $NewState string New private state.
	 * @param $ExtraConsitions array[string] Additional SQL conditions.
	 * @param $ExtraSets array[string=>string] Addition SQL set statements (NOT ESCAPED).
	 * @return integer Number of changed occurrences.
	 */
	function OccurrencesChangeStateByTimestamp($EventId, $Timestamps, $OldStates, $NewState, $ExtraConditions = array(), $ExtraSets = '')
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		// Don't bother continuing of parameters are empty
		if (!is_int($EventId) ||
			!is_array($Timestamps) || empty($Timestamps) ||
			!is_array($OldStates)  || empty($OldStates))
		{
			return 0;
		}
		$occurrence_query = new EventOccurrenceQuery();

		// change the state to $NewState
		// where the state was in $OldStates
		$sql = 'UPDATE event_occurrences
			INNER JOIN events
				ON	event_id = event_occurrence_event_id
			LEFT JOIN event_entities
				ON	event_entities.event_entity_event_id
						= event_occurrences.event_occurrence_event_id
			SET		event_occurrences.event_occurrence_state='.$this->db->escape($NewState).',
					event_occurrences.event_occurrence_last_modified=CURRENT_TIMESTAMP()';
		if ($ExtraSets != '') {
			$sql .= ", $ExtraSets";
		}
		$sql .= " WHERE	event_occurrences.event_occurrence_event_id=$EventId
				AND	".$occurrence_query->ExpressionOwned();
		
		$start_times = implode(',',array_map(array($this->db, 'escape'), $Timestamps));
		$old_states  = implode(',',array_map(array($this->db, 'escape'), $OldStates));
		$sql .= " AND	UNIX_TIMESTAMP(event_occurrences.event_occurrence_start_time) IN ($start_times)";
		$sql .= " AND	event_occurrences.event_occurrence_state IN ($old_states)";
		foreach ($ExtraConditions as $ExtraCondition) {
			$sql .= ' AND ('.$ExtraCondition.')';
		}
		$this->db->query($sql);
		return $this->db->affected_rows();
	}


	/// Publish a draft occurrence to the feed.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'draft'
	 * @pre Occurrence.start NOT NULL and Occurrence.end NOT NULL
	 * @post 'published'
	 */
	function OccurrenceDraftPublish($EventId, $OccurrenceId)
	{
		return $this->OccurrenceChangeState($EventId,$OccurrenceId, 'draft','published',
			array(	'event_occurrences.event_occurrence_start_time != 0',
					'event_occurrences.event_occurrence_end_time != 0'));
	}

	/// Publish an entire event.
	/**
	 * @param $EventId int ID of event to publish.
	 * @return integer Number of changed occurrences.
	 */
	function EventPublish($EventId)
	{
		return $this->OccurrenceDraftPublish($EventId, FALSE);
	}

	/// Trash a draft occurrence.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'draft'
	 * @post 'trashed'
	 */
	function OccurrenceDraftTrash($EventId, $OccurrenceId)
	{
		return $this->OccurrenceChangeState($EventId,$OccurrenceId, 'draft','trashed');
	}

	/// Restore a trashed occurrence.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'trashed'
	 * @post 'draft'
	 */
	function OccurrenceTrashedRestore($EventId, $OccurrenceId)
	{
		return $this->OccurrenceChangeState($EventId,$OccurrenceId, 'trashed','draft');
	}

	/// Publish a draft occurrence to the feed.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'movedraft'
	 * @pre Occurrence.start NOT NULL and Occurrence.end NOT NULL
	 * @post 'published'
	 */
	function OccurrenceMovedraftPublish($EventId, $OccurrenceId)
	{
		return $this->OccurrenceChangeState($EventId,$OccurrenceId, 'movedraft','published',
			array(	'event_occurrences.event_occurrence_start_time != 0',
					'event_occurrences.event_occurrence_end_time != 0'));
	}

	/// Delete a move draft occurrence, cleaning up after inactive occurrences.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @param $ActivationState string Private state to set postponed occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'movedraft'
	 * @post deleted
	 * @post Inactive occurrence which was postponed is set to @a $ActivationState.
	 */
	protected function OccurrenceMovedraftDelete($EventId, $OccurrenceId, $ActivationState)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		// find recently changed occurrence
		// change all others pointing to $OccurrenceId to point to it, and it to null
		// change state to $ActivationState
		// delete occurrence

		if (FALSE) {
			// Recreate the procedure!
			$occurrence_query = new EventOccurrenceQuery();
			$sql_event_occurrence_movedraft_delete = '
CREATE PROCEDURE event_occurrence_movedraft_delete(
	entity_id			INT,
	event_id			INT,
	occurrence_id		INT,
	activation_state	VARCHAR(10) )
BEGIN
	DECLARE owned INT;
	DECLARE preactive_id INT;
	SELECT COUNT(*) INTO owned
		FROM event_occurrences
		LEFT JOIN event_entities
			ON	event_entities.event_entity_event_id
					= event_occurrences.event_occurrence_event_id
		WHERE	event_occurrences.event_occurrence_id = occurrence_id
			AND	event_occurrences.event_occurrence_event_id = event_id
			AND ' . $occurrence_query->ExpressionOwned('entity_id') . ';
	IF owned THEN
		SELECT preactive.event_occurrence_id INTO preactive_id
			FROM event_occurrences AS preactive
			WHERE preactive.event_occurrence_active_occurrence_id = occurrence_id
			ORDER BY preactive.event_occurrence_last_modified DESC
			LIMIT 1;
		UPDATE	event_occurrences
			SET		event_occurrences.event_occurrence_active_occurrence_id
						= IF(event_occurrences.event_occurrence_id = preactive_id,
							NULL,
							preactive_id),
					event_occurrences.event_occurrence_state
						= IF(event_occurrences.event_occurrence_id = preactive_id,
							activation_state,
							event_occurrences.event_occurrence_state)
			WHERE	event_occurrences.event_occurrence_active_occurrence_id = occurrence_id;
		DELETE FROM event_occurrences
			WHERE	event_occurrences.event_occurrence_id = occurrence_id;
	END IF;
END';
			// Drop and create
			$this->db->query('DROP PROCEDURE event_occurrence_movedraft_delete');
			$this->db->query($sql_event_occurrence_movedraft_delete);

		} else {
			// Call the procedure
			$this->db->query('CALL event_occurrence_movedraft_delete(?,?,?,?)',
				array($this->GetActiveEntityId(), $EventId, $OccurrenceId, $ActivationState));
		}

		// Something should have changed
		return $this->db->affected_rows();
	}

	/// Restore the postponed occurrence of a move draft.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'movedraft'
	 * @post deleted
	 * @post Inactive occurrence which was postponed is restored
	 */
	function OccurrenceMovedraftRestore($EventId, $OccurrenceId)
	{
		return $this->OccurrenceMovedraftDelete($EventId, $OccurrenceId, 'published');
	}

	/// Cancel the postponed occurrences of a move draft.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'movedraft'
	 * @post deleted
	 * @post Inactive occurrence which was postponed is cancelled
	 */
	function OccurrenceMovedraftCancel($EventId, $OccurrenceId)
	{
		// Add an exclude date first
		
		return $this->OccurrenceMovedraftDelete($EventId, $OccurrenceId, 'cancelled');
	}

	/// Cancel a published occurrence.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'published'
	 * @post 'cancelled'
	 */
	function OccurrencePublishedCancel($EventId, $OccurrenceId)
	{
		// Add an exclude date first
		$result = $this->OccurrenceChangeState($EventId,$OccurrenceId, 'published','cancelled');
		if ($result) {
			$this->EventAddExclusionDate($EventId, $OccurrenceId);
		}
		return $result;
	}
	
	/// Add an exclusion date to the recurrence of an event.
	/**
	 * @param $EventId      int The id of the event.
	 * @param $OccurrenceId int The id of the occurrence.
	 * @note This only affects the rule of an event. Occurrences should be
	 *  appropriately cancelled separately.
	 * @note This is a wrapper for EventAddInclusionDate that sets @a $Exclude
	 *  to true.
	 */
	function EventAddExclusionDate($EventId, $OccurrenceId)
	{
		return $this->EventAddInclusionDate($EventId, $OccurrenceId, true);
	}
	
	/// Add an inclusion date to the recurrence of an event.
	/**
	 * @param $EventId      int  The id of the event.
	 * @param $OccurrenceId int  The id of the occurrence.
	 * @param $Exclude      bool Whether to exclude instead of include.
	 * @note This only affects the rule of an event. Occurrences should be
	 *  appropriately created separately.
	 */
	function EventAddInclusionDate($EventId, $OccurrenceId, $Exclude = false)
	{
		$occurrence_query = new EventOccurrenceQuery();
		$sql_replace = '
		REPLACE INTO event_dates (
			event_date_event_id,
			event_date_start,
			event_date_time_associated,
			event_date_duration,
			event_date_exclude
		) SELECT
			event_id,
			event_occurrence_start_time,
			FALSE,
			NULL,
			'.$this->db->escape($Exclude).'
		FROM events
		LEFT JOIN event_entities
			ON	event_entity_event_id = event_id
		INNER JOIN event_occurrences
			ON	event_occurrence_event_id = event_id
			AND	event_occurrence_id = '.$this->db->Escape($OccurrenceId).'
		WHERE event_id = '.$this->db->escape($EventId).'
			AND ' . $occurrence_query->ExpressionOwned() . '
		LIMIT 1';
		$this->db->query($sql_replace);
		return $this->db->affected_rows();
	}

	/// Activate an occurrence.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return int Number of altered rows.
	 * @pre occurrence.active.state = 'cancelled'
	 * @pre occurrence.active.active IS NULL
	 * @post occurrence.active IS NULL
	 */
	function OccurrenceCancelledActivate($EventId, $OccurrenceId)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		// Make this occurrence the active occurrence.
		// update new_active
		// on sibling (same active occurrence)
		// or is active occurrence
		// set active=(sibling is active ? NULL : active)
		// where new active is this
		//	and where new active.active is cancelled and active
		$occurrence_query = new EventOccurrenceQuery();
		$sql_activate = 'UPDATE event_occurrences AS new_active
			INNER JOIN events
				ON	event_id = new_active.event_occurrence_event_id
			LEFT JOIN event_entities
				ON	event_entities.event_entity_event_id
						= new_active.event_occurrence_event_id
			LEFT JOIN event_occurrences AS old_active
				ON	old_active.event_occurrence_id
						= new_active.event_occurrence_active_occurrence_id
			INNER JOIN event_occurrences AS sibling
				ON	sibling.event_occurrence_active_occurrence_id
						= new_active.event_occurrence_active_occurrence_id
				OR	sibling.event_occurrence_id
						= new_active.event_occurrence_active_occurrence_id
			SET	sibling.event_occurrence_active_occurrence_id
					= IF(sibling.event_occurrence_id = new_active.event_occurrence_id,
						NULL,
						new_active.event_occurrence_id)
			WHERE	new_active.event_occurrence_id = ?
				AND	new_active.event_occurrence_event_id = ?
				AND ' . $occurrence_query->ExpressionOwned() . '
				AND	(	old_active.event_occurrence_state IS NULL
					OR	(	old_active.event_occurrence_state = \'cancelled\'
						AND	old_active.event_occurrence_active_occurrence_id IS NULL))';

		$query = $this->db->query($sql_activate, array($OccurrenceId, $EventId));
		return $this->db->affected_rows();
	}

	/// Restore a cancelled occurrence.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'cancelled'
	 * @pre occurrence.active IS 'cancelled'
	 * @post 'published'
	 */
	function OccurrenceCancelledRestore($EventId, $OccurrenceId)
	{
		$this->OccurrenceCancelledActivate($EventId, $OccurrenceId);
		// This occurrence should now be active

		return $this->OccurrenceChangeState($EventId, $OccurrenceId, 'cancelled','published',
			array(	'event_occurrences.event_occurrence_active_occurrence_id IS NULL',
			));
	}

	/// Move a public occurrence.
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return
	 *	- FALSE failure.
	 *	- int success New occurrence id.
	 * @pre 'published' | ('cancelled' & active)
	 * @post 'cancelled' linking to new occurrence
	 * @post new occurrence created in 'movedraft' at new position
	 */
	function OccurrencePostpone($EventId, $OccurrenceId)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		$this->OccurrenceCancelledActivate($EventId, $OccurrenceId);

		$occurrence_query = new EventOccurrenceQuery();

		// create new movedraft
		$sql_insert = 'INSERT INTO event_occurrences (
				event_occurrence_event_id,
				event_occurrence_state,
				event_occurrence_location_id,
				event_occurrence_location_name,
				event_occurrence_postcode,
				event_occurrence_start_time,
				event_occurrence_end_time,
				event_occurrence_ends_late)
			SELECT
				event_occurrences.event_occurrence_event_id,
				\'movedraft\',
				event_occurrences.event_occurrence_location_id,
				event_occurrences.event_occurrence_location_name,
				event_occurrences.event_occurrence_postcode,
				event_occurrences.event_occurrence_start_time,
				event_occurrences.event_occurrence_end_time,
				event_occurrences.event_occurrence_ends_late
			FROM event_occurrences
			LEFT JOIN event_entities
				ON	event_entities.event_entity_event_id
						= event_occurrences.event_occurrence_event_id
			WHERE	event_occurrences.event_occurrence_id = ?
				AND	event_occurrences.event_occurrence_event_id = ?
				AND ' . $occurrence_query->ExpressionOwned();
		$query = $this->db->query($sql_insert,array($OccurrenceId, $EventId));

		// If not owner, then no movedraft will have been created
		// so we can assume ownership from now on
		if ($this->db->affected_rows() > 0) {
			$new_id = $this->db->insert_id();

			// set all children cancelled
			// set $occurrenceid pointing to new occurrence
			// set $occurrenceid's children pointing to new occurrence
			// update timestamp of previous active
			$sql_update = 'UPDATE event_occurrences
				SET		event_occurrences.event_occurrence_active_occurrence_id
							= ?,
						event_occurrences.event_occurrence_state
							= \'cancelled\',
						event_occurrences.event_occurrence_last_modified
							= IF(event_occurrences.event_occurrence_id = ?,
								CURRENT_TIMESTAMP(),
								event_occurrences.event_occurrence_last_modified)
				WHERE	event_occurrences.event_occurrence_id = ?
					OR	event_occurrences.event_occurrence_active_occurrence_id = ?';

			$query = $this->db->query($sql_update,
					array($new_id, $OccurrenceId, $OccurrenceId, $OccurrenceId));

			if ($this->db->affected_rows() > 0) {
				return $new_id;
			} else {
				$sql_delete = 'DELETE FROM event_occurrences
					WHERE event_occurrences.event_occurrence_id=?';
				$query = $this->db->query($sql_delete, $new_id);
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/// Delete an occurrence
	/**
	 * @param $EventId integer Id of event.
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return integer Number of changed occurrences.
	 * @pre 'draft' | 'trashed' | in past
	 * @post 'deleted'
	 * @post any associated event_occurrence_users rows deleted
	 */
	function OccurrenceDelete($EventId,$OccurrenceId)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		return 0;
		/// @todo Implement.
		// cleanup after anything linking to occurrences
		//	- event_occurrence_users (leave to get detected when user logs in)
		//	- child events (reactivate another, unlink this)
		// mark the occurrence as deleted
		return $this->OccurrenceChangeState($EventId,$OccurrenceId, FALSE, 'deleted');
	}


	// subscriber
	/// Set user-occurrence link.
	/**
	 * @param $OccurrenceId integer Id of occurrence.
	 * @param $NewAttending bool,NULL Whether the user is attending
	 * @return bool True on success, False on failure.
	 * @pre Occurrence is visible to user.
	 */
	function SetOccurrenceUserAttending($OccurrenceId, $NewAttending)
	{
		/// @pre GetActiveEntityId() === $sEntityUser
		assert('$this->mActiveEntityType === self::$cEntityUser');
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}

		$occurrence_query = new EventOccurrenceQuery();

		$sql = '
			INSERT INTO event_occurrence_users (
				event_occurrence_users.event_occurrence_user_user_entity_id,
				event_occurrence_users.event_occurrence_user_event_occurrence_id,
				event_occurrence_users.event_occurrence_user_attending)
			SELECT	'.$this->GetActiveEntityId().', ?, ?
			FROM	event_occurrences
			INNER JOIN events
				ON	event_id = event_occurrence_event_id
			LEFT JOIN event_entities
				ON	event_entity_event_id = event_id
				AND	event_entity_entity_id = '.$this->GetActiveEntityId().'
			WHERE	(	event_occurrences.event_occurrence_id = ?
					AND	('.$occurrence_query->ExpressionPublic().'
						OR	'.$occurrence_query->ExpressionOwned().'))
			ON DUPLICATE KEY UPDATE
				event_occurrence_users.event_occurrence_user_attending = ?';
		$bind_data = array(
				$OccurrenceId,
				$NewAttending,
				$OccurrenceId,
				$NewAttending,
			);
		$this->db->query($sql, $bind_data);
		return ($this->db->affected_rows() > 0);
	}

	/// Set user-occurrence link to RSVP.
	/**
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return bool True on success, False on failure.
	 * @pre Occurrence is visible to user.
	 * @pre $mActiveEntityId === $sEntityUser
	 * @post Occurrence is RSVP'd to user.
	 */
	function OccurrenceRsvp($OccurrenceId)
	{
		return $this->SetOccurrenceUserAttending($OccurrenceId, TRUE);
	}

	/// Set user-occurrence link to Hide.
	/**
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return bool True on success, False on failure.
	 * @pre Occurrence is visible to user.
	 * @pre $mActiveEntityId === $sEntityUser
	 * @post Occurrence is hidden to user.
	 */
	function OccurrenceHide($OccurrenceId)
	{
		return $this->SetOccurrenceUserAttending($OccurrenceId, FALSE);
	}

	/// Set user-occurrence link to Show.
	/**
	 * @param $OccurrenceId integer Id of occurrence.
	 * @return bool True on success, False on failure.
	 * @pre Occurrence is visible to user.
	 * @pre $mActiveEntityId === $sEntityUser
	 * @post Occurrence is neither RSVP'd or hidden to user.
	 */
	function OccurrenceShow($OccurrenceId)
	{
		return $this->SetOccurrenceUserAttending($OccurrenceId, NULL);
	}


	/// Create subscription event-entity link
	function EventSubscribe($EventId)
	{
		/// @todo Implement.
	}
	/// Remove subscription event-entity link
	function EventUnsubscribe($EventId)
	{
		/// @todo Implement.
	}


}

?>
