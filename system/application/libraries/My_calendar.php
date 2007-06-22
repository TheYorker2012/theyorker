<?php

/**
 * @file libraries/My_calendar.php
 * @brief Functions for using a calendar interface.
 */

/// Calendar interface library class.
class My_calendar
{

	protected $sFilterDef = array(
		// category
		'cat' => array(
			'name' => 'category',
			array(
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
	protected $mCategories;
	
	protected $mAgenda = FALSE;
	protected $mRangeUrl = '';
	protected $mTabs = TRUE;
	protected $mDateRange = 'today';
	protected $mDefaultRange;
	protected $mReadOnly = TRUE;
	/// array[string => string]
	protected $mPaths = array(
		'add' => '/dead',
		'edit' => '/dead',
	);
	
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->library('academic_calendar');
		$CI->load->library('calendar_backend');
		$CI->load->library('calendar_frontend');
		$CI->load->library('date_uri');
		
		$this->mDefaultRange = 'today:1week';
		
		$CI->load->model('calendar/events_model');
		$this->mReadOnly = $CI->events_model->IsReadOnly();
		
		// Get categories and reindex by name
		$categories = $CI->events_model->CategoriesGet();
		foreach ($categories as $category) {
			$this->mCategories[$category['name']] = $category;
			$this->sFilterDef['cat'][0][] = 'no-'.$category['name'];
		}
	}
	
	function SetAgenda($Prefix)
	{
		$this->mAgenda = $Prefix;
	}
	
	function SetTabs($Tabs)
	{
		$this->mTabs = FALSE;
	}
	
	/// Set the range url information.
	/**
	 * @param $Prefix string
	 * @param $Format string
	 * @param $Postfix string
	 */
	function SetUrlPrefix($Prefix)
	{
		$this->mRangeUrl = $Prefix;
	}
	
	function SetDefaultRange($range)
	{
		$this->mDefaultRange = $range;
	}
	
	function SetPath($Name, $Value)
	{
		assert('array_key_exists($Name, $this->mPaths)');
		$this->mPaths[$Name] = $Value;
	}
	
	/// Show the calendar interface.
	/**
	 * @return FramesView View class.
	 */
	function GetMyCalendar(&$Sources, $DateRange = NULL, $Filter = NULL)
	{
		$CI = & get_instance();
		$range = $CI->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if (!$range['valid']) {
			$DateRange = $this->mDefaultRange;
			$range = $CI->date_uri->ReadUri($DateRange, TRUE);
			assert($range['valid']);
		}
		$start	= $range['start'];
		$end	= $range['end'];
		
		$days = Academic_time::DaysBetweenTimestamps(
			$start->Timestamp(),
			$end->Timestamp()
		);
		
		$this->mDateRange = $DateRange;
		
		$CI->main_frame->SetTitleParameters(array(
			'range' => $range['description'],
		));
		
		if ($days > 7) {
			return $this->GetWeeks($Sources, $DateRange, $Filter, $range['format']);
		} elseif ($days > 1) {
			return $this->GetDays($Sources, $DateRange, $Filter, $range['format']);
		} else {
			return $this->GetDay($Sources, $DateRange, $Filter, $range['format']);
		}
	}
	
	function ReadFilter(&$sources, $Filter)
	{
		$CI = & get_instance();
		
		// Read filter
		// eg
		// cat:no-social.att:no-no.search:all:yorker:case
		$CI->load->library('filter_uri');
		$filter_def = new FilterDefinition($this->sFilterDef);
		if (NULL === $Filter) {
			$Filter = '';
		}
		
		$filter = $filter_def->ReadUri($Filter);
		
		if (FALSE === $filter) {
			$CI->messages->AddMessage('error', 'The filter text in the uri was not valid');
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
	
	function GetDay(&$sources, $DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$CI = & get_instance();
		$range = $CI->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start = $range['start'];
		} else {
			$start = $now->Midnight();
		}
		$end = $start->Adjust('1day');
		
		$sources->SetRange($start->Timestamp(), $end->Timestamp());
		$sources->SetTodoRange(time(), time());
		$this->ReadFilter($sources, $Filter);
		$sources->EnableGroup('todo');
		
		$calendar_data = new CalendarData();
		
		$CI->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$CI->load->library('calendar_view_days');
		$CI->load->library('calendar_view_todo_list');
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($calendar_data);
		$days->SetRangeUrl(
			$this->mRangeUrl,
			$Format,
			NULL !== $Filter ? '/'.$Filter : ''
		);
		$days->SetStartEnd($start->Timestamp(), $end->Timestamp());
		$days->SetCategories($this->mCategories);
		
		$todo = new CalendarViewTodoList();
		$todo->SetCalendarData($calendar_data);
		$todo->SetCategories($this->mCategories);
		
		$view_mode_data = array(
			'DateDescription' => 'Today probably!',
			'DaysView'        => &$days,
			'TodoView'        => &$todo,
		);
		$view_mode = new FramesFrame('calendar/day', $view_mode_data);
		
		$data = array(
			'Filters'	=> $this->GetFilters($sources),
			'ViewMode'	=> $view_mode,
			'RangeDescription' => $range['description'],
			'ReadOnly' => $this->mReadOnly,
			'Path' => $this->mPaths,
		);
		
		$this->SetupTabs('day', $start, $Filter);
		
		return new FramesView('calendar/my_calendar', $data);
	}
	
	function GetDays(&$sources, $DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$CI = & get_instance();
		// Read date range
		$range = $CI->date_uri->ReadUri($DateRange, TRUE);
		$now = new Academic_time(time());
		if ($range['valid']) {
			$start = $range['start'];
			$end = $range['end'];
		} else {
			$start = $now->Midnight();
			$end = $start->Adjust('7day');
		}
		
		$sources->SetRange($start->Timestamp(), $end->Timestamp());
		$this->ReadFilter($sources, $Filter);
		
		$calendar_data = new CalendarData();
		
		$CI->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$CI->load->library('calendar_view_days');
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($calendar_data);
		$days->SetStartEnd($start->Timestamp(), $end->Timestamp());
		$days->SetRangeUrl(
			$this->mRangeUrl,
			$Format,
			NULL !== $Filter ? '/'.$Filter : ''
		);
		$days->SetCategories($this->mCategories);
		
		$data = array(
			'Filters'	=> $this->GetFilters($sources),
			'ViewMode'	=> $days,
			'RangeDescription' => $range['description'],
			'ReadOnly' => $this->mReadOnly,
			'Path' => $this->mPaths,
		);
		
		$this->SetupTabs('days', $start, $Filter);
		
		return new FramesView('calendar/my_calendar', $data);
	}
	
	function GetWeeks(&$sources, $DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$CI = & get_instance();
		// Read date range
		$range = $CI->date_uri->ReadUri($DateRange, TRUE);
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
		
		$calendar_data = new CalendarData();
		
		$CI->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$CI->load->library('calendar_view_weeks');
		
		$weeks = new CalendarViewWeeks();
		$weeks->SetCalendarData($calendar_data);
		$weeks->SetStartEnd($start->Timestamp(), $end->Timestamp());
		$weeks->SetRangeUrl(
			$this->mRangeUrl,
			$Format,
			NULL !== $Filter ? '/'.$Filter : ''
		);
		$weeks->SetCategories($this->mCategories);
		
		$data = array(
			'Filters'	=> $this->GetFilters($sources),
			'ViewMode'	=> $weeks,
			'RangeDescription' => $range['description'],
			'ReadOnly' => $this->mReadOnly,
			'Path' => $this->mPaths,
		);
		
		$this->SetupTabs('weeks', $start, $Filter);
		
		return new FramesView('calendar/my_calendar', $data);
	}
	
	
	function GetAgenda(&$sources, $DateRange = NULL, $Filter = NULL, $Format = 'ac:re')
	{
		$this->mDateRange = $DateRange;
		
		$CI = & get_instance();
		
		$sources->SetRange(time(), strtotime('2month'));
		$this->ReadFilter($sources, $Filter);
		
		$calendar_data = new CalendarData();
		
		$CI->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$CI->load->library('calendar_frontend');
		$CI->load->library('calendar_view_agenda');
		
		$agenda = new CalendarViewAgenda();
		$agenda->SetCalendarData($calendar_data);
		$agenda->SetCategories($this->mCategories);
		
		$data = array(
			'Filters'	=> $this->GetFilters($sources),
			'ViewMode'	=> $agenda,
		);
		
		$this->SetupTabs('agenda', new Academic_time(time()), $Filter);
		
		return new FramesView('calendar/my_calendar', $data);
	}
	
	/// Display event information.
	function GetEvent($SourceId = NULL, $EventId = NULL, $OccurrenceId = NULL)
	{
		if (is_numeric($SourceId)) {
			$SourceId = (int)$SourceId;
			
			$CI = & get_instance();
			
			// Get the specific event
			$CI->load->helper('uri_tail');
			$CI->load->library('calendar_backend');
			$CI->load->library('calendar_source_my_calendar');
			$sources = new CalendarSourceMyCalendar();
			$calendar_data = new CalendarData();
			$sources->FetchEvent($calendar_data, $SourceId, $EventId);
			$events = $calendar_data->GetEvents();
			if (array_key_exists(0, $events)) {
				$event = $events[0];
				// Find the occurrence
				$found_occurrence = NULL;
				foreach ($event->Occurrences as $key => $occurrence) {
					if ($occurrence->SourceOccurrenceId == $OccurrenceId) {
						$found_occurrence = & $event->Occurrences[$key];
						break;
					}
				}
				if (NULL === $found_occurrence) {
					return show_404();
				}
				if ($CI->input->post('evview_return')) {
					// REDIRECT
					$CI->load->helper('uri_tail');
					RedirectUriTail(5);
					
				} else {
					$data = array(
						'Event' => &$event,
						'Occurrence' => &$found_occurrence,
						'ReadOnly' => $this->mReadOnly,
						'Attendees' => $sources->GetOccurrenceAttendanceList($SourceId, $OccurrenceId),
						'FailRedirect' => '/'.GetUriTail(5),
					);
					
					$CI->main_frame->SetTitleParameters(array(
						'event' => $event->Name,
					));
		
					$this->SetupTabs('', $found_occurrence->StartTime);
					
					return new FramesView('calendar/event', $data);
				}
			} else {
				$CI->messages->AddMessage('error', 'The event coud not be found');
				RedirectUriTail(5);
			}
		} else {
			show_404();
		}
	}
	
	/// Display and handle an event adder form.
	function GetAdder()
	{
		$CI = & get_instance();
		
		/// @todo make standard functions and views for recurrence interface
		$form_id = 'caladd_';
		$length_ranges = array(
			'summary' => array(3, 255),
			'description' => array(NULL, 1 << 24 - 1),
		);
		
		$input = array(
			'name' => '',
			'summary' => '',
			'description' => '',
			'start' => strtotime('+3hour'),
			'end'   => strtotime('+4hour'),
			'allday' => FALSE,
			'time_associated'    => TRUE,
			'eventcategory' => -1,
		);
		$summary = $CI->input->post($form_id.'summary');
		if (FALSE !== $summary) {
			// Get the data
			$failed_validation = FALSE;
			$input['summary']        = $summary;
			$input['start']          = $CI->input->post($form_id.'start');
			$input['end']            = $CI->input->post($form_id.'end');
			$input['allday']         = (FALSE !== $CI->input->post($form_id.'allday'));
			$input['location']       = $CI->input->post($form_id.'location');
			$input['category']       = $CI->input->post($form_id.'category');
			$input['description']    = $CI->input->post($form_id.'description');
			$input['frequency']      = $CI->input->post($form_id.'frequency');
			// Simple derived data
			$input['time_associated'] = !$input['allday'];
			$input['name'] = $input['summary'];
			
			// Validate numbers
			foreach (array('start','end') as $ts_name) {
				if (is_numeric($input[$ts_name])) {
					$input[$ts_name] = (int)$input[$ts_name];
				} else {
					$this->messages->AddMessage('error', 'Invalid '.$ts_name.' timestamp.');
				}
			}
			
			// Validate strings
			foreach ($length_ranges as $field => $range) {
				if (FALSE !== $input[$field]) {
					$len = strlen($input[$field]);
					if (NULL !== $range[0] && $len < $range[0]) {
						$failed_validation = TRUE;
						$CI->messages->AddMessage('error', 'The specified '.$field.' was not long enough. It must be at least '.$range[0].' characters long.');
					}
					if (NULL !== $range[1] && $len > $range[1]) {
						$failed_validation = TRUE;
						$CI->messages->AddMessage('error', 'The specified '.$field.' was too long. It must be at most '.$range[1].' characters long.');
					}
				}
			}
			
			// Validate category
			if (!array_key_exists($input['category'], $this->mCategories))
			{
				var_dump($input['category']);
				var_dump($this->mCategories);
				$failed_validation = TRUE;
				$CI->messages->AddMessage('error', 'You did not specify a valid event category');
			} else {
				$input['eventcategory'] = $input['category'];
				$input['category'] = $this->mCategories[$input['category']]['id'];
			}
			
			// Validate recurrence based on frequency
			if ('none' !== $input['frequency']) {
				// Read interval
				$input['interval']     = $CI->input->post($form_id.'interval');
				// Validate interval
				if (!is_numeric($input['interval']) || $input['interval'] < 1) {
					$failed_validation = TRUE;
					$CI->messages->AddMessage('error', 'You specified an invalid interval');
				} else {
					$input['interval'] = (int)$input['interval'];
				}
				if ('daily' === $input['frequency']) {
				} elseif ('weekly' === $input['frequency']) {
					$input['onday']          = $CI->input->post($form_id.'onday');
				} elseif ('yearly' === $input['frequency']) {
				}
			}
			
			if (!$failed_validation) {
				$start = $input['start'];
				$end   = $input['end'];
				if ($end < $start) {
					$CI->messages->AddMessage('error', 'You specified the end time before the start time.');
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
						$results = $CI->events_model->EventCreate($input);
						$CI->messages->AddMessage('success', 'Event created successfully.');
					} catch (Exception $e) {
						$CI->messages->AddMessage('error', $e->getMessage());
					}
				}
			}
		}
		
		$input['target'] = $CI->uri->uri_string();
		$data = array(
			'EventCategories' => $this->mCategories,
			'AddForm' => $input,
		);
		
		return new FramesView('calendar/simpleadd', $data);
	}
	
	
	/// Set up the tabs on the main_frame.
	/**
	 * @param $SelectedPage string Selected Page.
	 * @pre CheckPermissions must have already been called.
	 */
	protected function SetupTabs($SelectedPage, $Start, $Filter = NULL)
	{
		if ($this->mTabs) {
			$CI = & get_instance();
			$navbar = $CI->main_frame->GetNavbar();
			if (NULL === $Filter) {
				$Filter = '/';
			} else {
				$Filter = '/'.$Filter;
			}
			$navbar->AddItem('day', 'Day',
				$this->mRangeUrl.
				$Start->AcademicYear().'-'.$Start->AcademicTermNameUnique().'-'.$Start->AcademicWeek().'-'.$Start->Format('D').
				$Filter
			);
			$monday = $Start->BackToMonday();
			$navbar->AddItem('days', 'Week',
				$this->mRangeUrl.
				$monday->AcademicYear().'-'.$monday->AcademicTermNameUnique().'-'.$monday->AcademicWeek().
				$Filter
			);
			$navbar->AddItem('weeks', 'Term',
				$this->mRangeUrl.
				$monday->AcademicYear().'-'.$monday->AcademicTermNameUnique().
				$Filter
			);
			if (is_string($this->mAgenda)) {
				$navbar->AddItem('agenda', 'Agenda',
					$this->mAgenda.
					$Start->Year().'-'.strtolower($Start->Format('M')).'-'.$Start->DayOfMonth().
					$Filter
				);
			}
			$CI->main_frame->SetPage($SelectedPage);
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
		return $this->mRangeUrl.$date_range. '/' . implode('.',$results);
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
		$Filter = array(
			'att' => array(
				'declined' => $Sources->GroupEnabled('hide'),
				'no-accepted' => !$Sources->GroupEnabled('rsvp'),
				'no-maybe'    => !$Sources->GroupEnabled('show'),
			),
			'cat' => array(
				// Filled in in after initialisation
			),
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
				'colour'		=> $category['colour'],
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'cat', 'no-'.$category['name'])),
			);
		}
		
		// Then the attendance filters
		$filters['hidden'] = array(
			'name'			=> 'not attending',
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
			'name'			=> 'maybe attending',
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
			'name'			=> 'attending',
			'field'			=> 'visibility',
			'value'			=> 'yes',
			'selected'		=> $Sources->GroupEnabled('rsvp'),
			'description'	=> 'Only those to which I\'ve RSVPd',
			'display'		=> 'image',
			'selected_image'	=> '/images/prototype/calendar/filter_rsvp_select.gif',
			'unselected_image'	=> '/images/prototype/calendar/filter_rsvp_unselect.gif',
			'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'att', 'no-accepted')),
		);
		
		// The filters are the deliverable
		return $filters;
	}
	
}

?>