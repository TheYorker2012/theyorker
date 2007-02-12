<?php

/**
 * @file recurrence_model.php
 * @brief Date Recurrence Rule model.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

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
 *
 * FindTimes() is the most useful function, producing a set of dates which
 *	match the rule.
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
	/// Method to use when calculating days from base.
	/**
	 * Values:
	 *	- 0 (Next enabled day on or after base. 1 result per base).
	 *	- 1 (Next enabled days on and after base. |mDayDays| results per base).
	 *	- 2 (Closest enabled day to base. 1 result per base).
	 *	- 3 (Closest enabled days to base. |mDayDays| results per base).
	 *
	 * @note Default value is 1 (Next enabled days on and after base).
	 */
	private $mDayMethod;
	
	/// Which days of the week to use near the base.
	/**
	 * Array of bools indexed 0..6 (sunday = 0 etc.)
	 *	- TRUE (Use this day near the base).
	 *	- FALSE (Don't use this day near the base).
	 *
	 * This can be stored in a bitvector (integer) in the database.
	 * @note Defaults to all TRUE.
	 */
	private $mDayDays;
	
	/// The number of weeks past the base to use.
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
	 * @param $ArrayData array Initialisation data.
	 */
	function __construct($ArrayData = FALSE)
	{
		if (is_array($ArrayData)) {
			$translation = array(
					'DayMonth' => 0,
					'Academic' => 1,
					'Easter' => 2,
				);
			/// @pre ArrayData['recurrence_rule_date_method'] in {'DayMonth','Academic','Easter'}
			assert('array_key_exists($ArrayData[\'recurrence_rule_date_method\'], $translation)');
			
			$this->mMinDate	= (NULL === $ArrayData['recurrence_rule_min_date'])
							? FALSE
							: $$ArrayData['recurrence_rule_min_date'];
			$this->mMaxDate	= (NULL === $ArrayData['recurrence_rule_max_date'])
							? FALSE
							: $$ArrayData['recurrence_rule_max_date'];
			$this->mYearInterval	= $ArrayData['recurrence_rule_year_interval'];
			$this->mYearOffset		= $ArrayData['recurrence_rule_year_offset'];
			$this->mDateMethod		= $translation[$ArrayData['recurrence_rule_date_method']];
			
			$months				= $ArrayData['recurrence_rule_daymonth_months'];
			$this->mDateDmMonths = array();
			for ($bit_number = 0; $bit_number < 12; ++$bit_number) {
				$this->mDataDmMonths[$bit_number] = (($months % 2) != 0);
				$months = (int)($months / 2);
			}
			
			$this->mDateDmDate	= $ArrayData['recurrence_rule_daymonth_date'];
			$this->mDateAcTerm	= $ArrayData['recurrence_rule_academic_term'];
			
			$weeks = $ArrayData['recurrence_rule_academic_weeks'];
			$this->mDateAcWeeks	= array();
			for ($bit_number = -15; $bit_number <= 16; ++$bit_number) {
				$this->mDateAcWeeks[$bit_number] = (($weeks % 2) != 0);
				$weeks = (int)($weeks / 2);
			}
			
			$this->mDayMethod	= $ArrayData['recurrence_rule_day_method'];
			
			$days = $ArrayData['recurrence_rule_day_days'];
			$this->mDayDays = array();
			for ($bit_number = 0; $bit_number < 7; ++$bit_number) {
				$this->mDayDays[$bit_number] = (($days % 2) != 0);
				$days = (int)($days / 2);
			}
			
			$this->mDayWeek		= $ArrayData['recurrence_rule_day_week'];
			$this->mOffsetDays	= $ArrayData['recurrence_rule_offset_days'];
			$this->mOffsetMins	= $ArrayData['recurrence_rule_offset_minutes'];
			
			
		} else {
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
			$this->mDateAcTerm = 0;
			$this->mDateAcWeeks = array();
			$this->ClearAcademicWeeks();
			
			/// - No days of the week (straight base).
			$this->mDayDays = array();
			$this->UseNextEnabledDays();
			$this->AnyDayOfWeek();
			$this->SetWeekOffset();
			
			/// - No offset.
			$this->SetOffsetDays();
			$this->Time();
		}
	}
	
	/// Generate an array from the rule.
	/**
	 * @return array Data as inputtable into the default constructor.
	 */
	function ToArray()
	{
		$ArrayData = array();
		
		$translation = array(
				0 => 'DayMonth',
				1 => 'Academic',
				2 => 'Easter',
			);
		/// @pre ArrayData['recurrence_rule_date_method'] in {'DayMonth','Academic','Easter'}
		assert('array_key_exists($this->mDateMethod, $translation)');
		
		$ArrayData['recurrence_rule_min_date']	= (FALSE === $this->mMinDate)
												? NULL
												: $this->mMinDate;
		$ArrayData['recurrence_rule_max_date']	= (FALSE === $this->mMaxDate)
												? NULL
												: $this->mMaxDate;
		$ArrayData['recurrence_rule_year_interval']		= $this->mYearInterval;
		$ArrayData['recurrence_rule_year_offset']		= $this->mYearOffset;
		
		$ArrayData['recurrence_rule_date_method']		= $translation[$this->mDateMethod];
		
		$ArrayData['recurrence_rule_daymonth_months']	= 0;
		$factor = 1;
		for ($bit_number = 1; $bit_number <= 12; ++$bit_number) {
			if ($this->mDateDmMonths[$bit_number]) {
				$ArrayData['recurrence_rule_daymonth_months'] |= $factor;
			}
			$factor *= 2;
		}
		
		$ArrayData['recurrence_rule_daymonth_date']		= $this->mDateDmDate;
		$ArrayData['recurrence_rule_academic_term']		= $this->mDateAcTerm;
		
		$ArrayData['recurrence_rule_academic_weeks']	= 0;
		$factor = 1;
		for ($bit_number = -15; $bit_number <= 16; ++$bit_number) {
			if ($this->mDateAcWeeks[$bit_number]) {
				$ArrayData['recurrence_rule_academic_weeks'] |= $factor;
			}
			$factor *= 2;
		}
		
		$ArrayData['recurrence_rule_day_method']		= $this->mDayMethod;
		
		$ArrayData['recurrence_rule_day_days']			= 0;
		$factor = 1;
		for ($bit_number = 0; $bit_number < 7; ++$bit_number) {
			if ($this->mDayDays[$bit_number]) {
				$ArrayData['recurrence_rule_day_days'] |= $factor;
			}
			$factor *= 2;
		}
		
		$ArrayData['recurrence_rule_day_week']			= $this->mDayWeek;
		$ArrayData['recurrence_rule_offset_days']		= $this->mOffsetDays;
		$ArrayData['recurrence_rule_offset_minutes']	= $this->mOffsetMins;
		
		return $ArrayData;
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
	function MonthDate($Month, $Date = 1)
	{
		$this->mDateMethod = 0; // DayMonth
		$this->ClearMonths();
		$this->AddMonth($Month);
		$this->DayOfMonth($Date);
		$this->UseNextEnabledDay();
		$this->AnyDayOfWeek();
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
	 * - Set to use next enabled day.
	 * - Enables all days (although really it'll be sunday).
	 * - Resets the offset days.
	 */
	function EasterSunday()
	{
		$this->mDateMethod = 2; // Easter
		$this->UseNextEnabledDay();
		$this->AnyDayOfWeek();
		$this->SetOffsetDays();
	}
	
	/// Set the day filter to use the next enabled day after each base.
	/**
	 * - Sets the day method to 0
	 */
	function UseNextEnabledDay()
	{
		$this->mDayMethod = 0;
	}
	
	/// Set the day filter to use the next enabled days after each base.
	/**
	 * - Sets the day method to 1
	 */
	function UseNextEnabledDays()
	{
		$this->mDayMethod = 1;
	}
	
	/// Set the day filter to use the closest enabled day to each base.
	/**
	 * - Sets the day method to 2
	 */
	function UseClosestEnabledDay()
	{
		$this->mDayMethod = 2;
	}
	
	/// Set the day filter to use the closest enabled days to each base.
	/**
	 * - Sets the day method to 3
	 */
	function UseClosestEnabledDays()
	{
		$this->mDayMethod = 3;
	}
	
	/// Enable all days of the week near base.
	/**
	 * - Clears the array to TRUE.
	 */
	function AnyDayOfWeek()
	{
		for ($day_counter = 0; $day_counter < 7; ++$day_counter) {
			$this->mDayDays[$day_counter] = TRUE;
		}
	}
	
	/// Enable a particular day of the week to result near each base.
	/**
	 * @param $Day integer Day of the week (0 = sunday).
	 */
	function EnableDayOfWeek($Day)
	{
		$this->mDayDays[$Day] = TRUE;
	}
	
	/// Disable a particular day of the week from resulting near each base.
	/**
	 * @param $Day integer Day of the week (0 = sunday).
	 */
	function DisableDayOfWeek($Day)
	{
		$this->mDayDays[$Day] = FALSE;
	}
	
	/// Set a particular day of the week as the only day.
	/**
	 * @param $Day integer Day of the week (0 = sunday).
	 */
	function OnlyDayOfWeek($Day = -1)
	{
		for ($day_counter = 0; $day_counter < 7; ++$day_counter) {
			$this->mDayDays[$day_counter] = ($day_counter == $Day);
		}
	}
	
	/// Set the number of weeks to offset the day filter results.
	/**
	 * @param $Week integer Number of weeks to offset the day filter results.
	 */
	function SetWeekOffset($Week = 0)
	{
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
		if ($End >= $Start) {
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
					if ($date >= $Start && $date < $End) {
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
	private function FilterYears($start, $end)
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
	private function ProduceFirstRoundDates($Year, &$Results)
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
	private function ProduceSecondRoundDates($Date, &$Results)
	{
		$day_of_week = (int)date('w',$Date); // 0-6
		$results_limit = 1 + ($this->mDayMethod%2)*6;
		if ($this->mDayMethod < 2) {
			// Next enabled day(s) on or after base.
			$day_order = array( 0, 1, 2, 3, 4, 5, 6);
		} else {
			 // Closest enabled day(s) to base.
			$day_order = array( 0, 1,-1, 2,-2, 3,-3);
		}
		// Decide whether to limit to the same month
		if (	0 === $this->mDateMethod &&
				1 === $this->mDateDmDate &&
				4 < $this->mDayWeek) {
			$only_same_month = TRUE;
			$month = (int)date('n',$Date);
		} else {
			$only_same_month = FALSE;
		}
		// Go through days after base.
		foreach ($day_order as $days_after_base) {
			// If that day of the week is accepted, add a result.
			if ($this->mDayDays[(7+$day_of_week+$days_after_base)%7]) {
				$with_week_offset = $days_after_base + $this->mDayWeek*7;
				$transformed = strtotime($with_week_offset.'day', $Date);
				// If we need to check in same month, do so now
				if (!$only_same_month || $month === (int)date('n',$transformed)) {
					// Add to results and check if we can get any more results
					// from this base.
					$Results[$transformed] = TRUE;
					if (--$results_limit <= 0) {
						break;
					}
				}
			}
		}
	}
	
	/// Offset a given date by the day and minute offset in the rule.
	/**
	 * @param $Date timestsamp Input date.
	 * @return @a $Date + @a $this->mOffsetDays days + @a $this->mOffsetMins minutes.
	 */
	private function OffsetDate($Date)
	{
		return strtotime($this->mOffsetDays.'day'.$this->mOffsetMins.'min', $Date);
	}
	
}

/// Main recurrence model class
/**
 *
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Recurrence_model extends Model
{
	/// Default constructor
	function __construct()
	{
		parent::Model();
		
		// Load the academic calendar library
		$this->load->library('academic_calendar');
	}
	
	/// Get comma separated list of fields to get.
	/**
	 * @param $EventAlias string Alias of events table.
	 */
	function SqlSelectRecurrenceRule($RuleAlias = 'recurrence_rules')
	{
		return
			$RuleAlias.'.recurrence_rule_id,'.
			'UNIX_TIMESTAMP('.$RuleAlias.'.recurrence_rule_min_date) AS recurrence_rule_min_date,'.
			'UNIX_TIMESTAMP('.$RuleAlias.'.recurrence_rule_max_date) AS recurrence_rule_max_date,'.
			$RuleAlias.'.recurrence_rule_year_interval,'.
			$RuleAlias.'.recurrence_rule_year_offset,'.
			$RuleAlias.'.recurrence_rule_date_method,'.
			$RuleAlias.'.recurrence_rule_daymonth_months,'.
			$RuleAlias.'.recurrence_rule_daymonth_date,'.
			$RuleAlias.'.recurrence_rule_academic_term,'.
			$RuleAlias.'.recurrence_rule_academic_weeks,'.
			$RuleAlias.'.recurrence_rule_day_method,'.
			$RuleAlias.'.recurrence_rule_day_days,'.
			$RuleAlias.'.recurrence_rule_day_week,'.
			$RuleAlias.'.recurrence_rule_offset_days,'.
			$RuleAlias.'.recurrence_rule_offset_minutes';
	}
	
	/// Run a save query on a recurrence rule.
	/**
	 * @param $RecurrenceRule RecurrenceRule RecurrenceRule to save.
	 * @param $Operation string SQL operation (e.g. 'UPDATE','INSERT INTO').
	 * @param $Condition string SQL condition.
	 * @param $ConditionBind array Variables to bind in the condition.
	 * @return string Number of affected rows.
	 */
	private function RunSaveQuery($RecurrenceRule, $Operation,
		$Condition = FALSE, $ConditionBind = array())
	{
		$rule_array = $RecurrenceRule->ToArray();
		$fields = array();
		$bind_array = array();
		if (array_key_exists('recurrence_rule_min_date', $rule_array)) {
			$fields[] = 'recurrence_rule_min_date=FROM_UNIXTIME(?)';
			$bind_array[] = $rule_array['recurrence_rule_min_date'];
			unset($rule_array['recurrence_rule_min_date']);
		}
		if (array_key_exists('recurrence_rule_max_date', $rule_array)) {
			$fields[] = 'recurrence_rule_max_date=FROM_UNIXTIME(?)';
			$bind_array[] = $rule_array['recurrence_rule_max_date'];
			unset($rule_array['recurrence_rule_max_date']);
		}
		foreach ($rule_array as $key => $value) {
			$fields[] = $key . '=?';
			$bind_array[] = $value;
		}
		$sql =	$Operation . ' recurrence_rules ' .
				'SET ' . implode(',',$fields);
		if (is_string($Condition)) {
			' WHERE ' . $Condition;
			foreach ($ConditionBind as $value) {
				$bind_array[] = $value;
			}
		}
		$query = $this->db->query($sql, $bind_array);
		return ($this->db->affected_rows() > 0);
	}
	
	/// Create a new recurrence rule in the db.
	/**
	 * @param $RecurrenceRule RecurrenceRule RecurrenceRule to save.
	 * @return
	 *	- int recurrence_rule_id of new rule.
	 *	- FALSE if the rule could not be saved.
	 */
	function AddRule($RecurrenceRule)
	{
		if ($this->RunSaveQuery($RecurrenceRule, 'INSERT INTO')) {
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}
	
	/// Save a recurrence rule in the db.
	/**
	 * @param $RecurrenceRuleId int recurrence_rule_id of rule to save.
	 * @param $RecurrenceRule RecurrenceRule RecurrenceRule to save.
	 * @return bool Whether the rule was successfully saved.
	 */
	function SaveRule($RecurrenceRuleId, $RecurrenceRule)
	{
		return $this->RunSaveQuery($RecurrenceRule, 'INSERT INTO',
			'recurrence_rules.recurrence_rule_id = ?',
			array($RecurrenceRuleId)
		);
	}
	
	/// Load a recurrence rule from the db.
	/**
	 * @param $RecurrenceRuleId int recurrence_rule_id of rule to get.
	 * @return
	 *	- RecurrenceRule rule from database.
	 *	- FALSE if the rule could not be loaded.
	 */
	function GetRule($RecurrenceRuleId)
	{
		$sql =	'SELECT	' . $this->SqlSelectRecurrenceRule() .
				' FROM	recurrence_rules ' .
				'WHERE	recurrence_rules.recurrence_rule_id = ?';
		$query = $this->db->query($sql, $RecurrenceRuleId);
		if ($query->num_rows() === 1) {
			return new RecurrenceRule($query->result_array());
		} else {
			return FALSE;
		}
	}
}

?>