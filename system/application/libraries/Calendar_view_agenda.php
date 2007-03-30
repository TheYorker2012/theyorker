<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_view_agenda.php
 * @brief Calendar view for the agenda.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library Calendar_frontend)
 *
 * List of upcoming events
 *
 * @version 29-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Agenda calendar view class.
class CalendarViewAgenda extends CalendarView
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/agenda');
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
class Calendar_view_agenda
{

}


?>