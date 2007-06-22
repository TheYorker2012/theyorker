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
		
		$this->SetData('MiniMode', FALSE);
		
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/calendar.js" type="text/javascript"></script>
			<link href="/stylesheets/calendar.css" rel="stylesheet" type="text/css" />
EXTRAHEAD;
		$CI = & get_instance();
		$CI->main_frame->SetExtraHead($extra_head);
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
		if (!is_array($Categories)) {
			$Categories = array();
		}
		$occurrences = $Data->GetCalendarOccurrences();
		$events = $Data->GetEvents();
		
		$this->SetData('Occurrences', $occurrences);
		$this->SetData('Categories', $Categories);
		$this->SetData('Events', $events);
	}
}

/// Dummy class.
class Calendar_view_agenda
{

}


?>