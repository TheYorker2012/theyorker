<?php

/**
 * @brief Controller for event manager.
 * @author David Harker (dhh500@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Listings extends Controller {

	/**
	 * @brief Default constructor.
	 */
	function Listings()
	{
		parent::Controller();
		
		// Used for processing the events
		$this->load->library('event_manager');
		
		// Used for producing friendly date strings
		$this->load->library('academic_calendar');
		
		date_default_timezone_set(Academic_time::InternalTimezone());
		
		// Make use of the public frame
		$this->load->library('frame_public');
		
		$this->load->model('listings/events_model');
	}
	
	/**
	 * @brief Default function.
	 */
	function index()
	{
		$this->week();
	}
	
	/**
	 * @brief Show the calendar between certain dates.
	 * @param $AcademicYear Academic year of week to show (e.g. 2006).
	 * @param $AcademicTerm Academic term value
	 *	(accepted by Academic_time::TranslateAcademicTermName):
	 *	- Either an integer 0 <= @a $AcademicTerm < 6.
	 *	- Or a string such as 'au', 'xmas', 'spring.
	 * @param $AcademicWeek Academic week of week to show.
	 */
	function week($AcademicYear = false, $AcademicTerm = 0, $AcademicWeek = 1)
	{
		// Translate the term name
		$AcademicTerm = Academic_time::TranslateAcademicTermName($AcademicTerm);
		
		// Check the parameters are valid integers
		if (is_numeric($AcademicYear) &&
			is_numeric($AcademicTerm) &&
			is_numeric($AcademicWeek)) {
			
			$AcademicYear = (int)$AcademicYear;
			$AcademicTerm = (int)$AcademicTerm;
			$AcademicWeek = (int)$AcademicWeek;
			
			if ($AcademicYear >= 2004 &&
				$AcademicTerm >= 0) {
				
				// And check that the term in question exists
				if (Academic_time::ValidateAcademicTerm($AcademicYear, $AcademicTerm)) {
					// Slow the week specified
					$start_date = $this->academic_calendar->Academic(
							$AcademicYear,
							$AcademicTerm,
							$AcademicWeek);
					
					$this->_ShowCalendar(
							$start_date, 7,
							$this->_GenerateWeekPresentation($start_date));
					return;
				}
			}
		}
		
		// Invalid so just show the next week
		$now = new Academic_time(time());
		$monday = $now->Adjust('-'.($now->DayOfWeek()-1).'day')->Midnight();
		
		$this->_ShowCalendar(
				$monday, 7,
				$this->_GenerateWeekPresentation($monday));
	}
	
	/**
	 * @brief Generate a URI path for a week view of a Academic_time.
	 * @param Academic_time $Start.
	 * @return Something in the format "listings/week/$year/$term/$week"
	 */
	function _GenerateWeekUri($Start)
	{
		/// @todo compensate if not in a valid academic term.
		return '/listings/week/' . $Start->AcademicYear() .
				'/' . $Start->AcademicTermNameUnique() .
				'/' . $Start->AcademicWeek();
	}
	
	/**
	 * @brief Generate a presentation array for a week display.
	 * @param Academic_time $Start.
	 * @return Presentation array:
	 *	- 'academic_year': e.g. 2006/2007
	 *	- 'academic_term': e.g. christmas holiday
	 *	- 'academic_week': e.g. 8
	 *	- 'prev': e.g. listings/week/2006/xmas/2
	 *	- 'next': e.g. listings/week/2006/autumn/10
	 */
	function _GenerateWeekPresentation($Start)
	{
		/// @todo compensate if not in a valid academic term.
		return array(
				'academic_year' => $Start->AcademicYearName(),
				'academic_term' => $Start->AcademicTermName() . ' ' .
						$Start->AcademicTermTypeName(),
				'academic_week' => $Start->AcademicWeek(),
				'prev' => $this->_GenerateWeekUri($Start->Adjust('-1week')),
				'next' => $this->_GenerateWeekUri($Start->Adjust('+1week')),
			);
	}
	
	/**
	 * @brief Show the calendar between certain Academic_times.
	 */
	function _ShowCalendar($StartTime, $Days, $Presentation)
	{
		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/listings.js" type="text/javascript"></script>
			<link href="/stylesheets/listings.css" rel="stylesheet" type="text/css" />
EXTRAHEAD;
		
		
		// Load my "minitemplater" helper.
		// This is a very basic S&R script
		// : Allows chunks of template code to be parsed without cluttering
		// up the script :)
		$this->load->helper('minitemplater');
		
		// This array get sent to the view listings_view.php
		$data = Array();
		
		// I don't trust users to set their clocks properly
		$data['server_dt'] = time();
		
		// next and previous uri
		foreach ($Presentation as $index => $value) {
			$data[$index] = $value;
		}
		
		$end_time = $StartTime->Adjust($Days.'day');
		
		// Get data from the database
		$this->events_model->EnableDayInformation();
		$this->events_model->EnableOccurrences();
		$this->events_model->Retrieve($StartTime, $end_time);
		
		// Day information
		$days_information = $this->events_model->GetDayInformation();
		
		// this is temporary for testing only
		$data['days'] = array();
		$data['any_day_events'] = FALSE;
		$daycalc = array();
		foreach ($days_information as $day_info) {
			$day_time = $day_info['date'];
			$data['days'][$day_info['index']] = $day_time->Format('jS M');
			
			$day_info['is_holiday'    ] = $day_time->IsHoliday();
			$day_info['is_weekend'    ] = $day_time->DayOfWeek() > 5;
			$day_info['year'          ] = $day_time->AcademicYear();
			$day_info['date_and_month'] = $day_time->Format('jS M');
			$day_info['day_of_week'   ] = $day_time->Format('l');
			$day_info['academic_year' ] = $day_time->AcademicYearName(2);
			$day_info['academic_term' ] = $day_time->AcademicTermName().' '.$day_time->AcademicTermTypeName();
			$day_info['academic_week' ] = $day_time->AcademicWeek();
			
			$data['dayinfo'][$day_info['index']] = $day_info;
		}
		
		// Event occurrences
		$occurrences = $this->events_model->GetOccurrences();
		$data['events'] = $this->_ProcessEvents($occurrences, $days_information);
		
		// Set up the public frame to use the listings view
		$this->frame_public->SetTitle('Listing viewer prototype');
		$this->frame_public->SetExtraHead($extra_head);
		$this->frame_public->SetContentSimple('listings/listings', $data);
		
		// Load the public frame view
		$this->frame_public->Load();
	}
	
	/**
	 * @brief Process events ready for the view.
	 * @param array $Occurrences Array of event occurrences (using timestamps).
	 * @param array $DaysInfo Information about days on the calendar.
	 * @return array similar to @a $Occurrences with the following extra fields:
	 *	- 'date' (the date of the start of the slice formatted as 'Y-m-d').
	 *	- 'day' (the day index on the visible calendar (using @a $DayCalc).
	 *	- 'starttime' (the start time of the occurrence using user preferences to format).
	 *	- 'endtime' (the end time of the occurrence using user preferences to format).
	 */
	function _ProcessEvents($Occurrences, $DaysInfo)
	{
		// Slice up the events
		$event_occurrences = $this->event_manager->SliceOccurrences($Occurrences, 4*60);
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