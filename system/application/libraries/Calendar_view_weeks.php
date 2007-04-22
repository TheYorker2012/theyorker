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
	/// timestamp Start time of range of events to fetch.
	protected $mStartTime = NULL;
	/// timestamp End time of range of events to fetch.
	protected $mEndTime = NULL;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/weeks');
		
		
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/calendar.js" type="text/javascript"></script>
			<link href="/stylesheets/calendar.css" rel="stylesheet" type="text/css" />
EXTRAHEAD;
		$CI = & get_instance();
		$CI->main_frame->SetExtraHead($extra_head);
	}
	
	function SetStartEnd($Start, $End)
	{
		$this->mStartTime = $Start;
		$this->mEndTime = $End;
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
		$occurrences = $Data->GetCalendarOccurrences();
		$events = $Data->GetEvents();
		
		$days = array();
		$start = new Academic_time($this->mStartTime);
		$end   = new Academic_time($this->mEndTime);
		$start = $start->Midnight()->BackToMonday();
		$end = $end->Midnight()->BackToMonday()->Adjust('1week');
		while ($start < $end) {
			$date = $start->Format('Ymd');
			$days[$date] = array(
				'date' => $start,
				'events' => array(),
			);
			$start = $start->Adjust('1day');
		}
		
		foreach ($occurrences as $key => $occurrence) {
			$occ = & $occurrences[$key];
			$date = $occ->StartTime->Format('Ymd');
			$time = $occ->StartTime->Format('His');
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
			$weeks[(int)($day_counter/7)][$date] = $day;
			++$day_counter;
		}
		
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