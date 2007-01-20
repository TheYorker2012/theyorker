<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file View_calendar.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Frame view for any calendar events view.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');
$CI->load->library('academic_calendar');
$CI->load->library('date_uri');

/// Abstract calendar view class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Automatically loads the Frames library.
 *
 * Abstract base class of calendar view.
 */
abstract class ViewCalendar extends FramesView
{
	/// Whether to include special day headings.
	protected $mIncludeSpecials;
	
	/// EventOccurrenceFilter Occurrence filter.
	protected $mOccurrenceFilter;
	
	/// Academic_time Start time of view.
	protected $mStartTime;
	/// Academic_time End time of view.
	protected $mEndTime;
	
	/// URI format (see Date_uri)
	protected $mUriFormat;
	/// URI base address (with trailing slash)
	protected $mUriBase;
	
	/// Primary constructor.
	/**
	 * @param $ViewPath string The path of a CI view.
	 * @param $Data array The initial data array.
	 */
	function __construct($ViewPath, $Data = array())
	{
		parent::__construct($ViewPath, $Data);
		
		// Initialise data
		$this->IncludeSpecialHeadings(TRUE);
		$this->SetOccurrenceFilter();
		$this->mUriFormat = Date_uri::DefaultFormat();
		$this->mUriBase = '';
	}
	
	/// Set the base uri on which to base links.
	/**
	 * @param $UriBase string URL of previous period of days.
	 */
	function SetUriBase($UriBase)
	{
		$this->mUriBase = $UriBase;
	}
	
	/// Set the uri format.
	/**
	 * @param $Format string Formatting string as used by Date_uri.
	 */
	function SetUriFormat($Format)
	{
		$this->mUriFormat = $Format;
	}
	
	/// Use date_uri to produce a valid uri for a date
	/**
	 * @param $Date Academic_time Time to encode in a URI.
	 * @return string Full site url with encoded date.
	 */
	function GenerateUri($Start, $End = FALSE)
	{
		$CI = &get_instance();
		return site_url($this->mUriBase . $CI->date_uri->GenerateUri($this->mUriFormat, $Start, $End));
	}
	
	/// Set whether to display special day headings.
	/**
	 * @param $Display bool Whether to display special day headings.
	 */
	function IncludeSpecialHeadings($Display)
	{
		$this->mIncludeSpecials = ($Display === TRUE);
	}
	
	/// Set the event occurrence filter to use.
	/**
	 * @param $Filter EventOccurrenceFilter Event filter object
	 *	(A value of FALSE means use a default filter)
	 */
	function SetOccurrenceFilter($Filter = FALSE)
	{
		$this->mOccurrenceFilter = $Filter;
	}
	
	/// Set the range to display (user function).
	/**
	 * @param $StartTime Academic_time Start time of calendar.
	 * @param $Duration integer Number of time units to display.
	 */
	abstract function SetRange($StartTime, $Duration);
	
	/// Set the range to display (internal function).
	/**
	 * @param $StartTime Academic_time Start time of calendar.
	 * @param $EndTime Academic_time End time of calendar.
	 */
	protected function _SetRange($StartTime, $EndTime)
	{
		$this->mStartTime = $StartTime;
		$this->mEndTime = $EndTime;
	}
	
	/// Retrieve the relevent data, ready for the view.
	/**
	 * @pre SetRange() must have already been called.
	 */
	function Retrieve()
	{
		$CI = &get_instance();
	
		// Get data from the database
		$CI->load->model('calendar/events_model');
		$CI->events_model->IncludeDayInformation(TRUE);
		$CI->events_model->IncludeDayInformationSpecial($this->mIncludeSpecials);
		$CI->events_model->IncludeOccurrences(TRUE);
		$CI->events_model->SetOccurrenceFilter($this->mOccurrenceFilter);
		$error = $CI->events_model->Retrieve($this->mStartTime, $this->mEndTime);
		
		if (FALSE === $error) {
			throw new Exception('Events model retrieval failed');
			//return FALSE;
		}
		
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
			$day_info['date'          ] = $day_time->Format('jS');
			$day_info['month_short'   ] = $day_time->Format('M');
			$day_info['month_long'   ] = $day_time->Format('F');
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
	protected function ProcessEvents($Occurrences, $DaysInfo)
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

/// View_calendar Library class.
/**
 * This exists because ViewCalendar is abstract and so shouldn't be created
 *	automatically as it would be if it was the CI library class.
 */
class View_calendar
{
	
}

?>