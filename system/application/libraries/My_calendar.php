<?php

/**
 * @file libraries/My_calendar.php
 * @brief Functions for using a calendar interface.
 */

/// Calendar interface library class.
class My_calendar
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
	
	protected $mAgenda = FALSE;
	protected $mRangeUrl = '';
	protected $mTabs = TRUE;
	protected $mDateRange = 'today';
	protected $mDefaultRange;
	protected $mReadOnly = TRUE;
	/// array[string => string]
	protected $mPaths = array(
		'add' => '/dead',
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
		$filter_def = new FilterDefinition(self::$sFilterDef);
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
		$todo = new CalendarViewTodoList();
		$todo->SetCalendarData($calendar_data);
		
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
		
		$sources->SetRange(time(), strtotime('1month'));
		$this->ReadFilter($sources, $Filter);
		
		$calendar_data = new CalendarData();
		
		$CI->messages->AddMessages($sources->FetchEvents($calendar_data));
		
		// Display data
		$CI->load->library('calendar_frontend');
		$CI->load->library('calendar_view_agenda');
		
		$agenda = new CalendarViewAgenda();
		$agenda->SetCalendarData($calendar_data);
		
		$data = array(
			'Filters'	=> $this->GetFilters($sources),
			'ViewMode'	=> $agenda,
		);
		
		$this->SetupTabs('agenda', new Academic_time(time()), $Filter);
		
		return new FramesView('calendar/my_calendar', $data);
	}
	
	function GetAdder()
	{
		$CI = & get_instance();
		
		$event_categories = $CI->events_model->CategoriesGet();
		
		/// @todo make standard functions and views for recurrence interface
		$input = array();
		$input['name'] = $CI->input->post('a_summary');
		if (FALSE !== $input['name']) {
			$failed_validation = FALSE;
			$input['startdate']      = $CI->input->post('a_startdate');
			$input['starttime']      = $CI->input->post('a_starttime');
			$input['enddate']        = $CI->input->post('a_enddate');
			$input['endtime']        = $CI->input->post('a_endtime');
			$input['time_associated'] = FALSE === $CI->input->post('a_allday');
			$input['location']       = $CI->input->post('a_location');
			$input['category']       = $CI->input->post('a_category');
			if (!is_numeric($input['category']) ||
				!array_key_exists($input['category'] = (int)$input['category'], $event_categories))
			{
				$failed_validation = TRUE;
				$CI->messages->AddMessage('error', 'You did not specify a valid event category');
			}
			$input['description']    = $CI->input->post('a_description');
			$input['frequency']      = $CI->input->post('a_frequency');
			// Figure out recurrence
			if ('none' !== $input['frequency']) {
				$input['interval']       = $CI->input->post('a_interval');
				if (!is_numeric($input['interval']) || $input['interval'] < 1) {
					$failed_validation = TRUE;
					$CI->messages->AddMessage('error', 'You specified an invalid interval');
				} else {
					$input['interval'] = (int)$input['interval'];
				}
				if ('daily' === $input['frequency']) {
				} elseif ('weekly' === $input['frequency']) {
					$input['onday']          = $CI->input->post('a_onday');
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
						$CI->messages->AddMessage('error',
							'You specified a '.$startend.' date that does not exist: '.
							$matches[1].'/'.$matches[2].'/'.$matches[3]
						);
						$failed_validation = TRUE;
					}
				} else {
					$CI->messages->AddMessage('error',
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
							$CI->messages->AddMessage('error',
								'You specified a '.$startend.' time that does not exist: '.
								$hour.':'.$minute.':'.$second
							);
							$failed_validation = TRUE;
						}
					} else {
						$CI->messages->AddMessage('error',
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
		
		$data = array(
			'EventCategories' => $event_categories,
			'AddForm' => array(
				'target' => $CI->uri->uri_string(),
				'default_summary' => '',
				'default_startdate' => date('d/m/Y', strtotime('+3hour')),
				'default_starttime' => date('H:m',   strtotime('+3hour')),
				'default_enddate'   => date('d/m/Y', strtotime('+4hour')),
				'default_endtime'   => date('H:m',   strtotime('+4hour')),
				'default_allday'    => FALSE,
				'default_eventcategory' => -1,
			),
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
			$now = new Academic_time(time());
			$navbar->AddItem('day', 'Today',
				$this->mRangeUrl.
				'today'.
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
		);
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
				'name'			=> 'attending',
				'field'			=> 'visibility',
				'value'			=> 'yes',
				'selected'		=> $Sources->GroupEnabled('rsvp'),
				'description'	=> 'Only those to which I\'ve RSVPd',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_rsvp_select.gif',
				'unselected_image'	=> '/images/prototype/calendar/filter_rsvp_unselect.gif',
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'att', 'no-accepted')),
			),
			'visible' => array(
				'name'			=> 'maybe attending',
				'field'			=> 'visibility',
				'value'			=> 'maybe',
				'selected'		=> $Sources->GroupEnabled('show'),
				'description'	=> 'Include those which I have not hidden',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_visible_select.png',
				'unselected_image'	=> '/images/prototype/calendar/filter_visible_unselect.png',
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'att', 'no-maybe')),
			),
			'hidden' => array(
				'name'			=> 'not attending',
				'field'			=> 'visibility',
				'value'			=> 'no',
				'selected'		=> $Sources->GroupEnabled('hide'),
				'description'	=> 'Include those which I have hidden',
				'display'		=> 'image',
				'selected_image'	=> '/images/prototype/calendar/filter_hidden_select.gif',
				'unselected_image'	=> '/images/prototype/calendar/filter_hidden_unselect.gif',
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'att', 'declined')),
			),
		);
	}
	
}

?>