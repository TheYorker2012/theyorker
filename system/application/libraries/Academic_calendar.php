<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Academic_calendar.php
 * @brief Library of calendar helper functions.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * This file is very much a work in progress.
 * It'll probably get renamed in the near future.
 *
 * The Academic_time class is intended to allow views to choose what format to
 *	represent dates and times.
 *
 * The Academic_calendar class is the main library which can be loaded by a controller.
 *
 * Academic calendaring calculations are performed in an internal timezone,
 *	(currently set to 'Europe/London'), externally however all dates are
 *	represented in the default timezone (see php date_default_timezone_get()).
 * This is mostly only significant because of the clocks changing in april and
 *	october.
 */
 
/**
 * @brief Time class with academic calendar capabilities.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Represents a time, which can be obtained in various formats.
 *
 * This class is intended to allow views to choose what format to
 *	represent dates and times.
 *
 */
class Academic_time
{
	// Time information (cached in other forms)
	private $mTimestamp;      ///< @brief Main timestamp.
	
	private $mGregorianYear;  ///< @brief Year integer [2006...].
	private $mGregorianMonth; ///< @brief Month integer [1..12].
	private $mGregorianDate;  ///< @brief Day of month integer [1..31).
	
	private $mAcademicYear;   ///< @brief Year at start of academic year [2006...].
	private $mAcademicTerm;   ///< @brief Academic term integer [0..5].
	private $mAcademicWeek;   ///< @brief Academic term integer [1..10(or more)].
	private $mAcademicDay;    ///< @brief Day of the academic term.
	
	private $mDayOfWeek;      ///< @brief Day of week integer [1..7] where 1=monday.
	
	private $mHours;          ///< @brief Number of hours integer [0..23].
	private $mMinutes;        ///< @brief Number of minutes integer [0..59].
	private $mSeconds;        ///< @brief Number of seconds integer [0..59].
	
	private $mTime;           ///< @brief Formatted time.
	
	
	/**
	 * @brief Associative cache of academic year data.
	 * @see GetAcademicYearData
	 */
	private static $sAcademicYears = array();
	
	/**
	 * @brief Internal timezone for use in academic term calculation.
	 *
	 * E.g. when calculating day of term, uses day of year which depends on
	 * timezone.
	 */
	private static $sInternalTimezone = 'Europe/London';
	
	/**
	 * @brief Names of terms.
	 */
	private static $sTermNames = array(
		0 => 'autumn', 1 => 'christmas',
		2 => 'spring', 3 => 'easter',
		4 => 'summer', 5 => 'summer',
	);
	
	/**
	 * @brief Short names of terms.
	 */
	private static $sUniqueTermNames = array(
		0 => 'autumn', 1 => 'christmas',
		2 => 'spring', 3 => 'easter',
		4 => 'summer', 5 => 'holiday',
	);
	
	/**
	 * @brief Names of term types.
	 */
	private static $sTermTypeNames = array(
		0 => 'term',   1 => 'holiday');
	
	/**
	 * @brief Term name to term number translation array.
	 */
	private static $sTermTranslation = array(
		0           => 0,
		'autumn'    => 0,
		'au'        => 0,
		
		1           => 1,
		'christmas' => 1,
		'xmas'      => 1,
		
		2           => 2,
		'spring'    => 2,
		'sp'        => 2,
		
		3           => 3,
		'easter'    => 3,
		
		4           => 4,
		'summer'    => 4,
		'su'        => 4,
		
		5           => 5,
		'holiday'   => 5,
	);
	
	/**
	 * @brief Construct a time object from a timestamp.
	 * @param $Timestamp Timestamp to initialise time object to.
	 */
	function __construct($Timestamp)
	{
		$this->mTimestamp = $Timestamp;
	}
	
	/**
	 * @brief Create a time object set to midnight this morning.
	 * @return academic_time Midnight this morning.
	 */
	static function NewToday()
	{
		return new Academic_time(strtotime(date('d M Y',time())));
	}
	
	/**
	 * @brief Create a time object set to midnight of the same day.
	 * @return academic_time Midnight of same day as the timestamp.
	 */
	function Midnight()
	{
		return new Academic_time(strtotime(date('d M Y',$this->mTimestamp)));
	}
	
	/**
	 * @brief Create a time object set to midnight on monday (backwards).
	 * @return academic_time Midnight of monday as the timestamp.
	 */
	function BackToMonday()
	{
		return $this->Midnight()->Adjust((-($this->mDayOfWeek)+1).'day');
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
	 * @brief Adjust the timestamp using php strtotime.
	 * @param $Difference Time difference text.
	 * @return Academic_time initialised with strtotime(@a $Difference, @a this).
	 */
	function Adjust($Difference)
	{
		return new Academic_time(strtotime($Difference,$this->mTimestamp));
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
		if (!isset($this->mGregorianYear)) {
			$this->mGregorianYear = (int)date('Y', $this->mTimestamp);
		}
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
	 * @brief Get the academic year.
	 * @return The year at the start of the academic year.
	 */
	function AcademicYear()
	{
		if (!isset($this->mAcademicYear)) {
			$academic_year_start = self::StartOfAcademicTerm($this->Year());
			if ($this->mTimestamp >= $academic_year_start)
				$this->mAcademicYear = $this->mGregorianYear;
			else
				$this->mAcademicYear = $this->mGregorianYear-1;
		}
		return $this->mAcademicYear;
	}
	 
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
		if (!isset($this->mAcademicTerm)) {
			// get the term data for the $this->AcademicYear()
			// go through to find out which term we're in
			$academic_year_data = self::GetAcademicYearData($this->AcademicYear());
			if (FALSE === $academic_year_data) {
				// No records about the specified academic year exist!
				$error_message = 'Unknown academic year: ' .
						$AcademicYear .
						'provided to Academic_time::AcademicTerm';
				throw new Exception($error_message);
				
			} else {
				$this->mAcademicTerm = 5;
				// Records exist, see where the timestamp fits in
				// (no point doing binary search on just 6 items)
				for ($term_counter = 0; $term_counter <= 4; ++$term_counter) {
					// If the date is before the end of the term, its in the term.
					if ($this->mTimestamp < $academic_year_data['term_start'][$term_counter+1]) {
						$this->mAcademicTerm = $term_counter;
						break;
					}
				}
			}
			
		}
		return $this->mAcademicTerm;
	}
	
	/**
	 * @brief Get the week of the academic term.
	 * @return The week number of the academic term of the time stored,
	 *	as an integer (1: week 1 etc).
	 */
	function AcademicWeek()
	{
		if (!isset($this->mAcademicWeek)) {
			// get the start of the academic term
			// find out how many weeks have elapsed
			$monday_week1 = self::MondayWeek1OfAcademicTerm(
					$this->AcademicYear(),
					$this->AcademicTerm());
			$days_in_between = self::DaysBetweenTimestamps($monday_week1, $this->mTimestamp);
			$this->mAcademicWeek = (int)($days_in_between/7)+1;
		}
		return $this->mAcademicWeek;
	}
	
	/**
	 * @brief Get the day of the academic term.
	 * @return integer The number of days since the start of the academic term
	 *	of the time (0: first day of term).
	 */
	function AcademicDay()
	{
		if (!isset($this->mAcademicDay)) {
			// get the start of the academic term
			// find out how many weeks have elapsed
			$start_of_term = self::StartOfAcademicTerm($this->AcademicYear(), $this->AcademicTerm());
			$this->mAcademicDay = self::DaysBetweenTimestamps($start_of_term, $this->mTimestamp);
		}
		return $this->mAcademicDay;
	}
	
	/**
	 * @brief Get the day of the week.
	 * @return The day of the week of the time stored, as an integer:
	 *	- 1: Monday
	 *	- 7: Sunday
	 */
	function DayOfWeek()
	{
		if (!isset($this->mDayOfWeek)) {
			$this->mDayOfWeek = (int)date('N', $this->mTimestamp);
		}
		return $this->mDayOfWeek;
	}
	
	// TIME
	/**
	 * @brief Get the hour of the day.
	 * @return The hour of the day of the time stored, as an integer [0..23].
	 */
	function Hour()
	{
		if (!isset($this->mHours))
			$this->mHours = date('H', $this->mTimestamp);
		return $this->mHours;
	}
	
	/**
	 * @brief Get the minute of the hour.
	 * @return The minute of the hour of the time stored, as an integer [0..59].
	 */
	function Minute()
	{
		if (!isset($this->mMinutes))
			$this->mMinutes = date('i', $this->mTimestamp);
		return $this->mMinutes;
	}
	
	/**
	 * @brief Get the second of the minute.
	 * @return The second of the minute of the time stored, as an integer [0..59].
	 */
	function Second()
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
			if (self::IsTwentyFourHourClock())
				$this->mTime = date('H:i', $this->mTimestamp);
			else
				$this->mTime = date('g:ia', $this->mTimestamp);
		}
		return $this->mTime;
	}
	
	/**
	 * @brief Get the name associated with the academic year.
	 *
	 * @param $YearLength Integer indicating how long each year should be.
	 *	- if @a $YearLength == 2, return value might be 06/07
	 *	- if @a $YearLength == 4, return value might be 2006/2007
	 * @param $Separator String separator between years.
	 *
	 * @return String in the form 'Y1' . @a $Separator . 'Y2' where:
	 *	- Y1 is the first year trimmed to length @a $YearLength .
	 *	- Y2 is the second year trimmed to length @a $YearLength .
	 */
	function AcademicYearName($YearLength = 4, $Separator = '/')
	{
		$academic_year = $this->AcademicYear();
		return substr($academic_year, -$YearLength) .
			$Separator .
			substr($academic_year+1, -$YearLength);
	}
	
	/**
	 * @brief Get the unique name associated with the academic term.
	 * @return String containing term name.
	 */
	function AcademicTermNameUnique()
	{
		return self::$sUniqueTermNames[$this->AcademicTerm()];
	}
	
	/**
	 * @brief Get the name associated with the academic term.
	 * @return String containing term name.
	 */
	function AcademicTermName()
	{
		return self::$sTermNames[$this->AcademicTerm()];
	}
	
	/**
	 * @brief Get the name associated with the term type.
	 * @return String containing term type name.
	 */
	function AcademicTermTypeName()
	{
		return self::$sTermTypeNames[$this->AcademicTerm() % 2];
	}
	
	/**
	 * @brief Translate a string term name.
	 * @return integer Term index or FALSE if unknown.
	 * @see Academic_time::$sTermTranslation
	 */
	static function TranslateAcademicTermName($Term)
	{
		if (array_key_exists($Term, self::$sTermTranslation)) {
			return self::$sTermTranslation[$Term];
		}
		return false;
	}
	
	/**
	 * @brief Find out whether the time is in term time.
	 * @return Boolean whether in term time.
	 */
	function IsTermTime()
	{
		return 0 === ($this->AcademicTerm() % 2);
	}
	
	/**
	 * @brief Find out whether the time is in holday.
	 * @return Boolean whether in holday.
	 */
	function IsHoliday()
	{
		return 1 === ($this->AcademicTerm() % 2);
	}
	
	
	// Static
	/**
	 * @brief Get the internal timezone.
	 * @return The internal timezone in which academic calendar calculations are
	 *	performed.
	 */
	static function InternalTimezone()
	{
		return self::$sInternalTimezone;
	}
	
	/**
	 * @brief Performs php strtotime as internal timezone.
	 * @param $time The string to parse, according to the GNU Date Input
	 *	Formats syntax.
	 * @param $now The timestamp used to calculate the returned value.
	 * @return Returns a timestamp on success, FALSE otherwise.
	 * @see @link http://uk2.php.net/manual/en/function.strtotime.php
	 */
	static function InternalStrToTime($time, $now)
	{
		// Change to local timezone for this calculation
		$prev_tz = date_default_timezone_get();
		date_default_timezone_set(self::InternalTimezone());
		
		$result = strtotime($time,$now);
		
		// Reset timezone before returning
		date_default_timezone_set($prev_tz);
		return $result;
	}
	
	/**
	 * @brief Find the number of days between close timestamps.
	 * @param $FirstTimestamp Earlier timestamp;
	 * @param $SecondTimestamp Later timestamp;
	 * @return integer The number of days between two timestamps.
	 *
	 * @pre @a $FirstTimestamp <= @a $SecondTimestamp
	 * @pre @a $FirstTimestamp and @a $SecondTimestamp are less than a 365 days apart.
	 */
	static function DaysBetweenTimestamps($FirstTimestamp, $SecondTimestamp)
	{
		// Change to local timezone for this calculation
		$prev_tz = date_default_timezone_get();
		date_default_timezone_set(self::InternalTimezone());
		// Find difference in day of year.
		$day_of_year_of_first = (int)date('z',$FirstTimestamp);
		$day_of_year_of_second = (int)date('z',$SecondTimestamp);
		$difference = $day_of_year_of_second-$day_of_year_of_first;
		if ($difference < 0) {
			// $SecondTimestamp is in the year after $FirstTimestamp
			// dif = doy2 + diy(1)+1-doy1
			// dif = dif + diy(1)
			$difference += 365 + (int)date('L',$FirstTimestamp);
		}
		// Reset timezone before returning
		date_default_timezone_set($prev_tz);
		return $difference;
	}
	
	/**
	 * @brief Find out whether to use 24 hour times.
	 * @return Whether to use 24 hour times.
	 */
	private static function IsTwentyFourHourClock()
	{
		/// @todo jh559: Get whether to use 24 hour clock from user preferences.
		return TRUE;
	}
	
	/**
	 * @brief Get the start timestamp of an academic term.
	 * @param $AcademicYear Year at start of academic year integer [2006..].
	 * @param $Term Term of the year integer [0..5].
	 * @return Timestamp of midnight on the first day of the specified term.
	 * @pre 0 <= @a $Term < 6.
	 */
	static function StartOfAcademicTerm($AcademicYear, $Term = 0)
	{
		$academic_year_data = self::GetAcademicYearData($AcademicYear);
		if (FALSE === $academic_year_data) {
			// No records about the specified academic year exist!
			$error_message = 'Unknown academic year: ' .
					$AcademicYear .
					'provided to Academic_time::StartOfAcademicTerm';
			throw new Exception($error_message);
			
		} elseif (array_key_exists($Term, $academic_year_data['term_start'])) {
			// The records exist and $Term is valid.
			return $academic_year_data['term_start'][$Term];
			
		} else {
			// The records exist but $Term is invalid.
			$error_message = 'Invalid $Term: ' .
					$Term .
					'provided to Academic_time::StartOfAcademicTerm';
			throw new Exception($error_message);
		}
	}
	
	/**
	 * @brief Get the day of the week of the start of an academic term.
	 * @param $AcademicYear Year at start of academic year integer [2006..].
	 * @param $Term Term of the year integer [0..5].
	 * @return The day of the week of StartOfAcademicTerm (0=monday to 6=sunday)
	 * @pre 0 <= @a $Term < 6.
	 */
	static function DayOfStartOfAcademicTerm($AcademicYear, $Term = 0)
	{
		$academic_year_data = self::GetAcademicYearData($AcademicYear);
		if (FALSE === $academic_year_data) {
			// No records about the specified academic year exist!
			$error_message = 'Unknown academic year: ' .
					$AcademicYear .
					'provided to Academic_time::DayOfStartOfAcademicTerm';
			throw new Exception($error_message);
			
		} elseif (array_key_exists($Term, $academic_year_data['term_start_day'])) {
			// The records exist and $Term is valid.
			return $academic_year_data['term_start_day'][$Term];
			
		} else {
			// The records exist but $Term is invalid.
			$error_message = 'Invalid $Term: ' .
					$Term .
					'provided to Academic_time::DayOfStartOfAcademicTerm';
			throw new Exception($error_message);
		}
	}
	
	/**
	 * @brief Get the timestamp of monday week 1 of an academic term.
	 * @param $AcademicYear Year at start of academic year integer [2006..].
	 * @param $Term Term of the year integer [0..5].
	 * @return Timestamp of midnight on the monday of week 1 of the specified
	 *	term. Note that this will be before the start of the term if the term
	 *	begins on a day other than monday.
	 * @pre 0 <= @a $Term < 6.
	 */
	static function MondayWeek1OfAcademicTerm($AcademicYear, $Term = 0)
	{
		$academic_year_data = self::GetAcademicYearData($AcademicYear);
		if (FALSE === $academic_year_data) {
			// No records about the specified academic year exist!
			$error_message = 'Unknown academic year: ' .
					$AcademicYear .
					'provided to Academic_time::MondayWeek1OfAcademicTerm';
			throw new Exception($error_message);
			
		} elseif (array_key_exists($Term, $academic_year_data['term_monday_week1'])) {
			// The records exist and $Term is valid.
			return $academic_year_data['term_monday_week1'][$Term];
			
		} else {
			// The records exist but $Term is invalid.
			$error_message = 'Invalid $Term: ' .
					$Term .
					'provided to Academic_time::MondayWeek1OfAcademicTerm';
			throw new Exception($error_message);
		}
	}
	
	/**
	 * @brief Get the length of an academic term in days.
	 * @param $AcademicYear Year at start of academic year integer [2006..].
	 * @param $Term Term of the year integer [0..5].
	 * @return The length of the academic term measured in days.
	 * @pre 0 <= @a $Term < 6.
	 */
	static function LengthOfAcademicTerm($AcademicYear, $Term = 0)
	{
		$academic_year_data = self::GetAcademicYearData($AcademicYear);
		if (FALSE === $academic_year_data) {
			// No records about the specified academic year exist!
			$error_message = 'Unknown academic year: ' .
					$AcademicYear .
					'provided to Academic_time::LengthOfAcademicTerm';
			throw new Exception($error_message);
			
		} elseif (array_key_exists($Term, $academic_year_data['term_days'])) {
			// The records exist and $Term is valid.
			return $academic_year_data['term_days'][$Term];
			
		} else {
			// The records exist but $Term is invalid.
			$error_message = 'Invalid $Term: ' .
					$Term .
					'provided to Academic_time::LengthOfAcademicTerm';
			throw new Exception($error_message);
		}
	}
	
	/**
	 * @brief Find whether an academic term is valid (or known about).
	 * @param $AcademicYear Year at start of academic year integer [2006..].
	 * @param $Term Term of the year integer [0..5].
	 * @return bool Whether the specified academic term is valid.
	 * @pre 0 <= @a $Term < 6.
	 */
	static function ValidateAcademicTerm($AcademicYear, $Term = 0)
	{
		$academic_year_data = self::GetAcademicYearData($AcademicYear);
		if (FALSE === $academic_year_data) {
			return FALSE;
			
		} elseif (array_key_exists($Term, $academic_year_data['term_days'])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * @brief Get the term dates of an academic year.
	 * @param $AcademicYear integer Year of start of academic year.
	 * @return FALSE if @a $AcademicYear is unknown,
	 *	or an array of academic year data formatted as follows:
	 *	- 'year': year integer (e.g. 2006)
	 *	- 'term_start': array of timestamps:
	 *		- 0: First day of autumn term       (midnight)
	 *		- 1: First day of christmas holiday (midnight)
	 *		- 2: First day of spring term       (midnight)
	 *		- 3: First day of easter holiday    (midnight)
	 *		- 4: First day of summer term       (midnight)
	 *		- 5: First day of summer holiday    (midnight)
	 *	- 'term_start_day': array of integer representing the day of the week
	 *		of the first day of each term (0 = monday, 6 = sunday).
	 *	- 'term_monday_week1': array of timestamps where each element is:
	 *		'term_start' - 'term_Start_Day' days
	 *	- 'term_days': array of integers:
	 *		- 0: Number of days in autumn term
	 *		- 1: Number of days in spring term
	 *		- 2: Number of days in summer term
	 *
	 * @see http://www.york.ac.uk/admin/po/terms.htm
	 */ 
	private static function GetAcademicYearData($AcademicYear)
	{
		if (!array_key_exists($AcademicYear, self::$sAcademicYears)) {
			// The academic year hasn't been cached, so do it now
			
			/**
			 * @todo jh559: Implement GetAcademicYearData using data from db.
			 * The academic term data needs to be stored in the database:
 			 *	DB Structure:
			 *	- AcademicYear
			 *		- start_term_[1-6] -- could be timestamp or week number
			 *			(since this must be midnight on a monday morning)
			 *		- num_term_weeks_[1-6]
			 */
			$year_data = array(
					'year' => $AcademicYear,
					'term_days' => array(
							0 => 10*7, 1 => 3*7,
							2 => 10*7, 3 => 5*7,
							4 => 10*7, 5 => 14*7),
					'term_start_day' => array(),
					'term_monday_week1' => array(),
					);
			$prev_tz = date_default_timezone_get();
			date_default_timezone_set(self::InternalTimezone());
			// Hardwire the term dates:
			if (2004 == $AcademicYear) {
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10,11, 2004),
						2 => mktime(0,0,0,  1,10, 2005),
						4 => mktime(0,0,0,  4,25, 2005));
			} elseif (2005 == $AcademicYear) {
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10,10, 2005),
						2 => mktime(0,0,0,  1, 9, 2006),
						4 => mktime(0,0,0,  4,24, 2006));
			} elseif (2006 == $AcademicYear) {
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10, 9, 2006),
						2 => mktime(0,0,0,  1, 8, 2007),
						4 => mktime(0,0,0,  4,23, 2007));
			} elseif (2007 == $AcademicYear) {
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10, 8, 2007),
						2 => mktime(0,0,0,  1, 7, 2008),
						4 => mktime(0,0,0,  4,21, 2008));
				$year_data['term_days'][5] = 15*7; // Slightly longer summer
			} elseif (2008 == $AcademicYear) {
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10,13, 2008),
						2 => mktime(0,0,0,  1,12, 2009),
						4 => mktime(0,0,0,  4,27, 2009));
			} elseif (2009 == $AcademicYear) {
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10,12, 2009),
						2 => mktime(0,0,0,  1,11, 2010),
						4 => mktime(0,0,0,  4,26, 2010));
			} elseif (2010 == $AcademicYear) {
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10,11, 2010),
						2 => mktime(0,0,0,  1,10, 2011),
						4 => mktime(0,0,0,  4,27, 2011));
				$year_data['term_days'][3] += 2; // Slightly longer easter
				$year_data['term_days'][4] -= 2; // Slightly shorter summer term
			} elseif (2011 == $AcademicYear) {
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10,10, 2011),
						2 => mktime(0,0,0,  1, 9, 2012),
						4 => mktime(0,0,0,  4,23, 2012));
			} elseif (2012 == $AcademicYear) {
				// Mostly unknown:
				$year_data['term_start'] = array(
						0 => mktime(0,0,0, 10, 8, 2012),
						2 => mktime(0,0,0,  1, 9, 2013),
						4 => mktime(0,0,0,  4,23, 2013));
			} else {
				// The year in question is invalid
				// (cause it ain't yet implemented)
				date_default_timezone_set($prev_tz);
				return FALSE;
			}
			// Calculate holiday start dates
			for ($term_counter = 0; $term_counter < 6; $term_counter += 2) {
				// Holiday start dates
				$year_data['term_start'][$term_counter + 1] = strtotime(
						'+' . $year_data['term_days'][$term_counter] . ' days',
						$year_data['term_start'][$term_counter]);
			}
			for ($term_counter = 0; $term_counter < 6; ++$term_counter) {
				// Day of week of start of term
				$year_data['term_start_day'][$term_counter] =
						(int)date('N', $year_data['term_start'][$term_counter])-1; // 0-6
				// Timestamp of monday week 1 (possibly before start of term
				$year_data['term_monday_week1'][$term_counter] =
						strtotime((-$year_data['term_start_day'][$term_counter]) . 'days',
								  $year_data['term_start'][$term_counter]);
			}
			date_default_timezone_set($prev_tz);
			// Cache the result
			self::$sAcademicYears[$AcademicYear] = $year_data;
			
		}
		// The academic year should now have been cached
		return self::$sAcademicYears[$AcademicYear];
	}
}

/**
 * @brief Library of calendar helper functions.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The Academic_calendar class is the main library (which can be loaded by a
 * controller).
 *
 * Create a time object (Academic_time) using one of the time creators:
 *	- Timestamp: from a timestamp.
 *	- Gregorian: from a gregorian date (year, month, day, hour, minute, second).
 *	- Academic: from an academic date (academic year, term, week, day of week, etc.)
 */
class Academic_calendar {

	
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		// Nothing to do yet
	}


	/**
	 * @brief Get a time object from a timestamp.
	 * @param $Timestamp timestamp.
	 * @return Academic_time object set to @a $Timestamp.
	 */
	function Timestamp($Timestamp)
	{
		return new Academic_time($Timestamp);
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
	 * @return Academic_time object set using php mktime function.
	 *
	 * @note Inputs are considered to be in the current default timezone.
	 */
	function Gregorian($Year,$Month,$Day,$Hour = 0,$Minute = 0,$Second = 0,$IsDst = -1)
	{
		return new Academic_time(mktime($Hour,$Minute,$Second,$Month,$Day,$Year,$IsDst));
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
	 * @return Academic_time object set with academic term data.
	 *
	 * @note Inputs are considered to be in the internal timezone
	 *	(as returned by Academic_time::InternalTimezone).
	 */
	function Academic($AcademicYear, $Term = 0, $Week = 1, $DayOfWeek = 1, $Hour = 0, $Minute = 0, $Second = 0)
	{
		$start_of_term = Academic_time::MondayWeek1OfAcademicTerm($AcademicYear, $Term);
		return new Academic_time(Academic_time::InternalStrToTime(
				($Week-1) . 'week' .
				($DayOfWeek-1) . 'day' .
				($Hour) . 'hour' .
				($Minute) . 'min' .
				($Second) . 'sec',$start_of_term));
	}
	
	/**
	 * @brief Get a time object from an academic date & time.
	 * @param $AcademicYear Year at start of academic year integer [2006..].
	 * @param $Term Term of the year integer [0..5].
	 * @param $DayOfTerm Day of the term integer [0..].
	 * @param $Hour Hour of the day integer [0..23].
	 * @param $Minute Minute of the hour integer [0..59].
	 * @param $Second Second of the minute integer [0..59].
	 * @return Academic_time object set with academic term data.
	 *
	 * @note Inputs are considered to be in the internal timezone
	 *	(as returned by Academic_time::InternalTimezone).
	 */
	function AcademicDayOfTerm($AcademicYear, $Term = 0, $DayOfTerm = 0, $Hour = 0, $Minute = 0, $Second = 0)
	{
		$monday_week1 = Academic_time::StartOfAcademicTerm($AcademicYear, $Term);
		return new Academic_time(Academic_time::InternalStrToTime(
				($DayOfTerm) . 'day' .
				($Hour) . 'hour' .
				($Minute) . 'min' .
				($Second) . 'sec',$monday_week1));
	}
	
	/**
	 * @brief Perform tests on the academic calendar functions.
	 * @return The number of errors detected.
	 *
	 * Runs through every day in the academic calendar checking that the term
	 * number and week number functions are correct and that days are
	 * consecutive when calculated using Academic_calendar::Academic.
	 */
	function PerformTests()
	{
		// Store the previous date so can see how many days have elapsed
		$prev_date = 0;
		$errors = 0;
		// Go through academic years
		for ($year = 2004; $year < 2012; ++$year) {
			// Go through all 6 academic terms
			for ($term_counter = 0; $term_counter < 6; ++$term_counter) {
				// Go through every week in the term
				$term_days = Academic_time::LengthOfAcademicTerm($year, $term_counter);
				$term_day = 0;
				$dow_counter = Academic_time::DayOfStartOfAcademicTerm($year, $term_counter) + 1;
				for ($week_counter = 1; $term_day < $term_days; ++$week_counter) {
					// Go through every day in the week
					for ($dow_counter = $dow_counter; $dow_counter <= 7 && $term_day < $term_days; ++$dow_counter) {
						// Create a date object from academic year/term/week/day_of_week
						$actime = $this->Academic(
								$year, $term_counter,
								$week_counter, $dow_counter);
						// Create a date object from academic year/term/day_of_term
						$actime2= $this->AcademicDayOfTerm(
								$year, $term_counter,
								$term_day);
						// Detect any inconsistencies
						$error_detected = FALSE;
						// Do Academic and AcademicDayOfTerm give the same result?
						if ($actime != $actime2) {
							echo '!!Academic and AcademicDayOfTerm give different results!!<br/>';
							++$errors;
							$error_detected = TRUE;
						}
						// Has more than one day elapsed?
						if (0 !== $prev_date &&
							1 !== Academic_time::DaysBetweenTimestamps(
									$prev_date->Timestamp(),
									$actime->Timestamp())) {
							echo '!!days not consecutive!!<br/>';
							++$errors;
							$error_detected = TRUE;
						}
						// Is the calculated academic year consistent?
						if ($year != $actime->AcademicYear()) {
							echo '!!year doesn\'t match!!<br/>';
							++$errors;
							$error_detected = TRUE;
						}
						// Is the calculated academic term consistent?
						if ($term_counter != $actime->AcademicTerm()) {
							echo '!!term doesn\'t match!!<br/>';
							++$errors;
							$error_detected = TRUE;
						}
						// Is the calculated academic week consistent?
						if ($week_counter != $actime->AcademicWeek()) {
							echo '!!week doesn\'t match!!<br/>';
							++$errors;
							$error_detected = TRUE;
						}
						if ($error_detected) {
							// an error has been detected so print date information
							if (0 !== $prev_date) {
								// starting with previous date
								echo '&nbsp;&nbsp;prev date: ' .
									$prev_date->Format(DATE_RFC822) . '<br/>'; //*/
								// elapsed days
								echo '&nbsp;&nbsp;days between: ' .
										Academic_time::DaysBetweenTimestamps(
											$prev_date->Timestamp(),
											$actime->Timestamp()) . '<br/>'; //*/;
							}
							// academic date input
							echo '&nbsp;&nbsp;Year: ' . $year .
								', Term: ' . $term_counter .
				 				', Week: ' . $week_counter .
				 				', Day of week: ' . $dow_counter .
				 				', Day of term: ' . $term_day . '<br/>'; //*/
							// standard date output
							echo '&nbsp;&nbsp;date1: ' .
								$actime->Format(DATE_RFC822) . '<br/>'; //*/
							echo '&nbsp;&nbsp;date2: ' .
								$actime2->Format(DATE_RFC822) . '<br/>'; //*/
							// gregorian & academic date output
							echo '&nbsp;&nbsp;date: ' .
								$actime->DayOfMonth() . '/' .
								$actime->Month() . '/' .
								$actime->Year() . ': ' .
								$actime->AcademicWeek() . ',' .
								$actime->AcademicTermName() . ',' .
								$actime->AcademicYearName() . '<br/>'; //*/
							
							// data from inside DaysBetweenTimestamps function
							// probably no longer required now its fixed
							echo '&nbsp;&nbsp;&nbsp;Day of year of first: ' .
									(int)date('z',$prev_date->Timestamp()) . '<br/>';
							echo '&nbsp;&nbsp;&nbsp;Day of year of second: ' .
									(int)date('z',$actime->Timestamp()) . '<br/>';
							$difference = (int)date('z',$actime->Timestamp()) - (int)date('z',$prev_date->Timestamp());
							if ($difference < 0) {
								echo '&nbsp;&nbsp;&nbsp;Days of year of first: ' .
										(365 + (int)date('L',$prev_date->Timestamp())) . '<br/>';
							}
							
							// New line to seperate error dates
							echo '<br/>';
						}
						$prev_date = $actime;
						++$term_day;
					}
					$dow_counter = 1;
				}
			}
		}
		return $errors;
	}
	
}

?>