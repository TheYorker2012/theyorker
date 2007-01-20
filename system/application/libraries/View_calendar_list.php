<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file View_calendar_list.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Frame view for a simple list of days and events.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('view_calendar');

/// Calendar list view library class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Automatically loads the view_calendar library.
 *
 * Load the library from the controller constructor:
 * @code
 *	$this->load->library('view_calendar_list');
 * @endcode
 *
 * You can then refer to it as $this->view_calendar_list in order to adjust
 *	how to display the view.
 * The view can then be loaded using Load().
 *
 * Example of usage from a controller function and within a Frame_public:
 * @code
 *	// Set up the subview
 *	$this->view_calendar_list->SetPrevUrl($this->_GenUrl($Start, -14));
 *	$this->view_calendar_list->SetNextUrl($this->_GenUrl($Start, +14));
 *	$this->view_calendar_list->SetDayRange($Start, 14);
 *	
 *	// Set up the public frame
 *	$this->frame_public->SetContent($this->view_calendar_list);
 *	
 *	// Load the public frame view (which will load the content view)
 *	$this->frame_public->Load();
 * @endcode
 */
class ViewCalendarList extends ViewCalendar
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/calendar_list');
	}
	
	/// Set the day range to display.
	/**
	 * @param $StartTime Academic_time Start time of calendar.
	 * @param $EndTime Academic_time End time of calendar.
	 *
	 * @todo The first 3 statements don't really belong here. Move elsewhere.
	 */
	function SetRange($StartTime, $EndTime)
	{
		$CI = &get_instance();
		
		// Load my "minitemplater" helper.
		// This is a very basic S&R script
		// : Allows chunks of template code to be parsed without cluttering
		// up the script :)
		$CI->load->helper('minitemplater');
		
		// Don't trust users to set their clocks properly
		$this->SetData('server_dt', time());
		
		
		// Make sure that the time is rounded back to midnight on monday
		$StartTime = $StartTime->Midnight();
		
		/// @todo compensate if not in a valid academic term.
		
		$this->SetData('academic_year', $StartTime->AcademicYearName());
		$this->SetData('academic_term', $StartTime->AcademicTermName() . ' ' . $StartTime->AcademicTermTypeName());
		$this->SetData('academic_week', $StartTime->AcademicWeek());
		
		// Get the stuff from the db
		$this->_SetRange($StartTime, $EndTime);
	}
	
}

/// View_calendar_list Library class.
/**
 * This exists because we don't want just 1 existance of ViewCalendarList
 *	necessarily.
 */
class View_calendar_list
{
	
}

?>