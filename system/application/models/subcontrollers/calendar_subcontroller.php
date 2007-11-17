<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file helpers/calendar_control.php
 * @brief General calendar controller implementing the main calendar uri interface.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

/* URL HIERARCHY

/calendar							- personal calendar
	/index							- index page with more config links + preview of cal
	/[view]							- viewing multiple items over time
		/[range]/$range/$filter		- view depends on range
		/agenda/$range/$filter		- agenda style
		/export/$range/$filter		- export to a file format
			/[index]				- information about export options
			/ical					- ical export
	/src							- sources
		/[index]					- information about event sources
		/$source					- event source
			/[index]				- information about the source
			/create					- create an event in the source
			/event/$event			- about a specific event
				/[info]				- view info about specific event
				/edit				- edit a specific event
				/op/$op				- operations such as delete, publish etc
				/occ/$occurrence	- stuff about a specific occurrence
					/[info]/$range	- view info
					/edit/$range	- make changes
					/op				- operations such as delete, publish etc
			/export					- export the contents of a source
			/import					- import data into the source
			/subscribe/$orgid/$type		- for subscribing to organisations
			/unsubscribe/$orgid/$type	- for subscribing to organisations
				
	/ajax
		/recursimplevalidate
*/

/// Interface to give to view for providing paths to bits of calendar system.
class CalendarPaths
{
	/// string Base path of calendar.
	protected $mPath = NULL;
	
	/// string Set the default range in usual format.
	protected $mDefaultRange = NULL;
	
	/// string Current calendar mode (range/agenda).
	protected $mCalendarMode = NULL;
	
	/// Set the base path.
	function SetPath($Path)
	{
		$this->mPath = $Path;
	}
	
	/// Set the default range.
	function SetDefaultRange($Range)
	{
		$this->mDefaultRange = $Range;
	}
	
	/// Set the calendar mode (range/agenda).
	function SetCalendarMode($Mode)
	{
		$this->mCalendarMode = $Mode;
	}
	
	/// Get the index path.
	function Index()
	{
		return $this->mPath . '/index';
	}
	
	/// Get the notification action path.
	function NotificationAction()
	{
		return $this->mPath . '/notification';
	}
	
	/// Get subscrptions tab path.
	function Subscriptions()
	{
		$path = $this->mPath . '/subscriptions';
		return $path;
	}
	
	/// Get the range path.
	function Range($range = NULL, $filter = NULL)
	{
		$path = $this->mPath . '/view/range/';
		if (NULL !== $range) {
			$path .= $range.'/';
			if (NULL !== $filter) {
				$path .= $filter.'/';
			}
		}
		return $path;
	}
	
	/// Get the agenda path.
	function Agenda($range = NULL, $filter = NULL)
	{
		$path = $this->mPath . '/view/agenda/';
		if (NULL !== $range) {
			$path .= $range.'/';
			if (NULL !== $filter) {
				$path .= $filter.'/';
			}
		}
		return $path;
	}
	
	/// Gets the path to the set calendar mode.
	function Calendar($range = NULL, $filter = NULL)
	{
		switch ($this->mCalendarMode) {
			case 'agenda':
				return $this->Agenda($range, $filter);
				
			case 'range':
				return $this->Range($range, $filter);
				
			default:
				return NULL;
		}
	}
	
	/// Validate simple recurrence.
	function SimpleRecurValidate()
	{
		return $this->mPath . '/ajax/recursimplevalidate';
	}
	
	/// Get the event creation path.
	function EventCreateRaw($SourceId, $Range = NULL)
	{
		$path = $this->mPath . "/src/$SourceId/create/";
		if (NULL !== $Range) {
			$path .= $Range;
		} elseif (NULL !== $this->mDefaultRange) {
			$path .= $this->mDefaultRange.'/';
		} else {
			$path .= 'default/';
		}
		return $path;
	}
	
	/// Get the event creation path.
	function EventCreate($Source, $Range = NULL)
	{
		return $this->EventCreateRaw($Source->GetSourceId(), $Range);
	}
	
	/// Get the event creation path.
	function EventCreateQuickRaw($SourceId)
	{
		return $this->EventCreateRaw($SourceId);
	}
	
	/// Get the event creation path.
	function EventCreateQuick($Source)
	{
		return $this->EventCreateQuickRaw($Source->GetSourceId(), $Range);
	}
	
	/// Get the event information path.
	function EventInfo($Event, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Event->Source->GetSourceId().
			'/event/'.	$Event->SourceEventId.
			'/info'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event edit path.
	function EventEdit($Event, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Event->Source->GetSourceId().
			'/event/'.	$Event->SourceEventId.
			'/edit'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event publish path.
	function EventPublish($Event, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Event->Source->GetSourceId().
			'/event/'.	$Event->SourceEventId.
			'/op/publish'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event delete path.
	function EventDelete($Event, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Event->Source->GetSourceId().
			'/event/'.	$Event->SourceEventId.
			'/op/delete'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event occurrence information path.
	function OccurrenceRawInfo($SourceId, $EventId, $OccurrenceId, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			"/src/$SourceId/event/$EventId/occ/$OccurrenceId/info".
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event occurrence information path.
	function OccurrenceInfo($Occurrence, $range = NULL, $filter = NULL)
	{
		return $this->OccurrenceRawInfo(
			$Occurrence->Event->Source->GetSourceId(),
			$Occurrence->Event->SourceEventId,
			$Occurrence->SourceOccurrenceId,
			$range, $filter);
	}
	
	/// Get the event occurrence edit path.
	function OccurrenceEdit($Occurrence, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Occurrence->Event->Source->GetSourceId().
			'/event/'.	$Occurrence->Event->SourceEventId.
			'/occ/'.	$Occurrence->SourceOccurrenceId.
			'/edit'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event occurrence publish path.
	function OccurrencePublish($Occurrence, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Occurrence->Event->Source->GetSourceId().
			'/event/'.	$Occurrence->Event->SourceEventId.
			'/occ/'.	$Occurrence->SourceOccurrenceId.
			'/op/publish'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event occurrence delete path.
	function OccurrenceDelete($Occurrence, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Occurrence->Event->Source->GetSourceId().
			'/event/'.	$Occurrence->Event->SourceEventId.
			'/occ/'.	$Occurrence->SourceOccurrenceId.
			'/op/delete'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event occurrence cancel path.
	function OccurrenceCancel($Occurrence, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Occurrence->Event->Source->GetSourceId().
			'/event/'.	$Occurrence->Event->SourceEventId.
			'/occ/'.	$Occurrence->SourceOccurrenceId.
			'/op/cancel'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event occurrence postpone path.
	function OccurrencePostpone($Occurrence, $range = NULL, $filter = NULL)
	{
		return $this->mPath .
			'/src/'.	$Occurrence->Event->Source->GetSourceId().
			'/event/'.	$Occurrence->Event->SourceEventId.
			'/occ/'.	$Occurrence->SourceOccurrenceId.
			'/op/postpone'.
			'/'.(NULL !== $range  ? $range  : 'default').
			'/'.(NULL !== $filter ? $filter : 'default');
	}
	
	/// Get the event occurrence set attendence path.
	/**
	 * @param $attend string Should be one of 'yes', 'no', 'maybe'.
	 * @param $ajax bool Whether to get the xml version.
	 */
	function OccurrenceAttend($Occurrence, $attend, $ajax = false)
	{
		return $this->mPath .
			'/src/'.	$Occurrence->Event->Source->GetSourceId().
			'/event/'.	$Occurrence->Event->SourceEventId.
			'/occ/'.	$Occurrence->SourceOccurrenceId.
			($ajax?'/ajax':'').'/attend/'.	$attend;
	}
	
	/// Get the subscribe to organisation stream path.
	function OrganisationSubscribe($OrgId, $Type)
	{
		return $this->mPath . "/subscribe/$OrgId/$Type";
	}
	/// Get the unsubscribe from organisation stream path.
	function OrganisationUnsubscribe($OrgId, $Type)
	{
		return $this->mPath . "/unsubscribe/$OrgId/$Type";
	}
	
	/// Get the HTML for a subscribe link.
	function OrganisationSubscribeLink($OrgName, $OrgId, $Type)
	{
		return '<a href="'.site_url($this->OrganisationSubscribe($OrgId, 'calendar').get_instance()->uri->uri_string()).'">'.
			"Show $OrgName events in my Personal Calendar</a>";
	}
	
	/// Get the HTML for a subscribe link.
	function OrganisationUnsubscribeLink($OrgName, $OrgId, $Type)
	{
		return '<a href="'.site_url($this->OrganisationUnsubscribe($OrgId, 'calendar').get_instance()->uri->uri_string()).'">'.
			"Do not show $OrgName events in my Personal Calendar</a>";
	}
}


$CI = & get_instance();
$CI->load->model('subcontrollers/uri_tree_subcontroller');

/// Calendar main controller.
class Calendar_subcontroller extends UriTreeSubcontroller
{
	/// string Required permission level.
	protected $mPermission = 'public';
	/// CalendarPaths Path calculator.
	protected $mPaths = NULL;
	/// array[name => category] Categories to display filters for.
	protected $mCategories = NULL;
	/// string Current range of dates.
	protected $mDateRange = 'today';
	/// string The default date range.
	protected $mDefaultRange = 'today:1week';
	/// string Overlap past midnight to get events, as input to strtotime.
	protected $mDefaultOverlap = '6hours';
	/// string Page code for index pages.
	protected $mIndexPageCode = 'calendar_index_personal';
	/// string Page code for range pages.
	protected $mRangePageCode = 'calendar_personal';
	/// bool Various permission flags.
	protected $mPermissions = array();
	
	/// CalendarSource Main source
	protected $mMainSource = NULL;
	/// CalendarSource Source in use.
	protected $mSource = NULL;
	/// string Event identifier
	protected $mEvent = NULL;
	
	/// array(int => array('subscribed','name','shortname')) Stream information.
	protected $mStreams = NULL;
	
	/// bool Whether to have tabs.
	protected $mTabs = TRUE;
	
	/// Filter definition.
	protected $sFilterDef = array(
		// category
		'cat' => array(
			'name' => 'category',
			array(
				// Filled in by SetupCategories
			),
		),
		'att' => array(
			'name' => 'attending',
			array(
				'no-declined',
				'no-maybe',
				'no-accepted',
				'declined',
				'maybe',
				'accepted',
			),
		),
		'source' => array(
			array(
				'type' => 'int',
			),
		),
		'search' => array(
			array(
				'name' => 'field',
				'all',
				'name',
				'description',
			),
			array(
				'name' => 'criteria',
				'type' => 'string',
			),
			array(
				'name' => 'flags',
				'count' => array(0),
				'regex',
				'case',
			),
		),
	);
	
	/// virtual Set the base path.
	function SetPath($Path)
	{
		$this->mPaths = new CalendarPaths();
		$this->mPaths->SetPath($Path);
	}
	
	/// Set the page code for the ranges.
	function SetRangePageCode($PageCode)
	{
		$this->mRangePageCode = $PageCode;
	}
	
	/// Set the page code for the index.
	function SetIndexPageCode($PageCode)
	{
		$this->mIndexPageCode = $PageCode;
	}
	
	/// Default constructor.
	function __construct()
	{
		// Provide the ComplexController class with the url structure
		$range_filter = array(
			'' => '*',
			'*' => array(
				'_store' => 'Range',
				'' => '*',
				'*' => array(
					'_store' => 'Filter'
				),
			),
		);
		$event_op = array(
			'' => NULL,
			'*' => array(
				'_store' => 'OperationId',
				'' => '*',
				'*' => array(
					'_store' => 'Range',
					'' => '*',
					'*' => array(
						'_store' => 'Filter',
						'_call' => 'src_event_op',
					),
				),
			),
		);
		parent::__construct(array(
			'' => 'view',
			'index' => 'index',
			'view' => array(
				'' => 'range',
				'_in' => array(
					array(
						$range_filter,
						'range' => 'range',
						'agenda' => 'agenda',
						'export' => array(
							'' => 'index',
							'index' => NULL,
							'ical' => NULL,
						),
					),
				),
			),
			'subscriptions' => array(
				'' => 'index',
				'index' => 'subscriptions_index',
			),
			'src' => array(
				'' => 'index',
				'index' => 'src_index',
				'_match' => array(
					'is_numeric' => array(
						'_store' => 'SourceId',
						'' => 'index',
						'index' => 'src_source_index',
						'create' => 'src_source_create',
						'event' => array(
							'*' => array(
								'_store' => 'EventId',
								'' => 'info',
								'_in' => array(
									array(
										$range_filter,
										'info' => 'src_event_info',
										'edit' => 'src_event_edit',
									),
								),
								'op' => $event_op,
								'occ' => array(
									'*' => array(
										'_store' => 'OccurrenceId',
										'' => 'info',
										'_in' => array(
											array(
												$range_filter,
												'info' => 'src_event_info',
												'edit' => 'src_event_edit',
											),
										),
										'op' => $event_op,
										'attend' => 'src_event_attend',
										'ajax' => array(
											'_store' => 'ajax',
											'attend' => 'src_event_attend',
										),
									),
								),
							),
						),
						'export' => NULL,
						'import' => NULL,
					),
				),
			),
			'notification'=> 'notification_action',
			'subscribe'   => 'subscribe_stream',
			'unsubscribe' => 'unsubscribe_stream',
			'ajax' => array(
				'recursimplevalidate' => 'ajax_recursimplevalidate',
			),
		));
	}
	
	/// Set the required permission level.
	/**
	 * @param $Permission string Required permission level.
	 */
	function _SetPermission($Permission = 'public')
	{
		/// @pre is_string(@a $Permission).
		assert('is_string($Permission)');
		// Initialise
		$this->mPermission = $Permission;
	}
	
	/// Add an allowed permission.
	/**
	 * @param $Permission string/array Allowed action(s).
	 * @pre foreach argument Z : is_string(Z) || (is_array(Z) && forall(Z, is_string))
	 */
	function _AddPermission($Permission)
	{
		// Initialise
		foreach (func_get_args() as $val) {
			if (is_array($val)) {
				foreach ($val as $subval) {
					assert('is_string($subval)');
					$this->mPermissions[$subval] = true;
				}
			} else {
				assert('is_string($val)');
				$this->mPermissions[$val] = true;
			}
		}
	}
	
	/// Set the default first segment.
	function _SetDefault($Default)
	{
		$this->mStructure[''] = $Default;
	}
	
	/// An action on notification.
	function notification_action()
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		$this->load->library('calendar_notifications');
		
		// Perform any notification actions.
		$this->messages->AddMessages(Notifications::CheckNotificationActions());
		
		// If there are messages, redirect to self to flush the post data.
		if (isset($_POST['refer'])) {
			return redirect($_POST['refer']);
		} else {
			return redirect($this->mPaths->Index());
		}
	}
	
	/// Index page with calendar preview + other stuff.
	function index()
	{
		if (!isset($this->mPermissions['index'])) {
			return show_404();
		}
		OutputModes('xhtml','fbml');
		if (!CheckPermissions($this->mPermission)) return;
		
		/// @todo Put this into a view library.
		$this->load->library('calendar_notifications');
		
		$this->pages_model->SetPageCode($this->mIndexPageCode);
		$this->SetupTabs('index', new Academic_time(time()));
		
		$data = array(
			'IntroHtml' => $this->pages_model->GetPropertyWikitext('intro'),
			'RightbarHtml' => $this->pages_model->GetPropertyWikitext('rightbar'),
			'Paths' => $this->mPaths,
			'Notifications' => Calendar_notifications::GetNotifications($this->mPaths),
		);
		$this->main_frame->SetContentSimple('calendar/index', $data);
		
		$this->main_frame->Load();
	}
	
	function range()
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		$this->pages_model->SetPageCode($this->mRangePageCode);
		
		$this->_SetupMyCalendar();
		$this->mPaths->SetCalendarMode('range');
		$this->SetupCategories();
		
		$date_range = array_key_exists('Range', $this->mData)
					? $this->mData['Range']
					: $this->mDefaultRange;
		$filter = array_key_exists('Filter', $this->mData)
				? $this->mData['Filter']
				: NULL;
		
		$date_range_split = explode(':', $date_range);
		$this->mPaths->SetDefaultRange($date_range_split[0]);
		
		$this->load->library('date_uri');
		$range = $this->date_uri->ReadUri($date_range, TRUE);
		$now = new Academic_time(time());
		if (!$range['valid']) {
			$date_range = $this->mDefaultRange;
			$range = $this->date_uri->ReadUri($date_range, TRUE);
			assert($range['valid']);
		}
		$start	= $range['start'];
		$end	= $range['end'];
		
		$days = Academic_time::DaysBetweenTimestamps(
			$start->Timestamp(),
			$end->Timestamp()
		);
		
		$this->mDateRange = $date_range;
		
		$Organisation_names = 'Yorker';
		if (is_array($this->mStreams)) {
			$org_names = array();
			foreach ($this->mStreams as $org_info) {
				$org_names[] = $org_info['name'];
			}
			$Organisation_names = implode(', ', $org_names);
		}
		$this->main_frame->SetTitleParameters(array(
			'range' => $range['description'],
			'organisation' => $Organisation_names,
		));
		
		/// @todo it seems to be calling ReadUri twice, once in this function and once in each callee.
		if ($days > 7) {
			$range_view = $this->GetWeeks(
				$this->mMainSource,
				$date_range,
				$filter,
				$range['format']);
		} elseif ($days > 1) {
			$range_view = $this->GetDays(
				$this->mMainSource,
				$date_range,
				$filter,
				$range['format']);
		} else {
			$range_view = $this->GetDay(
				$this->mMainSource,
				$date_range,
				$filter,
				$range['format']);
		}
		if (is_array($this->mStreams)) {
			$range_view->SetData('streams', $this->mStreams);
			$range_view->SetData('Path', $this->mPaths);
		}
		$range_view->SetData('Permissions', $this->mPermissions);
		
		$this->main_frame->SetContent($range_view);
		
		$this->main_frame->Load();
	}
	
	function GetCreateSources(&$sources)
	{
		$create_sources = array();
		if (isset($this->mPermissions['create'])) {
			foreach ($sources->GetSources() as $source) {
				if ($source->IsSupported('create')) {
					$create_sources[] = $source;
				}
			}
		}
		return $create_sources;
	}
	
	function GetDay(&$sources, $DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$range = $this->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start = $range['start'];
		} else {
			$start = $now->Midnight();
		}
		$end = $start->Adjust('1day');
		$stretch_end = $end->Adjust($this->mDefaultOverlap);
		
		$sources->SetRange($start->Timestamp(), $stretch_end->Timestamp());
// 		$sources->SetTodoRange(time(), time());
		$this->ReadFilter($sources, $Filter);
// 		$sources->EnableGroup('todo');
		
		$create_sources = $this->GetCreateSources($sources);
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_frontend');
		$this->load->library('calendar_view_days');
		$this->load->library('calendar_view_todo_list');
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($calendar_data);
		$days->SetPaths($this->mPaths);
		$days->SetRangeFormat($Format);
		$days->SetRangeFilter(NULL !== $Filter ? '/'.$Filter : '');
		$days->SetStartEnd($start->Timestamp(), $end->Timestamp());
		$days->SetCategories($this->mCategories);
		$days->EnableCreate(
			!empty($create_sources)
		);
		
// 		$todo = new CalendarViewTodoList();
// 		$todo->SetCalendarData($calendar_data);
// 		$todo->SetCategories($this->mCategories);
		
		$view_mode_data = array(
			'DateDescription' => 'Today probably!',
			'DaysView'        => &$days,
// 			'TodoView'        => &$todo,
		);
		$view_mode = new FramesFrame('calendar/day', $view_mode_data);
		
		$data = array(
			'Filters'	=> $this->GetFilters($sources),
			'ViewMode'	=> $view_mode,
			'RangeDescription' => $range['description'],
			'Path'		=> $this->mPaths,
			'CreateSources'	=> $create_sources,
		);
		
		$this->SetupTabs('day', $start, $Filter);
		
		return new FramesView('calendar/my_calendar', $data);
	}
	
	function GetDays(&$sources, $DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		// Read date range
		$range = $this->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start = $range['start'];
			$end = $range['end'];
		} else {
			$start = $now->Midnight();
			$end = $start->Adjust('7day');
		}
		$stretch_end = $end->Adjust($this->mDefaultOverlap);
		
		$sources->SetRange($start->Timestamp(), $stretch_end->Timestamp());
		$this->ReadFilter($sources, $Filter);
		
		$create_sources = $this->GetCreateSources($sources);
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_frontend');
		$this->load->library('calendar_view_days');
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($calendar_data);
		$days->SetStartEnd($start->Timestamp(), $end->Timestamp());
		$days->SetPaths($this->mPaths);
		$days->SetRangeFormat($Format);
		$days->SetRangeFilter(NULL !== $Filter ? '/'.$Filter : '');
		$days->SetCategories($this->mCategories);
		$days->EnableCreate(
			isset($this->mPermissions['create']) &&
			$sources->GetSource(0)->IsSupported('create')
		);
		
		$data = array(
			'Filters'	=> $this->GetFilters($sources),
			'ViewMode'	=> $days,
			'RangeDescription' => $range['description'],
			'Path' => $this->mPaths,
			'CreateSources'	=> $create_sources,
		);
		
		if ($now->Timestamp() >= $start->Timestamp() &&
			$now->Timestamp() < $end->Timestamp())
		{
			$focus = $now->Midnight();
		} else {
			$focus = $start;
		}
		$this->SetupTabs('days', $focus, $Filter);
		
		return new FramesView('calendar/my_calendar', $data);
	}
	
	function GetWeeks(&$sources, $DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		// Read date range
		$range = $this->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start = $range['start'];
			$end = $range['end'];
		} else {
			$start = $now->BackToMonday();
			$end = $start->Adjust('4weeks');
		}
		
		$sources->SetRange($start->Timestamp(), $end->Timestamp());
		$this->ReadFilter($sources, $Filter);
		
		$create_sources = $this->GetCreateSources($sources);
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_frontend');
		$this->load->library('calendar_view_weeks');
		
		$weeks = new CalendarViewWeeks();
		$weeks->SetCalendarData($calendar_data);
		$weeks->SetStartEnd($start->Timestamp(), $end->Timestamp());
		$weeks->SetPaths($this->mPaths);
		$weeks->SetRangeFormat($Format);
		$weeks->SetRangeFilter(NULL !== $Filter ? '/'.$Filter : '');
		$weeks->SetCategories($this->mCategories);
		
		$data = array(
			'Filters'	=> $this->GetFilters($sources),
			'ViewMode'	=> $weeks,
			'RangeDescription' => $range['description'],
			'Path' => $this->mPaths,
			'CreateSources'	=> $create_sources,
		);
		
		if ($now->Timestamp() >= $start->Timestamp() &&
			$now->Timestamp() < $end->Timestamp())
		{
			$focus = $now->Midnight();
		} else {
			$focus = $start;
		}
		$this->SetupTabs('weeks', $focus, $Filter);
		
		return new FramesView('calendar/my_calendar', $data);
	}
	
	/// Display agenda.
	function agenda()
	{
		if (!isset($this->mPermissions['agenda'])) {
			show_404();
		}
		if (!CheckPermissions($this->mPermission)) return;
		
		$this->_SetupMyCalendar();
		$this->mPaths->SetCalendarMode('agenda');
		$this->pages_model->SetPageCode('calendar_agenda');
		
		$date_range = array_key_exists('Range', $this->mData)
					? $this->mData['Range']
					: NULL;
		$filter = array_key_exists('Filter', $this->mData)
				? $this->mData['Filter']
				: NULL;
		
		if (NULL !== $date_range) {
			$this->mDateRange = $date_range;
		}
		
		$this->SetupCategories();
		$this->mMainSource->SetRange(time(), strtotime('2month'));
		$this->ReadFilter($this->mMainSource, $filter);
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($this->mMainSource->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_frontend');
		$this->load->library('calendar_view_agenda');
		
		$agenda = new CalendarViewAgenda();
		$agenda->SetCalendarData($calendar_data);
		$agenda->SetCategories($this->mCategories);
		
		$data = array(
			'Filters'	=> $this->GetFilters($this->mMainSource),
			'ViewMode'	=> $agenda,
			'Path'		=> $this->mPaths,
		);
		
		$this->SetupTabs('agenda', new Academic_time(time()), $filter);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		$this->main_frame->Load();
	}
	
	/// Subscriptions index page.
	function subscriptions_index()
	{
		if (!isset($this->mPermissions['subscriptions'])) {
			show_404();
		}
		// This is a special case.
		// It does not use the calendar backend.
		if (!CheckPermissions('student')) return;
		
		$this->pages_model->SetPageCode('calendar_subscriptions_index');
		
		$data = array(
			'Wikitexts' => array(
				'intro' => $this->pages_model->GetPropertyWikitext('intro'),
				'help_main' => $this->pages_model->GetPropertyWikitext('help_main'),
			),
		);
		
		$this->SetupTabs('subscriptions', new Academic_time(time()));
		$this->main_frame->SetContentSimple('calendar/subscriptions_index', $data);
		$this->main_frame->Load();
	}
	
	/// Sources index page.
	function src_index()
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		$this->_SetupMyCalendar();
		$sources = $this->mMainSource->GetSources();
		foreach ($sources as $source) {
			
		}
		
		$this->main_frame->SetContentSimple('calendar/sources');
		$this->main_frame->Load();
	}
	
	/// Specific source index page.
	function src_source_index()
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		$this->main_frame->SetContentSimple('calendar/source');
		$this->main_frame->Load();
	}
	
	function src_source_create_quick()
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		if (!isset($this->mPermissions['create'])) {
			return show_404();
		}
		
		// Validate the source
		if (!$this->_GetSource()) {
			return;
		}
		
		// check for post data from mini creater
		$input = array();
		if (isset($_POST['evad_create'])) {
			$input_valid = true;
			
			$source = & $this->mMainSource->GetSource(0);
			if (!$source->IsSupported('create')) {
				// Create isn't supported with this source
				$this->messages->AddMessage('error', 'You cannot create events in this calendar. You may have to be logged in.');
				$input_valid = false;
			}
			
			// Get more post data
			$input['evad_summary'] = $this->input->post('evad_summary');
			if (false === $input['evad_summary']) {
				$input['evad_summary'] = '';
			}
			if (strlen($input['evad_summary']) <= 3 or strlen($input['evad_summary']) >= 256) {
				$input_valid = false;
				$this->messages->AddMessage('error', 'Event summary is too long or too short.');
			}
			
			$input_category = $this->input->post('evad_category');
			if (false !== $input_category) {
				$input['evad_category'] = $input_category;
			}
			$input_location = $this->input->post('evad_location');
			if (false !== $input_location) {
				$input['evad_location'] = $input_location;
				if (strlen($input['evad_location']) > 50) {
					$input_valid = false;
					$this->messages->AddMessage('error', 'Event location is too long.');
				}
			}
			$input_date = $this->input->post('evad_date');
			$input_start = $this->input->post('evad_start');
			$input_end = $this->input->post('evad_end');
			if (false === $input_date || false === $input_start || false === $input_end) {
				$this->messages->AddMessage('error', 'Missing event time information.');
				$input_valid = false;
			} else {
				if (!is_numeric($input_date)) {
					$this->messages->AddMessage('error', 'Invalid date');
					$input_valid = false;
				}
				if (!is_numeric($input_start) || $input_start < 0 || $input_start > 48*60) {
					$this->messages->AddMessage('error', 'Invalid start time');
					$input_valid = false;
				}
				if (!is_numeric($input_end) || $input_end < 0 || $input_end > 48*60) {
					$this->messages->AddMessage('error', 'Invalid end time');
					$input_valid = false;
				}
				if ($input_valid) {
					$starthour = (int)($input_start / 60);
					$startminute = (int)($input_start % 60);
					$start = strtotime("$input_date 000000");
					if ($starthour >= 24) {
						$start = strtotime('+1day', $start);
						$starthour -= 24;
					}
					$start = strtotime(date('Ymd', $start).' '.sprintf("%02d%02d", $starthour, $startminute).'00');
					
					$endhour = (int)($input_end / 60);
					$endminute = (int)($input_end % 60);
					$end = strtotime("$input_date 000000");
					if ($endhour >= 24) {
						$end = strtotime('+1day', $end);
						$endhour -= 24;
					}
					$end = strtotime(date('Ymd', $end).' '.sprintf("%02d%02d", $endhour, $endminute).'00');
					
					if ($start >= $end) {
						$this->messages->AddMessage('error', 'Event must not end before it starts');
						$input_valid = false;
					}
					if ($start < strtotime('today-1year')) {
						$this->messages->AddMessage('error', 'Event out of range');
						$input_valid = false;
					}
					if ($end > strtotime('today+2year')) {
						$this->messages->AddMessage('error', 'Event out of range');
						$input_valid = false;
					}
				}
			}
			
			if ($input_valid) {
				$event_info = array(
					'name' => $input['evad_summary'],
					'category' => $input['evad_category'],
					'recur' => new RecurrenceSet(),
				);
				if (isset($input['evad_location'])) {
					$event_info['location_name'] = $input['evad_location'];
				}
				
				$event_info['recur']->SetStartEnd($start, $end);
				$messages = $source->CreateEvent($event_info, $event_id);
				$this->messages->AddMessages($messages);
				if (!isset($messages['error'])) {
					$this->messages->AddMessage('success', 'Event created successfully');
				}
			}
		}
		
		// Get the redirect url tail
		$args = func_get_args();
		$tail = implode('/', $args);
		redirect($tail);
	}
	
	function src_source_create($range = NULL)
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		if (!isset($this->mPermissions['create'])) {
			return show_404();
		}
		
		// Validate the source
		if (!$this->_GetSource()) {
			return;
		}
		
		$this->load->library('calendar_view_edit_simple');
		
		$this->pages_model->SetPageCode('calendar_new_event');
		$this->main_frame->SetTitleParameters(array(
			'source' => $this->mSource->GetSourceName(),
		));
		
		// Get the redirect url tail
		$args = func_get_args();
		array_shift($args);
		$tail = implode('/', $args);
		// Whether to redirect to tail on success
		$success_redirect_to_tail = false;
		if (isset($_POST['eved_success_redirect'])) {
			$success_redirect_to_tail = true;
		}
		
		if (!$this->mSource->IsSupported('create')) {
			// Create isn't supported with this source
			$this->ErrorNotCreateable($tail);
			$this->main_frame->Load();
			return;
		}
		
		$prefix = 'eved';
		// Default start and end
		$start = strtotime('tomorrow+12hours');
		$end = strtotime('tomorrow+13hours');
		
		// check for post data from mini creater
		// this simply sets the _POST data so it can be analysed as if clicked save
		$input = array();
		if (isset($_POST['evad_create'])) {
			$input_valid = true;

			// Transfer post data
			@$_POST[$prefix.'_summary'] = $_POST['evad_summary'];
			@$_POST[$prefix.'_category'] = $_POST['evad_category'];
			@$_POST[$prefix.'_location'] = $_POST['evad_location'];
			@$_POST[$prefix.'_description'] = $_POST['evad_description'];
			
			$input_date = $this->input->post('evad_date');
			$input_start = $this->input->post('evad_start');
			$input_end = $this->input->post('evad_end');
			if (false === $input_date || false === $input_start || false === $input_end) {
				$this->messages->AddMessage('error', 'Missing event time information.');
				$input_valid = false;
			} else {
				if (!is_numeric($input_date)) {
					$this->messages->AddMessage('error', 'Invalid date');
					$input_valid = false;
				}
				if (!is_numeric($input_start) || $input_start < 0 || $input_start > 48*60) {
					$this->messages->AddMessage('error', 'Invalid start time');
					$input_valid = false;
				}
				if (!is_numeric($input_end) || $input_end < 0 || $input_end > 48*60) {
					$this->messages->AddMessage('error', 'Invalid end time');
					$input_valid = false;
				}
				if ($input_valid) {
					$starthour = (int)($input_start / 60);
					$startminute = (int)($input_start % 60);
					$start = strtotime("$input_date 000000");
					if ($starthour >= 24) {
						$start = strtotime('+1day', $start);
						$starthour -= 24;
					}
					$start = strtotime(date('Ymd', $start).' '.sprintf("%02d%02d", $starthour, $startminute).'00');
					
					$endhour = (int)($input_end / 60);
					$endminute = (int)($input_end % 60);
					$end = strtotime("$input_date 000000");
					if ($endhour >= 24) {
						$end = strtotime('+1day', $end);
						$endhour -= 24;
					}
					$end = strtotime(date('Ymd', $end).' '.sprintf("%02d%02d", $endhour, $endminute).'00');
					
					if ($start >= $end) {
						$this->messages->AddMessage('error', 'Event must not end before it starts');
						$input_valid = false;
					}
					if ($start < strtotime('today-1year')) {
						$this->messages->AddMessage('error', 'Event out of range');
						$input_valid = false;
					}
					if ($end > strtotime('today+2year')) {
						$this->messages->AddMessage('error', 'Event out of range');
						$input_valid = false;
					}
				}
			}
			
			if ($input_valid) {
				$_POST[$prefix.'_save'] = 'save';
				$start_end_predefined = true;
				$success_redirect_to_tail = true;
			}
		}
		
		// Get the buttons from post data
		if (isset($_POST[$prefix.'_return'])) {
			// REDIRECT
			return redirect($tail);
		}
		
		$errors = array();
		
		// Read the recurrence data
		if (isset($_POST[$prefix.'_recur_simple']) and
			isset($_POST[$prefix.'_start']) and
			isset($_POST[$prefix.'_duration']) and
			isset($_POST[$prefix.'_inex']))
		{
			$rset_arr = $_POST[$prefix.'_recur_simple'];
			$rset = Calendar_view_edit_simple::validate_recurrence_set_data(
				!(isset($_POST[$prefix.'_allday']) && $_POST[$prefix.'_allday']),
				$_POST[$prefix.'_start'],
				$_POST[$prefix.'_duration'],
				$_POST[$prefix.'_recur_simple'],
				$_POST[$prefix.'_inex'],
				$errors);
		}
		// Fill it in if none supplied
		if (!isset($rset_arr)) {
			$rset = new RecurrenceSet();
			$rset->SetStartEnd($start, $end);
			$rset_arr = Calendar_view_edit_simple::transform_recur_for_view($rset, $errors);
		}
		// Always fill in the inex info again, ignoring input from form.
		$inex_arr = Calendar_view_edit_simple::transform_inex_for_view($rset, $errors);
			
		list($start, $end) = $rset->GetStartEnd();
		$categories = $this->mSource->GetAllCategories();
		
		$input = array(
			'name' => '',
			'description' => '',
			'location_name' => '',
			'category' => 0,
			'time_associated' => true,
		);
		$input_summary = $this->input->post($prefix.'_summary');
		$confirm_list = NULL;
		if (false !== $input_summary) {
			$input_valid = true;
			
			// Get more post data
			$input['name'] = $input_summary;
			if (strlen($input['name']) <= 3 or strlen($input['name']) >= 256) {
				$input_valid = false;
				$this->messages->AddMessage('error', 'Event summary is too long or too short.');
			}
			
			$input_description = $this->input->post($prefix.'_description');
			if (false !== $input_description) {
				$input['description'] = $input_description;
				if (strlen($input['name']) > 65535) {
					$input_valid = false;
					$this->messages->AddMessage('error', 'Event description is too long.');
				}
			}
			$input_category = $this->input->post($prefix.'_category');
			if (false !== $input_category) {
				$input['category'] = $input_category;
			}
			$input_location = $this->input->post($prefix.'_location');
			if (false !== $input_location) {
				$input['location_name'] = $input_location;
				if (strlen($input['location_name']) > 50) {
					$input_valid = false;
					$this->messages->AddMessage('error', 'Event location is too long.');
				}
			}
			$input['time_associated'] = ($this->input->post($prefix.'_allday') === false);
			
			// at this point $start and $end are still plain timestamps
			$input['recur'] = $rset;
			
			if ($input_valid && empty($errors)) {
				$event = new CalendarEvent(-1, $this->mSource);
				if (isset($_POST[$prefix.'_save'])) {
					$confirm_list = $this->mMainSource->GetEventRecurChanges($event, $rset);
					if (isset($confirm_list['draft']) && !$this->mMainSource->IsSupported('publish')) {
						unset($confirm_list['draft']);
					}
					if (empty($confirm_list)) {
						$_POST[$prefix.'_confirm']['confirm_btn'] = 'Confirm';
					}
				}
				if (isset($_POST[$prefix.'_confirm']['confirm_btn'])) {
					$event_id = -1;
					$messages = array();
					$messages = $this->mSource->CreateEvent($input, $event_id);
					$this->messages->AddMessages($messages);
					if (!array_key_exists('error', $messages) || empty($messages['error'])) {
						$this->messages->AddMessage('success', 'Event created successfully.');
						
						// Publish the specified occurrences.
						$publish_occurrences = array();
						foreach (array('create'/*,'draft'*/) as $namespace) {
							if (isset($_POST[$prefix.'_confirm'][$namespace.'_publish'])) {
								if (NULL === $confirm_list) {
									$confirm_list = $this->mMainSource->GetEventRecurChanges($event, $rset);
								}
								foreach ($_POST[$prefix.'_confirm'][$namespace.'_publish'] as $day => $dummy) {
									if (isset($confirm_list[$namespace][$day])) {
										$publish_occurrences[] = $confirm_list[$namespace][$day]['start_time'];
									}
								}
							}
						}
						if (!empty($publish_occurrences)) {
							$event = new CalendarEvent(-1, $this->mSource);
							$event->SourceEventId = $event_id;
							$published = $this->mSource->PublishOccurrences($event, $publish_occurrences);
							$desired = count($publish_occurrences);
							if ($published < $desired) {
								$message_type = 'warning';
							} else {
								$message_type = 'success';
							}
							$this->messages->Addmessage($message_type, "$published out of $desired occurrences were published.");
						}
						if ($success_redirect_to_tail) {
							return redirect($tail);
						} else {
							return redirect($this->mPaths->Range(date('Y-M-j', $start)));
						}
					}
				}
			}
		}
		
		// Ready output data
		$start = new Academic_time($start);
		$end   = new Academic_time($end);
		$eventinfo = array(
			'summary' => $input['name'],
			'description' => $input['description'],
			'location' => $input['location_name'],
			'category' => $input['category'],
			'allday' => !$input['time_associated'],
			'start' => array(
				'monthday' => $start->DayOfMonth(),
				'month' => $start->Month(),
				'year' => $start->Year(),
				'time' => $start->Hour().':'.$start->Minute(),
				'yearday' => $start->DayOfYear(),
				'day' => $start->DayOfWeek(),
				'monthweek' => (int)(($start->DayOfMonth()+6)/7),
			),
			'duration' => Calendar_view_edit_simple::calculate_duration($start, $end),
		);
		if ($this->events_model->IsVip()) {
			$help_xhtml = $this->pages_model->GetPropertyWikitext('help_vip');
		} else {
			$help_xhtml = $this->pages_model->GetPropertyWikitext('help_personal');
		}
		$data = array(
			'SimpleRecur' => $rset_arr,
			'InExDates' => $inex_arr,
			'FailRedirect' => site_url($tail),
			'Path' => $this->mPaths,
			'EventCategories' => $categories,
			'FormPrefix' => $prefix,
			'EventInfo' => $eventinfo,
			'Help' => $help_xhtml,
			'CanPublish' => $this->mMainSource->IsSupported('publish'),
			'Create' => true,
			'SuccessRedirect' => $success_redirect_to_tail,
		);
		if (is_array($confirm_list)) {
			$data['Confirms'] = $confirm_list;
			if (isset($_POST[$prefix.'_confirm'])) {
				$data['Confirm'] = $_POST[$prefix.'_confirm'];
			} else {
				$data['Confirm'] = NULL;
			}
		}
		foreach ($errors as $error) {
			$this->messages->AddMessage('error', $error['text']);
		}
		
		$this->SetupTabs('', $start);

		$this->main_frame->IncludeCss('stylesheets/calendar.css');
		$this->main_frame->IncludeJs('javascript/simple_ajax.js');
		$this->main_frame->IncludeJs('javascript/calendar_edit.js');
		
		$this->main_frame->SetContent(
			new FramesView('calendar/event_edit', $data)
		);
		$this->main_frame->Load();
	}
	
	/// Event information.
	function src_event_info()
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		$source_id = $this->mData['SourceId'];
		$event_id = $this->mData['EventId'];
		$occurrence_specified = array_key_exists('OccurrenceId', $this->mData);
		if ($occurrence_specified) {
			$occurrence_id = $this->mData['OccurrenceId'];
		} else {
			$occurrence_id = NULL;
		}
		
		if (!$this->_GetSource()) {
			return;
		}
		
		$calendar_data = new CalendarData();
		$this->mMainSource->FetchEvent($calendar_data, $source_id, $event_id);
		$calendar_data->FindOrganisationInformation();
		$events = $calendar_data->GetEvents();
		// Get the redirect url tail
		$args = func_get_args();
		$tail = implode('/', $args);
		if (array_key_exists(0, $events)) {
			$event = $events[0];
		
			$this->pages_model->SetPageCode('calendar_event_info');
			
			// Find the occurrence
			$found_occurrence = NULL;
			if (NULL !== $occurrence_id) {
				foreach ($event->Occurrences as $key => $occurrence) {
					if ($occurrence->SourceOccurrenceId == $occurrence_id) {
						$found_occurrence = & $event->Occurrences[$key];
						break;
					}
				}
				if (NULL === $found_occurrence) {
					$this->messages->AddMessage('warning', 'The event occurrence with id '.$occurrence_id.' does not belong to the event with id '.$event_id.'.');
					redirect($this->mPaths->EventInfo($event).'/'.$tail);
					return;
				}
			}
			if (NULL === $occurrence_id) {
				// default to the only occurrence if there is only one.
				if (count($event->Occurrences) == 1) {
					$found_occurrence = & $event->Occurrences[0];
					$occurrence_id = $found_occurrence->SourceOccurrenceId;
				}
			}
			if ($this->input->post('evview_return')) {
				// REDIRECT
				redirect($tail);
				
			} elseif ($this->input->post('evview_edit')) {
				if ($event->ReadOnly) {
					$this->messages->AddMessage('error', 'You do not have permission to make changes to this event.');
				} else {
					// REDIRECT
					if ($occurrence_specified) {
						$path = $this->mPaths->OccurrenceEdit($found_occurrence);
					} else {
						$path = $this->mPaths->EventEdit($event);
					}
					return redirect($path.'/'.$tail);
				}
			}
			
			$data = array(
				'Event' => &$event,
				'ReadOnly' => $this->mSource->IsSupported('create'),
				'FailRedirect' => '/'.$tail,
				'Path' => $this->mPaths,
			);
			if (NULL !== $occurrence_id) {
				$data['Occurrence'] = &$found_occurrence;
				$data['Attendees'] = $this->mSource->GetOccurrenceAttendanceList($occurrence_id);
			} else {
				$data['Occurrence'] = NULL;
			}
			
			$this->main_frame->SetTitleParameters(array(
				'source' => $this->mSource->GetSourceName(),
				'event' => $event->Name,
			));
			
			if (NULL !== $occurrence_id) {
				$link_time = $found_occurrence->StartTime;
			} else {
				$link_time = new Academic_time(time());
			}
			$this->SetupTabs('', $link_time);
			
			$this->main_frame->IncludeCss('stylesheets/calendar.css');
			$this->main_frame->SetContent(
				new FramesView('calendar/event', $data)
			);
			
		} else {
			$this->ErrorNotAccessible($tail);
		}
		$this->main_frame->Load();
	}
	
	function src_event_edit()
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		// Get data from uri resolution.
		$source_id = $this->mData['SourceId'];
		$event_id = $this->mData['EventId'];
		$occurrence_specified = array_key_exists('OccurrenceId', $this->mData);
		if ($occurrence_specified) {
			$occurrence_id = $this->mData['OccurrenceId'];
		} else {
			$occurrence_id = NULL;
		}
		
		// Validate the source
		if (!$this->_GetSource()) {
			return;
		}
		
		$this->load->library('calendar_view_edit_simple');
		
		$this->pages_model->SetPageCode('calendar_event_edit');
		
		// Get the redirect url tail
		$args = func_get_args();
		$tail = implode('/', $args);
		
		$get_action = '';
		if (isset($_GET['action'])) {
			$get_action = $_GET['action'];
		}
		
		// Fetch the specified event
		$calendar_data = new CalendarData();
		$this->mMainSource->FetchEvent($calendar_data, $source_id, $event_id);
		$events = $calendar_data->GetEvents();
		if (array_key_exists(0, $events)) {
			$event = $events[0];
			
			// Find the occurrence
			$found_occurrence = NULL;
			if (NULL !== $occurrence_id) {
				foreach ($event->Occurrences as $key => $occurrence) {
					if ($occurrence->SourceOccurrenceId == $occurrence_id) {
						$found_occurrence = & $event->Occurrences[$key];
						break;
					}
				}
				if (NULL === $found_occurrence) {
					$this->messages->AddMessage('warning', 'The event occurrence with id '.$occurrence_id.' does not belong to the event with id '.$event_id.'.');
					redirect($this->mPaths->EventInfo($event).'/'.$tail);
					return;
				}
			}
			if (NULL === $occurrence_id) {
				// default to the only occurrence if there is only one.
				if (count($event->Occurrences) == 1) {
					$found_occurrence = & $event->Occurrences[0];
					$occurrence_id = $found_occurrence->SourceOccurrenceId;
				}
			}
			// Get the buttons from post data
			$prefix = 'eved';
			$return_button = isset($_POST[$prefix.'_return']);
			if (false !== $event && $event->ReadOnly) {
				$return_button = TRUE;
				$this->messages->AddMessage('error', 'You do not have permission to make changes to this event.');
			}
			
			if ($return_button) {
				// REDIRECT
				if ($occurrence_specified) {
					$path = $this->mPaths->OccurrenceInfo($found_occurrence);
				} else {
					$path = $this->mPaths->EventInfo($event);
				}
				return redirect($path.'/'.$tail);
			}
			
			$input_summary = $this->input->post($prefix.'_summary');
			$errors = array();
			$process_input = false;
			
			// Read the recurrence data
			if (isset($_POST[$prefix.'_recur_simple']) and
				isset($_POST[$prefix.'_start']) and
				isset($_POST[$prefix.'_duration']) and
				isset($_POST[$prefix.'_inex']))
			{
				$rset_arr = $_POST[$prefix.'_recur_simple'];
				$rset = Calendar_view_edit_simple::validate_recurrence_set_data(
					!(isset($_POST[$prefix.'_allday']) && $_POST[$prefix.'_allday']),
					$_POST[$prefix.'_start'],
					$_POST[$prefix.'_duration'],
					$_POST[$prefix.'_recur_simple'],
					$_POST[$prefix.'_inex'],
					$errors);
			}
			// Fill it in if none supplied
			/// @todo Fix that the new rset doesn't have the same rrule ids, so theres a deletion/insertion instead of update.
			if (!isset($rset_arr)) {
				$rset = $event->GetRecurrenceSet();
				if ((isset($_POST['evview_delete']) || 'delete' == $get_action) &&
					NULL !== $found_occurrence)
				{
					$inex_date = array($found_occurrence->StartTime->Format('Ymd') => array(NULL => NULL));
					$rset->RemoveRDates($inex_date);
					$rset->AddExDates($inex_date);
					$process_input = true;
					$_POST[$prefix.'_save'] = true;
					$input_valid = true;
				} elseif (	isset($_POST['evview_delete_all']) || 'delete_all' == $get_action) {
					$rset->ClearRecurrence();
					$process_input = true;
					$_POST[$prefix.'_save'] = true;
					$input_valid = true;
				} elseif (	(isset($_POST['evview_restore']) || 'restore' == $get_action) &&
							NULL !== $found_occurrence)
				{
					$inex_date = array($found_occurrence->StartTime->Format('Ymd') => array(NULL => NULL));
					$rset->RemoveExDates($inex_date);
					$rset->AddRDates($inex_date);
					$process_input = true;
					$_POST[$prefix.'_save'] = true;
					$input_valid = true;
				}
				$rset_arr = Calendar_view_edit_simple::transform_recur_for_view($rset, $errors);
			}
			// Always fill in the inex info again, ignoring input from form.
			$inex_arr = Calendar_view_edit_simple::transform_inex_for_view($rset, $errors);
			
			list($start, $end) = $rset->GetStartEnd();
			$categories = $this->mSource->GetAllCategories();
			
			$input = array(
				'name' => $event->Name,
				'description' => $event->Description,
				'location_name' => $event->LocationDescription,
				'category' => 0,
				'time_associated' => $event->TimeAssociated,
			);
			if (isset($categories[$event->Category])) {
				$input['category'] = $categories[$event->Category]['id'];
			}
			$input_summary = $this->input->post($prefix.'_summary');
			$confirm_list = NULL;
			if (false !== $input_summary) {
				$input_valid = true;
				$process_input = true;
				
				// Get more post data
				$input['name'] = $input_summary;
				if (strlen($input['name']) <= 3 or strlen($input['name']) >= 256) {
					$input_valid = false;
					$this->messages->AddMessage('error', 'Event summary is too long or too short.');
				}
				
				$input_description = $this->input->post($prefix.'_description');
				if (false !== $input_description) {
					$input['description'] = $input_description;
					if (strlen($input['name']) > 65535) {
						$input_valid = false;
						$this->messages->AddMessage('error', 'Event description is too long.');
					}
				}
				$input_category = $this->input->post($prefix.'_category');
				if (false !== $input_category) {
					$input['category'] = $input_category;
				}
				$input_location = $this->input->post($prefix.'_location');
				if (false !== $input_location) {
					$input['location_name'] = $input_location;
					if (strlen($input['location_name']) > 50) {
						$input_valid = false;
						$this->messages->AddMessage('error', 'Event location is too long.');
					}
				}
				$input['time_associated'] = ($this->input->post($prefix.'_allday') === false);
			}
			if ($process_input) {
				// at this point $start and $end are still plain timestamps
				$input['recur'] = $rset;
				
				if ($input_valid && empty($errors)) {
					if (isset($_POST[$prefix.'_save'])) {
						$confirm_list = $this->mMainSource->GetEventRecurChanges($event, $rset);
						if (isset($confirm_list['draft']) && !$this->mMainSource->IsSupported('publish')) {
							unset($confirm_list['draft']);
						}
						if (empty($confirm_list)) {
							$_POST[$prefix.'_confirm']['confirm_btn'] = 'Confirm';
						}
					}
					if (isset($_POST[$prefix.'_confirm']['confirm_btn'])) {
						if (NULL === $confirm_list) {
							$confirm_list = $this->mMainSource->GetEventRecurChanges($event, $rset);
						}
						// Make the change
						$messages = $this->mMainSource->AmmendEvent($event, $input);
						
						$this->messages->AddMessages($messages);
						if (!array_key_exists('error', $messages) || empty($messages['error'])) {
							// Success
							$this->messages->AddMessage('success', 'Event updated');
							
							// Publish the specified occurrences.
							$publish_occurrences = array();
							foreach (array('create','draft') as $namespace) {
								if (isset($_POST[$prefix.'_confirm'][$namespace.'_publish'])) {
									foreach ($_POST[$prefix.'_confirm'][$namespace.'_publish'] as $day => $dummy) {
										if (isset($confirm_list[$namespace][$day])) {
											$publish_occurrences[] = $confirm_list[$namespace][$day]['start_time'];
										}
									}
								}
							}
							if (!empty($publish_occurrences)) {
								$published = $this->mSource->PublishOccurrences($event, $publish_occurrences);
								$desired = count($publish_occurrences);
								if ($published < $desired) {
									$message_type = 'warning';
								} else {
									$message_type = 'success';
								}
								$this->messages->Addmessage($message_type, "$published out of $desired occurrences were published.");
							}
							
							// REDIRECT
							if ($occurrence_specified) {
								$path = $this->mPaths->OccurrenceInfo($found_occurrence);
							} else {
								$path = $this->mPaths->EventInfo($event);
							}
							return redirect($path.'/'.$tail);
						}
					}
				}
			}
			
			// Ready output data
			$start = new Academic_time($start);
			$end   = new Academic_time($end);
			$eventinfo = array(
				'summary' => $input['name'],
				'description' => $input['description'],
				'location' => $input['location_name'],
				'category' => $input['category'],
				'allday' => !$input['time_associated'],
				'start' => array(
					'monthday' => $start->DayOfMonth(),
					'month' => $start->Month(),
					'year' => $start->Year(),
					'time' => $start->Hour().':'.$start->Minute(),
					'yearday' => $start->DayOfYear(),
					'day' => $start->DayOfWeek(),
					'monthweek' => (int)(($start->DayOfMonth()+6)/7),
				),
				'duration' => Calendar_view_edit_simple::calculate_duration($start, $end),
			);
			if ($this->events_model->IsVip()) {
				$help_xhtml = $this->pages_model->GetPropertyWikitext('help_vip');
			} else {
				$help_xhtml = $this->pages_model->GetPropertyWikitext('help_personal');
			}
			$data = array(
				'SimpleRecur' => $rset_arr,
				'InExDates' => $inex_arr,
				'FailRedirect' => '/'.$tail,
				'Path' => $this->mPaths,
				'EventCategories' => $categories,
				'FormPrefix' => $prefix,
				'EventInfo' => $eventinfo,
				'Help' => $help_xhtml,
				'CanPublish' => $this->mMainSource->IsSupported('publish'),
				'Create' => false,
				'SuccessRedirect' => false,
			);
			if (is_array($confirm_list)) {
				$data['Confirms'] = $confirm_list;
				if (isset($_POST[$prefix.'_confirm'])) {
					$data['Confirm'] = $_POST[$prefix.'_confirm'];
				} else {
					$data['Confirm'] = NULL;
				}
			}
			foreach ($errors as $error) {
				$this->messages->AddMessage('error', $error['text']);
			}
			
			$this->SetupTabs('', $start);
			
			$this->main_frame->SetTitleParameters(array(
				'source' => $this->mSource->GetSourceName(),
				'event' => $event->Name,
			));
			
			$this->main_frame->IncludeCss('stylesheets/calendar.css');
			$this->main_frame->IncludeJs('javascript/simple_ajax.js');
			$this->main_frame->IncludeJs('javascript/calendar_edit.js');
			
			$this->main_frame->SetContent(
				new FramesView('calendar/event_edit', $data)
			);
		} else {
			// Event not in list
			$this->ErrorNotAccessible($tail);
		}
		$this->main_frame->Load();
	}
	
	/// AJAX: Validate post recurrence data asynchronously.
	function ajax_recursimplevalidate()
	{
		$this->load->library('calendar_view_edit_simple');
		$validator = new CalendarViewEditSimpleValidate();
		$validator->SetData($_GET);
		$validator->EchoXML();
	}
	
	/// [AJAX:] Set the attendence of an event.
	function src_event_attend($new_attendence = NULL)
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		// Get data from uri resolution.
		$source_id = $this->mData['SourceId'];
		$event_id = $this->mData['EventId'];
		$occurrence_id = $this->mData['OccurrenceId'];
		
		// Validate the source
		if (!$this->_GetSource()) {
			return;
		}
		
		// Validate $new_attendence
		$new_attend = NULL;
		switch ($new_attendence) {
			case 'yes':
				$new_attend = true;
				break;
			case 'no':
				$new_attend = false;
				break;
			case 'maybe':
				break;
			default:
				return show_404();
		}
		
		// Get the redirect url tail
		$args = func_get_args();
		array_shift($args);
		$tail = implode('/', $args);
		
		// Fetch the specified event
		$calendar_data = new CalendarData();
		$this->mMainSource->FetchEvent($calendar_data, $source_id, $event_id);
		$events = $calendar_data->GetEvents();
		if (array_key_exists(0, $events)) {
			$event = $events[0];
			
			// Find the occurrence
			$found_occurrence = NULL;
			foreach ($event->Occurrences as $key => $occurrence) {
				if ($occurrence->SourceOccurrenceId == $occurrence_id) {
					$found_occurrence = & $event->Occurrences[$key];
					break;
				}
			}
			if (NULL === $found_occurrence) {
				$this->messages->AddMessage('warning', 'The event occurrence with id '.$occurrence_id.' does not belong to the event with id '.$event_id.'.');
				redirect($tail);
				return;
			}
			
			if ($occurrence->UserHasPermission('set_attend')) {
				$messages = $this->mSource->AttendingOccurrence($occurrence->SourceOccurrenceId, $new_attend);
				if (!isset($messages['error']) || !empty($messages['error'])) {
					$this->messages->AddMessage('success',
						'Your attendance to &quot;'.htmlentities($occurrence->Event->Name, ENT_QUOTES, 'utf-8')."&quot; has been changed to $new_attendence.");
				}
			} else {
				$this->messages->AddMessage('error', 'You cannot set an attendance on this event.');
			}
			redirect($tail);
		} else {
			// Event not in list
			$this->ErrorNotAccessible($tail);
			$this->main_frame->Load();
		}
	}
	
	/// Perform an operation on an event or occurrence
	function src_event_op()
	{
		if (!CheckPermissions($this->mPermission)) return;
		
		// get event/occurrence id.
		$operation = $this->mData['OperationId'];
		$source_id = $this->mData['SourceId'];
		$event_id = $this->mData['EventId'];
		$occurrence_specified = array_key_exists('OccurrenceId', $this->mData);
		if ($occurrence_specified) {
			$occurrence_id = $this->mData['OccurrenceId'];
		} else {
			$occurrence_id = NULL;
		}
		
		if (!$this->_GetSource()) {
			return;
		}
		
		$calendar_data = new CalendarData();
		$this->mMainSource->FetchEvent($calendar_data, $source_id, $event_id);
		$events = $calendar_data->GetEvents();
		// Get the redirect url tail
		$args = func_get_args();
		$tail = implode('/', $args);
		
		if (!array_key_exists(0, $events)) {
			// event is not accessible
			$this->ErrorNotAccessible($tail);
		} else {
			$event = $events[0];
			if ($event->ReadOnly || 'owner' !== $event->UserStatus) {
				// event is read only to the current user.
				$this->ErrorNotModifiable($tail);
			} else {
				$op_words = array(
					'publish' => array(
						'page_code' => 'calendar_event_publish',
						'verb_past' => 'published',
						'action_func' => 'PublishOccurrence',
					),
					'cancel' => array(
						'page_code' => 'calendar_event_cancel',
						'verb_past' => 'cancelled',
						'action_func' => 'CancelOccurrence',
					),
					'delete' => array(
						'page_code' => 'calendar_event_delete',
						'verb_past' => 'deleted',
						'action_func' => 'DeleteOccurrence',
					),
					/// @todo postpone: only one at a time
					'postpone' => array(
						'page_code' => 'calendar_event_delete',
						'verb_past' => 'postponed',
						'action_func' => 'PostponeOccurrence',
					),
				);
				if (array_key_exists($operation, $op_words)) {
					$words = $op_words[$operation];

					// Get the occurrences.
					$occurrences = $event->GetOccurrencesWithUserPermission($operation);
					
					$form_selected = array();
					$op_statuses = array();
					
					foreach ($occurrences as $key => $value) {
						$form_selected[$key] = FALSE;
					}
					
					// Check post data for selection.
					if ($this->input->post('ev'.$operation.'_confirm') !== FALSE) {
						$occurrences_chosen = $this->input->post('ev'.$operation.'_occurrences');
						if (is_array($occurrences_chosen) && !empty($occurrences_chosen)) {
							// Check each chosen occurrence is in the list
							// if not show error,  keep selection of valid ones
							$fails = array();
							foreach ($occurrences_chosen as $id => $state) {
								if (array_key_exists((int)$id, $occurrences)) {
									$form_selected[(int)$id] = TRUE;
								} else {
									$fails[] = (int)$id;
								}
							}
							if (!empty($fails)) {
								$this->messages->AddMessage('error', count($fails).' of the occurrences specified '.(count($fails) === 1 ? 'is' : 'are').' not able to be '.$words['verb_past'].' (occurrence id '.implode(', ', $fails).') so no action was taken.');
							} else {
								// if all valid, perform action
								// show summary page
								$op_statuses = array();
								$fails = 0;
								$succeeds = 0;
								foreach ($occurrences_chosen as $id => $state) {
									// call the main action function
									/// @todo tackle that from hereon, the calendara data is out of date, and so the summary also
									$this->messages->AddMessage('warning','(TODO) ignore the current state here, this is the old state');
									$result = $this->mSource->$words['action_func']($occurrences[$id]);
									if ($result > 0) {
										unset($form_selected[$id]);
										$op_statuses[$id] = 1;
										++$succeeds;
									} else {
										$op_statuses[$id] = 0;
										++$fails;
									}
								}
								if (!$fails) {
									$this->messages->AddMessage("success", 'All occurrences have been successfully '.$words['verb_past'].'.');
								} elseif (!$succeeds) {
									$this->messages->AddMessage("error", 'No occurrences have been '.$words['verb_past'].'.');
								} else {
									$this->messages->AddMessage("warning", $succeeds.' occurrence'.($succeeds > 1 ? 's':'').' have been '.$words['verb_past'].' ('.$fails.' '.($fails != 1 ? 'has' : 'have').' not).');
								}
							}
						} else {
							$this->messages->AddMessage('error', 'No occurrences were chosen to '.$operation.'. Please select the occurrences you wish to '.$operation.'.');
						}
					} elseif ($this->input->post('ev'.$operation.'_cancel') !== FALSE) {
						redirect($this->mPaths->EventInfo($event).'/'.$tail);
					} else {
						// first view, so automatically select the chosen occurrence
						if ($occurrence_specified && array_key_exists($occurrence_id, $occurrences)) {
							$form_selected[$occurrence_id] = TRUE;
						}
					}
					
					
					$this->pages_model->SetPageCode($words['page_code']);
					$data = array(
						'Event' => & $event,
						'Occurrences' => $occurrences,
						'Properties' => $this->pages_model->GetPropertyArrayNew(NULL),
						'FormName' => 'ev'.$operation,
						'FormSelected' => $form_selected,
						'OpStatuses' => $op_statuses,
						'OpStates' => array(
							0 => array('label' => 'not '.$words['verb_past']),
							1 => array('label' => $words['verb_past'].' successfully'),
						),
					);
					
					$this->main_frame->SetContentSimple('calendar/event_action', $data);
					$this->main_frame->SetTitleParameters(array(
							'source' => $this->mSource->GetSourceName(),
							'event' => $event->Name,
					));

				} else {
					show_404();
				};
			}
		}
		
		$this->main_frame->Load();
	}
	
	/// Export as ical.
	function export_ical()
	{
		OutputModes('ical');
		if (!CheckPermissions($this->mPermission)) return;
		
		$this->_LoadCalendarSystem();
		$sources = $this->_SetupSources(time(), strtotime('1week'));
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_view_icalendar');
		
		$ical = new CalendarViewICalendar();
		$ical->SetCalendarData($calendar_data);
		
		$ical->Load();
	}
	
	/// Show an error, not createable message.
	protected function ErrorNotCreateable($Tail)
	{
		$this->load->library('Custom_pages');
		$page = new CustomPageView('calendar_source_no_create');
		$page->SetData('referer', site_url($Tail));
		$this->main_frame->SetContent($page);
	}
	
	/// Show an error, not accessible message.
	protected function ErrorNotAccessible($Tail)
	{
		$this->load->library('Custom_pages');
		$page = new CustomPageView('calendar_event_not_accessible');
		$page->SetData('referer', site_url($Tail));
		$this->main_frame->SetContent($page);
	}
	
	/// Show an error, not modifiable message.
	protected function ErrorNotModifiable($Tail)
	{
		$this->load->library('Custom_pages');
		$page = new CustomPageView('calendar_event_not_modifiable');
		$page->SetData('referer', site_url($Tail));
		$this->main_frame->SetContent($page);
	}
	
	/// Setup the main source.
	function _SetupMyCalendar()
	{
		if (NULL === $this->mMainSource) {
			$this->load->library('calendar_backend');
			$this->load->library('calendar_source_my_calendar');
			
			$this->mMainSource = new CalendarSourceMyCalendar();
		}
		return $this->mMainSource;
	}
	
	/// Set up the calendar to display only certain streams.
	/**
	 * @param $streams array(int => array('subscribed' => bool)) Stream information.
	 * @return The main source.
	 */
	function UseStreams($streams)
	{
		$this->_SetupMyCalendar();
		
		$this->mMainSource->DisableGroup('subscribed');
		$this->mMainSource->DisableGroup('owned');
		$this->mMainSource->DisableGroup('private');
		$this->mMainSource->EnableGroup('active');
		$this->mMainSource->EnableGroup('inactive');
		$this->mMainSource->EnableGroup('hide');
		$this->mMainSource->EnableGroup('show');
		$this->mMainSource->EnableGroup('rsvp');
		
		if (!is_array($this->mStreams)) {
			$this->mStreams = array();
		}
		foreach ($streams as $entity_id => $stream_info) {
			$this->mMainSource->GetSource(0)->IncludeStream($entity_id, TRUE);
			$this->mStreams[$entity_id] = $stream_info;
		}
	}
	
	/// Get the main calendar source.
	/**
	 * @return &CalendarSources Main calendar source.
	 */
	function GetSources()
	{
		$this->_SetupMyCalendar();
		return $this->mMainSource;
	}
	
	/// Subscribe to a given stream and bounce back.
	function subscribe_stream($organisation, $mode)
	{
		$this->load->model('calendar/events_model');
		if ($this->events_model->IsNormalUser()) {
			$this->load->model('prefs_model');
			$result = $this->prefs_model->setCalendarSubscriptionByOrgName(
				$this->events_model->GetActiveEntityId(),
				$organisation,
				true
			);
			if ($result <= 0) {
				$this->messages->AddMessage('error', 'You could not be subscribed to this organisation&apos;s event stream. You may already be subscribed or the organisation name may be wrong.');
			} else {
				$this->messages->AddMessage('success', 'You have been successfully subscribed to this organisation&apos;s event stream.');
			}
		} elseif ($this->events_model->IsVip()) {
			$this->messages->AddMessage('error', 'You cannot subscribe to an organisation&apos;s event stream as a VIP.');
		} else {
			$this->messages->AddMessage('error', 'You must be logged in to subscribe to an organisation&apos;s event stream.');
		}
		
		$tail_segments = func_get_args();
		array_shift($tail_segments);
		array_shift($tail_segments);
		redirect(implode('/', $tail_segments));
	}
	/// Unsubscribe from a given stream and bounce back.
	function unsubscribe_stream($organisation, $mode)
	{
		$this->load->model('calendar/events_model');
		if ($this->events_model->IsNormalUser()) {
			$this->load->model('prefs_model');
			$result = $this->prefs_model->setCalendarSubscriptionByOrgName(
				$this->events_model->GetActiveEntityId(),
				$organisation,
				false
			);
			if ($result <= 0) {
				$this->messages->AddMessage('error', 'You could not be unsubscribed from this organisation&apos;s event stream. You may already be unsubscribed or the organisation name may be wrong.');
			} else {
				$this->messages->AddMessage('success', 'You have been successfully unsubscribed from this organisation&apos;s event stream.');
			}
		} elseif ($this->events_model->IsVip()) {
			$this->messages->AddMessage('error', 'You cannot unsubscribe from an organisation&apos;s event stream as a VIP.');
		} else {
			$this->messages->AddMessage('error', 'You must be logged in to unsubscribe from an organisation&apos;s event stream.');
		}
		
		$tail_segments = func_get_args();
		array_shift($tail_segments);
		array_shift($tail_segments);
		redirect(implode('/', $tail_segments));
	}
	
	/// Setup main source and get specific source, erroring if problem.
	protected function _GetSource()
	{
		$this->_SetupMyCalendar();
		$source_id = (int)$this->mData['SourceId'];
		$this->mSource = $this->mMainSource->GetSource($source_id);
		if (NULL === $this->mSource) {
			//show_404();
			$this->messages->AddMessage('error','You don&apos;t have permission to access calendar source #'.$source_id.'.');
			
			header("HTTP/1.1 404 Not Found");
			$CI = & get_instance();
			$CI->main_frame->Load();
			
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	/// Get the categories.
	protected function SetupCategories()
	{
		// Get categories and reindex by name
		$categories = $this->mMainSource->GetSource(0)->GetAllCategories();
		foreach ($categories as $category) {
			$this->mCategories[$category['name']] = $category;
			$this->sFilterDef['cat'][0][] = 'no-'.$category['name'];
		}
	}
	
	/// Set up the tabs on the main_frame.
	/**
	 * @param $SelectedPage string Selected Page.
	 * @pre CheckPermissions must have already been called.
	 */
	protected function SetupTabs($SelectedPage, $Start, $Filter = NULL)
	{
		if ($this->mTabs) {
			$navbar = $this->main_frame->GetNavbar('calendar');
			if (NULL === $Filter) {
				$Filter = '/';
			} else {
				$Filter = '/'.$Filter;
			}
			if (isset($this->mPermissions['index'])) {
				$navbar->AddItem('index', 'Summary',
					site_url($this->mPaths->Index())
				);
			}
			$navbar->AddItem('day', 'Day',
				site_url($this->mPaths->Range(
					$Start->AcademicYear().'-'.$Start->AcademicTermNameUnique().'-'.$Start->AcademicWeek().'-'.$Start->Format('D'),
					$Filter
				))
			);
			$monday = $Start->BackToMonday();
			$navbar->AddItem('days', 'Week',
				site_url($this->mPaths->Range(
					$monday->AcademicYear().'-'.$monday->AcademicTermNameUnique().'-'.$monday->AcademicWeek(),
					$Filter
				))
			);
			$navbar->AddItem('weeks', 'Term',
				site_url($this->mPaths->Range(
					$monday->AcademicYear().'-'.$monday->AcademicTermNameUnique(),
					$Filter
				))
			);
			if (isset($this->mPermissions['agenda'])) {
				$navbar->AddItem('agenda', 'Agenda',
					site_url($this->mPaths->Agenda(
						$Start->Year().'-'.strtolower($Start->Format('M')).'-'.$Start->DayOfMonth(),
						$Filter
					))
				);
			}
			if (isset($this->mPermissions['subscriptions'])) {
				$navbar->AddItem('subscriptions', 'Subscriptions',
					site_url($this->mPaths->Subscriptions())
				);
			}
			$this->main_frame->SetPage($SelectedPage, 'calendar');
		}
	}
	
	/// Read the filter url and set up the sources.
	function ReadFilter(&$sources, $Filter)
	{
		// Read filter
		// eg
		// cat:no-social.att:no-no.search:all:yorker:case
		$this->load->library('filter_uri');
		$filter_def = new FilterDefinition($this->sFilterDef);
		if (NULL === $Filter) {
			$Filter = '';
		}
		
		$filter = $filter_def->ReadUri($Filter);
		
		if (FALSE === $filter) {
			$this->messages->AddMessage('error', 'The filter text in the uri was not valid');
		} else {
			$sources->DisableGroup('hide');
			$sources->EnableGroup('show');
			$sources->EnableGroup('rsvp');
			if (array_key_exists('att', $filter)) {
				foreach ($filter['att'] as $attendence) {
					switch ($attendence[0]) {
						case 'no-declined':
							$sources->DisableGroup('hide');
							break;
						case 'no-maybe':
							$sources->DisableGroup('show');
							break;
						case 'no-accepted':
							$sources->DisableGroup('rsvp');
							break;
						case 'declined':
							$sources->EnableGroup('hide');
							break;
						case 'maybe':
							$sources->EnableGroup('show');
							break;
						case 'accepted':
							$sources->EnableGroup('rsvp');
							break;
					}
				}
			}
			if (array_key_exists('cat', $filter)) {
				$cats = array();
				foreach ($filter['cat'] as $category) {
					$cats[] = $category[0];
				}
				foreach ($this->mCategories as $category) {
					$negator = 'no-'.$category['name'];
					if (in_array($negator, $cats)) {
						$sources->DisableCategory($category['name']);
					}
				}
			}
		}
	}
	
	
	protected function GenFilterUrl($Filters)
	{
		$results = array();
		foreach ($Filters as $key => $values) {
			foreach ($values as $name => $value) {
				if ($value) {
					$results[] = $key.':'.$name;
				}
			}
		}
		$date_range = (NULL === $this->mDateRange ? $this->mDefaultRange : $this->mDateRange);
		return site_url($this->mPaths->Calendar($date_range, implode('.',$results)));
	}
	
	protected function AlteredFilter($Filter, $key, $name, $value = NULL)
	{
		if (NULL === $value) {
			$Filter[$key][$name] = !$Filter[$key][$name];
		} else {
			$Filter[$key][$name] = $value;
		}
		return $Filter;
	}
	
	/// Get the filters.
	/**
	 */
	protected function GetFilters($Sources)
	{
		$Filter = array();
		$Filter['cat'] = array(
			// Filled in in after initialisation
		);
		// Fill categories
		foreach ($this->mCategories as $category) {
			$Filter['cat']['no-'.$category['name']] = !$Sources->CategoryEnabled($category['name']);
		}
		
		// First add categories to the filters
		$filters = array();
		foreach ($this->mCategories as $category) {
			$filters['cat_'.$category['name']] = array(
				'name'			=> $category['name'],
				'field'			=> 'category',
				'value'			=> $category['name'],
				'selected'		=> $Sources->CategoryEnabled($category['name']),
				'description'	=> $category['name'],
				'display'		=> 'block',
				'colour'		=> $category['heading_colour'],
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'cat', 'no-'.$category['name'])),
			);
		}
		
		if ($Sources->IsSupported('attend')) {
			$Filter['att'] = array(
				'declined' => $Sources->GroupEnabled('hide'),
				'no-accepted' => !$Sources->GroupEnabled('rsvp'),
				'no-maybe'    => !$Sources->GroupEnabled('show'),
			);
			// Then the attendance filters
			$filters['hidden'] = array(
				'name'			=> 'filter not attending',
				'field'			=> 'visibility',
				'value'			=> 'no',
				'selected'		=> $Sources->GroupEnabled('hide'),
				'description'	=> 'Include those which I have hidden',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_hidden_select.gif',
				'unselected_image'	=> '/images/prototype/calendar/filter_hidden_unselect.gif',
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'att', 'declined')),
			);
			$filters['visible'] = array(
				'name'			=> 'filter maybe attending',
				'field'			=> 'visibility',
				'value'			=> 'maybe',
				'selected'		=> $Sources->GroupEnabled('show'),
				'description'	=> 'Include those which I have not hidden',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_visible_select.png',
				'unselected_image'	=> '/images/prototype/calendar/filter_visible_unselect.png',
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'att', 'no-maybe')),
			);
			$filters['rsvp'] = array(
				'name'			=> 'filter attending',
				'field'			=> 'visibility',
				'value'			=> 'yes',
				'selected'		=> $Sources->GroupEnabled('rsvp'),
				'description'	=> 'Only those to which I\'ve RSVPd',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_rsvp_select.gif',
				'unselected_image'	=> '/images/prototype/calendar/filter_rsvp_unselect.gif',
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'att', 'no-accepted')),
			);
		};
		
		// The filters are the deliverable
		return $filters;
	}
}

?>
