<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Academic_calendar.php
 * @brief Library of calendar helper functions.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * This file is very much a work in progress.
 * It'll probably get renamed in the near future.
 *
 * The YkrTime class is intended to allow views to choose what format to
 *	represent dates and times.
 *
 * The Academic_calendar class is the main library which can be loaded by a controller.
 *
 */
 
/**
 * @brief Time class.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Represents a time, which can be obtained in various formats.
 *
 * This class is intended to allow views to choose what format to
 *	represent dates and times.
 */
class YkrTime
{
	private $mTimestamp;      ///< @brief Main timestamp.
	
	private $mGregorianYear;  ///< @brief Year integer [2006...].
	private $mGregorianMonth; ///< @brief Month integer [1..12].
	private $mGregorianDate;  ///< @brief Day of month integer [1..31).
	
	private $mAcademicTerm;   ///< @brief Academic term integer [0..5].
	private $mAcademicWeek;   ///< @brief Academic term integer [1..10(or more)].
	
	private $mDayOfWeek;      ///< @brief Day of week integer [1..7] where 1=monday.
	
	private $mHours;          ///< @brief Number of hours integer [0..23].
	private $mMinutes;        ///< @brief Number of minutes integer [0..59].
	private $mSeconds;        ///< @brief Number of seconds integer [0..59].
	
	private $mTime;           ///< @brief Formatted time.
	
	/**
	 * @brief Construct a time object from a timestamp.
	 * @param $Timestamp Timestamp to initialise time object to.
	 */
	function YkrTime($Timestamp)
	{
		$this->mTimestamp = $Timestamp;
	}
	
	/**
	 * @brief Format the timestamp using the php date function.
	 * @param $Format Formatting string to use in the php date function.
	 * @return The formatted time string.
	 */
	function Format($Format)
	{
		return date($Format, $this->mTimestamp);
	}
	
	/**
	 * @brief Get the time as a timestamp.
	 * @return The time stored as a timestamp.
	 */
	function Timestamp()
	{
		return $this->mTimestamp;
	}
	
	// GREGORIAN
	/**
	 * @brief Get the year.
	 * @return The full year of the time stored, as an integer (e.g. 2006).
	 */
	function Year()
	{
		if (!isset($this->mGregorianYear))
			$this->mGregorianYear = (int)date('Y', $this->mTimestamp);
		return $this->mGregorianYear;
	}
	
	/**
	 * @brief Get the month.
	 * @return The month of the time stored, as an integer [1..12].
	 */
	function Month()
	{
		if (!isset($this->mGregorianMonth))
			$this->mGregorianMonth = (int)date('n', $this->mTimestamp);
		return $this->mGregorianMonth;
	}
	
	/**
	 * @brief Get the day of the month.
	 * @return The day of the month of the time stored, as an integer [1..31].
	 */
	function DayOfMonth()
	{
		if (!isset($this->mGregorianDate))
			$this->mGregorianDate = (int)date('j', $this->mTimestamp);
		return $this->mGregorianDate;
	}
	
	// ACADEMIC
	/**
	 * @brief Get the academic term.
	 * @return The id of the academic term of the time stored, as an integer:
	 *	- 0: Autumn Term
	 *	- 1: Christmas Holidays
	 *	- 2: Spring Term
	 *	- 3: Easter Holidays
	 *	- 4: Summer Term
	 *	- 5: Summer Holidays
	 */
	function AcademicTerm()
	{
		if (!isset($this->mAcademicTerm))
			/// @todo Implement academic term calculation
			$this->mAcademicTerm = 0;
		return $this->mAcademicTerm;
	}
	/**
	 * @brief Get the week of the academic term.
	 * @return The week number of the academic term of the time stored,
	 *	as an integer (1: week 1 etc).
	 */
	function AcademicWeek()
	{
		if (!isset($this->mAcademicWeek))
			/// @todo Implement week of academic term calculation
			$this->mAcademicWeek = 0;
		return $this->mAcademicWeek;
	}
	
	/**
	 * @brief Get the day of the week.
	 * @return The day of the week of the time stored, as an integer:
	 *	- 1: Monday
	 *	- 7: Sunday
	 */
	function DayOfWeek()
	{
		if (!isset($this->mDayOfWeek))
			$this->mDayOfWeek = (int)date('N', $this->mTimestamp);
		return $this->mDayOfWeek;
	}
	
	// TIME
	/**
	 * @brief Get the hour of the day.
	 * @return The hour of the day of the time stored, as an integer [0..23].
	 */
	function Hours()
	{
		if (!isset($this->mHours))
			$this->mHours = date('H', $this->mTimestamp);
		return $this->mHours;
	}
	
	/**
	 * @brief Get the minute of the hour.
	 * @return The minute of the hour of the time stored, as an integer [0..59].
	 */
	function Minutes()
	{
		if (!isset($this->mMinutes))
			$this->mMinutes = date('i', $this->mTimestamp);
		return $this->mMinutes;
	}
	
	/**
	 * @brief Get the second of the minute.
	 * @return The second of the minute of the time stored, as an integer [0..59].
	 */
	function Seconds()
	{
		if (!isset($this->mSeconds))
			$this->mSeconds = date('s', $this->mTimestamp);
		return $this->mSeconds;
	}
	
	// Custom
	/**
	 * @brief Get the time, formatted as appropriate.
	 * @return The time formatted as HH:MM [am/pm].
	 */
	function Time()
	{
		if (!isset($this->mTime)) {
			if ($this->IsTwentyFourHourClock())
				$this->mTime = date('H:i', $this->mTimestamp);
			else
				$this->mTime = date('g:ia', $this->mTimestamp);
		}
		return $this->mTime;
	}
	
	/**
	 * @brief Find out whether to use 24 hour times.
	 * @return Whether to use 24 hour times.
	 */
	private function IsTwentyFourHourClock()
	{
		/// @todo Get whether 24 hour clock from use preferences.
		return TRUE;
	}
}

/**
 * @brief Library of calendar helper functions.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The Academic_calendar class is the main library which can be loaded by a controller.
 *
 * Provides a number of calendar helper functions including:
 *	- converting between timestamps, gregorian, and academic calendars
 *
 * Gregorian dates could be stored in an associative array:
 *	- 'year' : year integer  (e.g. 2006)
 *	- 'month': month integer (e.g. 1: jan)
 *	- 'date' : date integer  (e.g. 1: 1st)
 *
 * Academic dates could be stored in an associative array:
 *	- 'acyear' : academic year integer (year of start of academic year)
 *	- 'year' : year integer        (of start of term)
 *	- 'term' : term integer        (e.g. 0: autumn)
 *	- 'week' : month integer       (e.g. 1: week1)
 *	- 'day'  : day of week integer (e.g. 0: sun?)
 *
 * General time structure
 *	- 'ts'   : timestamp
 *	- '
 *
 * With time included in gregorian and academic arrays:
 *	- 'hour' : hour of day      (0 - 23)
 *	- 'min'  : minute of hour   (0 - 59)
 *	- 'sec'  : second of minute (0 - 59)
 *
 * The academic term data needs to be stored in the database.
 *	DB Structure:
 *	- AcademicYear
 *		- start_term_[1-3]
 *		- num_term_weeks_[1-3]
 */
class Academic_calendar {
	/**
	 * @brief Get a time object from a timestamp.
	 * @param $Timestamp timestamp.
	 * @return Time object set to @a $Timestamp.
	 */
	function Timestamp($Timestamp)
	{
		return new YkrTime($Timestamp);
	}
	
	/**
	 * @brief Get a time object from a gregorian date & time.
	 * @param $Year Year.
	 * @param $Month Month.
	 * @param $Day Day of month.
	 * @param $Hour Hour of day.
	 * @param $Minute Minute of hour.
	 * @param $Second Second of minute.
	 * @param $IsDst Is the time in daylight saving time.
	 * @return Time object set using php mktime function.
	 */
	function Gregorian($Year,$Month,$Day,$Hour = 0,$Minute = 0,$Second = 0,$IsDst = 0)
	{
		return new YkrTime(mktime($Hour,$Minute,$Second,$Month,$Day,$Year,$IsDst));
	}
	
	/**
	 * @brief Get a time object from an academic date & time.
	 * @param $AcademicYear Year at start of academic year integer [2006..].
	 * @param $Term Term of the year integer [0..5].
	 * @param $Week Week integer [1..].
	 * @param $DayOfWeek Day of week integer [1..7].
	 * @param $Hour Hour of the day integer [0..23].
	 * @param $Minute Minute of the hour integer [0..59].
	 * @param $Second Second of the minute integer [0..59].
	 * @return Time object set academic term data.
	 */
	function Academic($AcademicYear, $Term = 0, $Week = 1, $DayOfWeek = 1, $Hour = 0, $Minute = 0, $Second = 0)
	{
		$start_of_term = $this->StartOfAcademicTerm($AcademicYear, $Term);
		return new YkrTime(strtotime(
				'+' . ($Week-1) . ' week ' .
				'+' . ($DayOfWeek-1) . 'day' .
				'+' . ($Hour) . 'hour' .
				'+' . ($Minute) . 'min' .
				'+' . ($Second) . 'sec',$start_of_term));
		return new YkrTime($start_of_term);
	}
	
	/**
	 * @brief Get the start timestamp of an academic term.
	 * @param $AcademicYear Year at start of academic year integer [2006..].
	 * @param $Term Term of the year integer [0..5].
	 * @return Timestamp of midnight on the morning of the first monday of the
	 *	specified term.
	 */
	private function StartOfAcademicTerm($AcademicYear, $Term)
	{
		/// @todo Implement StartOfAcademicTerm using academic term data.
		if ($Term === 0)
			return mktime(0,0,0,10,9,$AcademicYear);
		elseif ($Term === 1)
			return mktime(0,0,0,12,20,$AcademicYear);
		elseif ($Term === 2)
			return mktime(0,0,0,1,1,$AcademicYear+1);
		elseif ($Term === 3)
			return mktime(0,0,0,2,20,$AcademicYear+1);
		elseif ($Term === 4)
			return mktime(0,0,0,4,1,$AcademicYear+1);
		elseif ($Term === 5)
			return mktime(0,0,0,6,1,$AcademicYear+1);
		else
			return FALSE;
	}
	
	// THE FOLLOWING IS STILL BEING WORKED ON
	
	/**
	 * @brief Names of terms.
	 */
	private static $sTermNames = array(
		0 => 'autumn', 1 => 'christmas',
		2 => 'spring', 2 => 'easter',
		4 => 'summer', 5 => 'summer');
	
	/**
	 * @brief Names of term types.
	 */
	private static $sTermTypeNames = array(
		0 => 'term',   1 => 'holiday');
	
	/**
	 * @brief Create a gregorial time.
	 *
	 * @param
	 */
	/*function CreateGregorian($year, $month, $date, $hour = 0, $minute = 0, $second = 0)
	{
		return array(
			'year' => $year,
			'month'=> $month,
			'date' => $date,
			'hour' => $hour,
			'min'  => $minute,
			'sec'  => $second);
	}
	function CreateAcademic($academic_year, $academic_term, $day, $hour = 0, $minute = 0, $second = 0)
	{
		return array(
			'acyear' => $academic_year,
			'term'   => $academic_term,
			'day'    => $day,
			'hour'   => $hour,
			'min'    => $minute,
			'sec'    => $second);
	}*/
	
	/**
	 * @brief Convert a timestamp into a gregorian time.
	 *
	 * @param $timestamp Input timestamp.
	 * @return Gregorian associative array structure with time.
	 */
	function TimestampToGregorian($timestamp)
	{
		
	}
	
	/**
	 * @brief Convert a timestamp into an academic time.
	 *
	 * @param $timestamp Input timestamp.
	 * @return Academic associative array structure with time.
	 */
	function TimestampToAcademic($timestamp)
	{}
	
	/**
	 * @brief Convert a gregorian time into a timestamp.
	 *
	 * @param $gregorian Gregorian associative array structure.
	 * @return Timestamp.
	 */
	function GregorianToTimestamp($gregorian)
	{}
	
	/**
	 * @brief Convert a gregorian time into an academic time.
	 *
	 * @param $gregorian Gregorian associative array structure.
	 * @return Academic associative array structure with time if applicable.
	 */
	function GregorianToAcademic($gregorian)
	{}
	
	/**
	 * @brief Convert a academic time into a timestamp.
	 *
	 * @param $academic Academic associative array structure.
	 * @return Timestamp.
	 */
	function AcademicToTimestamp($academic)
	{}
	
	/**
	 * @brief Convert an academic time into a gregorian time.
	 *
	 * @param $academic Academic associative array structure.
	 * @return Gregorian associative array structure with time if applicable.
	 */
	function AcademicToGregorian($academic)
	{}
	
	/**
	 * @brief Get the name associated with an academic term number.
	 *
	 * @param $academic_term Academic term of the year.
	 * @return String containing term name.
	 *
	 * @pre 0 <= @a $academic_term < 6.
	 */
	function AcademicTermName($academic_term)
	{
		return $this->sTermNames[$academic_term];
	}
	
	/**
	 * @brief Get the name associated with an academic year.
	 *
	 * @param $academic_year Academic year to translate.
	 * @param $year_length Integer indicating how long each year should be.
	 *	- if @a $year_length == 2, return value might be 06/07
	 *	- if @a $year_length == 4, return value might be 2006/2007
	 * @param $separator String separator between years.
	 *
	 * @return String in the form 'Y1' . @a $separator . 'Y2' where:
	 *	- Y1 is the first year trimmed to length @a $year_length .
	 *	- Y2 is the second year trimmed to length @a $year_length .
	 */
	function AcademicYearName($academic_year, $year_length = 4, $separator = '/')
	{
		return substr($academic_year, -$year_length)
			. $separator
			. substr($academic_year+1, -$year_length);
	}
	
	/**
	 * @brief Translates a term id into an academic year.
	 *
	 * @param $term_id Integer term id (counting from autumn 2006).
	 * @return The year of the beginning of the academic year of which
	 *	@a $term_id belongs.
	 */
	private function TermIdToAcademicYear($term_id)
	{
		return 2006 + (int)($term_number / 6);
	}
	
	/**
	 * @brief Translates a term id into a year.
	 *
	 * @param $term_id Integer term id (counting from autumn 2006).
	 * @return The year at the start of the term specified by @a $term_id.
	 */
	private function TermIdToYear($term_id)
	{
		// This is 4 terms ahead of academic year
		return 2005 + (int)(($term_number+2) / 6);
	}
	
	/**
	 * @brief Translates a term id into an academic term number.
	 *
	 * @param $term_id Integer term id (counting from autumn 2006).
	 * @return The year at the start of the term specified by @a $term_id.
	 */
	private function TermIdToAcademicTerm($term_id)
	{
		return $term_number % 6;
	}
}

?>