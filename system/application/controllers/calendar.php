<?php

/**
 * @file calendar.php
 * @brief Calendar controller.
 */

/// Calenda actions psuedocontroller.
class CalendarActions
{
	/// Main code igniter instance.
	private $CI;
	
	function __construct()
	{
		$this->CI = & get_instance();
		
		$this->CI->load->helper('uri_tail');
	}
	
	/// Display a form for setting occurrence attendence.
	function attend($SourceId = NULL, $OccurrenceId = NULL, $Action = NULL)
	{
		if (!CheckPermissions('student')) return;
		
		if (is_numeric($SourceId)) {
			static $mapping = array(
				'accept'  => TRUE,
				'decline' => FALSE,
				'maybe'   => NULL,
			);
			if (array_key_exists($Action, $mapping)) {
				$action = $mapping[$Action];
				// Now determine what protocol to use
				$this->CI->load->library('calendar_backend');
				$this->CI->load->library('calendar_source_my_calendar');
				$my_calendar = new CalendarSourceMyCalendar();
				$messages = $my_calendar->AttendingOccurrence((int)$SourceId, $OccurrenceId, $action);
				if (!array_key_exists('error', $messages)) {
					$this->CI->messages->AddMessage('success', 'Your attending status has been set to '.$Action);
				}
				$this->CI->messages->AddMessages($messages);
				RedirectUriTail(6);
			} else {
				return show_404();
			}
		} else {
			return show_404();
		}
	}
	
	function delete($SourceId = NULL, $EventId = NULL)
	{
		if (!CheckPermissions('student')) return;
		
		if (is_numeric($SourceId)) {
			static $mapping = array(
				'accept'  => TRUE,
				'decline' => FALSE,
				'maybe'   => NULL,
			);
			// Now determine what protocol to use
			$this->CI->load->library('calendar_backend');
			$this->CI->load->library('calendar_source_my_calendar');
			$my_calendar = new CalendarSourceMyCalendar();
			$messages = $my_calendar->DeleteEvent((int)$SourceId, $EventId);
			if (!array_key_exists('error', $messages)) {
				$this->CI->messages->AddMessage('success', 'The event was successfully deleted.');
			}
			$this->CI->messages->AddMessages($messages);
			RedirectUriTail(5);
		} else {
			return show_404();
		}
	}
	
	function add($type = '')
	{
		if (!CheckPermissions('student')) return;
		
		$method = '_add_'.$type;
		if (method_exists($this, $method)) {
			$this->$method();
			RedirectUriTail(4);
		} else {
			show_404();
		}
	}
	
	function _add_todo()
	{
		$CI = & $this->CI;
		// Read the post data
		$name = $CI->input->post('todo_name');
		if (FALSE !== $name) {
			if (empty($name)) {
				$CI->messages->AddMessage('warning', 'You didn\'t specify a name for the to do list item.');
			} else {
				$CI->load->model('calendar/events_model');
				$input['recur'] = new RecurrenceSet();
				$input['todo'] = TRUE;
				$input['name'] = $name;
				
				try {
					$results = $CI->events_model->EventCreate($input);
					$CI->messages->AddMessage('success', 'To do list item added.');
				} catch (Exception $e) {
					$CI->messages->AddMessage('error', $e->getMessage());
				}
			}
		} else {
			$CI->messages->AddMessage('error', 'Invalid todo name');
		}
	}
	
	function _add_event()
	{
		$this->CI->messages->AddMessage('error', 'Not yet implemented');
	}
}

/// Controller for event manager.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @version 20/03/2007 James Hogan (jh559)
 *	- Doxygen tidy up.
 */
class Calendar extends Controller
{
	protected static $sFilterDef = array(
		// category
		'cat' => array(
			'name' => 'category',
			array(
				'no-social',
				'no-academic',
				'no-meeting',
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

	/// Default constructor.
	function __construct()
	{
		parent::Controller();
	}
	
	/// Default function.
	function index()
	{
		return $this->range();
	}
	
	function actions()
	{
		// do the magic
		$actor = new CalendarActions();
		$args = func_get_args();
		$func = array_shift($args);
		if ('_' !== substr($func,0,1) && method_exists($actor, $func)) {
			call_user_func_array(array(&$actor, $func), $args);
		} else {
			show_404();
		}
	}
	
	function range($DateRange = NULL, $Filter = NULL)
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->library('academic_calendar');
		$this->load->library('date_uri');
		$range = $this->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start	= $range['start'];
			$end	= $range['end'];
		} else {
			$start	= $now->Midnight();
			$end	= $start->Adjust('1week');
		}
		
		$days = Academic_time::DaysBetweenTimestamps(
			$start->Timestamp(),
			$end->Timestamp()
		);
		
		if ($days > 7) {
			$this->_ShowWeeks($DateRange, $Filter, $range['format']);
		} elseif ($days > 1) {
			$this->_ShowDays($DateRange, $Filter, $range['format']);
		} else {
			$this->_ShowDay($DateRange, $Filter, $range['format']);
		}
		
		$this->main_frame->Load();
	}
	
	function agenda($DateRange = NULL, $Filter = NULL)
	{
		if (!CheckPermissions('public')) return;
		$this->_ShowAgenda($DateRange, $Filter);
		$this->main_frame->Load();
	}
	
	function ical()
	{
		if (!CheckPermissions('public')) return;
		
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
	
	/// Load the calendar system libraries.
	function _LoadCalendarSystem()
	{
		// Load libraries
		$this->load->library('academic_calendar');
		$this->load->library('calendar_backend');
		$this->load->library('calendar_frontend');
	}
	
	/// Setup the user's event sources.
	function _SetupSources()
	{
		$this->load->library('calendar_source_my_calendar');
		return new CalendarSourceMyCalendar();
	}
	
	function _ShowDay($DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$this->_LoadCalendarSystem();
		$range = $this->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start = $range['start'];
		} else {
			$start = $now->Midnight();
		}
		$end = $start->Adjust('1day');
		
		$sources = $this->_SetupSources();
		$sources->SetRange($start->Timestamp(), $end->Timestamp());
		$sources->EnableGroup('todo');
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_view_days');
		$this->load->library('calendar_view_todo_list');
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($calendar_data);
		$days->SetRangeUrl(
			'calendar/range/',
			$Format,
			NULL !== $Filter ? '/'.$Filter : ''
		);
		$days->SetStartEnd($start->Timestamp(), $end->Timestamp());
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
			'RangeDescription' => $range['description'],
		);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		
		$this->_SetupTabs('day', $start, $Filter);
	}
	
	function _ShowAgenda($DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$this->_LoadCalendarSystem();
		
		$sources = $this->_SetupSources();
		$sources->SetRange(time(), strtotime('1month'));
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_view_agenda');
		
		$agenda = new CalendarViewAgenda();
		$agenda->SetCalendarData($calendar_data);
		
		$data = array(
			'Filters'	=> $this->_GetFilters(),
			'ViewMode'	=> $agenda,
		);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		
		$this->_SetupTabs('agenda', new Academic_time(time()), $Filter);
	}
	
	function _ShowDays($DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$this->_LoadCalendarSystem();
		// Read date range
		$this->load->library('date_uri');
		$range = $this->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start = $range['start'];
			$end = $range['end'];
		} else {
			$start = $now->Midnight();
			$end = $start->Adjust('7day');
		}
		
		// Read filter
		// eg
		// cat:no-social.att:no-no.search:all:yorker:case
		$this->load->library('filter_uri');
		$filter_def = new FilterDefinition(self::$sFilterDef);
		if (NULL === $Filter) {
			// simulate "att:no-declined"
			$filter = array(
			);
		} else {
			$filter = $filter_def->ReadUri($Filter);
		}
		
		$sources = $this->_SetupSources();
		$sources->SetRange($start->Timestamp(), $end->Timestamp());
		
		if (FALSE === $filter) {
			$this->messages->AddMessage('error', 'The filter text in the uri was not valid');
		} else {
			$this->load->model('calendar/events_model');
			$sources->EnableGroup('hide');
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
		}
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_view_days');
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($calendar_data);
		$days->SetStartEnd($start->Timestamp(), $end->Timestamp());
		$days->SetRangeUrl(
			'calendar/range/',
			$Format,
			NULL !== $Filter ? '/'.$Filter : ''
		);
		
		$data = array(
			'Filters'	=> $this->_GetFilters(),
			'ViewMode'	=> $days,
			'RangeDescription' => $range['description'],
		);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		
		$this->_SetupTabs('days', $start, $Filter);
	}
	
	function _ShowWeeks($DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$this->_LoadCalendarSystem();
		// Read date range
		$this->load->library('date_uri');
		$range = $this->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start = $range['start'];
			$end = $range['end'];
		} else {
			$start = $now->BackToMonday();
			$end = $start->Adjust('4weeks');
		}
		
		// Read filter
		// eg
		// cat:no-social.att:no-no.search:all:yorker:case
		$this->load->library('filter_uri');
		$filter_def = new FilterDefinition(self::$sFilterDef);
		if (NULL === $Filter) {
			// simulate "att:no-declined"
			$filter = array(
			);
		} else {
			$filter = $filter_def->ReadUri($Filter);
		}
		
		$sources = $this->_SetupSources();
		$sources->SetRange($start->Timestamp(), $end->Timestamp());
		
		if (FALSE === $filter) {
			$this->messages->AddMessage('error', 'The filter text in the uri was not valid');
		} else {
			$this->load->model('calendar/events_model');
			$sources->EnableGroup('hide');
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
		}
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$this->load->library('calendar_view_weeks');
		
		$weeks = new CalendarViewWeeks();
		$weeks->SetCalendarData($calendar_data);
		$weeks->SetStartEnd($start->Timestamp(), $end->Timestamp());
		$weeks->SetRangeUrl(
			'calendar/range/',
			$Format,
			NULL !== $Filter ? '/'.$Filter : ''
		);
		
		$data = array(
			'Filters'	=> $this->_GetFilters(),
			'ViewMode'	=> $weeks,
			'RangeDescription' => $range['description'],
		);
		
		$this->main_frame->SetContentSimple('calendar/my_calendar', $data);
		
		$this->_SetupTabs('weeks', $start, $Filter);
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
	
	function add()
	{
		if (!CheckPermissions('student')) return;
		
		$this->load->model('calendar/events_model');
		$event_categories = $this->events_model->CategoriesGet();
		
		/// @todo make standard functions and views for recurrence interface
		$input = array();
		$input['name'] = $this->input->post('a_summary');
		if (FALSE !== $input['name']) {
			$failed_validation = FALSE;
			$input['startdate']      = $this->input->post('a_startdate');
			$input['starttime']      = $this->input->post('a_starttime');
			$input['enddate']        = $this->input->post('a_enddate');
			$input['endtime']        = $this->input->post('a_endtime');
			$input['time_associated'] = FALSE === $this->input->post('a_allday');
			$input['location']       = $this->input->post('a_location');
			$input['category']       = $this->input->post('a_category');
			if (!is_numeric($input['category']) ||
				!array_key_exists($input['category'] = (int)$input['category'], $event_categories))
			{
				$failed_validation = TRUE;
				$this->messages->AddMessage('error', 'You did not specify a valid event category');
			}
			$input['description']    = $this->input->post('a_description');
			$input['frequency']      = $this->input->post('a_frequency');
			// Figure out recurrence
			if ('none' !== $input['frequency']) {
				$input['interval']       = $this->input->post('a_interval');
				if (!is_numeric($input['interval']) || $input['interval'] < 1) {
					$failed_validation = TRUE;
					$this->messages->AddMessage('error', 'You specified an invalid interval');
				} else {
					$input['interval'] = (int)$input['interval'];
				}
				if ('daily' === $input['frequency']) {
				} elseif ('weekly' === $input['frequency']) {
					$input['onday']          = $this->input->post('a_onday');
				} elseif ('yearly' === $input['frequency']) {
				}
			}
			foreach (array('start','end') as $startend) {
				// Validate dates
				$field = $startend.'date';
				if (preg_match('/^[ \t]*(\d{1,2})\/(\d{1,2})\/(\d{4})[ \t]*$/', $input[$field], $matches)) {
					if (checkdate((int)$matches[2], (int)$matches[1], (int)$matches[3])) {
						$input[$field] = $matches;
					} else {
						$this->messages->AddMessage('error',
							'You specified a '.$startend.' date that does not exist: '.
							$matches[1].'/'.$matches[2].'/'.$matches[3]
						);
						$failed_validation = TRUE;
					}
				} else {
					$this->messages->AddMessage('error',
						'You specified an invalid '.$startend.' date: "'.$input[$field].'"'
					);
					$failed_validation = TRUE;
				}
				// Validate times
				$field = $startend.'time';
				if ($input['time_associated']) {
					if (preg_match('/^[ \t]*([012]?\d):([0-5]?\d)(:([0-5]?\d))?[ \t]*$/', $input[$field], $matches)) {
						$hour = (int)$matches[1];
						$minute = (int)$matches[2];
						if (!empty($matches[4])) {
							$second = (int)$matches[4];
						} else {
							$second = 0;
						}
						if ($hour < 24 && $minute < 60 && $second < 60) {
							$input[$field] = array($hour, $minute, $second);
						} else {
							$this->messages->AddMessage('error',
								'You specified a '.$startend.' time that does not exist: '.
								$hour.':'.$minute.':'.$second
							);
							$failed_validation = TRUE;
						}
					} else {
						$this->messages->AddMessage('error',
							'You specified an invalid '.$startend.' time: "'.$input[$field].'"'
						);
						$failed_validation = TRUE;
					}
				} else {
					$input[$field] = array(0,0,0);
				}
			}
			
			if (!$failed_validation) {
				$start = mktime(
					$input['starttime'][0],
					$input['starttime'][1],
					$input['starttime'][2],
					$input['startdate'][2],
					$input['startdate'][1],
					$input['startdate'][3]
				);
				$end = mktime(
					$input['endtime'][0],
					$input['endtime'][1],
					$input['endtime'][2],
					$input['enddate'][2],
					$input['enddate'][1],
					$input['enddate'][3]
				);
				if ($end < $start) {
					$this->messages->AddMessage('error', 'You specified the end time before the start time.');
					$failed_validation = TRUE;
				} else {
					if (!$input['time_associated']) {
						$end = strtotime('1day', $end);
					}
					$input['recur'] = new RecurrenceSet();
					$input['recur']->SetStartEnd($start, $end);
					
					// daily
					if ('daily' === $input['frequency']) {
						$rrule = new CalendarRecurRule();
						$rrule->SetFrequency('daily');
						$rrule->SetInterval($input['interval']);
						$input['recur']->AddRRules($rrule);
						
					} elseif ('weekly' === $input['frequency']) {
						$rrule = new CalendarRecurRule();
						$rrule->SetFrequency('weekly');
						$rrule->SetInterval($input['interval']);
						static $onday_translate = array(
							'mon' => 'MO',
							'tue' => 'TU',
							'wed' => 'WE',
							'thu' => 'TH',
							'fri' => 'FR',
							'sat' => 'SA',
							'sun' => 'SU',
						);
						foreach ($input['onday'] as $day => $on) {
							$short_day = strtoupper(substr($day,0,2));
							if (array_key_exists($short_day, CalendarRecurRule::$sWeekdays)) {
								$rrule->SetByDay(CalendarRecurRule::$sWeekdays[$short_day]);
							}
						}
						$input['recur']->AddRRules($rrule);
						
					} elseif ('yearly' === $input['frequency']) {
						$rrule = new CalendarRecurRule();
						$rrule->SetFrequency('yearly');
						$rrule->SetInterval($input['interval']);
						$input['recur']->AddRRules($rrule);
						
					}
					
					try {
						$results = $this->events_model->EventCreate($input);
						$this->messages->AddMessage('success', 'Event created successfully.');
					} catch (Exception $e) {
						$this->messages->AddMessage('error', $e->getMessage());
					}
				}
			}
		}
		
		$data = array(
			'EventCategories' => $event_categories,
			'AddForm' => array(
				'target' => $this->uri->uri_string(),
				'default_summary' => '',
				'default_startdate' => date('d/m/Y', strtotime('+3hour')),
				'default_starttime' => date('H:m',   strtotime('+3hour')),
				'default_enddate'   => date('d/m/Y', strtotime('+4hour')),
				'default_endtime'   => date('H:m',   strtotime('+4hour')),
				'default_allday'    => FALSE,
				'default_eventcategory' => -1,
			),
		);
		
		$this->main_frame->SetContentSimple('calendar/simpleadd',$data);
		
		$this->main_frame->Load();
	}
	
	/// Set up the tabs on the main_frame.
	/**
	 * @param $SelectedPage string Selected Page.
	 * @pre CheckPermissions must have already been called.
	 */
	protected function _SetupTabs($SelectedPage, $Start, $Filter = NULL)
	{
		$navbar = $this->main_frame->GetNavbar();
		if (NULL === $Filter) {
			$Filter = '/';
		} else {
			$Filter = '/'.$Filter;
		}
		$now = new Academic_time(time());
		$navbar->AddItem('day', 'Today',	site_url(
			'calendar/range/today'.
			$Filter
		));
		$monday = $Start->BackToMonday();
		$navbar->AddItem('days', 'Week',	site_url(
			'calendar/range/'.
			$monday->AcademicYear().'-'.$monday->AcademicTermNameUnique().'-'.$monday->AcademicWeek().
			$Filter
		));
		$navbar->AddItem('weeks', 'Term',	site_url(
			'calendar/range/'.
			$monday->AcademicYear().'-'.$monday->AcademicTermNameUnique().
			$Filter
		));
		$navbar->AddItem('agenda', 'Agenda',site_url(
			'calendar/agenda/'.
			$Start->Year().'-'.strtolower($Start->Format('M')).'-'.$Start->DayOfMonth().
			$Filter
		));
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
					event_occurrences.event_occurrence_last_modified');
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
