<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_view_days.php
 * @brief Calendar view for a set of days.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library Calendar_frontend)
 *
 * Cunning fuzzy-absolute time.
 *
 * @version 29-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Days calendar view class.
class CalendarViewDays extends CalendarView
{
	/// Academic_time Start time of view.
	protected $mStartTime;
	/// Academic_time End time of view.
	protected $mEndTime;
	/// Number of days to display
	private $mNumDays;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/calendar');
		$this->mNumDays = 7;
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
		
		$days_information = array();
		// Initialise the day info array
		$current_time = $this->mStartTime->Midnight();
		$current_index = 0;
		while ($current_time->Timestamp() < $this->mEndTime->Timestamp()) {
			$days_information[$current_time->Timestamp()] = array(
				'index' => $current_index,
				'date' => $current_time,
				'special_headings' => array(),
			);
			
			// Onto the next day
			$current_time = $current_time->Adjust('1day');
			++$current_index;
		}
		
		foreach ($occurrences as $key => $event) {
			$midnight_time = $event->StartTime->Midnight()->Timestamp();
			if (array_key_exists($midnight_time, $days_information)) {
				$occurrences[$key]->Day = $days_information[$midnight_time]['index'];
			}
		}
		$this->SetData('Occurrences', $occurrences);
		$this->SetData('Events', $events);
		
		// this is temporary for testing only
		$this->SetData('days', array());
		$this->mDataArray['dayinfo'] = array();
		foreach ($days_information as $day_info) {
			$day_time = $day_info['date'];
			$this->mDataArray['days'][$day_info['index']] = $day_time->Format('jS M');
			
			$day_info['is_holiday'    ] = $day_time->IsHoliday();
			$day_info['is_weekend'    ] = $day_time->DayOfWeek() > 5;
			$day_info['year'          ] = $day_time->AcademicYear();
			$day_info['date'          ] = $day_time->Format('jS');
			$day_info['month_short'   ] = $day_time->Format('M');
			$day_info['month_long'    ] = $day_time->Format('F');
			$day_info['day_of_week'   ] = $day_time->Format('l');
			$day_info['academic_year' ] = $day_time->AcademicYearName(2);
			$day_info['academic_term' ] = $day_time->AcademicTermName().' '.$day_time->AcademicTermTypeName();
			$day_info['academic_week' ] = $day_time->AcademicWeek();
			
			$this->mDataArray['dayinfo'][$day_info['index']] = $day_info;
		}
		
		//$this->SetData('prev', $this->GenerateUri($this->mStartTime->Adjust((-($this->mNumDays)).'day'),$this->mStartTime));
		//$this->SetData('next', $this->GenerateUri($this->mEndTime,$this->mEndTime->Adjust($this->mNumDays.'day')));
	}
}

/// Dummy class.
class Calendar_view_days
{

}


?>