<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_view_icalendar.php
 * @brief Calendar view for vcalendar/icalendar file formats.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library Calendar_frontend)
 *
 * @version 18-04-2007 James Hogan (jh559)
 *	- Created.
 */

/// iCal/vCal view class.
class CalendarViewICalendar extends CalendarView
{
	/// Default constructor.
	function __construct($UseIcal = TRUE)
	{
		parent::__construct('calendar/'.($UseIcal ? 'ical' : 'vcal'));
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
	
	function Load()
	{
		header('Content-type: text/calendar');
		parent::Load();
	}
}

/// Dummy class.
class Calendar_view_icalendar
{

}


?>