<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_backend.php
 * @brief Back end of calendar framework.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Calendar data classes and source abstract base class.
 *
 * @version 28-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Class to represent event occurrence from any source.
class CalendarOccurrence
{
	/*
	/// array[string] Valid values of @a $State.
	protected static $ValidStates = array(
		'draft',
		'movedraft',
		'trashed',
		'published',
		'cancelled',
	);
	/// array[string] Valid members of @a $SpecialTags.
	protected static $ValidSpecialTags = array(
		'ends_late',
	);
	/// array[string] Valid members of @a $UserAttending.
	protected static $ValidAttendings = array(
		'no',
		'maybe',
		'yes',
	);
	*/
	
	/// int ID of occurrence, unique within connection.
	public $OccurrenceId;
	/// int,NULL ID of occurrence, unique within source.
	public $SourceOccurrenceId	= NULL;
	/// &CalendarEvent Reference to event this occurrence is part of.
	public $Event;
	/// string State of the occurrence (must be member of @a $ValidStates).
	public $State				= 'published';
	/// &CalendarOccurrence,NULL Reference to the active occurrence.
	public $ActiveOccurrence	= NULL;
	/// bool Whether the occurrence is a todo item.
	public $Todo				= FALSE;
	/// AcademicTime,NULL Start time of event.
	public $StartTime			= NULL;
	/// AcademicTime,NULL End time of event.
	public $EndTime				= NULL;
	/// bool Whether the event is time associated
	public $TimeAssociated		= TRUE;
	/// string,NULL Description of location.
	public $LocationDescription	= NULL;
	/// url,NULL URL of location.
	public $LocationLink		= NULL;
	/// array[string] Special tags (each must be member of @a $ValidSpecialTags).
	public $SpecialTags			= array();
	
	/// timestamp,NULL Time last ackowledged by user.
	public $UserLastUpdate		= NULL;
	/// string Whether the user is attending (must be member of @a $ValidAttendings).
	public $UserAttending		= 'maybe';
	/// int[0,100],NULL How complete the item is for the user.
	public $UserProgress		= NULL;
	
	/// bool Whether to display on the users calendar.
	public $DisplayOnCalendar	= TRUE;
	/// bool Whether to display on the users to-do list.
	public $DisplayOnTodo		= FALSE;
	/// AcademicTime,NULL Effective start time of todo.
	public $TodoStartTime		= NULL;
	/// AcademicTime,NULL Effective end time of todo.
	public $TodoEndTime			= NULL;
	
	/// Primary constructor.
	/**
	 * @param $OccurrenceId int ID of occurrence (unique within connection).
	 * @param $Event &CalendarEvent Reference to event this occurrence is a part of.
	 */
	function __construct($OccurrenceId, &$Event)
	{
		$this->OccurrenceId = $OccurrenceId;
		$this->Event = &$Event;
	}
}

/// Class to represent event from any source.
class CalendarEvent
{
	/*
	/// array[string] Valid values of @a $UserStatus.
	protected $ValidUserStatuses = array(
		'none',
		'subscriber',
		'owner',
	);
	*/
	
	/// int ID of event, unique within connection.
	public $EventId;
	/// int,NULL ID of event, unique within source.
	public $SourceEventId	= NULL;
	/// &CalendarSource Reference to source of event.
	public $Source;
	/// array[&CalendarOccurrence] Array of occurrences of this event.
	public $Occurrences		= array();
	/// string,NULL Category value.
	public $Category		= NULL;
	/// string Name of event.
	public $Name			= '';
	/// bool Whether the event is time associated
	public $TimeAssociated		= TRUE;
	/// string,NULL Description of event (parsed wikitext).
	public $Description		= NULL;
	/// array[&CalendarOrganisation] Array of owner organisation references.
	public $Organisations	= array();
	/// timestamp,NULL Time of last significant updaate to the event.
	public $LastUpdate		= NULL;
	/// string Status of the user regarding the event (must be in @a $ValidUserStatuses).
	public $UserStatus		= 'none';
	/// RecurrenceSet Recurrence information.
	public $Recur			= NULL;
	/// string URL of image.
	public $Image			= NULL;
	
	/// Primary constructor.
	/**
	 * @param $EventId int ID of event (unique within connection).
	 * @param $Source &CalendarSource Reference to event source.
	 */
	function __construct($EventId, &$Source)
	{
		$this->EventId = $EventId;
		$this->Source = &$Source;
	}
	
	/// Add an organisation to the organisations managing the event.
	/**
	 * @param $Organisation &CalendarOrganisation Reference to organisation.
	 */
	function AddOrganisation(&$Organisation)
	{
		// If the event already has this organisation, don't add again.
		foreach ($this->Organisations as $org) {
			if ($org->OrganisationId === $Organisation->OrganisationId) {
				return;
			}
		}
		// Add to end.
		$this->Organisations[] = &$Organisation;
	}
}

/// Class to represent organisation from a source.
class CalendarOrganisation
{
	/// int ID of organisation, unique within connection.
	public $OrganisationId;
	/// int,NULL ID of organisation, unique within source.
	public $SourceOrganisationId	= NULL;
	/// &CalendarSource Reference to source of event.
	public $Source;
	/// string Name of organisation.
	public $Name = '';
	/// string Short name of organisation
	public $ShortName = '';
	
	/// Primary constructor.
	/**
	 * @param $EventId int ID of event (unique within connection).
	 * @param $Source &CalendarSource Reference to event source.
	 */
	function __construct($OrganisationId, &$Source)
	{
		$this->OrganisationId = $OrganisationId;
		$this->Source = &$Source;
	}
}

/// Source of calendar items.
abstract class CalendarSource
{
	/*
	/// array[string] Array of valid members of @a mCapabilities.
	protected static $sValidCapabilities = array(
		'attend',  // its possible to rsvp these event
		'cache', // these events can be cached in the database
	);
	*/

	/// int,NULL Source id.
	protected $mSourceId = NULL;
	/// string Source name.
	protected $mName = '';
	/// array[string] Array of capabilities (each must be in @a $sValidCapabilities).
	protected $mCapabilities = array();
	
	
	/// array[bool] Groups of events to include.
	protected $mGroups = array(
		'owned'      => TRUE,
		'subscribed' => TRUE,
		'all'        => FALSE,
		
		'private'    => TRUE,
		'active'     => TRUE,
		'inactive'   => TRUE,
		
		'hide'       => FALSE,
		'show'       => TRUE,
		'rsvp'       => TRUE,
		
		'event'      => TRUE,
		'todo'       => FALSE,
	);
	
	/// array[2*timestamp] For filtering by time.
	protected $mRange = array( NULL, NULL );
	
	/// Default constructor.
	function __construct()
	{
	}
	
	/// Set the source id.
	/**
	 * @param $SourceId int Source id.
	 */
	function SetSourceId($SourceId)
	{
		$this->mSourceId = $SourceId;
	}
	
	/// Get the source id.
	/**
	 * @return int Source id.
	 */
	function GetSourceId()
	{
		return $this->mSourceId;
	}
	
	/// Find whether a capability is supported.
	/**
	 * @param $Capability string Capabiltity.
	 * @return bool Whether the capability is supported.
	 */
	function IsSupported($Capability)
	{
		return in_array($Capability, $this->mCapabilities);
	}
	
	/// Enable a group of events.
	/**
	 * @param $GroupName string Index to $this->mSources.
	 * @return bool Whether successfully enabled.
	 */
	function EnableGroup($GroupName)
	{
		if (array_key_exists($GroupName, $this->mGroups)) {
			$this->mGroups[$GroupName] = TRUE;
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/// Disable a group of events.
	/**
	 * @param $SourceName string Index to $this->mGroups.
	 * @return bool Whether successfully disabled.
	 */
	function DisableGroup($GroupName)
	{
		if (array_key_exists($GroupName, $this->mGroups)) {
			$this->mGroups[$GroupName] = FALSE;
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/// Find whether a group of events is enabled.
	/**
	 * @param $SourceName string Index to $this->mGroups.
	 * @return bool Whether the group is enabled.
	 */
	function GroupEnabled($GroupName)
	{
		if (array_key_exists($GroupName, $this->mGroups)) {
			return $this->mGroups[$GroupName];
		} else {
			return FALSE;
		}
	}
	
	/// Set the date range.
	/**
	 * @param $Start timestamp,NULL Start time.
	 * @param $End timestamp,NULL End time.
	 */
	function SetRange($Start = NULL, $End = NULL)
	{
		assert('is_int($Start) || NULL === $Start');
		assert('is_int($End)   || NULL === $End');
		$this->mRange[0] = $Start;
		$this->mRange[1] = $End;
	}
	
	/// Fetch a specific event.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 * @param $Event identifier Source event identitier.
	 */
	function FetchEvent($Data, $Event)
	{
		$Data->NewCurrentSource($this);
		$this->_FetchEvent($Data, $Event);
		return array();
	}
	
	/// Fetch the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	function FetchEvents(&$Data)
	{
		$Data->NewCurrentSource($this);
		$this->_FetchEvents($Data);
		return array();
	}
	
	/// Fetch the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 * @param $Event identifier Source event identitier.
	 */
	protected abstract function _FetchEvent(&$Data, $Event);
	
	/// Fetch the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	protected abstract function _FetchEvents(&$Data);
	
	// MAKING CHANGES **********************************************************
	
	/// Set the user's attending status on an occurrence.
	/**
	 * @param $Occurrence Occurrence identifier.
	 * @param $Attending bool,NULL Whether attending.
	 * @return array Array of message arrays, indexed by type.
	 */
	function AttendingOccurrence($Occurrence, $Attending)
	{
		return array('error' => array('Attendance status information for this event source is not currently supported.'));
	}
	
	/// Delete an event.
	/**
	 * @param $Event Event identifier.
	 * @return array Array of messages.
	 */
	function DeleteEvent($Event)
	{
		return array('error' => array('Deleting events in this event source is not currently supported.'));
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
		return array();
	}
}

/// Calendar data object.
class CalendarData
{
	/// int The next occurrence index to use.
	protected $mNextOccurrenceIndex		= 0;
	/// int The next event index to use.
	protected $mNextEventIndex			= 0;
	/// int The next organisation index to use.
	protected $mNextOrganisationIndex	= 0;
	
	/// int The last inserted source index.
	protected $mLastSourceIndex			= 0;
	
	/// array[$CalendarOccurrence] List of occurrences.
	protected $mOccurrences		= array();
	/// array[&CalendarEvent] List of events.
	protected $mEvents			= array();
	/// array[&CalendarOrganisation] List of organisations.
	protected $mOrganisations	= array();
	/// array[&CalendarSource] List of source objects.
	protected $mSources			= array();
	
	/// array[string => bool] Flags for enabling different information.
	protected $mFlags			= array();
	
	/// Constructors.
	/**
	 * @param $Argument1 (NULL), &CalendarData, array[$CalendarData]
	 *
	 * If @a $Argument1 isn't specified, values are initialised to default.
	 *
	 * If @a $Arguemnt1 refers to another CalendarData, the start indicies are copied.
	 *
	 * If @a $Argument1 is an array of CalendarData, they are merged.
	 *
	 * @todo Implement merge constructor or consider its necessity.
	 */
	function __construct($Argument1 = NULL)
	{
		if (is_array($Argument1)) {
			// __construct(in PreviousData:CalendarData)
			
			
		} elseif (NULL !== $Argument1) {
			// __construct(in CalendarDatum:array[CalendarData])
			$this->mNextOccurrenceIndex   = $Argument1->mNextOccurrenceIndex;
			$this->mNextEventIndex        = $Argument1->mNextEventIndex;
			$this->mNextOrganisationIndex = $Argument1->mNextOrganisationIndex;
			
		}
	}
	
	/// Create a new occurrence.
	/**
	 * @param $Event &CalendarEvent Reference to event.
	 * @return &CalendarOccurrence Reference to new occurrence.
	 */
	function NewOccurrence(&$Event)
	{
		$occurrence_id = $this->mNextOccurrenceIndex++;
		return	$Event->Occurrences[] =
				$this->mOccurrences[$occurrence_id] =
					new CalendarOccurrence($occurrence_id, $Event);
	}
	
	/// Create a new event.
	/**
	 * @param $Source &CalendarSource Reference to source.
	 * @return &CalendarEvent Reference to new event.
	 */
	function NewEvent()
	{
		$event_id = $this->mNextEventIndex++;
		return $this->mEvents[$event_id] =
			new CalendarEvent($event_id, $this->GetCurrentSource());
	}
	
	/// Create a new organisation.
	/**
	 * @param $Source &CalendarSource Reference to source.
	 * @return &CalendarOrganisation Reference to new organisation.
	 */
	function NewOrganisation()
	{
		$organisation_id = $this->mNextOrganisationIndex++;
		return $this->mOrganisations[$organisation_id] =
			new CalendarOrganisation($organisation_id, $this->GetCurrentSource());
	}
	
	/// Add a source as the current source.
	/**
	 * @param $Source &CalendarSource Reference to source.
	 */
	function NewCurrentSource(&$Source)
	{
		$this->mLastSourceIndex = $Source->GetSourceId();
		$this->mSource[$this->mLastSourceIndex] = &$Source;
	}
	
	/// Get the current source.
	/**
	 * @return &CalendarSource Reference to current source.
	 * @pre NewCurrentSource must have been called.
	 */
	function GetCurrentSource()
	{
		return $this->mSource[$this->mLastSourceIndex];
	}
	
	/// Get the array of occurrences.
	/**
	 * @return array[&CalendarOccurrence] Array of occurrence references.
	 */
	function GetOccurrences()
	{
		return $this->mOccurrences;
	}
	
	/// Get the array of calendar occurrences.
	/**
	 * @return array[&CalendarOccurrence] Array of occurrence references.
	 */
	function GetCalendarOccurrences()
	{
		$result = array();
		foreach ($this->mOccurrences as $key => $occurrence) {
			if ($occurrence->DisplayOnCalendar) {
				$result[] = & $this->mOccurrences[$key];
			}
		}
		return $result;
	}
	
	/// Get the array of todo occurrences.
	/**
	 * @return array[&CalendarOccurrence] Array of occurrence references.
	 */
	function GetTodoOccurrences()
	{
		$result = array();
		foreach ($this->mOccurrences as $key => $occurrence) {
			if ($occurrence->DisplayOnTodo) {
				$result[] = & $this->mOccurrences[$key];
			}
		}
		return $result;
	}
	
	/// Get the array of Events
	/**
	 * @return array[&CalendarEvent] Array of event references.
	 */
	function GetEvents()
	{
		return $this->mEvents;
	}
	
	/// Get the array of organisations.
	/**
	 * @return array[&CalendarOrganisation] Array of organisation references.
	 */
	function GetOrganisations()
	{
		return $this->mOrganisations;
	}
	
	/// Get the array of sources.
	/**
	 * @return array[&CalendarSource] Array of source references.
	 */
	function GetSource()
	{
		return $this->mSources;
	}
	
	/// Enable a flag.
	/**
	 * @param $FlagName string The name of the flag to enable.
	 */
	function EnableFlag($FlagName)
	{
		$this->mFlags[$FlagName] = TRUE;
	}
	
	/// Disable a flag.
	/**
	 * @param $FlagName string The name of the flag to disable.
	 */
	function DisableFlag($FlagName)
	{
		$this->mFlags[$FlagName] = FALSE;
	}
	
	/// Get the value of a flag.
	/**
	 * @param $FlagName string The name of the flag.
	 * @return bool The value of the flag specified by @a $FlagName.
	 */
	function GetFlag($FlagName)
	{
		return $this->mFlags[$Flagname];
	}
	
	/// Get the events from multiple sources
	/**
	 * @return Array of messages to display.
	 */
	function FetchEventsFromSources($Sources)
	{
		if (!is_array($Sources)) {
			$Sources = array($Sources);
		}
		$CI = & get_instance();
		$messages = array();
		// Accumulate data from sources in $this
		foreach ($Sources as $source) {
			try {
				$source->FetchEvents($this);
			} catch (Exception $e) {
				$messages[] = array('calendar data source failed: '.$e->getMessage(), 'error');
			}
		}
		return $messages;
	}
}


/// Calendar source for standard sources.
class CalendarSources extends CalendarSource
{
	/// array[CalendarSource] Array of sources.
	protected $mSources = array();
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct();
		
		$this->mName = 'My Calendar';
		//$this->mCapabilities[] = 'rsvp';
		//$this->mCapabilities[] = 'refer';
	}
	
	/// Add a source.
	/**
	 * @param $Source CalendarSource Event source to add
	 * @return @a $Source.
	 */
	function AddSource(&$Source)
	{
		$source_id = $Source->GetSourceId();
		assert('NULL !== $source_id');
		$this->mSources[$Source->GetSourceId()] = &$Source;
		return $Source;
	}
	
	function EnableGroup($GroupName)
	{
		foreach ($this->mSources as $source) {
			$source->EnableGroup($GroupName);
		}
		return parent::EnableGroup($GroupName);
	}
	
	function DisableGroup($GroupName)
	{
		foreach ($this->mSources as $source) {
			$source->DisableGroup($GroupName);
		}
		return parent::DisableGroup($GroupName);
	}
	
	function AddInclusion($FeedId, $EnableInclusions = FALSE)
	{
		foreach ($this->mSources as $source) {
			$source->AddInclusion($FeedId, $EnableInclusions);
		}
		return parent::AddInclusion($FeedId, $EnableInclusions);
	}
	
	function ClearInclusions($DisableInclusions = FALSE)
	{
		foreach ($this->mSources as $source) {
			$source->ClearInclusions($DisableInclusions);
		}
		return parent::ClearInclusions($DisableInclusions);
	}
	
	function SetRange($Start = NULL, $End = NULL)
	{
		foreach ($this->mSources as $source) {
			$source->SetRange($Start, $End);
		}
		return parent::SetRange($Start, $End);
	}
	
	/// Fetch the event of the sources and return an array of messages.
	function FetchEvent(&$Data, $SourceId, $Event)
	{
		if (array_key_exists($SourceId, $this->mSources)) {
			return $this->mSources[$SourceId]->FetchEvent($Data, $Event);
		} else {
			return;
		}
	}
	
	/// Fetch the events of the sources and return an array of messages.
	function FetchEvents(&$Data)
	{
		$messages = array();
		foreach ($this->mSources as $source) {
			try {
				$source->FetchEvents($Data);
			} catch (Exception $e) {
				$messages[] = array('calendar data source '.$source->mName.' failed: '.$e->getMessage(), 'error');
			}
		}
		return $messages;
	}
	
	protected function _FetchEvent(&$Data, $Event)
	{
	}
	
	protected function _FetchEvents(&$Data)
	{
	}
	
	/// Set the user's attending status on an occurrence.
	/**
	 * @param $SourceId int Event source id.
	 * @param $Occurrence Occurrence identifier.
	 * @param $Attending bool,NULL Whether attending.
	 * @return bool Whether successful.
	 */
	function AttendingOccurrence($SourceId, $Occurrence, $Attending)
	{
		if (array_key_exists($SourceId, $this->mSources)) {
			return $this->mSources[$SourceId]->AttendingOccurrence($Occurrence, $Attending);
		} else {
			return parent::AttendingOccurrence($Occurrence, $Attending);
		}
	}
	
	/// Delete event.
	/**
	 * @param $SourceId int Event source id.
	 * @param $Event Event identifier.
	 * @return array Array of messages.
	 */
	function DeleteEvent($SourceId, $Event)
	{
		if (array_key_exists($SourceId, $this->mSources)) {
			return $this->mSources[$SourceId]->DeleteEvent($Event);
		} else {
			return parent::DeleteEvent($Event);
		}
	}
	
	/// Get list of known attendees.
	/**
	 * @param $SourceId int Event source id.
	 * @param $Occurrence Occurrence identifier.
	 * @return array Attendees, defined by fields:
	 *	- 'name' string Name of attendee.
	 *	- 'link' string URL about user.
	 *	- 'entity_id' int Entity id if known.
	 *	- 'attend' bool,NULL TRUE for attending, FALSE for not attending, NULL for maybe.
	 */
	function GetOccurrenceAttendanceList($SourceId, $Occurrence)
	{
		if (array_key_exists($SourceId, $this->mSources)) {
			return $this->mSources[$SourceId]->GetOccurrenceAttendanceList($Occurrence);
		} else {
			return parent::GetOccurrenceAttendanceList($Occurrence);
		}
	}
}

/// Dummy class
class calendar_backend
{
}

?>