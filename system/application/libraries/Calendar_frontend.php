<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_frontend.php
 * @brief Front end of calendar framework.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library frames)
 *
 * Calendar data processing and display classes.
 *
 * @version 29-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Main calendar display class.
abstract class CalendarView extends FramesView
{
	/// array[category] Stored array of categories.
	private $mCategories = NULL;
	/// CalendarData Stored calendar data.
	private $mData = NULL;
	
	/// timestamp Start time of range of events to fetch.
	protected $mStartTime = NULL;
	/// timestamp End time of range of events to fetch.
	protected $mEndTime = NULL;
	/// array Range url information.
	private $mRangeUrl = NULL;

	/// Primary constructor.
	/**
	 * @param $ViewFile string Path of the view file to use.
	 */
	function __construct($ViewFile)
	{
		parent::__construct($ViewFile);
	}
	
	/// Set the categories to use.
	/**
	 * @param $Categories array[category] Array of categories.
	 */
	function SetCategories($Categories)
	{
		$this->mCategories = $Categories;
	}
	
	/// Set the actual calendar data to use.
	/**
	 * @param $Data CalendarData Calendar data.
	 */
	function SetCalendarData(&$Data)
	{
		$this->mData = &$Data;
	}
	
	/// Set start and end time of range
	/**
	 * @
	 */
	function SetStartEnd($Start, $End)
	{
		$this->mStartTime = $Start;
		$this->mEndTime = $End;
	}
	
	/// Set the range url information.
	/**
	 * @param $Prefix string
	 * @param $Format string
	 * @param $Postfix string
	 */
	function SetRangeUrl($Prefix, $Format, $Postfix)
	{
		$this->mRangeUrl = array($Prefix, $Format, $Postfix);
	}
	
	/// Generate a range url.
	/**
	 * @param $Start Academic_time
	 * @param $End Academic_time
	 */
	function GenerateRangeUrl($Start, $End)
	{
		assert('NULL !== $this->mRangeUrl');
		$CI = & get_instance();
		return (
			$this->mRangeUrl[0].
			$CI->date_uri->GenerateUri($this->mRangeUrl[1], $Start, $End).
			$this->mRangeUrl[2]
		);
	}
	
	/// Process the calendar data to produce view data.
	/**
	 * @param $Data CalendarData Calendar data.
	 * @param $Categories array[category] Array of categories.
	 *
	 * This should be the data which is specific to the view.
	 * General data such as day information should be calculated then passed in.
	 */
	protected abstract function ProcessEvents(&$Data, $Categories);
	
	/// Load the view.
	function Load()
	{
		/// Process the data before loading
		$this->ProcessEvents($this->mData, $this->mCategories);
		
		/// Make some links
		if (NULL !== $this->mRangeUrl && 
			NULL !== $this->mStartTime &&
			NULL !== $this->mEndTime)
		{
			$days = Academic_time::DaysBetweenTimestamps($this->mStartTime, $this->mEndTime);
			$start = new Academic_time($this->mStartTime);
			$end   = new Academic_time($this->mEndTime);
			$now   = new Academic_time(time());
			
			$try_again = TRUE;
			if (0 === $start->AcademicDay() &&
				0 === $end->AcademicDay())
			{
				$CI = & get_instance();
				$terms_apart =
					($end->AcademicYear() - $start->AcademicYear())*6 +
					$end->AcademicTerm() - $start->AcademicTerm();
				// don't really need to scroll in years
				if (FALSE && $terms_apart >= 6) {
					$this->SetData('ForwardUrl', $this->GenerateRangeUrl(
						$CI->academic_calendar->Academic(
							$start->AcademicYear() + 1,
							$start->AcademicTerm(),
							$start->AcademicWeek()
						),
						$CI->academic_calendar->Academic(
							$end->AcademicYear() + 1,
							$end->AcademicTerm(),
							$end->AcademicWeek()
						)
					));
					$this->SetData('BackwardUrl', $this->GenerateRangeUrl(
						$CI->academic_calendar->Academic(
							$start->AcademicYear() - 1,
							$start->AcademicTerm(),
							$start->AcademicWeek()
						),
						$CI->academic_calendar->Academic(
							$end->AcademicYear() - 1,
							$end->AcademicTerm(),
							$end->AcademicWeek()
						)
					));
					$try_again = FALSE;
				} elseif ($terms_apart > 0) {
					$this->SetData('ForwardUrl', $this->GenerateRangeUrl(
						$CI->academic_calendar->Academic(
							$start->AcademicYear() + (5 === $start->AcademicTerm() ? 1 : 0),
							($start->AcademicTerm() + 1)%6,
							$start->AcademicWeek()
						),
						$CI->academic_calendar->Academic(
							$end->AcademicYear() + (5 === $start->AcademicTerm() ? 1 : 0),
							($end->AcademicTerm() + 1)%6,
							$end->AcademicWeek()
						)
					));
					$this->SetData('BackwardUrl', $this->GenerateRangeUrl(
						$CI->academic_calendar->Academic(
							$start->AcademicYear() - (0 === $start->AcademicTerm() ? 1 : 0),
							($start->AcademicTerm() + 5)%6,
							$start->AcademicWeek()
						),
						$CI->academic_calendar->Academic(
							$end->AcademicYear() - (0 === $start->AcademicTerm() ? 1 : 0),
							($end->AcademicTerm() + 5)%6,
							$end->AcademicWeek()
						)
					));
					$try_again = FALSE;
				}
				$this->SetData('NowUrl', $this->GenerateRangeUrl(
					$CI->academic_calendar->Academic(
						$now->AcademicYear(),
						$now->AcademicTerm(),
						1
					),
					$CI->academic_calendar->Academic(
						$now->AcademicYear() + (5 === $start->AcademicTerm() ? 1 : 0),
						($now->AcademicTerm() + 1)%6,
						1
					)
				));
				$this->SetData('NowUrlLabel', 'This term');
			}
			if ($try_again) {
				$now = $now->Midnight();
				if ($days >= 7) {
					$forward_jump = '1week';
					$now = $now->BackToMonday();
					$this->SetData('NowUrlLabel', 'This week');
				} else {
					$forward_jump = '1day';
					$this->SetData('NowUrlLabel', 'Today');
				}
				$this->SetData('ForwardUrl', $this->GenerateRangeUrl(
					$start->Adjust($forward_jump),
					$end->Adjust($forward_jump)
				));
				$this->SetData('BackwardUrl', $this->GenerateRangeUrl(
					$start->Adjust('-'.$forward_jump),
					$end->Adjust('-'.$forward_jump)
				));
				$this->SetData('NowUrl', $this->GenerateRangeUrl(
					$now,
					$now->Adjust('+'.$forward_jump)
				));
			}
		}
		
		parent::Load();
	}
}

/// Dummy class
class Calendar_frontend
{
	/// @todo Get days in range etc.
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->library('date_uri');
	}
}

?>