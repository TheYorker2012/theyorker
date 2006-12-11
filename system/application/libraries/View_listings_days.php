<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file View_listings_days.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Frame view for multiple days of events.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('view_listings');

/// Listings days view library class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Automatically loads the view_listings library.
 *
 * Load the library from the controller constructor:
 * @code
 *	$this->load->library('view_listings_days');
 * @endcode
 *
 * You can then refer to it as $this->view_listings_days in order to adjust
 *	how to display the view.
 * The view can then be loaded using Load().
 *
 * Example of usage from a controller function and within a Frame_public:
 * @code
 *	// Set up the subview
 *	$this->view_listings_day->SetPrevUrl($this->_GenUrl($Start, -1));
 *	$this->view_listings_day->SetNextUrl($this->_GenUrl($Start, +1));
 *	$this->view_listings_day->SetDayRange($Start, 7);
 *	
 *	// Set up the public frame
 *	$this->frame_public->SetContent($this->view_listings_days);
 *	
 *	// Load the public frame view (which will load the content view)
 *	$this->frame_public->Load();
 * @endcode
 */
class View_listings_days extends ViewListings
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('listings/listings');
	}
	
	/// Set the day range to display and retrieve the relevent data.
	/**
	 * @param $StartTime Academic_time Start time of calendar.
	 * @param $NumDays integer Number of days to display on the calendar.
	 */
	function SetDayRange($StartTime, $NumDays)
	{
		$CI = &get_instance();
		
		// Load my "minitemplater" helper.
		// This is a very basic S&R script
		// : Allows chunks of template code to be parsed without cluttering
		// up the script :)
		$CI->load->helper('minitemplater');
		
		// Make sure that the time is rounded back to midnight
		$StartTime = $StartTime->Midnight();
		
		// Don't trust users to set their clocks properly
		$this->SetData('server_dt', time());
		
		
		/// @todo compensate if not in a valid academic term.
		
		$this->SetData('academic_year', $StartTime->AcademicYearName());
		$this->SetData('academic_term', $StartTime->AcademicTermName() . ' ' . $StartTime->AcademicTermTypeName());
		$this->SetData('academic_week', $StartTime->AcademicWeek());
		
		$end_time = $StartTime->Adjust($NumDays.'day');
		
		// Get the stuff from the db
		$this->SetRange($StartTime, $end_time);
	}
	
}

?>