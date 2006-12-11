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
		$this->load->library('view_listings_days');
		
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
		/// @todo Generalise parameters for calendars into a function so that the same style of arguments can be used from multiple controllers (including _GenerateWeekUri()).
		
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
	 *	- 'prev': e.g. listings/week/2006/xmas/2
	 *	- 'next': e.g. listings/week/2006/autumn/10
	 */
	function _GenerateWeekPresentation($Start)
	{
		/// @todo compensate if not in a valid academic term.
		return array(
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

		$this->view_listings_days->SetPrevUrl($Presentation['prev']);
		$this->view_listings_days->SetNextUrl($Presentation['next']);
		$this->view_listings_days->SetDayRange($StartTime, $Days);
		
		// Set up the public frame to use the listings view
		$this->frame_public->SetTitle('Listing viewer prototype');
		$this->frame_public->SetExtraHead($extra_head);
		$this->frame_public->SetContent($this->view_listings_days);
		
		// Load the public frame view
		$this->frame_public->Load();
	}
	
	
}
?>