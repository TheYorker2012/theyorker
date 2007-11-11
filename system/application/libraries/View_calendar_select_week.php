<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file View_calendar_select_week.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Frame view for a simple academic week selector.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('view_calendar');

/// Calendar list view library class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Automatically loads the view_calendar library.
 */
class ViewCalendarSelectWeek extends ViewCalendar
{
	/// Academic_time Time of beginning of selected week.
	private $mSelectedWeekStart;
	/// Academic_time Time of end of selected week.
	private $mSelectedWeekEnd;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/calendar_select_week');
		
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
	
	/// The term to display.
	/**
	 * @param $AcademicYear integer Academic year.
	 * @param $AcademicTerm integer Academic term number.
	 */
	function SetAcademicTerm($AcademicYear, $AcademicTerm)
	{
		$CI = &get_instance();
		$this->SetRange(
				$CI->academic_calendar->AcademicDayOfTerm(
						$AcademicYear, $AcademicTerm, 1, 0, 0, 0 ),
				$CI->academic_calendar->AcademicDayOfTerm(
						$AcademicYear, $AcademicTerm+1, 0, 0, 0, 0 )
			);
	}
	
	/// Retrieve the relevent data, ready for the view.
	/**
	 * @pre SetRange() must have already been called.
	 */
	function Retrieve()
	{
		// Don't get any events from the database right now
		//parent::Retrieve();
		
		// Calculate links to next, previous, and this whole term
		$prev_term =
				($this->mSelectedWeekStart->AcademicYear() - ($this->mSelectedWeekStart->AcademicTerm()==0 ? 1 : 0)) . '-' .
				Academic_time::GetAcademicTermNameUnique(($this->mSelectedWeekStart->AcademicTerm()+5)%6) . '-1';
		//$this_term =
		//		$this->mSelectedWeekStart->AcademicYear() . '-' .
		//		$this->mSelectedWeekStart->AcademicTermNameUnique();
		$next_term =
				($this->mSelectedWeekStart->AcademicYear() + ($this->mSelectedWeekStart->AcademicTerm()==5 ? 1 : 0)) . '-' .
				Academic_time::GetAcademicTermNameUnique(($this->mSelectedWeekStart->AcademicTerm()+1)%6) . '-1';
		
		$this->SetData('links',
			array(
				'prev_term' => site_url($this->mUriBase.$prev_term),
				'this_term' => '',//site_url($this->mUriBase.$this_term),
				'next_term' => site_url($this->mUriBase.$next_term),
			));
		$this->SetData('term',
			array(
				// The name of the term as shown above the week select
				'name' => $this->mSelectedWeekStart->AcademicTermName() .
					' ' . $this->mSelectedWeekStart->AcademicTermTypeName()/* .
					' ' . $this->mSelectedWeekStart->AcademicYearName(2)*/,
			));
		
		// Get this monday
		$CI = &get_instance();
		$monday = Academic_time::NewToday()->BackToMonday()->Timestamp();
		
		$weeks = array();
		$last_term = -1;
		for ($week = $this->mStartTime;
		     $week->Timestamp() < $this->mEndTime->Timestamp();
		     $week = $next_week) {
			// Find the end of the week
			$next_week = $week->Adjust('1week');
			if (FALSE) {
				// Titles before each new term
				if ($last_term !== $week->AcademicTerm()) {
					$weeks[] = array(
						'link' => site_url(
								$this->mUriBase . $week->AcademicYear() . '-' .
								$week->AcademicTermNameUnique()
							),
						'name' => strtoupper(
								$week->AcademicTermName() . ' ' .
								$week->AcademicTermTypeName() . ' ' .
								$week->AcademicYearName()
							),
						'events' => 0,
						'select' => FALSE,
						'old' => FALSE,
						'heading' => TRUE,
						'start_date' => '',
					);
					$last_term = $week->AcademicTerm();
				}
			}
			$old = $week->Timestamp() < $monday; // Is the week in the past?
			$selected = ( // Selected iff the week is contained in the selection.
					$this->mSelectedWeekStart->Timestamp() <= $week->Timestamp() &&
					$this->mSelectedWeekEnd->Timestamp() >= $next_week->Timestamp()
				);
			$weeks[] = array(
				'link' => $this->GenerateUri($week,$next_week),
				'name' => 'Week '.$week->AcademicWeek(),
				'events' => 0,
				'select' => $selected,
				'old' => $old,
				'heading' => FALSE,
				//'start_date' => $week->Format('M jS'),
			);
		}
		
		$this->SetData('weeks', $weeks);
	}
	
}

/// View_calendar_select_week Library class.
/**
 * This exists because we don't want just 1 existance of ViewCalendarList
 *	necessarily.
 */
class View_calendar_select_week
{
	
}

?>