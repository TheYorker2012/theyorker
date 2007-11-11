<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_view_upcoming.php
 * @brief Calendar view for mini upcoming events.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library Calendar_frontend)
 *
 * List of upcoming events
 *
 * @version 30-04-2007 James Hogan (jh559)
 *	- Creaded from Calendar_view_Agenda.
 */

/// Upcoming calendar view class.
class CalendarViewUpcoming extends CalendarView
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/upcoming');
		
		$this->SetData('MiniMode', FALSE);
	}
	
	/// Whether to use mini mode.
	function SetMiniMode($Mini = TRUE)
	{
		$this->SetData('MiniMode', $Mini);
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
		
		$this->SetData('Occurrences', $occurrences);
		$this->SetData('Events', $events);
	}
}

/// Dummy class.
class Calendar_view_upcoming
{

}


?>