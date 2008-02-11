<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_view_weeks.php
 * @brief Calendar view for a set of weeks.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library Calendar_frontend)
 *
 * Term / month view.
 *
 * @version 29-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Weeks calendar view class.
class CalendarViewWeeks extends CalendarView
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/weeks');
		
		$CI = & get_instance();
		$CI->main_frame->IncludeJs('javascript/prototype.js');
		$CI->main_frame->IncludeJs('javascript/scriptaculous.js');
		$CI->main_frame->IncludeJs('javascript/calendar.js');
		$CI->main_frame->IncludeCss('stylesheets/calendar.css');
	}
	
	/// Process the calendar data to produce view data.
	/**
	 * @param $Data CalendarData Calendar data.
	 * @param $Categories array[category] Array of categories.
	 *
	 * This should be the data which is specific to the view.
	 * General data such as day information should be calculated then passed in.
	 */
	protected function ProcessEvents(&$Data, $Categories)
	{
		if (!is_array($Categories)) {
			$Categories = array();
		}
		$occurrences = $Data->GetCalendarOccurrences();
		$events = $Data->GetEvents();
		
		$days = array();
		$start = new Academic_time($this->mStartTime);
		$end   = new Academic_time($this->mEndTime);
		$start = $start->Midnight()->BackToMonday();
		$end = $end->Midnight();
		$monday = $end->BackToMonday();
		if ($end->Timestamp() !== $monday->Timestamp()) {
			$end = $monday->Adjust('1week');
		}
		while ($start->Timestamp() < $end->Timestamp()) {
			$date = $start->Format('Ymd');
			$days[$date] = array(
				'date' => $start,
				'events' => array(),
				'link'   => $this->GenerateRangeUrl($start, $start->Adjust('1day')),
			);
			$start = $start->Adjust('1day');
		}
		
		foreach ($occurrences as $key => $occurrence) {
			$occ = & $occurrences[$key];
			$date = $occ->StartTime->Format('Ymd');
			if ($occurrence->TimeAssociated) {
				$time = $occ->StartTime->Format('His');
			} else {
				$time = '000000';
			}
			if (array_key_exists($date, $days)) {
				if (!array_key_exists($time, $days[$date]['events'])) {
					$days[$date]['events'][$time] = array();
				}
				$days[$date]['events'][$time][] = &$occ;
			}
		}
		
		foreach ($days as $date => $times) {
			ksort($days[$date]);
		}
		
		$weeks = array();
		$day_counter = 0;
		foreach ($days as $date => $day) {
			$week = (int)($day_counter/7);
			if (!array_key_exists($week, $weeks)) {
				$weeks[$week] = array(
					'days' => array(),
					'link' => $this->GenerateRangeUrl($day['date'], $day['date']->Adjust('1week')),
					'start' => $day['date'],
				);
			}
			$weeks[$week]['days'][$date] = $day;
			$weeks[$week]['end'] = $day['date'];
			++$day_counter;
		}
		
		$this->SetData('Categories', $Categories);
		$this->SetData('Occurrences', $occurrences);
		$this->SetData('Events', $events);
		$this->SetData('Days', $days);
		$this->SetData('Weeks', $weeks);
	}
}

/// Dummy class.
class Calendar_view_weeks
{

}


?>