<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file View_listings_days.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Frame view for multiple days of events.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

/// Main public frame library class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Automatically loads the Frames library.
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
class View_listings_days extends FramesView
{
	protected $mIncludeSpecials;
	
	/// @brief Default constructor.
	function __construct()
	{
		parent::__construct('listings/listings');
		
		// Initialise data
		$this->SetPrevUrl('');
		$this->SetNextUrl('');
		$this->IncludeSpecialHeadings(TRUE);
	}
	
	/// Set the url for the previous period of days.
	/**
	 * @param $PrevUrl string URL of previous period of days.
	 */
	function SetPrevUrl($PrevUrl = '')
	{
		$this->SetData('prev', $PrevUrl);
	}
	
	/// Set the URL for the next period of days.
	/**
	 * @param $NextUrl string URL of next period of days.
	 */
	function SetNextUrl($NextUrl = '')
	{
		$this->SetData('next', $NextUrl);
	}
	
	/// Set whether to display special headings.
	function IncludeSpecialHeadings($Display)
	{
		$this->mIncludeSpecials = ($Display === TRUE);
	}
	
	/// Set the day range to display and retrieve the relevent data.
	/**
	 * @param $StartTime Academic_time Start time of calendar.
	 * @param $NumDays Number of days to display on the calendar.
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
		
		// Get data from the database
		$CI->load->model('listings/events_model');
		$CI->events_model->IncludeDayInformation(TRUE);
		$CI->events_model->IncludeDayInformationSpecial($this->mIncludeSpecials);
		$CI->events_model->IncludeOccurrences(TRUE);
		$CI->events_model->Retrieve($StartTime, $end_time);
		
		// Day information
		$days_information = $CI->events_model->GetDayInformation();
		
		// this is temporary for testing only
		$this->SetData('days', array());
		foreach ($days_information as $day_info) {
			$day_time = $day_info['date'];
			$this->mDataArray['days'][$day_info['index']] = $day_time->Format('jS M');
			
			$day_info['is_holiday'    ] = $day_time->IsHoliday();
			$day_info['is_weekend'    ] = $day_time->DayOfWeek() > 5;
			$day_info['year'          ] = $day_time->AcademicYear();
			$day_info['date_and_month'] = $day_time->Format('jS M');
			$day_info['day_of_week'   ] = $day_time->Format('l');
			$day_info['academic_year' ] = $day_time->AcademicYearName(2);
			$day_info['academic_term' ] = $day_time->AcademicTermName().' '.$day_time->AcademicTermTypeName();
			$day_info['academic_week' ] = $day_time->AcademicWeek();
			
			$this->mDataArray['dayinfo'][$day_info['index']] = $day_info;
		}
		
		// Event occurrences
		$occurrences = $CI->events_model->GetOccurrences();
		$this->SetData('events', $this->ProcessEvents($occurrences, $days_information));
	}
	
	
	/// Process events ready for the view.
	/**
	 * @param array $Occurrences Array of event occurrences (using timestamps).
	 * @param array $DaysInfo Information about days on the calendar.
	 * @return array similar to @a $Occurrences with the following extra fields:
	 *	- 'date' (the date of the start of the slice formatted as 'Y-m-d').
	 *	- 'day' (the day index on the visible calendar (using @a $DayCalc).
	 *	- 'starttime' (the start time of the occurrence using user preferences to format).
	 *	- 'endtime' (the end time of the occurrence using user preferences to format).
	 */
	private function ProcessEvents($Occurrences, $DaysInfo)
	{
		$CI = &get_instance();
		$CI->load->library('event_manager');
		
		// Slice up the events
		$event_occurrences = $CI->event_manager->SliceOccurrences($Occurrences, 4*60);
		// Perform date formatting
		$return_array = array();
		foreach ($event_occurrences as $event_index => $event_data) {
			// Convert timestamps to Academic_times so its easier to extract
			// date and time in certain formats
			$event_data['start']       = new Academic_time($event_data['start']);
			$event_data['end']         = new Academic_time($event_data['end']);
			$event_data['slice_start'] = new Academic_time($event_data['slice_start']);
			$event_data['slice_end']   = new Academic_time($event_data['slice_end']);
			
			// Which day of the current view? (must be valid)
			$midnight_time = $event_data['slice_start']->Midnight()->Timestamp();
			if (array_key_exists($midnight_time, $DaysInfo)) {
				$event_data['day'] = $DaysInfo[$midnight_time]['index'];
			
				// Produce and new 'date' field with string date
				$event_data['date'] = $event_data['slice_start']->Format('Y-m-d');
			
				// Start and end time (use slice start/end for the moment)
				// It should be made obvious when a slice is only part of an event
				$event_data['starttime'] = $event_data['start']->Time();
				$event_data['endtime'] = $event_data['end']->Time();
	
				$return_array[] = $event_data;
			}
		}
		return $return_array;
	}
}

?>