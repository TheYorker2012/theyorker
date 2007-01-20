<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file View_calendar_days.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Frame view for multiple days of events.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('view_calendar');

/// Calendar days view library class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Automatically loads the view_calendar library.
 *
 * Load the library from the controller constructor:
 * @code
 *	$this->load->library('view_calendar_days');
 * @endcode
 *
 * You can then refer to it as $this->view_calendar_days in order to adjust
 *	how to display the view.
 * The view can then be loaded using Load().
 *
 * Example of usage from a controller function and within a Frame_public:
 * @code
 *	// Set up the subview
 *	$view_calendar_days = new ViewCalendarDays();
 *	$view_calendar_days->SetPrevUrl($this->_GenUrl($Start, -1));
 *	$view_calendar_days->SetNextUrl($this->_GenUrl($Start, +1));
 *	$view_calendar_days->SetRange($Start,7);
 *	// Get the data from the db, then we're ready to load
 *	$view_calendar_days->Retrieve();
 *	
 *	// Set up the public frame
 *	$this->frame_public->SetContent($view_calendar_days);
 *	
 *	// Load the public frame view (which will load the content view)
 *	$this->frame_public->Load();
 * @endcode
 */
class ViewCalendarDays extends ViewCalendar
{
	/// Number of days to display
	private $mNumDays;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/calendar');
		
		$this->SetData('eventHandlerJS','');
		$this->mNumDays = 7;
	}
	
	/// Set the day range to display.
	/**
	 * @param $StartTime Academic_time Start time of calendar.
	 * @param $NumDays integer Number of days to display.
	 *
	 * @todo The first 3 statements don't really belong here. Move elsewhere.
	 */
	function SetRange($StartTime, $NumDays)
	{
		$CI = &get_instance();
		
		// Load my "minitemplater" helper.
		// This is a very basic S&R script
		// : Allows chunks of template code to be parsed without cluttering
		// up the script :)
		$CI->load->helper('minitemplater');
		
		// Don't trust users to set their clocks properly
		$this->SetData('server_dt', time());
		
		$this->mNumDays = $NumDays;
		
		// Make sure that the time is rounded back to midnight
		$StartTime = $StartTime->Midnight();
		
		/// @todo compensate if not in a valid academic term.
		
		$this->SetData('academic_year', $StartTime->AcademicYearName());
		$this->SetData('academic_term', $StartTime->AcademicTermName() . ' ' . $StartTime->AcademicTermTypeName());
		$this->SetData('academic_week', $StartTime->AcademicWeek());
		
		$end_time = $StartTime->Adjust($NumDays.'day');
		
		// Get the stuff from the db
		$this->_SetRange($StartTime, $end_time);
	}
	
	
	/// Retrieve the relevent data, ready for the view.
	/**
	 * @pre SetRange() must have already been called.
	 */
	function Retrieve()
	{
		parent::Retrieve();
		
		$this->SetData('prev', $this->GenerateUri($this->mStartTime->Adjust((-$this->mNumDays).'day'),$this->mStartTime));
		$this->SetData('next', $this->GenerateUri($this->mEndTime,$this->mEndTime->Adjust($this->mNumDays.'day')));
	}
	
}

/// View_calendar_days Library class.
/**
 * This exists because we don't want just 1 existance of ViewCalendarDays
 *	necessarily.
 */
class View_calendar_days
{
	
}

?>