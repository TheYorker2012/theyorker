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
	
	/// Produce an SQL expression for all and only public events.
	function ExpressionPublic()
	{
		return	'(	event_occurrences.event_occurrence_state = \'published\'
				OR	event_occurrences.event_occurrence_state = \'cancelled\')';
	}
	
	/// Produce an SQL expression for all and only owned events.
	function ExpressionOwned($EntityId = FALSE)
	{
		if (FALSE === $EntityId) {
			$EntityId = $this->mEntityId;
		}
		return	'(	event_entities.event_entity_entity_id = ' . $EntityId . '
				AND	event_entities.event_entity_confirmed = 1
				AND	event_entities.event_entity_relationship = \'own\')';
	}
	
	/// Produce an SQL expression for all and only subscribed events.
	function ExpressionSubscribed()
	{
		return	'(	(	event_entities.event_entity_entity_id = ' . $this->mEntityId . '
					AND	event_entities.event_entity_confirmed = 1
					AND	(	event_entities.event_entity_relationship = \'own\'
						OR	event_entities.event_entity_relationship = \'subscribe\'))
				OR	subscriptions.subscription_user_entity_id = ' . $this->mEntityId . ')';
	}
	
	/// Produce an SQL expression for all and only rsvp'd events.
	function ExpressionVisibilityRsvp()
	{
		return	'event_occurrence_users.event_occurrence_user_state=\'rsvp\'';
	}
	
	/// Produce an SQL expression for all and only hidden events.
	function ExpressionVisibilityHidden()
	{
		return	'event_occurrence_users.event_occurrence_user_state=\'hide\'';
	}
	
	/// Produce an SQL expression for all and only normal visibility events.
	function ExpressionVisibilityNormal()
	{
		return	'(		event_occurrence_users.event_occurrence_user_state=\'show\'
					OR	event_occurrence_users.event_occurrence_user_state IS NULL)';
	}
	
	/// Produce an SQL expression for all and only occurrences in a range of time.
	function ExpressionDateRange($Range)
	{
		assert('is_array($Range)');
		assert('is_int($Range[0]) || FALSE === $Range[0]');
		assert('is_int($Range[1]) || FALSE === $Range[1]');
		
		$conditions = array();
		if (FALSE !== $Range[0]) {
			$conditions[] = 'event_occurrences.event_occurrence_end_time >
								FROM_UNIXTIME('.$Range[0].')';
		}
		if (FALSE !== $Range[1]) {
			$conditions[] = 'event_occurrences.event_occurrence_start_time <
								FROM_UNIXTIME('.$Range[1].')';
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
 */
class EventOccurrenceFilter extends EventOccurrenceQuery
{
	/// array[bool] For enabling/disabling sources of events.
	protected $mSources;
	
	/// array[bool] For filtering sources of events.
	protected $mFilters;
	
	/// array[feed] For including additional sources.
	protected $mInclusions;
	
	/// array[2*timestamp] For filtering by time.
	protected $mRange;
	
	/// string Special mysql condition.
	protected $mSpecialCondition;
	
	/// Default constructor
	function __construct()
	{
		parent::__construct();
		
		// Initialise sources + filters
		$this->mSources = array(
				'owned'      => TRUE,
				'subscribed' => TRUE,
				'inclusions' => FALSE,
				'all'        => FALSE,
			);
		$this->mFilters = array(
				'private'    => TRUE,
				'active'     => TRUE,
				'inactive'   => TRUE,
				
				'hide'       => FALSE,
				'show'       => TRUE,
				'rsvp'       => TRUE,
			);
		
		$this->mInclusions = array();
		
		$this->mRange = array( FALSE, FALSE );
		
		$this->mSpecialCondition = FALSE;
	}
	
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
				subscription.interested & not subscription.deleted
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
			LEFT JOIN subscriptions
				ON	subscriptions.subscription_organisation_entity_id
						= event_entities.event_entity_entity_id
				AND	subscriptions.subscription_user_entity_id	= ?
				AND	subscriptions.subscription_deleted			= 0
				AND	subscriptions.subscription_interested		= 1
				AND	subscriptions.subscription_user_confirmed	= 1
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
			
			if ($this->mSources['inclusions'] && count($this->mInclusions) > 0)
{
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
		
		$date_range = $this->ExpressionDateRange($this->mRange);
		
		// SPECIAL CONDITION ---------------------------------------------------
		
		$conditions = array($date_range,$sources,$filters);
		
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
		
		// Get entity id from user_auth library
		$CI = &get_instance();
		$CI->load->library('user_auth');
		if ($CI->user_auth->isLoggedIn) {
			$this->mReadOnly = FALSE;
			if ($CI->user_auth->organisationLogin >= 0) {
				$this->mActiveEntityId = $CI->user_auth->organisationLogin;
				$this->mActiveEntityType = self::$cEntityVip;
			} else {
				$this->mActiveEntityId = $CI->user_auth->entityId;
				$this->mActiveEntityType = self::$cEntityUser;
			}
		} else {
			// Default to an entity id with default events
			$this->mReadOnly = TRUE;
			$this->mActiveEntityId = 0;
			$this->mActiveEntityType = self::$cEntityPublic;
		}
		
		$this->mStates = array();
		$this->mDayInformation = FALSE;
		$this->mOccurrences = FALSE;
		$this->SetOccurrenceFilter();
		
		$this->load->library('academic_calendar');
	}
	
	/// Get the entity id of the active entity.
	function GetActiveEntityId()
	{
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
	function SetOccurrenceFilter($Filter = FALSE)
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
			$filter = $this->mOccurrenceFilter;
			if (FALSE === $filter) {
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
					'shortloc' => 'event_occurrences.event_occurrence_location',
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
		
		$sql = 'SELECT
			event_occurrence_users.event_occurrence_user_event_occurrence_id
									AS occurrence_id,
			users.user_firstname	AS firstname,
			users.user_surname		AS surname,
			users.user_nickname		AS nickname,
			IF(subscriptions.subscription_member = 1, users.user_email, NULL)
									AS email
		FROM	event_occurrence_users
		INNER JOIN event_occurrences
			ON	event_occurrences.event_occurrence_id
				= event_occurrence_users.event_occurrence_user_event_occurrence_id
			AND	event_occurrences.event_occurrence_event_id = ' . $EventId . '
		INNER JOIN users
			On	users.user_entity_id
				= event_occurrence_users.event_occurrence_user_user_entity_id
		INNER JOIN event_entities
			ON	event_entities.event_entity_event_id = ' . $EventId . '
			AND ' . $occurrence_query->ExpressionOwned() . '
		LEFT JOIN subscriptions
			ON	subscriptions.subscription_user_entity_id
				= event_occurrence_users.event_occurrence_user_user_entity_id
			AND	subscriptions.subscription_organisation_entity_id
				= ' . $this->mActiveEntityId . '
		WHERE	event_occurrence_users.event_occurrence_user_state = \'rsvp\'';
		
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
		
		$sql = 'SELECT
			event_occurrence_users.event_occurrence_user_event_occurrence_id
									AS occurrence_id,
			users.user_firstname	AS firstname,
			users.user_surname		AS surname,
			users.user_nickname		AS nickname,
			IF(subscriptions.subscription_member = 1, users.user_email, NULL)
									AS email
		FROM	event_occurrence_users
		INNER JOIN event_occurrences
			ON	event_occurrences.event_occurrence_id
				= event_occurrence_users.event_occurrence_user_event_occurrence_id
			AND	event_occurrences.event_occurrence_id = ' . $OccurrenceId . '
		INNER JOIN users
			On	users.user_entity_id
				= event_occurrence_users.event_occurrence_user_user_entity_id
		INNER JOIN event_entities
			ON	event_entities.event_entity_event_id
				= event_occurrences.event_occurrence_event_id
			AND ' . $occurrence_query->ExpressionOwned() . '
		LEFT JOIN subscriptions
			ON	subscriptions.subscription_user_entity_id
				= event_occurrence_users.event_occurrence_user_user_entity_id
			AND	subscriptions.subscription_organisation_entity_id
				= ' . $this->mActiveEntityId . '
		WHERE	event_occurrence_users.event_occurrence_user_state = \'rsvp\'';
		
		$query = $this->db->query($sql);
		
		return $query->result_array();
	}
	
	// subscriber
	/// Get the information relating to a notice type.
	/**
	 * @param $NoticeType string Notice type as ['type'] in return of
	 *	GetNotices.
	 * @return array Notice type information including:
	 *	- 'actions' array of action names.
	 */
	function GetNoticeTypeInformation($NoticeType)
	{
		static $notice_types = array(
			'requested_subscription' => array(
				'description' => 'You have been invited to join the specified events feed.',
				'actions' => array(
					'Accept' => 'Subscribe to the events',
					'Reject' => 'Reject the subscription',
				),
			),
			'requested_membership' => array(
				'description' => 'You have been invited to join the specified organisation as a member.',
				'actions' => array(
					'Interested' => 'Accept the membership and subscribe to events',
					'Not interested' => 'Accept the membership but don\'t subscribe to events',
					'Reject' => 'Reject the membership',
				),
			),
		);
		return $notice_types[$NoticeType];
	}
	
	/// Get notices relating to a user's calendar.
	/**
	 * Get all pending
	 *	- subscriptions/memberships.
	 *	- event ownerships/event feed inclusions
	 * Moved RSVP'd events.
	 * @return array Array of notice information including:
	 *	- 'type' (e.g. requested_subscription, requested_membership)
	 *	- 'subscription_id' (if type == requested_subscription)
	 */
	function GetNotices()
	{
		/// @todo Implement.
		/*
		 * UNCONFIRMED SUBSCRIPTIONS
		 * select
		 *     from subscriptions
		 *     where user = me
		 *       and user_confirmed = 0
		 *
		 * RSVP'd CHANGED EVENTS
		 * select public_state
		 *     from event_occurrence_users
		 *     inner join event_occurrences
		 *     where user = me
		 *       and rsvp
		 *       and state isn't published
		 *
		 * UNCONFIRMED OWNERSHIPS
		 * select
		 *     from event_entities
		 *     where user = me
		 *       and confirmed = 0
		 */
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
			INNER JOIN event_entities
				ON	event_entities.event_entity_event_id = events.event_id
				AND	' . $occurrence_query->ExpressionOwned();
		if ($RecurrenceRule) {
			$sql .= '
			LEFT JOIN recurrence_rules
				ON events.event_recurrence_rule_id
					= recurrence_rules.recurrence_rule_id';
		}
		
		$bind_data = array();
		$conditions = array();
		if (FALSE !== $EventId) {
			$conditions[] = 'events.event_id = ?';
			$bind_data[] = $EventId;
		}
		if (!empty($Filter)) {
			$conditions[] = '('.$Filter.')';
			$bind_data += $FilterBind;
		}
		if (!empty($conditions)) {
			$sql .= '
				WHERE ' . implode(' AND ',$conditions);
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
	
	
	/// Create a new event.
	/**
	 * @param $EventData array Event data.
	 * @return array(
	 *	'event_id' => new event id,
	 *	'occurrences' => occurrences added)
	 * @exception Exception The event could not be created.
	 */
	function EventCreate($EventData)
	{
		if ($this->mReadOnly) {
			throw new Exception(self::$cReadOnlyMessage);
		}
		static $translation = array(
			'name'			=> 'events.event_name',
			'description'	=> 'events.event_description',
			'parent'		=> 'events.event_parent_id',
			'recurrence_rule_id'	=> 'events.event_recurrence_rule_id',
		);
		$fields = array();
		$bind_data = array();
		foreach ($translation as $input_name => $field_name) {
			if (array_key_exists($input_name, $EventData)) {
				$fields[] = $field_name.'=?';
				$bind_data[] = $EventData[$input_name];
			}
		}
		
		// create new event
		$sql = 'INSERT INTO events SET '.implode(',',$fields);
		$query = $this->db->query($sql, $bind_data);
		$affected_rows = $this->db->affected_rows();
		if ($affected_rows == 0) {
			throw new Exception('Event could not be created (1)');
		}
		$event_id = $this->db->insert_id();
		
		// link event to creater
		$sql = 'INSERT INTO event_entities (
				event_entity_entity_id,
				event_entity_event_id,
				event_entity_relationship,
				event_entity_confirmed)
			VALUES';
		$sql .= ' ('.$this->mActiveEntityId.','.$event_id.',\'own\',1)';
		$query = $this->db->query($sql);
		$affected_rows = $this->db->affected_rows();
		if ($affected_rows == 0) {
			// Should now delete event since couldn't bind to entity
			$sql = 'DELETE FROM events WHERE events.event_id='.$event_id;
			$query = $this->db->query($sql);
			$affected_rows = $this->db->affected_rows();
			// Throw error message
			$message = 'Event could not be created (';
			if ($affected_rows == 1) {
				$message .= '2-clean)';
			} else {
				$message .= '2-unclean)';
			}
			throw new Exception($message);
		}
		
		if (array_key_exists('occurrences',$EventData)) {
			$num_occurrences = $this->OccurrencesAdd($event_id, $EventData['occurrences']);
		} else {
			$num_occurrences = 0;
		}
		
		// Return some information about the created event.
		return array(
			'event_id'		=> $event_id,
			'occurrences'	=> $num_occurrences,
		);
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
			'name'					=> 'events.event_name',
			'description'			=> 'events.event_description',
			'recurrence_rule_id'	=> 'events.event_recurrence_rule_id',
		);
		$fields = array();
		$bind_data = array();
		foreach ($translation as $input_name => $field_name) {
			if (array_key_exists($input_name, $EventData)) {
				$fields[] = $field_name.'=?';
				$bind_data[] = $EventData[$input_name];
			}
		}
		
		$occurrence_query = new EventOccurrenceQuery();
		
		// create new event
		$sql = 'UPDATE events
				INNER JOIN event_entities
					ON	event_entities.event_entity_event_id = events.event_id
					AND ' . $occurrence_query->ExpressionOwned() . '
				SET ' . implode(',',$fields) . '
				WHERE events.event_id = ?';
		$bind_data[] = $event_id;
		
		$query = $this->db->query($sql, $bind_data);
		$num_events = array($this->db->affected_rows());
		
		if (array_key_exists('occurrences',$EventData)) {
			$num_occurrences = $this->OccurrencesAlter($event_id, $EventData['occurrences']);
		} else {
			$num_occurrences = 0;
		}
		
		// Return some information about the created event.
		return array($num_events, $num_occurrences);
	}
	
	/// Add occurrences to an event.
	/**
	 * @param $EventId int ID of event to add occurrence to.
	 * @param $OccurrenceData array Array of Occurrence Data arrays.
	 * @return integer Number of occurrences added.
	 */
	function OccurrencesAdd($EventId, $OccurrenceData)
	{
		$occurrence_query = new EventOccurrenceQuery();
		
		static $translation2 = array(
			'description',
			'location',
			'postcode',
			'all_day',
			'ends_late',
		);
		
		$sql_owned = 'SELECT COUNT(*) FROM event_entities
			WHERE event_entities.event_entity_event_id = '.$this->db->escape($EventId).'
			AND ' . $occurrence_query->ExpressionOwned();
		$query = $this->db->query($sql_owned);
		if ($query->num_rows() > 0) {
		
			// create each occurrences
			$sql = 'INSERT INTO event_occurrences (
					event_occurrence_event_id,
					event_occurrence_description,
					event_occurrence_location,
					event_occurrence_postcode,
					event_occurrence_start_time,
					event_occurrence_end_time,
					event_occurrence_all_day,
					event_occurrence_ends_late)
				VALUES';
			$first = TRUE;
			foreach ($OccurrenceData as $occurrence) {
				$values = array_fill(0, count($translation2), 'DEFAULT');
				foreach ($translation2 as $field_name => $input_name) {
					if (array_key_exists($input_name, $occurrence)) {
						$values[$field_name] = $this->db->escape($occurrence[$input_name]);
					}
				}
				if (!$first)
					$sql .= ',';
				$sql .=	' ('.$EventId.
						','.$values[0].		// description
						','.$values[1].		// location
						','.$values[2];		// postcode
				// start time
				if (array_key_exists('start', $occurrence))
					$sql .= ',FROM_UNIXTIME('.$occurrence['start'].')';
				else
					$sql .= ',DEFAULT';
				// end time
				if (array_key_exists('end', $occurrence))
					$sql .= ',FROM_UNIXTIME('.$occurrence['end'].')';
				else
					$sql .= ',DEFAULT';
				
				$sql .=	','.$values[3].		// all_day
						','.$values[4].')'; // ends_late
				
				$first = FALSE;
			}
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
		$occurrence_query = new EventOccurrenceQuery();
		
		static $translation = array(
			'description'	=> 'event_occurrences.event_occurrence_description',
			'location'		=> 'event_occurrences.event_occurrence_location',
			'postcode'		=> 'event_occurrences.event_occurrence_postcode',
			'all_day'		=> 'event_occurrences.event_occurrence_all_day',
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
					INNER JOIN event_entities
						ON	event_entities.event_entity_event_id
							= event_occurrences.event_occurrence_event_id
						AND ' . $occurrence_query->ExpressionOwned() . '
					SET ' . implode(', ', $sets) . '
					WHERE	event_occurrences.event_occurrence_id = ?
						AND	event_occurrences.event_occurrence_event_id = ?';
				$bind_data[] = $occurrence['id'];
				$bind_data[] = $EventId;
				
				$query = $this->db->query($sql, $bind_data);
				$result += $this->db->affected_rows();
			}
		}
		return $result;
	}
	
	/// Change the state of an occurrence explicitly.
	/**
	 * @param $EventId integer Id of event occurrence belongs to.
	 * @param $OccurrenceId integer Id of occurrence to change the state of.
	 * @param $OldState string Previous private state.
	 * @param $NewState string New private state.
	 * @return integer Number of changed occurrences.
	 */
	protected function OccurrenceChangeState($EventId, $OccurrenceId, $OldState, $NewState, $ExtraConditions = array())
	{
		$occurrence_query = new EventOccurrenceQuery();
		
		// change the state to $NewState
		// where the state was $OldState
		$sql = 'UPDATE event_occurrences
			INNER JOIN event_entities
				ON	event_entities.event_entity_event_id
						= event_occurrences.event_occurrence_event_id
				AND ' . $occurrence_query->ExpressionOwned() . '
			SET		event_occurrences.event_occurrence_state=?,
					event_occurrences.event_occurrence_timestamp=CURRENT_TIMESTAMP()
			WHERE	event_occurrences.event_occurrence_event_id=?';
		$bind_data = array($NewState, $EventId);
		if (FALSE !== $OccurrenceId) {
			$sql .= ' AND	event_occurrences.event_occurrence_id=?';
			$bind_data[] = $OccurrenceId;
		}
		if (is_string($OldState)) {
			$sql .= ' AND		event_occurrences.event_occurrence_state=?';
			$bind_data[] = $OldState;
		}
		foreach ($ExtraConditions as $ExtraCondition) {
			$sql .= ' AND ('.$ExtraCondition.')';
		}
		$this->db->query($sql, $bind_data);
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
		INNER JOIN event_entities
			ON	event_entities.event_entity_event_id
					= event_occurrences.event_occurrence_event_id
			AND ' . $occurrence_query->ExpressionOwned('entity_id') . '
		WHERE	event_occurrences.event_occurrence_id = occurrence_id
			AND	event_occurrences.event_occurrence_event_id = event_id;
	IF owned THEN
		SELECT preactive.event_occurrence_id INTO preactive_id
			FROM event_occurrences AS preactive
			WHERE preactive.event_occurrence_active_occurrence_id = occurrence_id
			ORDER BY preactive.event_occurrence_timestamp DESC
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
				array($this->mActiveEntityId, $EventId, $OccurrenceId, $ActivationState));
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
		return $this->OccurrenceChangeState($EventId,$OccurrenceId, 'published','cancelled');
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
		// Make this occurrence the active occurrence.
		// update new_active
		// on sibling (same active occurrence)
		// or is active occurrence
		// set active=(sibling is active ? NULL : active)
		// where new active is this
		//	and where new active.active is cancelled and active
		$occurrence_query = new EventOccurrenceQuery();
		$sql_activate = 'UPDATE event_occurrences AS new_active
			INNER JOIN event_entities
				ON	event_entities.event_entity_event_id
						= new_active.event_occurrence_event_id
				AND ' . $occurrence_query->ExpressionOwned() . '
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
		$this->OccurrenceCancelledActivate($EventId, $OccurrenceId);
		
		$occurrence_query = new EventOccurrenceQuery();
		
		// create new movedraft
		$sql_insert = 'INSERT INTO event_occurrences (
				event_occurrence_event_id,
				event_occurrence_state,
				event_occurrence_description,
				event_occurrence_location,
				event_occurrence_postcode,
				event_occurrence_start_time,
				event_occurrence_end_time,
				event_occurrence_all_day,
				event_occurrence_ends_late)
			SELECT
				event_occurrences.event_occurrence_event_id,
				\'movedraft\',
				event_occurrences.event_occurrence_description,
				event_occurrences.event_occurrence_location,
				event_occurrences.event_occurrence_postcode,
				event_occurrences.event_occurrence_start_time,
				event_occurrences.event_occurrence_end_time,
				event_occurrences.event_occurrence_all_day,
				event_occurrences.event_occurrence_ends_late
			FROM event_occurrences
			INNER JOIN event_entities
				ON	event_entities.event_entity_event_id
						= event_occurrences.event_occurrence_event_id
				AND ' . $occurrence_query->ExpressionOwned() . '
			WHERE	event_occurrences.event_occurrence_id = ?
				AND	event_occurrences.event_occurrence_event_id = ?';
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
						event_occurrences.event_occurrence_timestamp
							= IF(event_occurrences.event_occurrence_id = ?,
								CURRENT_TIMESTAMP(),
								event_occurrences.event_occurrence_timestamp)
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
	 * @param $NewState string new db state:
	 *	- 'show' (show the event normally).
	 *	- 'hide' (hide the event).
	 *	- 'rsvp' (rsvp the event).
	 * @return bool True on success, False on failure.
	 * @pre Occurrence is visible to user.
	 * @post Occurrence is RSVP'd to user.
	 */
	protected function SetOccurrenceUserState($OccurrenceId, $NewState)
	{
		/// @pre $mActiveEntityId === $sEntityUser
		assert('$this->mActiveEntityId === self::$cEntityUser');
		
		$occurrence_query = new EventOccurrenceQuery();
		
		$sql = '
			INSERT INTO event_occurrence_users (
				event_occurrence_users.event_occurrence_user_user_entity_id,
				event_occurrence_users.event_occurrence_user_event_occurrence_id,
				event_occurrence_users.event_occurrence_user_state)
			SELECT	'.$this->mActiveEntityId.', ?, ?
			FROM	event_occurrences
			WHERE	(	event_occurrences.event_occurrence_id = ?
					AND	'.$occurrence_query->ExpressionPublic().')
			ON DUPLICATE KEY UPDATE
				event_occurrence_users.event_occurrence_user_state = ?';
		$bind_data = array(
				$OccurrenceId,
				$NewState,
				$OccurrenceId,
				$NewState,
			);
		$query = $this->db->query($sql, $bind_data);
		return ($this->db->affected_rows > 0);
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
		return $this->SetOccurrenceUserState($OccurrenceId, 'rsvp');
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
		return $this->SetOccurrenceUserState($OccurrenceId, 'hide');
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
		return $this->SetOccurrenceUserState($OccurrenceId, 'show');
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
	
	/// Create subscription to @a $EntityId
	function FeedSubscribe($EntityId, $Data)
	{
		/// @todo Implement.
	}
	/// Remove subscription to @a $EntityId
	function FeedUnsubscribe($EntityId)
	{
		/// @todo Implement.
	}
	
}

?>