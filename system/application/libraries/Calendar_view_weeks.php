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
		$occurrences = $Data->GetOccurrences();
		$events = $Data->GetEvents();
		
		$this->SetData('Occurrences', $occurrences);
		$this->SetData('Events', $events);
	}
}

/// Dummy class.
class Calendar_view_weeks
{

}


?>