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
	protected $mDefaultRange = 'today:1week';
	
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->library('academic_calendar');
		$CI->load->library('calendar_backend');
		$CI->load->library('calendar_frontend');
		$CI->load->library('date_uri');
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
	
	/// Show the calendar interface.
	/**
	 * @return FramesView View class.
	 */
	function GetMyCalendar(&$Sources, $DateRange = NULL, $Filter = NULL)
	{
		$CI = & get_instance();
		$range = $CI->date_uri->ReadUri($DateRange, TRUE);
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
			// simulate "att:no-declined"
			$filter = array(
				'att' => array(array(0 => 'no-declined')),
			);
		} else {
			$filter = $filter_def->ReadUri($Filter);
		}
		
		if (FALSE === $filter) {
			$CI->messages->AddMessage('error', 'The filter text in the uri was not valid');
		} else {
			$CI->load->model('calendar/events_model');
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
				'no-declined' => !$Sources->GroupEnabled('hide'),
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
				'link'			=> $this->GenFilterUrl($this->AlteredFilter($Filter, 'att', 'no-declined')),
			),
		);
	}
	
}

?>