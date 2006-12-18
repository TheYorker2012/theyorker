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
	/// Academic_time Time of beginning of selected week.
	private $mSelectedWeekStart;
	/// Academic_time Time of end of selected week.
	private $mSelectedWeekEnd;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct('listings/listings_select_week');
		
		$now = new Academic_time(time());
		$this->mSelectedWeek = $now->BackToMonday()->Timestamp();
	}
	
	/// Set the week which is currently selected
	/**
	 * @param $StartTime Academic_time Time of start of selected week.
	 * @param $EndTime Academic_time Time of end of selected week.
	 */
	function SetSelectedWeek($StartTime, $EndTime)
	{
		$this->mSelectedWeekStart = $StartTime;
		$this->mSelectedWeekEnd = $EndTime;
	}
	
	/// Set the week range to display.
	/**
	 * @param $StartTime Academic_time Start time of calendar.
	 * @param $EndTime Academic_time End time of calendar.
	 */
	function SetRange($StartTime, $EndTime)
	{
		// Make sure that the time is rounded back to midnight on monday
		$StartTime = $StartTime->BackToMonday();
		
		/// @todo compensate if not in a valid academic term.
		
		$this->SetData('academic_year', $StartTime->AcademicYearName());
		$this->SetData('academic_term', $StartTime->AcademicTermName() . ' ' . $StartTime->AcademicTermTypeName());
		$this->SetData('academic_week', $StartTime->AcademicWeek());
		
		// Get the stuff from the db
		$this->_SetRange($StartTime, $EndTime);
	}
	
	
	/// Retrieve the relevent data, ready for the view.
	/**
	 * @pre SetRange() must have already been called.
	 */
	function Retrieve()
	{
		//parent::Retrieve();
		// Calculate links to next, previous, and this whole term
		$prev_term =
				($this->mSelectedWeekStart->AcademicYear() - ($this->mSelectedWeekStart->AcademicTerm()==0 ? 1 : 0)) . '-' .
				Academic_time::GetAcademicTermNameUnique(($this->mSelectedWeekStart->AcademicTerm()+5)%6);
		$this_term =
				$this->mSelectedWeekStart->AcademicYear() . '-' .
				$this->mSelectedWeekStart->AcademicTermNameUnique();
		$next_term =
				($this->mSelectedWeekStart->AcademicYear() + ($this->mSelectedWeekStart->AcademicTerm()==5 ? 1 : 0)) . '-' .
				Academic_time::GetAcademicTermNameUnique(($this->mSelectedWeekStart->AcademicTerm()+1)%6);
		
		$this->SetData('links',
			array(
				'prev_term' => site_url($this->mUriBase.$prev_term),
				'this_term' => site_url($this->mUriBase.$this_term),
				'next_term' => site_url($this->mUriBase.$next_term),
			));
		$this->SetData('term',
			array(
				'name' => $this->mSelectedWeekStart->AcademicTermName(),
			));
		
		$weeks = array();
		
		for ($week = $this->mStartTime;
		     $week->Timestamp() < $this->mEndTime->Timestamp();
		     $week = $next_week) {
			$next_week = $week->Adjust('1week');
			$selected = ( // Selected iff the week is contained in the selection.
					$this->mSelectedWeekStart->Timestamp() <= $week->Timestamp() &&
					$this->mSelectedWeekEnd->Timestamp() >= $next_week->Timestamp()
				);
			$weeks[] = array(
				'link' => $this->GenerateUri($week,$next_week),
				'name' => $week->AcademicTermNameUnique().' '.$week->AcademicWeek(),
				'events' => 0,
				'select' => $selected,
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