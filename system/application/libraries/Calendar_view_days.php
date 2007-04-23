<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_view_days.php
 * @brief Calendar view for a set of days.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library Calendar_frontend)
 *
 * Cunning fuzzy-absolute time.
 *
 * @version 29-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Days calendar view class.
class CalendarViewDays extends CalendarView
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/days');
		
		
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/calendar.js" type="text/javascript"></script>
			<link href="/stylesheets/calendar.css" rel="stylesheet" type="text/css" />
EXTRAHEAD;
		$CI = & get_instance();
		$CI->main_frame->SetExtraHead($extra_head);
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
		$start = $this->mStartTime;
		$end = $this->mEndTime;
		while ($start < $end) {
			$date = date('Ymd', $start);
			$ac_date = new Academic_time($start);
			$days[$date] = array(
				'date'   => $ac_date,
				'events' => array(),
				'link'   => $this->GenerateRangeUrl($ac_date, $ac_date->Adjust('1day')),
			);
			$start = strtotime('1day',$start);
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
		
		$this->SetData('Occurrences', $occurrences);
		$this->SetData('Events', $events);
		$this->SetData('Days', $days);
	}
}

/// Dummy class.
class Calendar_view_days
{

}


?>