<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file View_listings_select_week.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Frame view for a simple academic week selector.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('view_listings');

/// Listings list view library class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Automatically loads the view_listings library.
 */
class ViewListingsSelectWeek extends ViewListings
{
	/// timestamp Time of beginning of selected week.
	private $mSelectedWeek;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct('listings/listings_select_week');
		
		$now = new Academic_time(time());
		$this->mSelectedWeek = $now->BackToMonday()->Timestamp();
	}
	
	/// Set the week which is currently selected
	/**
	 * @param $Time Academic_time Time in selected week.
	 */
	function SetSelectedWeek($Time)
	{
		$this->mSelectedWeek = $Time->BackToMonday()->Timestamp();
	}
	
	/// Set the week range to display.
	/**
	 * @param $StartTime Academic_time Start time of calendar.
	 * @param $NumWeeks integer Number of weeks to display.
	 */
	function SetRange($StartTime, $NumWeeks)
	{
		// Make sure that the time is rounded back to midnight on monday
		$StartTime = $StartTime->BackToMonday();
		
		/// @todo compensate if not in a valid academic term.
		
		$this->SetData('academic_year', $StartTime->AcademicYearName());
		$this->SetData('academic_term', $StartTime->AcademicTermName() . ' ' . $StartTime->AcademicTermTypeName());
		$this->SetData('academic_week', $StartTime->AcademicWeek());
		
		$end_time = $StartTime->Adjust($NumWeeks.'week');
		
		// Get the stuff from the db
		$this->_SetRange($StartTime, $end_time);
	}
	
	
	/// Retrieve the relevent data, ready for the view.
	/**
	 * @pre SetRange() must have already been called.
	 */
	function Retrieve()
	{
		//parent::Retrieve();
		$this->SetData('links',
			array(
				'prev_term' => 'PREV',
				'next_term' => 'NEXT',
			));
		$this->SetData('term',
			array(
				'name' => $this->mStartTime->AcademicTermName().' '.$this->mStartTime->AcademicTermTypeName(),
			));
		
		$weeks = array();
		
		for ($week = $this->mStartTime;
		     $week->Timestamp() < $this->mEndTime->Timestamp();
		     $week = $week->Adjust('1week')) {
			$weeks[] = array(
				'link' => $this->GenerateUri($week),
				'name' => 'Week '.$week->AcademicWeek(),
				'events' => 0,
				'select' => ($this->mSelectedWeek === $week->Timestamp()),
				'start_date' => $week->Format('M jS'),
			);
		}
		
		$this->SetData('weeks', $weeks);
	}
	
}

/// View_listings_select_week Library class.
/**
 * This exists because we don't want just 1 existance of ViewListingsList
 *	necessarily.
 */
class View_listings_select_week
{
	
}

?>