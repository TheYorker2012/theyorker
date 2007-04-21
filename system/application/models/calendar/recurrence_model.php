<?php

/**
 * @file models/calendar/recurrence_model.php
 * @brief Recurrencce classes.
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @todo Tidy up these classes at the top and redistribute the code which
 * varies between frequencies.
 */

// dateOfWeek() takes a date in Ymd and a day of week in 3 letters or more
// and returns the date of that day. (ie: "sun" or "sunday" would be acceptable values of $day but not "su")
/**
* @note Much of this code is taken from phpicalendar v2.23, released under the GPL
* see http://phpicalendar.net/
* functions/date_functions
*/
function dateOfWeek($Ymd, $day) {
	global $week_start_day;
	if (!isset($week_start_day)) $week_start_day = 'Sunday';
	$timestamp = strtotime($Ymd);
	$num = date('w', strtotime($week_start_day));
	$start_day_time = strtotime((date('w',$timestamp)==$num ? "$week_start_day" : "last $week_start_day"), $timestamp);
	$ret_unixtime = strtotime($day,$start_day_time);
	// Fix for 992744
	// $ret_unixtime = strtotime('+12 hours', $ret_unixtime);
	$ret_unixtime += (12 * 60 * 60);
	$ret = date('Ymd',$ret_unixtime);
	return $ret;
}

abstract class RRuleFrequency
{
	abstract function Compare($now, $then);
	abstract function Increment($interval, $previous);
}

class RRuleFreq_daily extends RRuleFrequency
{
	function Compare($now, $then) {
		$seconds_now = strtotime($now);
		$seconds_then = strtotime($then);
		$diff_seconds = $seconds_now - $seconds_then;
		$diff_minutes = $diff_seconds/60;
		$diff_hours = $diff_minutes/60;
		$diff_days = round($diff_hours/24);
		
		return $diff_days;
	}
	
	function Increment($interval, $previous)
	{
		return strtotime('+'.$interval.' day', $previous);
	}
}

class RRuleFreq_weekly extends RRuleFrequency
{
	
	function Compare($now, $then) {
		global $week_start_day;
		$sun_now = dateOfWeek($now, "Sunday");
		$sun_then = dateOfWeek($then, "Sunday");
		$seconds_now = strtotime($sun_now);
		$seconds_then =  strtotime($sun_then);
		$diff_weeks = round(($seconds_now - $seconds_then)/(60*60*24*7));
		return $diff_weeks;
	}
	
	function Increment($interval, $previous)
	{
		return strtotime('+'.$interval.' week', $previous);
	}
}

class RRuleFreq_monthly extends RRuleFrequency
{
	function Compare($now, $then) {
		ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $now, $date_now);
		ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $then, $date_then);
		$diff_years = $date_now[1] - $date_then[1];
		$diff_months = $date_now[2] - $date_then[2];
		if ($date_now[2] < $date_then[2]) {
			$diff_years -= 1;
			$diff_months = ($diff_months + 12) % 12;
		}
		$diff_months = ($diff_years * 12) + $diff_months;
	
		return $diff_months;
	}
	
	function Increment($interval, $previous)
	{
		return strtotime('+'.$interval.' month', $previous);
	}
}

class RRuleFreq_yearly extends RRuleFrequency
{
	function Compare($now, $then) {
		ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $now, $date_now);
		ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $then, $date_then);
		$diff_years = $date_now[1] - $date_then[1];
		return $diff_years;
	}
	
	function Increment($interval, $previous)
	{
		return strtotime('+'.$interval.' year', $previous);
	}
}

/// A single recurrence rule.
class CalendarRecurRule
{
	/// Table of valid frequencies.
	static $sFrequencies = array(
		'secondly',
		'minutely',
		'hourly',
		'daily',
		'weekly',
		'monthly',
		'yearly',
		'termly',	// non standard
		'acyearly',	// non standard
	);
	/// Table of valid weekdays.
	static $sWeekdays = array(
		'SU'	=> 0,
		'MO'	=> 1,
		'TU'	=> 2,
		'WE'	=> 3,
		'TH'	=> 4,
		'FR'	=> 5,
		'SA'	=> 6,
	);
	/// $sFrequencies Frequency of repetition.
	protected $mFrequency;
	protected $mFrequencyClass;
	/// [DateTime] Maximum acceptable date and time.
	protected $mUntil		= NULL;
	/// [int] Number of acceptable date and times.
	protected $mCount		= NULL;
	/// int Interval of the frequency.
	protected $mInterval	= 1;
	/// array[int<[0,59]> unique] Seconds to match.
	protected $mBySecond	= NULL;
	/// array[int<[0,59]> unique] Minutes to match.
	protected $mByMinute	= NULL;
	/// array[int<[0,23]> unique] Hours to match.
	protected $mByHour		= NULL;
	/// array[array(int<[,-1]+[1,]>,$sWeekdays) unique] Days of week to match.
	protected $mByDay		= NULL;
	/// array[int<[-31,-1]+[1,31]> unique] Days of the month to match.
	protected $mByMonthDay	= NULL;
	/// array[int<[-366,-1]+[1,366]> unique] Days of the year to match.
	protected $mByYearDay	= NULL;
	/// array[int<[-53,-1]+[1,53]> unique] Weeks of the year to match.
	protected $mByWeekNo	= NULL;
	/// array[int<[1,12]> unique] Months of the year to match.
	protected $mByMonth		= NULL;
	/// array[int<[1,]> unique] Which occurrences to use.
	protected $mBySetPos	= NULL;
	/// array[int<[0,5]> unique] Which terms to match.
	protected $mByTerm		= NULL;		// non standard
	/// array[int unique] Which days of the terms.
	protected $mByTermDay	= NULL;		// non standard
	/// array[int<[-20,-1]+[1,20]> unique] Which weeks of the term to match.
	protected $mByTermWeek	= NULL;		// non standard
	/// array[int<[-366,-1]+[1,366]> unique] Which days relative to easter to match.
	protected $mByEasterDay	= NULL;		// non standard
	/// $sWeekdays Which day of the week to consider the start.
	protected $mWkSt		= 0;		// = self::$sWeekdays['SU']
	
	function __construct()
	{
	}
	
	/// Set the frequency type.
	function SetFrequency($Frequency)
	{
		$this->mFrequency = $Frequency;
		$class_name = 'RRuleFreq_'.$Frequency;
		$this->mFrequencyClass = new $class_name();
	}
	/// Get the frequency of repetition.
	/**
	 * @return string Frequency method.
	 */
	function GetFrequency()
	{
		return $this->mFrequency;
	}
	
	/// Set the maximum date to match.
	/**
	 * @param $Value timestamp,NULL New timestamp or NULL.
	 */
	function SetUntil($Value = NULL)
	{
		if (NULL !== $Value) {
			/// @pre @a $Value must be a unix timestamp if not NULL.
			assert('is_int($Value)');
			$this->mCount = NULL;
		}
		$this->mUntil = $Value;
	}
	/// Get the maximum date to match if set.
	/**
	 * @return timestamp,NULL Timestamp of maximum date to match or NULL.
	 */
	function GetUntil()
	{
		return $this->mUntil;
	}
	
	/// Set the maximum number of matches.
	/**
	 * @param $Value timestamp,NULL New maximum number of matches or NULL.
	 */
	function SetCount($Value = NULL)
	{
		if (NULL !== $Value) {
			/// @pre @a $Value must be a positive int if not NULL.
			assert('is_int($Value) && $Value > 0');
			$this->mUntil = NULL;
		}
		$this->mCount = $Value;
	}
	/// Get the maximum number of matches if set.
	/**
	 * @return int,NULL Maximum number of matches or NULL.
	 */
	function GetCount()
	{
		return $this->mCount;
	}
	
	/// Set the frequency interval.
	/**
	 * @param $Value int New frequency interval.
	 */
	function SetInterval($Value = 1)
	{
		/// @pre @a $Value must be a positive int.
		assert('is_int($Value) && $Value > 0');
		$this->mInterval = $Value;
	}
	/// Get the frequency interval if set.
	/**
	 * @return int Frequency interval.
	 */
	function GetInterval()
	{
		return $this->mInterval;
	}
	
	/// Get array of by values.
	/**
	 * @return array of by values, each with:
	 *	- 'by' string e.g. 'second','month','termweek'.
	 *	- 'primary' int Main value.
	 *	- 'secondary' int,NULL Optional value.
	 */
	function GetByArray()
	{
		$simples = array(
			'Second','Minute','Hour',
			'MonthDay','YearDay','WeekNo',
			'Month','SetPos',
			'Term','TermDay','TermWeek',
			'EasterDay'
		);
		$results = array();
		foreach ($simples as $simple) {
			$param = 'm'.$simple;
			if (!empty($this->$param)) {
				$by = strtolower($simple);
				foreach ($this->$param as $item) {
					$results[] = array(
						'by' => $by,
						'primary' => $item,
						'secondary' => NULL
					);
				}
			}
		}
		if (!empty($this->mByDay)) {
			foreach ($this->mByDay as $item) {
				$results[] = array(
					'by' => 'day',
					'primary' => $item[1],
					'secondary' => $item[0]
				);
			}
		}
		return $results;
	}
	
	/// Set the matching seconds.
	/**
	 * @param $Value array[int],int,NULL Array of matching seconds or NULL.
	 */
	function SetBySecond($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mBySecond) {
				$this->mBySecond = array($Value);
			} elseif (!in_array($Value, $this->mBySecond)) {
				$this->mBySecond[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mBySecond = $Value;
		}
	}
	
	/// Set the matching minutes.
	/**
	 * @param $Value array[int],NULL Array of matching minutes or NULL.
	 */
	function SetByMinute($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByMinute) {
				$this->mByMinute = array($Value);
			} elseif (!in_array($Value, $this->mByMinute)) {
				$this->mByMinute[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByMinute = $Value;
		}
	}
	
	/// Set the matching hours.
	/**
	 * @param $Value array[int],NULL Array of matching hours or NULL.
	 */
	function SetByHour($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByHour) {
				$this->mByHour = array($Value);
			} elseif (!in_array($Value, $this->mByHour)) {
				$this->mByHour[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByHour = $Value;
		}
	}
	
	/// Set the matching days.
	/**
	 * @param $Value array[array[int,int]],NULL Array of matching days or NULL.
	 * @param $Optional int,NULL Optional value.
	 */
	function SetByDay($Value = NULL, $Optional = NULL)
	{
		if (is_int($Value)) {
			$Value = array($Optional, $Value);
			if (NULL === $this->mByDay) {
				$this->mByDay = array($Value);
			} elseif (!in_array($Value, $this->mByDay)) {
				$this->mByDay[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_array\',$Value)))');
			$this->mByDay = $Value;
		}
	}
	
	/// Set the matching month days.
	/**
	 * @param $Value array[int],NULL Array of matching month days or NULL.
	 */
	function SetByMonthDay($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByMonthDay) {
				$this->mByMonthDay = array($Value);
			} elseif (!in_array($Value, $this->mByMonthDay)) {
				$this->mByMonthDay[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByMonthDay = $Value;
		}
	}
	
	/// Set the matching year days.
	/**
	 * @param $Value array[int],NULL Array of matching year days or NULL.
	 */
	function SetByYearDay($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByYearDay) {
				$this->mByYearDay = array($Value);
			} elseif (!in_array($Value, $this->mByYearDay)) {
				$this->mByYearDay[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByYearDay = $Value;
		}
	}
	
	/// Set the matching week numbers.
	/**
	 * @param $Value array[int],NULL Array of matching week numbers or NULL.
	 */
	function SetByWeekNo($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByWeekNo) {
				$this->mByWeekNo = array($Value);
			} elseif (!in_array($Value, $this->mByWeekNo)) {
				$this->mByWeekNo[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByWeekNo = $Value;
		}
	}
	
	/// Set the matching months.
	/**
	 * @param $Value array[int],NULL Array of matching months or NULL.
	 */
	function SetByMonth($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByMonth) {
				$this->mByMonth = array($Value);
			} elseif (!in_array($Value, $this->mByMonth)) {
				$this->mByMonth[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByMonth = $Value;
		}
	}
	
	/// Set the matching occurrence positions.
	/**
	 * @param $Value array[int],NULL Array of matching occurrence positions or NULL.
	 */
	function SetBySetPos($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mBySetPos) {
				$this->mBySetPos = array($Value);
			} elseif (!in_array($Value, $this->mBySetPos)) {
				$this->mBySetPos[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mBySetPos = $Value;
		}
	}
	
	/// Set the matching term.
	/**
	 * @param $Value array[int],NULL Array of matching occurrence positions or NULL.
	 */
	function SetByTerm($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByTerm) {
				$this->mByTerm = array($Value);
			} elseif (!in_array($Value, $this->mByTerm)) {
				$this->mByTerm[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByTerm = $Value;
		}
	}
	
	/// Set the matching days of term.
	/**
	 * @param $Value array[int],NULL Array of matching occurrence positions or NULL.
	 */
	function SetByTermDay($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByTermDay) {
				$this->mByTermDay = array($Value);
			} elseif (!in_array($Value, $this->mByTermDay)) {
				$this->mByTermDay[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByTermDay = $Value;
		}
	}
	
	/// Set the matching weeks of term.
	/**
	 * @param $Value array[int],NULL Array of matching occurrence positions or NULL.
	 */
	function SetByTermWeek($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByTermWeek) {
				$this->mByTermWeek = array($Value);
			} elseif (!in_array($Value, $this->mByTermWeek)) {
				$this->mByTermWeek[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByTermWeek = $Value;
		}
	}
	
	/// Set the matching days relative to easter.
	/**
	 * @param $Value array[int],NULL Array of matching occurrence positions or NULL.
	 */
	function SetByEaster($Value = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $this->mByEaster) {
				$this->mByEaster = array($Value);
			} elseif (!in_array($Value, $this->mByEaster)) {
				$this->mByEaster[] = $Value;
			}
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('NULL === $Value || (is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value)))');
			$this->mByEaster = $Value;
		}
	}
	
	/// Set the start day of the week.
	/**
	 * @param $Value int New week start.
	 */
	function SetWkSt($Value = 0)
	{
		/// @pre @a $Value must be in [0,6].
		assert('is_int($Value) && $Value >= 0 && $Value < 7');
		$this->mWkSt = $Value;
	}
	
	/// Get the start day of the week.
	/**
	 * @return int Week start.
	 */
	function GetWkSt()
	{
		return $this->mWkSt;
	}
	
	
	// takes iCalendar 2 day format and makes it into 3 characters
	// if $txt is true, it returns the 3 letters, otherwise it returns the
	// integer of that day; 0=Sun, 1=Mon, etc.
	function two2threeCharDays($day, $txt=true) {
		switch($day) {
			case 'SU': return ($txt ? 'sun' : '0');
			case 'MO': return ($txt ? 'mon' : '1');
			case 'TU': return ($txt ? 'tue' : '2');
			case 'WE': return ($txt ? 'wed' : '3');
			case 'TH': return ($txt ? 'thu' : '4');
			case 'FR': return ($txt ? 'fri' : '5');
			case 'SA': return ($txt ? 'sat' : '6');
		}
	}
	
	/// Get occurrences.
	/**
	 * @param $StartDate	timestamp Start date.
	 * @param $MaxDate		timestamp Max date to return.
	 * @return array['YYYYMMDD' => array[{'HHMMSS',NULL} => duration] ] Matching occurrences in range.
	 *
	 * @todo Rewrite GetOccurrences properly.
	 * @note Much of this code is taken from phpicalendar v 2.23, released under the GPL
	 *	see http://phpicalendar.net/
	 *	functions/ical_parser
	 */
	function GetOccurrences($start_date_time, $first_duration, $start_range_time, $end_range_time)
	{
		$byday = $this->mByDay;
		$count = $this->mCount;
		$number = $this->mInterval;
		$until = $this->mUntil;
		$byday = $this->mByDay;
		$bymonth = $this->mByMonth;
		$bymonthday = $this->mByMonthDay;
		$bysetpos = $this->mBySetPos;
		$byyearday = $this->mByYearDay;
		$except_dates = array();
		$bleed_time = -1;
		$bleed_check = 0;
		
		$end_unixtime 	= $start_date_time + $first_duration;
		$start_time 	= date ('His', $start_date_time);
		$end_time 		= date ('His', $end_unixtime);
		
		$hour   = substr($start_time,0,2);
		$minute = substr($start_time,2,2);
		$second = substr($start_time,4,2);
		
		$master_array = array();
		
		// Modify the COUNT based on BYDAY
		if ((is_array($byday)) && (NULL !== $count)) {
			$temp = sizeof($byday);
			$count = ($count / $temp);
			unset($temp);
		}
	
		// if $until isn't set yet, we set it to the end of our range we're looking at
		
		if (NULL === $until) $until = $end_range_time;
		$abs_until = date('YmdHis', $until);
		$end_date_time = $until;
		$start_range_time_tmp = $start_range_time;
		$end_range_time_tmp = $end_range_time;
		
		// If the $end_range_time is less than the $start_date_time, or $start_range_time is greater
		// than $end_date_time, we may as well forget the whole thing
		// It doesn't do us any good to spend time adding data we aren't even looking at
		// this will prevent the year view from taking way longer than it needs to
		if ($end_range_time_tmp >= $start_date_time && $start_range_time_tmp <= $end_date_time) {
		
			// if the beginning of our range is less than the start of the item, we may as well set it equal to it
			if ($start_range_time_tmp < $start_date_time){
				$start_range_time_tmp = $start_date_time;
			}
			if ($end_range_time_tmp > $end_date_time) $end_range_time_tmp = $end_date_time;

			// initialize the time we will increment
			$next_range_time = $start_range_time_tmp;
			
			// FIXME: This is a hack to fix repetitions with $interval > 1
	/// number is interval, count is quantity
	/// more than one, more than one frequency apart
	/// extends the quantity to cover the extension because of gaps
			if ($count > 1 && $number > 1) $count = 1 + ($count - 1) * $number;
			
			$count_to = 0;
			// start at the $start_range and go until we hit the end of our range.
			if(!isset($wkst)) $wkst='SU';
			$week_start_day = $wkst3char = $this->two2threeCharDays($wkst);
			while (($next_range_time >= $start_range_time_tmp) && ($next_range_time <= $end_range_time_tmp) && ($count_to !== $count)) {
		/// finds number of frequency units between start and next range
				$diff = $this->mFrequencyClass->Compare(date('Ymd',$next_range_time), date('Ymd',$start_date_time));
		/// most likely in range of events
				if ($diff < $count || NULL === $count) {
			/// one of the matching intervals?
					if ($diff % $number == 0) {
						$interval = $number;
						switch ($this->mFrequency) {
							case 'daily':
								$next_date_time = $next_range_time;
								$recur_data[] = $next_date_time;
								break;
							case 'weekly':
								// Populate $byday with the default day if it's not set.
								if (!isset($byday)) {
									$byday[] = strtoupper(substr(date('D', $start_date_time), 0, 2));
								}
								if (is_array($byday)) {
									foreach($byday as $day) {
										$day = $this->two2threeCharDays($day[1]);
										#need to find the first day of the appropriate week.
										#dateOfweek uses weekstartday as a global variable. This has to be changed to $wkst,
										#but then needs to be reset for other functions
										$week_start_day_tmp = $week_start_day;
										$week_start_day = $wkst3char;
										
										$the_sunday = dateOfWeek(date("Ymd",$next_range_time), $wkst3char);
										$next_date_time = strtotime($day,strtotime($the_sunday)) + (12 * 60 * 60);
										$week_start_day = $week_start_day_tmp; #see above reset to global value
										
										#reset $next_range_time to first instance in this week.
										if ($next_date_time < $next_range_time){
											$next_range_time = $next_date_time;
										}
										// Since this renders events from $next_range_time to $next_range_time + 1 week, I need to handle intervals
										// as well. This checks to see if $next_date_time is after $day_start (i.e., "next week"), and thus
										// if we need to add $interval weeks to $next_date_time.
										if ($next_date_time > strtotime($week_start_day, $next_range_time) && $interval > 1) {
										#	$next_date_time = strtotime('+'.($interval - 1).' '.$freq_type, $next_date_time);
										}
										$recur_data[] = $next_date_time;
									}
								}
								break;
							case 'monthly':
								if (empty($bymonth)) $bymonth = array(1,2,3,4,5,6,7,8,9,10,11,12);
								$next_range_time = strtotime(date('Y-m-01', $next_range_time));
								if (NULL !== $bysetpos) {
									/* bysetpos code from dustinbutler
									start on day 1 or last day.
									if day matches any BYDAY the count is incremented.
									SETPOS = 4, need 4th match
									SETPOS = -1, need 1st match
									*/
									/// @todo Standard supports multiple setpos values
									$bysetpos = $bysetpos[0];
									
									$year = date('Y', $next_range_time);
									$month = date('m', $next_range_time);
									if ($bysetpos > 0) {
										$next_day = '+1 day';
										$day = 1;
									} else {
										$next_day = '-1 day';
										$day = $totalDays[$month];
									}
									$day = mktime(0, 0, 0, $month, $day, $year);
									$countMatch = 0;
									while ($countMatch != abs($bysetpos)) {
										/* Does this day match a BYDAY value? */
										$thisDay = $day;
										$textDay = strtoupper(substr(date('D', $thisDay), 0, 2));
										if (NULL !== $byday && in_array($textDay, $byday)) {
											$countMatch++;
										}
										$day = strtotime($next_day, $thisDay);
									}
									$recur_data[] = $thisDay;
								} elseif ((isset($bymonthday)) && (!isset($byday))) {
									foreach($bymonthday as $day) {
										if ($day < 0) $day = ((date('t', $next_range_time)) + ($day)) + 1;
										$year = date('Y', $next_range_time);
										$month = date('m', $next_range_time);
										if (checkdate($month,$day,$year)) {
											$next_date_time = mktime(0,0,0,$month,$day,$year);
											$recur_data[] = $next_date_time;
										}
									}
								} elseif (is_array($byday)) {
									foreach($byday as $day_info) {
										list($nth, $on_day_num) = $day_info;
										//ereg ('([-\+]{0,1})?([0-9]{1})?([A-Z]{2})', $day, $byday_arr);
										//Added for 2.0 when no modifier is set
										//if ($byday_arr[2] != '') {
										if ($nth !== NULL) {
											$negative = ($nth < 0 ? -1 : 1);
											$nth += $negative;
										} else {
											$nth = 0;
										}
										$on_day = $this->two2threeCharDays(array_search($on_day_num,self::$sWeekdays));
										if ($nth !== NULL && $negative < 0) {
											$last_day_tmp = date('t',$next_range_time);
											$next_range_time = strtotime(date('Y-m-'.$last_day_tmp, $next_range_time));
											$last_tmp = (date('w',$next_range_time) == $on_day_num) ? '' : 'last ';
											$next_date_time = strtotime($last_tmp.$on_day, $next_range_time) - $nth * 604800;
											$month = date('m', $next_date_time);
											if (in_array($month, $bymonth)) {
												$recur_data[] = $next_date_time;
											}
											#reset next_range_time to start of month
											$next_range_time = strtotime(date('Y-m-'.'1', $next_range_time));

										} elseif (isset($bymonthday) && (!empty($bymonthday))) {
											// This supports MONTHLY where BYDAY and BYMONTH are both set
											foreach($bymonthday as $day) {
												$year 	= date('Y', $next_range_time);
												$month 	= date('m', $next_range_time);
												if (checkdate($month,$day,$year)) {
													$next_date_time = mktime(0,0,0,$month,$day,$year);
													$daday = strtolower(strftime("%a", $next_date_time));
													if ($daday == $on_day && in_array($month, $bymonth)) {
														$recur_data[] = $next_date_time;
													}
												}
											}
										} elseif ($nth !== NULL && $negative > 0) {
											$next_date_time = strtotime($on_day, $next_range_time) + $nth * 604800;
											$month = date('m', $next_date_time);
											if (in_array($month, $bymonth)) {
												$recur_data[] = $next_date_time;
											}
										}
										$next_date = date('Ymd', $next_date_time);
									}
								}
								break;
							case 'yearly':
								if (($bymonth === NULL) || (sizeof($bymonth) == 0)) {
									$m = date('m', $start_date_time);
									$bymonth = array("$m");
								}

								foreach($bymonth as $month) {
									// Make sure the month & year used is within the start/end_range.
									if ($month < date('m', $next_range_time)) {
										$year = date('Y', $next_range_time);
									} else {
										$year = date('Y', $next_range_time);
									}
									if (isset($bysetpos)){
										/* bysetpos code from dustinbutler
										start on day 1 or last day.
										if day matches any BYDAY the count is incremented.
										SETPOS = 4, need 4th match
										SETPOS = -1, need 1st match
										*/
										if ($bysetpos > 0) {
											$next_day = '+1 day';
											$day = 1;
										} else {
											$next_day = '-1 day';
											$day = date("t",$month);
										}
										$day = mktime(12, 0, 0, $month, $day, $year);
										$countMatch = 0;
										while ($countMatch != abs($bysetpos)) {
											/* Does this day match a BYDAY value? */
											$thisDay = $day;
											$textDay = strtoupper(substr(date('D', $thisDay), 0, 2));
											if (in_array($textDay, $byday)) {
												$countMatch++;
											}
											$day = strtotime($next_day, $thisDay);
										}
										$recur_data[] = $thisDay;
									}
									if ((isset($byday)) && (is_array($byday))) {
										$checkdate_time = mktime(0,0,0,$month,1,$year);
										foreach($byday as $byday_item) {
											$nth = (NULL === $byday_item[0] ? 0 : $byday_item[0]);
											$day = $byday_item[1];
											$on_day = $this->two2threeCharDays($day);
											$on_day_num = $this->two2threeCharDays($day,false);
											if ($nth < 0) {
												$nth = -$nth;
												$last_day_tmp = date('t',$checkdate_time);
												$checkdate_time = strtotime(date('Y-m-'.$last_day_tmp, $checkdate_time));
												$last_tmp = (date('w',$checkdate_time) == $on_day_num) ? '' : 'last ';
												$recur_data[] = strtotime($last_tmp.$on_day.' -'.$nth.' week', $checkdate_time);
											} else {
												$recur_data[] = strtotime($on_day.' +'.$nth.' week', $checkdate_time);
											}
										}
									} elseif (NULL !== $bymonthday) {
										foreach ($bymonthday as $day) {
											if ($day < 0) $day = ((date('t', $next_range_time)) + ($day)) + 1;
											$year = date('Y', $next_range_time);
											//$month = date('m', $next_range_time);
											if (checkdate($month,$day,$year)) {
												$next_date_time = mktime(0,0,0,$month,$day,$year);
												$recur_data[] = $next_date_time;
											}
										}
									} else {
										$day 	= date('d', $start_date_time);
										$recur_data[] = mktime(0,0,0,$month,$day,$year);
										//echo date('Ymd',$next_date_time).$summary.'<br>';
									}
									//$recur_data[] = $next_date_time;
								}
								if (isset($byyearday)) {
									foreach ($byyearday as $yearday) {
										ereg ('([-\+]{0,1})?([0-9]{1,3})', $yearday, $byyearday_arr);
										if ($byyearday_arr[1] == '-') {
											$ydtime = mktime(0,0,0,12,31,$this_year);
											$yearnum = $byyearday_arr[2] - 1;
											$next_date_time = strtotime('-'.$yearnum.' days', $ydtime);
										} else {
											$ydtime = mktime(0,0,0,1,1,$this_year);
											$yearnum = $byyearday_arr[2] - 1;
											$next_date_time = strtotime('+'.$yearnum.' days', $ydtime);
										}
										$recur_data[] = $next_date_time;
									}
								}
								break;
							default:
								// anything else we need to end the loop
								$next_range_time = $end_range_time_tmp + 100;
								$count_to = $count;
						} // switch
					} else {
						$interval = 1;
					} // ! $diff % $number == 0
					$next_range_time = $this->mFrequencyClass->Increment($interval, $next_range_time);
				} else {
					// end the loop because we aren't going to write this event anyway
					$count_to = $count;
				} // ! $diff < $count
				// use the same code to write the data instead of always changing it 5 times
				if (isset($recur_data) && is_array($recur_data)) {
					foreach($recur_data as $recur_data_time) {
						$recur_data_year = date('Y', $recur_data_time);
						$recur_data_month = date('m', $recur_data_time);
						$recur_data_day = date('d', $recur_data_time);
						$recur_data_date = $recur_data_year.$recur_data_month.$recur_data_day;
						
						if (($recur_data_time > $start_date_time) && ($recur_data_time <= $end_date_time) && ($count_to !== $count) && !in_array($recur_data_date, $except_dates)) {
							if (isset($allday_start) && $allday_start != '') {
								$start_time2 = $recur_data_time;
								$end_time2 = strtotime('+'.$diff_allday_days.' days', $recur_data_time);
								while ($start_time2 < $end_time2) {
									$start_date2 = date('Ymd', $start_time2);
									/// @todo figure out what this bit of code does.
									$master_array[$start_date2][('-1')] = $first_duration;
									$start_time2 = strtotime('+1 day', $start_time2);
								}
							} else {
								$start_unixtime_tmp = mktime($hour,$minute,$second,$recur_data_month,$recur_data_day,$recur_data_year);
								$end_unixtime_tmp = $start_unixtime_tmp + $first_duration;
								
								if (($end_time >= $bleed_time) && ($bleed_check == '-1')) {
									$start_tmp = strtotime(date('Ymd',$start_unixtime_tmp));
									$end_date_tmp = date('Ymd',$end_unixtime_tmp);
									while ($start_tmp < $end_unixtime_tmp) {
										$start_date_tmp = date('Ymd',$start_tmp);
										if ($start_date_tmp == $recur_data_year.$recur_data_month.$recur_data_day) {
											$time_tmp = $hour.$minute.$second;
										} else {
											$time_tmp = '000000';
										}
										if ($start_date_tmp == $end_date_tmp) {
											$end_time_tmp = $end_time;
										} else {
											$end_time_tmp = '240000';
										}
										
										// Let's double check the until to not write past it
										$until_check = $start_date_tmp.$time_tmp;
										if ($abs_until > $until_check) {
											$master_array[$start_date_tmp][$time_tmp] =  $first_duration;
											//checkOverlap($start_date_tmp, $time_tmp, $uid);
										}
										$start_tmp = strtotime('+1 day',$start_tmp);
									}
								} else {
									if ($bleed_check == '-1') {
										$end_time_tmp1 = '240000';
									}
									if (!isset($end_time_tmp1)) $end_time_tmp1 = $end_time;
								
									// Let's double check the until to not write past it
									$until_check = $recur_data_date.$hour.$minute.$second;
									if ($abs_until > $until_check) {
										$master_array[($recur_data_date)][($hour.$minute.$second)] = $first_duration;
										//checkOverlap($recur_data_date, ($hour.$minute), $uid);
									}
								}
							}
						}
					}
					unset($recur_data);
				}
			}
		}
		return $master_array;
	}
}

/// Recurrence set resolution class.
/**
 * @todo Do caching of results, clearing on changes.
 */
class RecurrenceSet
{
	/// timestamp Start time of first occurrence.
	protected $mStart = NULL;
	/// timestamp End time of first occurrence.
	protected $mEnd = NULL;
	/// array['YYYYMMDD' => array[{'HHMMSS',NULL} => {duration,NULL}] ] Dates to include.
	protected $mRDates  = array();
	/// array[CalendarRecurRule] Recurrence rules to include.
	protected $mRRules  = array();
	/// array['YYYYMMDD' => array[{'HHMMSS',NULL} => {duration,NULL}] ] Dates to exclude.
	protected $mExDates = array();
	/// array[CalendarRecurRule] Recurrence rules to exclude.
	protected $mExRules = array();
	
	/// Find whether the start occurrence has been set yet.
	/**
	 * @return bool Whether @a $mStart has been set yet.
	 */
	function IsStartSet()
	{
		return NULL !== $this->mStart;
	}
	
	/// Set the first occurrence by start and end.
	/**
	 * @param $Start timestamp Start time.
	 * @param $End timestamp,NULL End time.
	 */
	function SetStartEnd($Start, $End)
	{
		/// @pre $End IS NULL OR $Start <= $End
		assert('NULL === $End || $Start <= $End');
		$this->mStart = $Start;
		$this->mEnd = $End;
	}
	
	/// Set the first occurrence by start and duration.
	/**
	 * @param $Start timestamp Start time.
	 * @param $Duration int Duration measured in seconds.
	 */
	function SetStartDuration($Start, $Duration)
	{
		$this->mStart = $Start;
		$this->mEnd = $Start + $Duration;
	}
	
	/// Retrieve the start and end of the first occurrence.
	/**
	 * @return array(timestamp,timestamp) Start and end timestamp.
	 */
	function GetStartEnd()
	{
		return array($this->mStart, $this->mEnd);
	}
	
	/// Set the recur data from an array.
	/**
	 * @param $Data array
	 *	- rdates array['YYYYMMDD' => array[{'HHMMSS',NULL} => {duration,NULL}] ]
	 *	- rrules array[CalendarRecurRule]
	 *	- exdates array['YYYYMMDD' => array[{'HHMMSS',NULL} => {duration,NULL}] ]
	 *	- exrules array[CalendarRecurRule]
	 */
	function SetRecurData($Data)
	{
		list($this->mRDates, $this->mRRules, $this->mExDates, $this->mExRules)
			= $Data;
	}
	
	/// Get an array of recurrence dates.
	/**
	 * @return array Results, each with the following:
	 *	- 'start' timestamp
	 *	- 'time_associated' bool
	 *	- 'duration' int Measured in seconds
	 *	- 'exclude' bool
	 */
	function GetDatesArray()
	{
		$results = array();
		foreach (array('mRDates' => FALSE, 'mExDates' => TRUE) as $field => $exclude) {
			foreach ($this->$field as $date => $times) {
				foreach ($times as $time => $duration) {
					if ($time_associated = NULL !== $time) {
						$start = strtotime($date.' '.$time);
					} else {
						$start = strtotime($date);
					}
					// (exclude the start date which is added when resolving)
					if ($exclude || $start !== $this->mStart) {
						$results[] = array(
							'start' => $start,
							'duration' => $duration,
							'time_associated' => $time_associated,
							'exclude' => $exclude,
						);
					}
				}
			}
		}
		return $results;
	}
	
	/// Get an array of recurrence rules.
	/**
	 * @return array Results, each with the following:
	 *	- 'rule' CalendarRecurRule
	 *	- 'exclude' bool
	 */
	function GetRulesArray()
	{
		$results = array();
		foreach (array('mRRules' => FALSE, 'mExRules' => TRUE) as $field => $exclude) {
			foreach ($this->$field as $rule) {
				$results[] = array(
					'rule' => $rule,
					'exclude' => $exclude,
				);
			}
		}
		return $results;
	}
	
	/// Add recurring dates.
	/**
	 * @param $RDates array['YYYYMMDD' => array[{'HHMMSS',NULL} => {duration,NULL}] ] Dates to include.
	 */
	function AddRDates($RDates)
	{
		$this->mRDates = self::UnionDates($this->mRDates, $RDates);
	}
	
	/// Add exclusion dates.
	/**
	 * @param $ExDates array['YYYYMMDD' => array[{'HHMMSS',NULL} => {duration,NULL}] ] Dates to exclude.
	 */
	function AddExDates($ExDates)
	{
		$this->mExDates = self::UnionDates($this->mExDates, $ExDates);
	}
	
	/// Add recurrence rules.
	/**
	 * @param $RRules array[CalendarRecurRule],CalendarRecurRule Recurrence rule(s).
	 */
	function AddRRules($RRules = array())
	{
		if (is_array($RRules)) {
			foreach ($RRules as $rule) {
				$this->mRRules[] = $rule;
			}
		} else {
			$this->mRRules[] = $RRules;
		}
	}
	
	/// Add exclusion rules.
	/**
	 * @param $ExRules array[CalendarRecurRule],CalendarRecurRule Exclusion rule(s).
	 */
	function AddExRules($ExRules = array())
	{
		if (is_array($ExRules)) {
			foreach ($ExRules as $rule) {
				$this->mExRules[] = $rule;
			}
		} else {
			$this->mExRules[] = $ExRules;
		}
	}
	
	/// Find the resulting occurrences.
	/**
	 * @return array['YYYYMMDD' => {duration}]
	 */
	function Resolve($MinDate, $MaxDate)
	{
		/// @pre Start and duration must have been set.
		assert('isset($this->mStart)');
		if (NULL == $MinDate) {
			$MinDateStr = NULL;
		} else {
			$MinDateStr = date('Ymd', $MinDate);
		}
		assert('NULL !== $MaxDate');
		$MaxDateStr = date('Ymd', $MaxDate);
		
		// Make sure the start date is in RDates
		$StartDate = date('Ymd', $this->mStart);
		$StartTime = date('His', $this->mStart);
		if (is_int($this->mStart) && is_int($this->mEnd)) {
			$Duration = $this->mEnd - $this->mStart;
		} else {
			$Duration = NULL;
		}
		$this->AddRDates(
			array(
				$StartDate => array(
					$StartTime => $Duration,
				),
			)
		);
		
		$results = array();
		foreach (array('mRRules' => 'mRDates', 'mExRules' => 'mExDates') as $rule_name => $array_name) {
			// bound the range
			$results[$array_name] = self::BoundDates($this->$array_name, $MinDateStr, $MaxDateStr);
			// ensure all durations are set
			foreach ($results[$array_name] as $date => $dates) {
				foreach ($dates as $time => $duration) {
					if (NULL === $duration) {
						$results[$array_name][$date][$time] = $Duration;
					}
				}
			}
			// add the rule occurrences to the array
			foreach ($this->$rule_name as $rule) {
				$occurrences = $rule->GetOccurrences($this->mStart, $Duration, $MinDate, $MaxDate);
				$occurrences = self::BoundDates($occurrences, $MinDateStr, $MaxDateStr);
				$results[$array_name] = self::UnionDates($results[$array_name], $occurrences);
			}
		}
		// do the main exclusion
		return self::ExcludeDates($results['mRDates'], $results['mExDates']);
	}
	
	/// Bound a set of dates to a range.
	/**
	 * @param $Dates array[date => array[{time,NULL} => {int,NULL}] ].
	 * @param $MinDate string date In format 'YYYYMMDD'.
	 * @param $MaxDate string date In format 'YYYYMMDD'.
	 * @return array[date => array[{time,NULL} => {int,NULL}] ].
	 */
	static function BoundDates($Dates, $MinDate, $MaxDate)
	{
		// Go through minority adding to priority
		foreach ($Dates as $date => $times) {
			if (($MinDate !== NULL && $date < $MinDate) ||
				($MaxDate !== NULL && $date > $MaxDate)) {
				unset($Dates[$date]);
			}
		}
		return $Dates;
	}
	
	/// Union @a $Priority and @a $Minority giving @a $Priority priority in the case of duplicates.
	/**
	 * @param $Priority array[date => array[{time,NULL} => {int,NULL}] ].
	 * @param $Minority array[date => array[{time,NULL} => {int,NULL}] ].
	 * @return array[date => array[{time,NULL} => {int,NULL}] ].
	 */
	static function UnionDates($Priority, $Minority)
	{
		// Go through minority adding to priority
		foreach ($Minority as $date => $times) {
			if (!array_key_exists($date, $Priority)) {
				$Priority[$date] = $Minority[$date];
			} else {
				foreach ($times as $time => $duration) {
					if (!array_key_exists($time, $Priority[$date])) {
						$Priority[$date][$time] = $duration;
					}
				}
			}
		}
		return $Priority;
	}
	
	/// Compute the exclusion @a $Include - @a $Exclude.
	/**
	 * @param $Include array[date => array[{time,NULL} => {int,NULL}] ].
	 * @param $Exclude array[date => array[{time,NULL} => {int,NULL}] ].
	 * @return array[date => array[{time,NULL} => {int,NULL}] ].
	 *
	 * - Removes any times in @a $Include which are in @a Exclude.
	 * - A time of NULL will remove the entire day from @a Include.
	 * - Returns the new @a $Include.
	 */
	static function ExcludeDates($Include, $Exclude)
	{
		// Go through exclusions removing from inclusions
		foreach ($Exclude as $date => $times) {
			if (array_key_exists($date, $Include)) {
				foreach ($times as $time => $duration) {
					if (NULL == $time) {
						unset($Include[$date]);
						break;
					} elseif (array_key_exists($time, $Include[$date])) {
						unset($Include[$date][$time]);
					}
				}
			}
		}
		return $Include;
	}
	
	/// Compute the difference @a $Include - @a $Exclude.
	/**
	 * @param $Include array[date => array[{time,NULL} => {int,NULL}] ].
	 * @param $Exclude array[date => array[{time,NULL} => {int,NULL}] ].
	 * @return array[date => array[{time,NULL} => {int,NULL}] ].
	 *
	 * - Finds all times of @a $Include which aren't in @a $Exclude.
	 */
	static function SubtractDates($Include, $Exclude)
	{
		// Go through exclusions removing from inclusions
		foreach ($Exclude as $date => $times) {
			if (array_key_exists($date, $Include)) {
				foreach ($times as $time => $duration) {
					if (array_key_exists($time, $Include[$date])) {
						unset($Include[$date][$time]);
					}
				}
			}
		}
		return $Include;
	}
	
	/// Compute the changes between two occurrence sets.
	/**
	 * @param $Before array[date => array[{time,NULL} => {int,NULL}] ].
	 * @param $After array[date => array[{time,NULL} => {int,NULL}] ].
	 * @return array
	 *	- (-1) deleted = @a $Before | @a $After
	 *	- 0 unchanged = @a $Before | deleted
	 *	- 1 added = @a $After | @a $Before
	 */
	static function DiffDates($Before, $After)
	{
		$added = self::SubtractDates($After, $Before);
		$removed = self::SubtractDates($Before, $After);
		$unchanged = self::SubtractDates($Before, $removed);
		return array(-1 => $removed, 0 => $unchanged, 1 => $added);
	}
}

/// The recurrence model
/**
 * @todo Get entire event recurrence information from db
 * @todo Get current recurrences of an event
 * @todo Update all out of date events (diff, apply new ones, take into account for until date as well)
 * @todo Change recurrence rule (show diff, apply changes)
 * @todo reduce recur rule to ical (freq=term etc)
 */
class Recurrence_model extends model
{
	/// Insert the rdates, exdates, rrules, exrules in a set into the db.
	/**
	 * @param $Dates array['YYYYMMDD' => array[{'HHMMSS',NULL} => {duration,NULL}]] 
	 * @param $EventId int Event identifier.
	 */
	function InsertRecurrenceSet($Set, $EventId)
	{
		$dates = $Set->GetDatesArray();
		
		if (!empty($dates)) {
			// produce the sql for each row.
			foreach ($dates as $key => $date) {
				$dates[$key] =
					$EventId.','.
					'FROM_UNIXTIME('.$date['start'].'),'.
					($date['time_associated'] ? 'TRUE' : 'FALSE').','.
					$this->db->escape($date['duration']).','.
					($date['exclude'] ? 'TRUE' : 'FALSE');
			}
			
			// the main query
			$sql_insert_dates =
			'INSERT INTO event_dates (
				event_date_event_id,
				event_date_start,
				event_date_time_associated,
				event_date_duration,
				event_date_exclude
			) VALUES ('.implode('),(', $dates).')
			ON DUPLICATE KEY UPDATE
				event_date_duration=IF(event_date_exclude OR VALUES(event_date_exclude,
					NULL
					VALUES(event_date_duration)),
				event_date_exclude=event_date_exclude OR VALUES(event_date_exclude)';
			
			// run it
			$this->db->query($sql_insert_dates);
			$dates_added = $this->db->affected_rows();
		} else {
			$dates_added = 0;
		}
		
		$rules = $Set->GetRulesArray();
		
		$rules_added = 0;
		foreach ($rules as $rule) {
			$recur = $rule['rule'];
			$wkst = array_search($recur->GetWkSt(), CalendarRecurRule::$sWeekdays);
			if (FALSE === $wkst) {
				$wkst = NULL;
			}
			$fields = array(
				'event_recur_rule_event_id'   => $EventId,
				'event_recur_rule_exclude'    => $rule['exclude'],
				'event_recur_rule_frequency'  => $recur->GetFrequency(),
				'event_recur_rule_until'      => $recur->GetUntil(),
				'event_recur_rule_count'      => $recur->GetCount(),
				'event_recur_rule_interval'   => $recur->GetInterval(),
				'event_recur_rule_week_start' => $wkst,
			);
			
			$this->db->insert('event_recur_rules', $fields);
			$affected = $this->db->affected_rows();
			
			if ($affected > 0) {
				$rule_id = $this->db->insert_id();
				// add the other bits
				$bys = $recur->GetByArray();
				
				if (!empty($bys)) {
					// produce the sql for each row.
					foreach ($bys as $key => $by) {
						$bys[$key] =
							$rule_id.','.
							'"'.$by['by'].'",'.
							$this->db->escape($by['primary']).','.
						// secondary is part of primary key so can't be NULL, use zero
							$this->db->escape(NULL !== $by['secondary'] ? $by['secondary'] : 0);
					}
					
					// the main query
					$sql_insert_bys =
					'INSERT INTO event_recur_rule_by (
						event_recur_rule_by_event_recur_rule_id,
						event_recur_rule_by_by,
						event_recur_rule_by_primary,
						event_recur_rule_by_secondary
					) VALUES ('.implode('),(', $bys).')';
						
					// run it
					$this->db->query($sql_insert_bys);
					$bys_added = $this->db->affected_rows();
				} else {
					$bys_added = 0;
				}
			}
			$rules_added += $affected;
		}
	}
	
	/// Update a given recurrence rule in the database.
	function UpdateRecurrenceRule($Recur)
	{
		
	}
	
	/// Get a recurrence rule from the database.
	/**
	 * @param $EventId int Id of event.
	 * @return array
	 *	- rdates
	 *	- rrules
	 *	- exdates
	 *	- exrules
	 * @todo Optional date limits
	 */
	function SelectRecurByEvent($EventId)
	{
		static $date_fields = array(
			//'event_date_event_id		AS event_id',
			'event_date_start			AS start',
			'event_date_time_associated	AS time_associated',
			'event_date_duration		AS duration',
			'event_date_exclude			AS exclude',
		);
		
		// Get dates
		$this->db->select(implode(',',$date_fields));
		$this->db->from('event_dates');
		$this->db->where(array('event_date_event_id' => $EventId));
		/// @todo Limit by time using range of selection (min <= date <= max)
		$dates_query = $this->db->get();
		$dates = $dates_query->result_array();
		
		$include_ranges = array();
		$exclude_ranges = array();
		foreach ($dates as $date) {
			$start_parts = explode(' ', $date['start']);
			$start_date = str_replace('-','',$start_parts[0]); // YYYYMMDD
			if ($date['time_associated']) {
				$start_time = str_replace(':','',$start_parts[1]); // HHMMSS
			} else {
				$start_time = NULL;
			}
			if (NULL === $date['duration']) {
				$duration = NULL;
			} else {
				$duration = (int)$date['duration'];
			}
			if (!$date['exclude']) {
				// include
				if (!array_key_exists($start_date, $include_ranges)) {
					$include_ranges[$start_date] = array();
				}
				$include_ranges[$start_date][$start_time] = $duration;
			} else {
				// exclude (never a range)
				if (!array_key_exists($start_date, $exclude_ranges)) {
					$exclude_ranges[$start_date] = array();
				}
				$exclude_ranges[$start_date][$start_time] = FALSE;
			}
		}
		
		
		static $rule_fields = array(
			'event_recur_rule_id			AS rule_id',
			'event_recur_rule_event_id		AS event_id',
			'event_recur_rule_exclude		AS exclude',
			'event_recur_rule_frequency		AS frequency',
			'UNIX_TIMESTAMP(event_recur_rule_until)	AS until',
			'event_recur_rule_count			AS count',
			'event_recur_rule_interval		AS recur_interval',
			'event_recur_rule_week_start	AS week_start',
			/// @todo join with first occurrence
			
			'event_recur_rule_by_by			AS by_by',
			'event_recur_rule_by_primary	AS by_primary',
			'event_recur_rule_by_secondary	AS by_secondary',
		);
		/// @todo Limit by time using range of selection (until > min)
		
		// Get recurrence rules
		$this->db->select(implode(',',$rule_fields));
		$this->db->from('event_recur_rules');
		$this->db->join('event_recur_rule_by',
			'event_recur_rule_by_event_recur_rule_id = event_recur_rule_id','left');
		$this->db->where(array('event_recur_rule_event_id' => $EventId));
		$rules_query = $this->db->get();
		
		// Turn each rule into an object
		$rrules = array();
		$exrules = array();
		$results = $rules_query->result_array();
		foreach ($results as $rule_data) {
			$category_name = ($rule_data['exclude'] ? 'exrules' : 'rrules');
			// If not already created, do so now
			$recur_id = (int)$rule_data['rule_id'];
			if (!array_key_exists($recur_id, $$category_name)) {
				$rule = new CalendarRecurRule();
				$rule->SetFrequency($rule_data['frequency']);
				if (NULL !== $rule_data['until']) {
					$rule->SetUntil((int)$rule_data['until']);
				} elseif (NULL !== $rule_data['count']) {
					$rule->SetCount((int)$rule_data['count']);
				}
				if (NULL !== $rule_data['recur_interval']) {
					$rule->SetInterval((int)$rule_data['recur_interval']);
				}
				if (NULL !== $rule_data['week_start']) {
					$rule->SetWkSt(CalendarRecurRule::$sWeekdays[strtoupper($rule_data['week_start'])]);
				}
				if ($rule_data['exclude']) {
					$exrules[$recur_id] = $rule;
				} else {
					$rrules[$recur_id] = $rule;
				}
			}
			// If by data included, add it
			if (NULL !== $rule_data['by_by']) {
				$primary = (int)$rule_data['by_primary'];
				$optional = $rule_data['by_secondary'];
				if (NULL !== $optional) {
					if ($optional != 0) {
						$optional = (int)$optional;
					} else {
						$optional = NULL;
					}
				}
				if ($rule_data['exclude']) {
					$rule = & $exrules[$recur_id];
				} else {
					$rule = & $rrules[$recur_id];
				}
				switch ($rule_data['by_by']) {
					case 'second':
						$rule->SetBySecond($primary);
						break;
					case 'minute':
						$rule->SetByMinute($primary);
						break;
					case 'hour':
						$rule->SetByHour($primary);
						break;
					case 'day':
						$rule->SetByDay($primary, $optional);
						break;
					case 'monthday':
						$rule->SetByMonthDay($primary);
						break;
					case 'yearday':
						$rule->SetByYearDay($primary);
						break;
					case 'weekno':
						$rule->SetByWeekNo($primary);
						break;
					case 'month':
						$rule->SetByMonth($primary);
						break;
					case 'setpos':
						$rule->SetBySetPos($primary);
						break;
					case 'term':
						$rule->SetByTerm($primary);
						break;
					case 'termday':
						$rule->SetByTermDay($primary);
						break;
					case 'termweek':
						$rule->SetByTermWeek($primary);
						break;
					case 'easter':
						$rule->SetByEaster($primary);
						break;
				}
			}
		}
		
		return array($include_ranges, $rrules, $exclude_ranges, $exrules);
	}
}

?>