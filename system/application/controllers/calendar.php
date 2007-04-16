<?php

/**
 * @file calendar.php
 * @brief Calendar controller.
 */

/// Controller for event manager.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @author David Harker (dhh500@york.ac.uk)
 *
 * @version 20/03/2007 James Hogan (jh559)
 *	- Doxygen tidy up.
 */
class Calendar extends Controller {

	/// Default constructor.
	function __construct()
	{
		parent::Controller();
	}
	
	/// Default function.
	function index()
	{
		$this->days();
	}
	
	function add()
	{
		if (!CheckPermissions('student')) return;
		
		$data = array();
		
		$this->main_frame->SetContentSimple('calendar/simpleadd',$data);
		
		$this->main_frame->Load();
	}
	
	function day($DateRange = '')
	{
		return $this->days($DateRange);
		if (!CheckPermissions('public')) return;
		$this->_ShowDay();
		$this->main_frame->Load();
	}
	
	function agenda()
	{
		if (!CheckPermissions('public')) return;
		$this->_ShowAgenda();
		$this->main_frame->Load();
	}
	
	function days($DateRange = '')
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->library('academic_calendar');
		$this->load->library('date_uri');
		
		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
		
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/calendar.js" type="text/javascript"></script>
			<link href="/stylesheets/calendar.css" rel="stylesheet" type="text/css" />
			
EXTRAHEAD;
		$this->main_frame->SetExtraHead($extra_head);
		
		if (!empty($DateRange)) {
			// $DateRange Not empty
			
			// Read the date, only allowing a single date (no range data)
			$uri_result = $this->date_uri->ReadUri($DateRange, FALSE);
			if ($uri_result['valid']) {
				// $DateRange Valid
				$start_time = $uri_result['start'];
				$start_time = $start_time->BackToMonday();
				$format = $uri_result['format']; // Use the format in all links
				$days = 7; // force 7 days until view can handle different values.
				
				$this->_ShowDays($start_time, $days, $format);
				$this->main_frame->Load();
				return;
				
			} else {
				// $DateRange Invalid
				$this->main_frame->AddMessage('error','Unrecognised date: "'.$DateRange.'"');
			}
		}
		
		// Default to this week
		$format = 'ac';
		$base_time = new Academic_time(time());
		
		$monday = $base_time->BackToMonday();
		
		$this->_ShowDays($monday, 7, $format);
		$this->main_frame->Load();
	}
	
	function weeks()
	{
		if (!CheckPermissions('public')) return;
		$this->_ShowWeeks();
		$this->main_frame->Load();
	}
	
	/// Load the calendar system libraries.
	function _LoadCalendarSystem()
	{
		// Load libraries
		$this->load->library('academic_calendar');
		$this->load->library('calendar_backend');
		$this->load->library('calendar_frontend');
	}
	
	/// Setup the user's event sources.
	/**
	 * @param $StartTime timestamp Start time of events.
	 * @param $EndTime   timestamp End time of events.
	 */
	function _SetupSources($StartTime, $EndTime)
	{
		// Load calendar source libraries
		$this->load->library('calendar_source_yorker');
		
		// Set up data sources
		$sources = array();
		$sources[] = $source_yorker = new CalendarSourceYorker();
		
		// Set yorker date range to something relevent
		$source_yorker->SetRange($StartTime, $EndTime);
		
		return $sources;
	}
	
	function _FetchEventsFromSources(&$CalendarData, $Sources)
	{
		// Accumulate data from sources in $CalendarData
		foreach ($Sources as $source) {
			try {
				$source->FetchEvents($CalendarData);
			} catch (Exception $e) {
				$this->messages->AddMessage('error', 'calendar data source failed: '.$e->getMessage());
			}
		}
	}
	
	function _ShowDay()
	{
		$this->_LoadCalendarSystem();
		$sources = $this->_SetupSources(strtotime('month'), strtotime('1month'));
		$calendar_data = new CalendarData();
		
		$this->_FetchEventsFromSources($calendar_data, $sources);
		
		// Display data
		$this->load->library('calendar_view_days');
		$this->load->library('calendar_view_todo_list');
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($calendar_data);
		$todo = new CalendarViewTodoList();
		$todo->SetCalendarData($calendar_data);
		
		$view_mode_data = array(
			'DateDescription' => 'Today probably!',
			'DaysView'        => &$days,
			'TodoView'        => &$todo,
		);
		$view_mode = new FramesFrame('calendar/day', $view_mode_data);
		
		$data = array(
			'Filters'	=> $this->_GetFilters(),
			'ViewMode'	=> $view_mode,
		);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		
		$this->_SetupTabs('day');
	}
	
	function _ShowAgenda()
	{
		$this->_LoadCalendarSystem();
		$sources = $this->_SetupSources(strtotime('-2month'), strtotime('1month'));
		$calendar_data = new CalendarData();
		
		$this->_FetchEventsFromSources($calendar_data, $sources);
		
		// Display data
		$this->load->library('calendar_view_agenda');
		
		$agenda = new CalendarViewAgenda();
		$agenda->SetCalendarData($calendar_data);
		
		$data = array(
			'Filters'	=> $this->_GetFilters(),
			'ViewMode'	=> $agenda,
		);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		
		$this->_SetupTabs('agenda');
	}
	
	function _ShowDays($start, $num_days, $format)
	{
		$this->_LoadCalendarSystem();
		$sources = $this->_SetupSources($start->Timestamp(), $start->Adjust($num_days.'days')->Timestamp());
		$calendar_data = new CalendarData();
		
		$this->_FetchEventsFromSources($calendar_data, $sources);
		
		// Display data
		$this->load->library('calendar_view_days');
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($calendar_data);
		$days->SetRange($start, $num_days);
		
		$data = array(
			'Filters'	=> $this->_GetFilters(),
			'ViewMode'	=> $days,
		);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		
		$this->_SetupTabs('days');
	}
	
	function _ShowWeeks()
	{
		$this->_LoadCalendarSystem();
		$sources = $this->_SetupSources(strtotime('-2month'), strtotime('1month'));
		$calendar_data = new CalendarData();
		
		$this->_FetchEventsFromSources($calendar_data, $sources);
		
		// Display data
		$this->load->library('calendar_view_weeks');
		
		$weeks = new CalendarViewWeeks();
		$weeks->SetCalendarData($calendar_data);
		
		$data = array(
			'Filters'	=> $this->_GetFilters(),
			'ViewMode'	=> $weeks,
		);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		
		$this->_SetupTabs('weeks');
	}
	
	/// Get the filters.
	/**
	 */
	protected function _GetFilters()
	{
		return array(
			'id' => array(
				'name'			=> 'social',
				'field'			=> 'category',
				'value'			=> 'social',
				'selected'		=> TRUE,
				'description'	=> 'Social',
				'display'		=> 'block',
				'colour'		=> 'FFFF00',
			),
			'academic' => array(
				'name'			=> 'academic',
				'field'			=> 'category',
				'value'			=> 'academic',
				'selected'		=> TRUE,
				'description'	=> 'Academic',
				'display'		=> 'block',
				'colour'		=> '00FF00',
			),
			'meeting' => array(
				'name'			=> 'meeting',
				'field'			=> 'category',
				'value'			=> 'meeting',
				'selected'		=> TRUE,
				'description'	=> 'Meetings',
				'display'		=> 'block',
				'colour'		=> 'FF0000',
			),
			
			'rsvp' => array(
				'name'			=> 'rsvp',
				'field'			=> 'visibility',
				'value'			=> 'rsvp',
				'selected'		=> TRUE,
				'description'	=> 'Only those to which I\'ve RSVPd',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_rsvp_select.gif',
				'unselected_image'	=> '/images/prototype/calendar/filter_rsvp_unselect.gif',
			),
			'visible' => array(
				'name'			=> 'visible',
				'field'			=> 'visibility',
				'value'			=> 'visible',
				'selected'		=> TRUE,
				'description'	=> 'Include those which I\'ve hidden',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_visible_select.gif',
				'unselected_image'	=> '/images/prototype/calendar/filter_visible_unselect.gif',
			),
			'hidden' => array(
				'name'			=> 'hidden',
				'field'			=> 'visibility',
				'value'			=> 'hidden',
				'selected'		=> FALSE,
				'description'	=> 'Include those which I\'ve hidden',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_hidden_select.gif',
				'unselected_image'	=> '/images/prototype/calendar/filter_hidden_unselect.gif',
			),
		);
	}
	
	/// Set up the tabs on the main_frame.
	/**
	 * @param $SelectedPage string Selected Page.
	 * @pre CheckPermissions must have already been called.
	 */
	protected function _SetupTabs($SelectedPage)
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('day', 'Today',
				site_url('calendar/day'));
		$navbar->AddItem('days', 'Week',
				site_url('calendar/days'));
		$navbar->AddItem('weeks', 'Term',
				site_url('calendar/weeks'));
		$navbar->AddItem('agenda', 'Agenda',
				site_url('calendar/agenda'));
		$this->main_frame->SetPage($SelectedPage);
	}
	
	
	
	
	
	function oldweek($DateRange = '')
	{
		if (!CheckPermissions('public')) return;
		
		// Load libraries
		$this->load->library('event_manager');      // Processing events
		$this->load->library('academic_calendar');  // Using academic calendar
		$this->load->library('date_uri');           // Nice date uri segments
		$this->load->library('view_calendar_days'); // Days calendar view
		
		
		// Show change events
		$this->load->model('calendar/events_model');
		$ChangedEvents = new EventOccurrenceFilter();
		$ChangedEvents->DisableFilter('private');
		//$ChangedEvents->DisableFilter('active');
		$ChangedEvents->DisableFilter('show');
		$ChangedEvents->SetSpecialCondition(
			'event_occurrence_users.event_occurrence_user_timestamp <
					event_occurrences.event_occurrence_timestamp');
		$notices = $ChangedEvents->GenerateOccurrences(array(
				'state'			=> $ChangedEvents->ExpressionPublicState(),
				'name'			=> 'events.event_name',
				'organisation'	=> 'organisations.organisation_name',
				'start'			=> 'UNIX_TIMESTAMP(event_occurrences.event_occurrence_start_time)',
			));
		foreach ($notices as $notice) {
			$date = new Academic_time($notice['start']);
			$start = $date->Format('l') . ' of week ' . $date->AcademicWeek() .
				' of the ' . $date->AcademicTermName() . ' ' . $date->AcademicTermTypeName();
			
			$message = 'The event ' . $notice['name'] . ' from ' .
				$notice['organisation'] . ' (' . $start . ') has been ' . $notice['state'];
			$this->messages->AddMessage('information', $message, FALSE);
		}
		
		
		$this->pages_model->SetPageCode('calendar_personal');
		if (!empty($DateRange)) {
			// $DateRange Not empty
			
			// Read the date, only allowing a single date (no range data)
			$uri_result = $this->date_uri->ReadUri($DateRange, FALSE);
			if ($uri_result['valid']) {
				// $DateRange Valid
				$start_time = $uri_result['start'];
				$start_time = $start_time->BackToMonday();
				$format = $uri_result['format']; // Use the format in all links
				$days = 7; // force 7 days until view can handle different values.
				
				$this->_ShowCalendar(
						$start_time, $days,
						'/calendar/week/', $format
					);
				return;
				
			} else {
				// $DateRange Invalid
				$this->main_frame->AddMessage('error','Unrecognised date: "'.$DateRange.'"');
			}
		}
		
		// Default to this week
		$format = 'ac';
		$base_time = new Academic_time(time());
		
		$monday = $base_time->BackToMonday();
	
		$this->_ShowCalendar(
				$monday, 7,
				'/calendar/week/', $format
			);
	}
	
	/// Show the calendar between certain Academic_times.
	/**
	 * @param $StartTime Academic_time Start date.
	 * @param $Days integer Number of days to display.
	 * @param $UriBase string The base of the uri on which to build links,
	 *	e.g. '/calendar/week/'
	 * @param $UriFormat string Uri date format identifier as used in Date_uri.
	 */
	function _ShowCalendar($StartTime, $Days, $UriBase, $UriFormat)
	{
		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
		
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/calendar.js" type="text/javascript"></script>
			<link href="/stylesheets/calendar.css" rel="stylesheet" type="text/css" />
			
EXTRAHEAD;
		
		// Set up the days view
		$view_calendar_days = new ViewCalendarDays();
		$view_calendar_days->SetUriBase($UriBase);
		$view_calendar_days->SetUriFormat($UriFormat);
		$view_calendar_days->SetRange($StartTime, $Days);
		// Get the data from the db, then we're ready to load
		$view_calendar_days->Retrieve();
		
		// Set up the public frame to use the messages frame
		$this->main_frame->SetExtraHead($extra_head);
		$this->main_frame->SetContent($view_calendar_days);
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
}
?>
