<?php

/**
 * @file Recurrence.php
 * @brief Date Recurrence Library.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

// Load the academic calendar library
$CI = &get_instance();
$CI->load->library('academic_calendar');

/// Represents a rule for a set of dates following a common pattern.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * This class allows a recurring date pattern to be set up, then for dates
 *	within a particular range to be extracted.
 *
 * The objective is to support the majority of special calendar days
 *	(bank holidays, religious feasts, birthdays + anniversaries etc).
 * User and organisation events are unlikely to use this class.
 *
 * Supports:
 *	- Regular year intervals (e.g. every 4 years from 2001).
 *	- Base: N days past the start of any combination of months in a matching year.
 *	- Base: A term number and any pattern of weeks in a matching year.
 *	- Base: Easter sunday of a matching year (so that easter is supported).
 *	- The nth certain day after the base, or a pattern of days starting from the base.
 *	- An offset of days and minutes added to that.
 *
 * Other base ideas:
 *	- Base: Lunar months.
 *	- Base: [Any other useful bases? only ones which will be used!].
 *
 * @note This started off as a bit of a hack so feel free to make suggestions
 *	regarding the behaviour or patterns that it should support.
 */
class RecurrenceRule
{
	// Min/max date
	/// timestamp/bool Minimum time of occurrence.
	/**
	 * A value of FALSE indicates that there is no minimum.
	 * @note Default value is FALSE.
	 */
	private $mMinDate;
	
	/// timestamp/bool Maximum time of occurrence.
	/**
	 * A value of FALSE indicates that there is no maximum.
	 * @note Default value is FALSE.
	 */
	private $mMaxDate;
	
	
	// Year Filter : mDateMethod=Academic => Academic years
	/// integer Interval of years.
	/**
	 * e.g. An interval of 4 might be appropriate for leap days.
	 * @note Default value is 1.
	 */
	private $mYearInterval;
	
	/// An example of a valid year.
	/**
	 * @note Default value is 0.
	 */
	private $mYearOffset;
	
	
	// Date Filter
	/// Method to use when calculating bases.
	/**
	 * Values:
	 *	- 0 (DayMonth: Use a set of months and a 'day of month' number for the base).
	 *	- 1 (Academic: Use a term number and a set of weeks in that term that are used as bases).
	 *	- 2 (Easter: Use easter sunday as the base each year).
	 *
	 * @note Default value is 0 (DayMonth).
	 */
	private $mDateMethod;
	
	/// When in mode DayMonth, array whether each month is valid.
	/**
	 * Array of bools indexed 1..12 for the months:
	 *	- TRUE (Day @a $mDayDmDate of the month is to be a base).
	 *	- FALSE (This month doesn't have a base).
	 *
	 * This can be stored in a bitvector (integer) in the database.
	 * @note Defaults to all FALSE.
	 */
	
	private $mDateDmMonths;
	
	/// When in mode DayMonth, day of month to use as base.
	/**
	 * Integer day of month to use as the base for valid months.
	 * @note Defaults to 1.
	 */
	private $mDateDmDate;
	
	/// When in mode Academic, term number of weeks to use as bases.
	/**
	 * Integer term number as used by the Academic_calendar library:
	 *	- 0 (Autumn term).
	 *	- 1 (Christmas holiday).
	 *	- 2 (Spring term).
	 *	- 3 (Easter holiday).
	 *	- 4 (Summer term).
	 *	- 5 (Summer holiday).
	 *
	 * @note Defaults to 0 (Autumn term).
	 */
	private $mDateAcTerm;	// : int (0-5)
	
	/// When in mode Academic, which week numbers to use as bases.
	/**
	 * Array of bools indexed -15..16:
	 *	- TRUE (Use the start of the week of the term as a base).
	 *	- FALSE (Don't use the start of the week of the term as a base).
	 *
	 * Index 1 is the first week of the term, so the negative indicies represent
	 *	the last weeks of the previous term.
	 *
	 * This can be stored in a bitvector (integer) in the database.
	 * @note Defaults to all FALSE.
	 */
	private $mDateAcWeeks;
	
	
	// Day Filter
	/// Which days of the week to use near the base.
	/**
	 * Array of bools indexed 0..6 (sunday = 0 etc.)
	 *	- TRUE (Use this day near the base).
	 *	- FALSE (Don't use this day near the base).
	 *
	 * This can be stored in a bitvector (integer) in the database.
	 * @note Defaults to all FALSE.
	 */
	private $mDayDays;
	
	/// If exactly one day is set, the number of weeks past the base to use.
	/**
	 * For example:
	 *	- If @a $mDayDays [ THURSDAY ] = TRUE and @a $mDayWeek = 0, the results
	 *		would be the first thursday after each base.
	 *	- If @a $mDayDays [ TUESDAY ] = TRUE and @a $mDayWeek = 3, the results
	 *		would be the forth tuesday after each base.
	 *	- If @a $mDayDays [ SUNDAY ] = TRUE and @a $mDayWeek = -1, the results
	 *		would be the last sunday before each base.
	 *
	 * If the result is after the end of the month and @a $mDayWeek != 0 then
	 *	the result is discarded (This is usually for finding the nth certain day
	 *	of particular months such as for when the clocks change).
	 * @note Defaults to 0.
	 */
	private $mDayWeek;
	
	
	// Offset
	/// Number of days to offset the result.
	private $mOffsetDays;
	/// Number of minutes to offset the result.
	private $mOffsetMins;
	
	/// Default constructor.
	/**
	 * Initialises the recurrence rule to no dates.
	 */
	function __construct()
	{
		/// - No min or max.
		$this->mMinDate = $this->mMaxDate = FALSE;
		
		/// - Every year.
		$this->mYearInterval = 1;
		$this->mYearOffset = 0;
		
		/// - First day of no months.
		$this->mDateMethod = 0;
		$this->mDateDmMonths = array();
		$this->ClearMonths();
		$this->DayOfMonth(1);
		/// - No weeks of the autumn term.
		$this->mDateAcWeeks = array();
		$this->ClearAcademicWeeks();
		
		/// - No days of the week (straight base).
		$this->mDayDays = array();
		$this->DayOfWeek();
		
		/// - No offset.
		$this->SetOffsetDays();
		$this->Time();
	}
	
	/// Set the minimum date.
	/**
	 * @param $Min timestamp New minimum possible result.
	 */
	function Min($Min = FALSE)
	{
		$this->mMinDate = $Min;
	}
	
	/// Set the maximum date.
	/**
	 * @param $Max timestamp New maximum possible result.
	 */
	function Max($Max = FALSE)
	{
		$this->mMaxDate = $Max;
	}
	
	/// Set a month and a day of the month.
	/**
	 * @param $Month integer Month of the year (1..12).
	 * @param $Date integer Day of the month (1..31) specified in @a $Month to
	 *	use as base.
	 *
	 * - Sets the base mode to DayMonth.
	 * - Sets @a $Month as the only month to have bases.
	 * - Sets @a $Date as the base day of the month.
	 * - Clears day offset and days of week (so result is straight base).
	 */
	function MonthDate($Month, $Date = 0)
	{
		$this->mDateMethod = 0; // DayMonth
		$this->ClearMonths();
		$this->AddMonth($Month);
		$this->DayOfMonth($Date);
		$this->ClearDaysOfWeek();
		$this->SetOffsetDays();
	}
	
	/// Set the day of the base months to use as a base.
	/**
	 * @param $Date integer Day of the month (1..31) to use as base.
	 */
	function DayOfMonth($Date = 0)
	{
		$this->mDateDmDate = $Date;
	}
	
	/// Clear the months so no months can have bases.
	function ClearMonths()
	{
		for ($month_counter = 1; $month_counter <= 12; ++$month_counter) {
			$this->mDateDmMonths[$month_counter] = FALSE;
		}
	}
	
	/// Set all months to have bases.
	/**
	 * - Sets the base mode to DayMonth.
	 */
	function SetMonths()
	{
		$this->mDateMethod = 0; // DayMonth
		for ($month_counter = 1; $month_counter <= 12; ++$month_counter) {
			$this->mDateDmMonths[$month_counter] = TRUE;
		}
	}
	
	/// Set a particular month to have a base.
	/**
	 * @param $Month integer Month to enable a base for (1..12).
	 *
	 * - Sets the base mode to DayMonth.
	 */
	function AddMonth($Month)
	{
		$this->mDateMethod = 0; // DayMonth
		$this->mDateDmMonths[$Month] = TRUE;
	}
	
	/// Set a particular month to have no base.
	/**
	 * @param $Month integer Month to disable a base for (1..12).
	 *
	 * - Sets the base mode to DayMonth.
	 */
	function RemoveMonth($Week)
	{
		$this->mDateMethod = 0; // DayMonth
		$this->mDateDmMonths[$Week] = FALSE;
	}
	
	/// Set the academic term in the valid years to contain bases.
	/**
	 * @param $Term integer Term number as used by the Academic_calendar library:
	 *	- 0 (Autumn term).
	 *	- 1 (Christmas holiday).
	 *	- 2 (Spring term).
	 *	- 3 (Easter holiday).
	 *	- 4 (Summer term).
	 *	- 5 (Summer holiday).
	 *
	 * - Sets the base mode to Academic.
	 * - Clears which academic weeks have bases.
	 */
	function AcademicTerm($Term)
	{
		$this->mDateMethod = 1; // Academic
		$this->mDateAcTerm = $Term;
		$this->ClearAcademicWeeks();
	}
	
	/// Clear which academic weeks have bases.
	function ClearAcademicWeeks()
	{
		for ($week_counter = -15; $week_counter <= 16; ++$week_counter) {
			$this->mDateAcWeeks[$week_counter] = FALSE;
		}
	}
	
	/// Add an academic week to those with bases.
	/**
	 * @param $Week Academic week number of the term (-15..16):
	 *	- 1 is the first week of the term.
	 *	- Negative numbers represent the last weeks of the previous term.
	 *
	 * - Sets the base mode to Academic.
	 * - Add week @a $Week to those which have bases.
	 */
	function AddAcademicWeek($Week)
	{
		$this->mDateMethod = 1; // Academic
		$this->mDateAcWeeks[$Week] = TRUE;
	}
	
	/// Set to easter sunday (a base).
	/**
	 * - Sets the base mode to Easter.
	 * - Clears the days of week near base (result is straight base).
	 * - Resets the offset days.
	 */
	function EasterSunday()
	{
		$this->mDateMethod = 2; // Easter
		$this->ClearDaysOfWeek();
		$this->SetOffsetDays();
	}
	
	/// Clear the days of the week near base (result is straight base).
	/**
	 * - Clears the array.
	 * - Sets the week number to 0;
	 */
	function ClearDaysOfWeek()
	{
		for ($day_counter = 0; $day_counter < 7; ++$day_counter) {
			$this->mDayDays[$day_counter] = FALSE;
		}
		$this->mDayWeek = 0;
	}
	
	/// Set a particular day of the week to result near each base.
	/**
	 * @param $Day integer Day of the week (0 = sunday).
	 */
	function AddDayOfWeek($Day)
	{
		$this->mDayDays[$Day] = TRUE;
	}
	
	/// Set a particular day of the week as the only and the week number.
	/**
	 * @param $Day integer Day of the week (0 = sunday).
	 * @param $Week integer The number of weeks past the base to use the @a $Day.
	 */
	function DayOfWeek($Day = -1, $Week = 0)
	{
		for ($day_counter = 0; $day_counter < 7; ++$day_counter) {
			$this->mDayDays[$day_counter] = ($day_counter == $Day);
		}
		$this->mDayWeek = $Week;
	}
	
	/// Offsets the results by a number of days.
	/**
	 * @param $Days integer Number of days to offset results.
	 *
	 * If an offset has already been set, this will add to it.
	 */
	function OffsetDays($Days)
	{
		$this->mOffsetDays += $Days;
	}
	
	/// Sets the day offset of the results.
	/**
	 * @param $Days integer Number of days to offset results.
	 *
	 * If an offset has already been set, this will override it.
	 */
	function SetOffsetDays($Days = 0)
	{
		$this->mOffsetDays = $Days;
	}
	
	/// Set the time of day.
	/**
	 * @param $Hours integer Hour number of the day (0..23).
	 * @param $Minutes integer Minute number of the hour (0..59).
	 */
	function Time($Hours = 0, $Minutes = 0)
	{
		$this->mOffsetMins = $Hours*60 + $Minutes;
	}
	
	/// Calculate the date of easter on a particular year.
	/**
	 * @param $Year integer Year (e.g. 2006).
	 * @return array(@a $Year, month of easter, day of month of easter sunday).
	 *
	 * Uses the Meeus/Jones/Butcher Gregorian algorithm to calculate easter.
	 * @see http://en.wikipedia.org/wiki/Computus#Meeus.2FJones.2FButcher_Gregorian_algorithm
	 *
	 * This returns the date as an array so as to cover a larger range than
	 *	unix timestamps.
	 */
	static function CalculateEaster($Year)
	{
		$a = $Year % 19;
		$b = (int)($Year / 100);
		$c = $Year % 100;
		$d = (int)($b / 4);
		$e = $b % 4;
		$f = (int)(($b+8)/25);
		$g = (int)(($b - $f + 1)/3);
		$h = (19*$a + $b - $d - $g + 15) % 30;
		$i = (int)($c / 4);
		$k = $c % 4;
		$L = (32 + 2*($e+$i) - $h - $k) % 7;
		$m = (int)(($a + 11*$h + 22*$L)/451);
		$month = (int)(($h + $L - 7*$m + 114) / 31);
		$day = (($h + $L - 7*$m + 114) % 31) + 1;
		return array($Year, $month, $day);
	}
	
	/// Calculate the date of easter on a particular year as a timestamp.
	/**
	 * @param $Year integer Year (e.g. 2006).
	 * @return timestamp of midnight on the morning of easter sunday.
	 *
	 * Uses CalculateEaster and converts to a timestamp.
	 */
	static function CalculateEasterTimestamp($Year)
	{
		$easter_array = self::CalculateEaster($Year);
		return mktime(0,0,0,$easter_array[1], $easter_array[2], $easter_array[0]);
	}
	
	/// Given a start and end time, produce all occurrences in between.
	/**
	 * @param $Start timestamp Start of search interval.
	 * @param $End timestamp End of search interval.
	 * @return Associative array:
	 *	- Indexed by timestamps of occurrences.
	 *	- Each value is a boolean which should (atm) always be TRUE.
	 */
	function FindTimes($Start, $End)
	{
		// Find overlap
		if ($this->mMinDate !== FALSE && $Start < $this->mMinDate)
			$Start = $this->mMinDate;
		if ($this->mMaxDate !== FALSE && $End > $this->mMaxDate)
			$End = $this->mMaxDate;
		
		// produce and return array of matching dates
		$results = array();
		if ($end >= $start) {
			// Find the years that the date could occur on
			$years = $this->FilterYears((int)date('Y',$Start)-1, (int)date('Y',$End));
			// Find the bases in the year
			$first_dates = array();
			foreach ($years as $year) {
				$this->ProduceFirstRoundDates($year, $first_dates);
			}
			// perform week day filters
			$second_dates = array();
			foreach ($first_dates as $date => $enable) {
				if ($enable) {
					$this->ProduceSecondRoundDates($date, $second_dates);
				}
			}
			// Offset the results and check in range
			foreach ($second_dates as $date => $enable) {
				if ($enable) {
					$date = $this->OffsetDate($date);
					if ($date >= $Start && $date <= $End) {
						$results[$date] = TRUE;
					}
				}
			}
		}
		return $results;
	}
	
	/// Given a start and end year, produce all years with occurrences.
	/**
	 * @param $Start integer Start year (e.g. 2006).
	 * @param $End integer End year (e.g. 2007).
	 * @return array of integers, each representing a year in the interval.
	 */
	function FilterYears($start, $end)
	{
		// year = start-offset mod interval
		$year = ($start - $this->mYearOffset) % $this->mYearInterval;
		
		// year = year of first match
		if ($year > 0) {
			$year = $this->mYearInterval - $year;
		}
		$year += $start;
		
		// produce and return array of matching years
		$results = array();
		while ($year <= $end) {
			$results[] = $year;
			$year += $this->mYearInterval;
		}
		return $results;
	}
	
	/// Given a year, find all base dates.
	/**
	 * @param $Year integer Year (e.g. 2006).
	 * @param $Results reference to array to put timestamps.
	 *
	 * Find the base dates in the given year depending on method.
	 *
	 * Adds an element to @a $Results for each base with a timestamp index and
	 *	the value TRUE.
	 */
	function ProduceFirstRoundDates($Year, &$Results)
	{
		// Depends on date method
		switch ($this->mDateMethod)
		{
			case 0: // DayMonth
				foreach ($this->mDateDmMonths as $month => $enable) {
					if ($enable) {
						// Get dates of months (excluding those which are invalid)
						$timestamp = mktime(0,0,0, $month,$this->mDateDmDate,$Year);
						if ($timestamp !== FALSE &&
						    (int)date('n',$timestamp) === $month) {
							$Results[$timestamp] = TRUE;
						}
					}
				}
				break;
				
			case 1: // Academic
				foreach ($this->mDateAcWeeks as $week => $enable) {
					if ($enable) {
						$CI = &get_instance();
						// Use Academic_calendar to get timestamp from academic.
						$timestamp = $CI->academic_calendar->Academic(
								$Year,
								$this->mDateAcTerm,
								$week)->Timestamp();
						if ($timestamp !== FALSE) {
							$Results[$timestamp] = TRUE;
						}
					}
				}
				break;
				
			case 2: // Easter
				$Results[self::CalculateEasterTimestamp($Year)] = TRUE;
				break;
		}
	}
	
	/// Given a base date, produce results using day of week filters.
	/**
	 * @param $Date timestamp Time of base.
	 * @param $Results reference to array to put timestamps of results.
	 * @return @a $Results (even though its a reference).
	 *
	 * - If no days of week are set, pump the date straight through.
	 * - Otherwise add each day in days of week array after @a $Date to result,
	 *	offset by a number of weeks.
	 *
	 * Sorry, rubbish description, I'll try again...
	 *
	 * For each day of week thats set, the n'th of that day after @a $Date is
	 *	added to @a $Result, where n is the week offset (@a $mDayDays).
	 */
	function ProduceSecondRoundDates($Date, &$Results)
	{
		$month = (int)date('n',$Date);
		$year = (int)date('Y',$Date);
		$first_day_of_next_month = mktime(
				0,0,0,
				$month%12 + 1, 1, $year + (int)($month/12));
		$day_of_week = (int)date('N',$Date);
		$day_found = 0;
		foreach ($this->mDayDays as $day => $enable) {
			if ($enable) {
				++$day_found;
				$next_day = ($day - $day_of_week + 7) % 7
						+ $this->mDayWeek*7;
				$transformed = strtotime($next_day.'day', $Date);
				if ($this->mDayWeek === 0 ||
					$transformed < $first_day_of_next_month) {
					$Results[$transformed] = TRUE;
				}
			}
		}
		if (0 === $day_found) {
			$Results[$Date] = TRUE;
		}
	}
	
	/// Offset a given date by the day and minute offset in the rule.
	/**
	 * @param $Date timestsamp Input date.
	 * @return @a $Date + @a $this->mOffsetDays days + @a $this->mOffsetMins minutes.
	 */
	function OffsetDate($Date)
	{
		return strtotime($this->mOffsetDays.'day'.$this->mOffsetMins.'min', $Date);
	}
	
		
}


/// Main recurrence library class
/**
 * At the moment this just has some shortcuts to common calendar date rules.
 *
 * Useful for demonstrating the RecurrenceRule class.
 */
class Recurrence
{
	/// Return the RecurrenceRule for easter sunday.
	function EasterSunday()
	{
		$rule = new RecurrenceRule();
		$rule->EasterSunday();
		return $rule;
	}
	
	/// Return the RecurrenceRule for easter monday.
	function EasterMonday()
	{
		// monday after easter sunday
		$rule = $this->EasterSunday();
		$rule->SetOffsetDays(+1);
		return $rule;
	}
	
	/// Return the RecurrenceRule for good friday.
	function GoodFriday()
	{
		// friday before easter sunday
		$rule = $this->EasterSunday();
		$rule->SetOffsetDays(-2);
		return $rule;
	}
	
	/// Return the RecurrenceRule for christmas day.
	function ChristmasDay()
	{
		$rule = new RecurrenceRule();
		// 25th december
		$rule->MonthDate(12,25);
		return $rule;
	}
	
	/// Return the RecurrenceRule for St. Patrick's day.
	function StPatricksDay()
	{
		$rule = new RecurrenceRule();
		// normally 17th march (my mum's birthday)
		$rule->MonthDate(3,17);
		return $rule;
	}
	
	/// Return the RecurrenceRule for St. Stythian's day.
	function StStythiansFeastDay()
	{
		$rule = new RecurrenceRule();
		// first sunday in july with double figures
		$rule->MonthDate(7,10);
		$rule->DayOfWeek(0);
		return $rule;
	}
	
	/// Returns the RecurrenceRule for Stithians show day.
	function StithiansShowDay()
	{
		// monday after St. Stythian's day
		$rule = $this->StStythiansFeastDay();
		$rule->OffsetDays(+1);
		return $rule;
	}
	
	/// Returns the RecurrenceRule for the leap day in the gregorian calendar.
	function LeapDay()
	{
		$rule = new RecurrenceRule();
		// 29th february
		$rule->MonthDate(2,29);
		return $rule;
	}

}

?>