<?php

/// Abstract filter class for filtering event occurrences.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The purpose of this class is to allow classes which provide a layer of
 *	abstraction over the model (e.g. View_calendar_days) to provide a consistent
 *	filtering interface.
 *
 * Will have functions for creating MySQL queries or using CI db (although these
 *	could be in the Events_model class.
 *
 * @todo data about filtering by occurrence state/publicity level.
 *
 * @todo data about filtering by owner
 */
class EventOccurrenceFilter
{
	/*
	always in relation to an entity who is accessing, to determin permission
		occurrence must belong to an event owned by entity or be public
		event must be owned by entity and have public occurrences
		event must not be deleted
	sources
		owned events
		subscribed feeds
		extra feeds
	filters
		private? active? inactive?
		hidden events? normal events? rsvpd events?
		date range
	*/
	
	/// array[bool] For enabling/disabling sources of events.
	protected $mSources;
	
	/// array[bool] For filtering sources of events.
	protected $mFilters;
	
	/// array[feed] For including additional sources.
	protected $mInclusions;
	
	/// array[2*timestamp] For filtering by time.
	protected $mRange;
	
	/// Default constructor
	function __construct()
	{
		// Initialise sources + filters
		$this->mSources = array(
				'owned'      => TRUE,
				'subscribed' => TRUE,
				'inclusions' => FALSE,
			);
		$this->mFilters = array(
				'private'    => TRUE,
				'active'     => TRUE,
				'inactive'   => TRUE,
				
				'hidden'     => FALSE,
				'normal'     => TRUE,
				'rsvp'       => TRUE,
			);
		
		$this->mInclusions = array();
		
		$this->mRange = array( time(), time() );
	}
	
	/// Generate the sql to retrieve event occurrences.
	/**
	 * @return array Results.
	 */
	function GenerateOccurrences($FieldNames)
	{
	/*
	owned:
		occurrence.event.owners.id=me
	subscribed:
		occurrence.event.entities.subscribers.id=me
		subscription.interested & not subscription.deleted
	inclusions:
		occurrence.event.entities.id=inclusion
		
	own OR (public AND (subscribed OR inclusion))
	 */
		$entity_id = 2;
		$parameters = array();
		$sql = '
SELECT '.implode(',',$FieldNames).' FROM events
INNER JOIN event_occurrences
	ON	event_occurrences.event_occurrence_event_id = events.event_id
LEFT JOIN event_entities
	ON	event_entities.event_entity_event_id = events.event_id
LEFT JOIN entities
	ON	event_entities.event_entity_entity_id = entities.entity_id
LEFT JOIN subscriptions
	ON	subscriptions.subscription_organisation_entity_id = entities.entity_id
LEFT JOIN event_occurrence_users
	ON	event_occurrence_users.event_occurrence_user_event_occurrence_id = event_occurrences.event_occurrence_id
	AND	event_occurrence_users.event_occurrence_user_user_entity_id = ?';
		$parameters[] = $entity_id;
		
		// SOURCES
		
		if ($this->mSources['owned']) {
			$own =		'(	event_entities.event_entity_entity_id = ?
						AND	event_entities.event_entity_relationship = \'own\')';
			$parameters[] = $entity_id;
		} else {
			$own = '0';
		}
		
		$public =	'(	event_occurrences.event_occurrence_state = \'published\'
					OR	event_occurrences.event_occurrence_state = \'cancelled\')';
		
		if ($this->mSources['subscribed']) {
			$subscribed =	'(	subscriptions.subscription_user_entity_id = ?
							OR	event_entities.event_entity_entity_id = ?)';
			$parameters[] = $entity_id;
			$parameters[] = $entity_id;
		} else {
			$subscribed = '0';
		}
		
		if ($this->mSources['inclusions'] && count($this->mInclusions) > 0) {
			$includes = array();
			foreach ($this->mInclusions as $inclusion) {
				$includes[] = 'event_entities.event_entity_event_id=?';
				$parameters[] = $inclusion;
			}
			$included = '('.implode(' AND ', $includes).')';
		} else {
			$included = '0';
		}
		
		$sources = '('.$own.' OR ('.$public.' AND ('.$subscribed.' OR '.$included.')))';
		
		// FILTERS
		
		$occurrence_states = array(
				'private' => array('draft','trashed'),
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
		if ($this->mFilters['hidden']) {
			$visibility_predicates[] = 'event_occurrence_users.event_occurrence_user_hide=1';
		}
		if ($this->mFilters['normal']) {
			$visibility_predicates[] =
			'(		(	event_occurrence_users.event_occurrence_user_hide=0
					AND event_occurrence_users.event_occurrence_user_rsvp=0)
				OR	(	event_occurrence_users.event_occurrence_user_hide IS NULL))';
		}
		if ($this->mFilters['rsvp']) {
			$visibility_predicates[] = 'event_occurrence_users.event_occurrence_user_rsvp=1';
		}
		if (count($visibility_predicates) > 0) {
			$visibility = '('.implode(' OR ',$visibility_predicates).')';
		} else {
			$visibility = '0';
		}
		
		$filters = '('.$state.' AND '.$visibility.')';
		
		// WHERE CLAUSE		
		$sql .= ' WHERE '.$sources.' AND '.$filters.';';

		// Try it out
		$CI = &get_instance();
		$query = $CI->db->query($sql,$parameters);
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
	 * @param $Start Start time.
	 * @param $End End time.
	 */
	function SetRange($Start, $End)
	{
		$this->mRange[0] = $Start;
		$this->mRange[1] = $End;
	}
	
}

/// Model for access to events.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * User operations
 *
 * Admin operations
 *
 */
class Events_model extends Model
{
	private $mStates;
	
	private $mDayInformation;
	private $mOccurrences;
	
	protected $mOccurrenceFilter;
	
	/// Default constructor
	function __construct()
	{
		parent::Model();
		
		$this->mStates = array();
		$this->mDayInformation = FALSE;
		$this->mOccurrences = FALSE;
		$this->SetOccurrenceFilter();
		
		$this->load->library('academic_calendar');
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
	
	/// Set whether special day headings are enabled.
	function IncludeDayInformationSpecial($Enabled)
	{
		$this->SetEnabled('day-info-special', $Enabled);
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
	 *
	 * @todo Retrieve event data from the database instead of hardcoded.
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
			
			if ($this->IsEnabled('day-info-special', TRUE)) {
				// For now just calculate from the rules every time!
				$special_days = $this->RuleCollectionStdEngland();
				foreach ($special_days as $special_day) {
					$name = $special_day[0];
					$rule = $special_day[1];
					$matches = $rule->FindTimes($StartTime->Timestamp(), $EndTime->Timestamp());
					foreach ($matches as $date=>$enable) {
						if ($enable) {
							// For each one, put it in the apropriate day info item
							$day_time = $this->academic_calendar->Timestamp($date);
							$day_time = $day_time->Midnight()->Timestamp();
							$this->mDayInformation[$day_time]['special_headings'][] = $name;
						}
					}
				}
			}
		}
		
		// Event occurrences
		if ($this->IsEnabled('occurrences-all')) {
			$this->mOccurrences = array();
			$dummies = $this->DummyOccurrences();
			foreach ($dummies as $dummy) {
				if ($dummy['end'] > $StartTime->Timestamp() &&
					$dummy['start'] < $EndTime->Timestamp()) {
					$this->mOccurrences[] = $dummy;
				}
			}
		}
		
		return $success;
	}
	
	/// Get the day information retrieved.
	/**
	 * @pre IsEnabled('day-info')
	 * @pre Retrieve has been called successfully.
	 * @return Array of days with related information, indexed by timestamp of
	 *	midnight on the morning of the day.
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
	
	
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! //
	// THE FOLLOWING IS TEMPORARY HARDCODED DATA: //
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! //
	
	/// TEMP: Get a bunch of dummy occurrences.
	private function DummyOccurrences()
	{
		// define some dummy events with a rough schema until we have access
		// to some real data to play with
		return array (
			array (
				'ref_id' => '1',
				'name' => 'Xmas Party',
				'start' => mktime(21, 0,0, 12, 24,2006),
				'end'   => mktime( 0, 0,0, 12, 25,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social',
				'state' => 'published',
			),
			array (
				'ref_id' => '2',
				'name' => 'Boring lecture about vegetables',
				'start' => mktime(12,45,0, 12,29,2006),
				'end'   => mktime(15, 0,0, 12,29,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic',
				'state' => 'published',
			),
			array (
				'ref_id' => '3',
				'name' => 'Social Gathering',
				'start' => mktime(21, 0,0, 12,25,2006),
				'end'   => mktime( 0, 0,0, 12,26,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social',
				'state' => 'postponed',
			),
			array (
				'ref_id' => '4',
				'name' => 'Mince Pies and Punch',
				'start' => mktime(12,45,0, 12,29,2006),
				'end'   => mktime(15, 0,0, 12,29,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic',
				'state' => 'published',
			),
			array (
				'ref_id' => '5',
				'name' => 'International talk-like-a-pirate day',
				'start' => mktime(21, 0,0, 12,26,2006),
				'end'   => mktime( 0, 0,0, 12,27,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'athletic',
				'state' => 'published',
			),
			array (
				'ref_id' => '6',
				'name' => 'boring lecture about vegetables',
				'start' => mktime(12,45,0, 12,29,2006),
				'end'   => mktime(15, 0,0, 12,29,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'party',
				'state' => 'cancelled',
			),
			array (
				'ref_id' => '7',
				'name' => 'House Party',
				'start' => mktime(21, 0,0, 12,25,2006),
				'end'   => mktime( 0, 0,0, 12,26,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social',
				'state' => 'published',
			),
			array (
				'ref_id' => '8',
				'name' => 'House Party',
				'start' => mktime(21, 0,0, 12,26,2006),
				'end'   => mktime( 6, 0,0, 12,27,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social',
				'state' => 'published',
			),
			array (
				'ref_id' => '9',
				'name' => 'boring lecture about vegetables',
				'start' => mktime(12,45,0, 12,27,2006),
				'end'   => mktime(15, 0,0, 12,27,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic',
				'state' => 'published',
			),
			array (
				'ref_id' => '10',
				'name' => 'MARATHON Noodle eating contest',
				'start' => mktime(12,45,0, 12,27,2006),
				'end'   => mktime(15, 0,0, 12,27,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'Noodleishous!',
				'shortloc' => 'L/049',
				'type' => 'academic',
				'state' => 'draft',
			),
			array (
				'ref_id' => '11',
				'name' => 'Regional \'Pong\' championships, semi final',
				'start' => mktime(12,45,0, 12,27,2006),
				'end'   => mktime(15, 0,0, 12,27,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => '|&nbsp;.&nbsp;&nbsp;&nbsp;&nbsp;|<br />
							|&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;|<br />
							|&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;|<br />
							|&nbsp;&nbsp;&nbsp;&nbsp;.&nbsp;|<br />
							|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.|<br />
							|&nbsp;&nbsp;&nbsp;&nbsp;.&nbsp;|<br />
							|&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;|<br />etc.',
				'shortloc' => 'L/049',
				'type' => 'academic',
				'state' => 'published',
			),
			array (
				'ref_id' => '12',
				'name' => 'Better than vegetables',
				'start' => mktime(18, 0,0, 12,14,2006),
				'end'   => mktime(20, 0,0, 12,14,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'Just a few pints',
				'shortloc' => 'McQ\'s',
				'type' => 'social',
				'state' => 'published',
			)
		);
	}
	
	/// TEMP: Put the standard england special days into an array.
	/**
	 * @return array of recurrence rules, each in this form:
	 *	- element 0: Name.
	 *	- element 1: RecurrenceRule describing when it takes place.
	 */
	private function RuleCollectionStdEngland()
	{
		$this->load->library('recurrence');
		
		$result = array();
		$result[] = array('New Years Day',$this->NewYearsDay());
		$result[] = array('Valentines Day',$this->ValentinesDay());
		$result[] = array('St. Patricks Day',$this->StPatricksDay());
		$result[] = array('Shrove Tuesday',$this->ShroveTuesday());
		$result[] = array('Ash Wednesday',$this->AshWednesday());
		$result[] = array('Mothering Sunday',$this->MotheringSunday());
		$result[] = array('April Fools Day',$this->AprilFoolsDay());
		$result[] = array('Good Friday',$this->GoodFriday());
		$result[] = array('Bank Holiday',$this->GoodFriday());
		$result[] = array('Easter Sunday',$this->EasterSunday());
		$result[] = array('Bank Holiday',$this->EasterMonday());
		$result[] = array('British Summer Time Begins',$this->BstBegins());
		$result[] = array('Fathers Day',$this->FathersDay());
		$result[] = array('St. Georges Day',$this->StGeorgesDay());
		$result[] = array('British Summer Time Ends',$this->BstEnds());
		$result[] = array('Early May Bank Holiday',$this->EarlyMayBankHoliday());
		$result[] = array('Spring Bank Holiday',$this->SpringBankHoliday());
		$result[] = array('Summer Bank Holiday',$this->SummerBankHoliday());
		$result[] = array('Halloween',$this->Halloween());
		$result[] = array('Bonfire Night',$this->BonfireNight());
		$result[] = array('Remembrance Day',$this->RemembranceDay());
		$result[] = array('Remembrance Sunday',$this->RemembranceSunday());
		$result[] = array('Christmas Eve',$this->ChristmasEve());
		$result[] = array('Christmas Day',$this->ChristmasDay());
		$result[] = array('Boxing Day',$this->BoxingDay());
		$result[] = array('New Years Eve',$this->NewYearsEve());
		return $result;
	}
	
	/// TEMP: Return the RecurrenceRule for easter sunday.
	function EasterSunday()
	{
		$rule = new RecurrenceRule();
		$rule->EasterSunday();
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for easter monday.
	function EasterMonday()
	{
		// monday after easter sunday
		$rule = $this->EasterSunday();
		$rule->OffsetDays(+1);
		return $rule;
	}
	
	/// TEMP:  the RecurrenceRule for good friday.
	function GoodFriday()
	{
		// friday before easter sunday
		$rule = $this->EasterSunday();
		$rule->OffsetDays(-2);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for mothering sunday.
	function MotheringSunday()
	{
		// 3 weeks before easter
		$rule = $this->EasterSunday();
		$rule->OffsetDays(-3*7);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for fathers day.
	function FathersDay()
	{
		$rule = new RecurrenceRule();
		// 3rd sunday in june
		$rule->MonthDate(6);
		$rule->OnlyDayOfWeek(0);
		$rule->SetWeekOffset(2);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for ash wednesday.
	function AshWednesday()
	{
		// beginning of lent (39 days before easter)
		$rule = $this->EasterSunday();
		$rule->OffsetDays(-39);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for shrove tuesday.
	function ShroveTuesday()
	{
		// day before ash wednesday
		$rule = $this->AshWednesday();
		$rule->OffsetDays(-1);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for christmas day.
	function ChristmasDay()
	{
		$rule = new RecurrenceRule();
		// 25th december
		$rule->MonthDate(12,25);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for christmas eve.
	function ChristmasEve()
	{
		$rule = new RecurrenceRule();
		// 24th december
		$rule->MonthDate(12,24);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for boxing day.
	function BoxingDay()
	{
		$rule = new RecurrenceRule();
		// 24th december
		$rule->MonthDate(12,26);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for new years eve.
	function NewYearsEve()
	{
		$rule = new RecurrenceRule();
		// 24th december
		$rule->MonthDate(12,31);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for new years day.
	function NewYearsDay()
	{
		$rule = new RecurrenceRule();
		// 24th december
		$rule->MonthDate(1,1);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for St. George's day.
	function StGeorgesDay()
	{
		$rule = new RecurrenceRule();
		// normally 23th april
		$rule->MonthDate(4,23);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for St. Patrick's day.
	function StPatricksDay()
	{
		$rule = new RecurrenceRule();
		// normally 17th march (my mum's birthday)
		$rule->MonthDate(3,17);
		return $rule;
	}
	
	/// TEMP: Return the RecurrenceRule for St. Stythian's day.
	function StStythiansFeastDay()
	{
		$rule = new RecurrenceRule();
		// first sunday in july with double figures
		$rule->MonthDate(7,10);
		$rule->OnlyDayOfWeek(0);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for Stithians show day.
	function StithiansShowDay()
	{
		// monday after St. Stythian's day
		$rule = $this->StStythiansFeastDay();
		$rule->OffsetDays(+1);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for the leap day in the gregorian calendar.
	function LeapDay()
	{
		$rule = new RecurrenceRule();
		// 29th february
		$rule->MonthDate(2,29);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for valentines day.
	function ValentinesDay()
	{
		$rule = new RecurrenceRule();
		// 14th february
		$rule->MonthDate(2,14);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for april fools day.
	function AprilFoolsDay()
	{
		$rule = new RecurrenceRule();
		// 1st April
		$rule->MonthDate(4,1);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for halloween.
	function Halloween()
	{
		$rule = new RecurrenceRule();
		// 31st October
		$rule->MonthDate(10,31);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for bonfire night.
	function BonfireNight()
	{
		$rule = new RecurrenceRule();
		// 5th november
		$rule->MonthDate(11,5);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for remembrance day.
	function RemembranceDay()
	{
		$rule = new RecurrenceRule();
		// 11th november
		$rule->MonthDate(11,11);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for remembrance day.
	function RemembranceSunday()
	{
		$rule = new RecurrenceRule();
		// nearest sunday to 11th november
		$rule->MonthDate(11,11);
		$rule->UseClosestEnabledDay();
		$rule->OnlyDayOfWeek(0);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for spring bank holiday.
	function EarlyMayBankHoliday()
	{
		$rule = new RecurrenceRule();
		// first monday in may
		$rule->MonthDate(5);
		$rule->OnlyDayOfWeek(1);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for spring bank holiday.
	function SpringBankHoliday()
	{
		$rule = new RecurrenceRule();
		// last monday in may
		$rule->MonthDate(6);
		$rule->OnlyDayOfWeek(1);
		$rule->SetWeekOffset(-1);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for summer bank holiday.
	function SummerBankHoliday()
	{
		$rule = new RecurrenceRule();
		// last monday in august
		$rule->MonthDate(9);
		$rule->OnlyDayOfWeek(1);
		$rule->SetWeekOffset(-1);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for changing clocks forward.
	function BstBegins()
	{
		$rule = new RecurrenceRule();
		// last sunday in april
		$rule->MonthDate(5);
		$rule->OnlyDayOfWeek(0);
		$rule->SetWeekOffset(-1);
		$rule->Time(2);
		return $rule;
	}
	
	/// TEMP: Returns the RecurrenceRule for changing clocks back.
	function BstEnds()
	{
		$rule = new RecurrenceRule();
		// last sunday in october
		$rule->MonthDate(11);
		$rule->OnlyDayOfWeek(0);
		$rule->SetWeekOffset(-1);
		$rule->Time(2);
		return $rule;
	}

}

?>