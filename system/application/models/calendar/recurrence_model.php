<?php

/**
 * @file models/calendar/recurrence_model.php
 * @brief Recurrencce classes.
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @note James Hogan 25th Sep 07
 *  - rewrote generation code from scratch so no phpicalendar code included.
 */

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
	/// int Recurrence id in database if retrieved from database.
	protected $mRecurId;
	
	/// $sFrequencies Frequency of repetition.
	protected $mFrequency	= NULL;
	/// [DateTime] Maximum acceptable date and time.
	protected $mUntil		= NULL;
	/// [int] Number of acceptable date and times.
	protected $mCount		= NULL;
	/// int Interval of the frequency.
	protected $mInterval	= 1;
	/// array[int<[0,59]> unique] Seconds to match.
	protected $mBySecond	= array();
	/// array[int<[0,59]> unique] Minutes to match.
	protected $mByMinute	= array();
	/// array[int<[0,23]> unique] Hours to match.
	protected $mByHour		= array();
	/// array[array(int<[,-1]+[1,]>,$sWeekdays) unique] Days of week to match.
	protected $mByDay		= array();
	/// array[int<[-31,-1]+[1,31]> unique] Days of the month to match.
	protected $mByMonthDay	= array();
	/// array[int<[-366,-1]+[1,366]> unique] Days of the year to match.
	protected $mByYearDay	= array();
	/// array[int<[-53,-1]+[1,53]> unique] Weeks of the year to match.
	protected $mByWeekNo	= array();
	/// array[int<[1,12]> unique] Months of the year to match.
	protected $mByMonth		= array();
	/// array[int<[1,]> unique] Which occurrences to use.
	protected $mBySetPos	= array();
	/// array[int<[0,5]> unique] Which terms to match.
	protected $mByTerm		= array();		// non standard
	/// array[int unique] Which days of the terms.
	protected $mByTermDay	= array();		// non standard
	/// array[int<[-20,-1]+[1,20]> unique] Which weeks of the term to match.
	protected $mByTermWeek	= array();		// non standard
	/// array[int<[-366,-1]+[1,366]> unique] Which days relative to easter to match.
	protected $mByEasterDay	= array();		// non standard
	/// $sWeekdays Which day of the week to consider the start.
	protected $mWkSt		= 1;		// = self::$sWeekdays['MO']
	
	// Temporary
	/// [DateTime] Maximum effective date and time.
	protected $mEffectiveUntil	= NULL;
	
	function __construct($init = NULL)
	{
		if (is_array($init)) {
			if (isset($init['freq'])) {
				$this->mFrequency = $init['freq'];
			}
			if (isset($init['until'])) {
				$this->mUntil = $init['until'];
			}
			if (isset($init['count'])) {
				$this->mCount = $init['count'];
			}
			if (isset($init['interval'])) {
				$this->mInterval = $init['interval'];
			}
			if (isset($init['bysecond'])) {
				$this->mBySecond = $init['bysecond'];
			}
			if (isset($init['byminute'])) {
				$this->mByMinute = $init['byminute'];
			}
			if (isset($init['byhour'])) {
				$this->mByHour = $init['byhour'];
			}
			if (isset($init['byday'])) {
				$this->mByDay = $init['byday'];
			}
			if (isset($init['bymonthday'])) {
				$this->mByMonthDay = $init['bymonthday'];
			}
			if (isset($init['byyearday'])) {
				$this->mByYearDay = $init['byyearday'];
			}
			if (isset($init['byweekno'])) {
				$this->mByWeekNo = $init['byweekno'];
			}
			if (isset($init['bymonth'])) {
				$this->mByMonth = $init['bymonth'];
			}
			if (isset($init['bysetpos'])) {
				$this->mBySetPos = $init['bysetpos'];
			}
			if (isset($init['byterm'])) {
				$this->mByTerm = $init['byterm'];
			}
			if (isset($init['bytermday'])) {
				$this->mByTermDay = $init['bytermday'];
			}
			if (isset($init['bytermweek'])) {
				$this->mByTermWeek = $init['bytermweek'];
			}
			if (isset($init['byeasterday'])) {
				$this->mByEasterDay = $init['byeasterday'];
			}
			if (isset($init['wkst'])) {
				$this->mWkSt = $init['wkst'];
			}
		}
	}
	
	/// Get the recurrence rule id or NULL if none.
	function GetRecurId()
	{
		return isset($this->mRecurId) ? $this->mRecurId : NULL;
	}
	
	/// Get an array of the used fields.
	function GetUsedFields()
	{
		$result = array('interval' => true);
		if (NULL !== $this->mUntil) {
			$result['until'] = true;
		}
		if (NULL !== $this->mCount) {
			$result['count'] = true;
		}
		if (!empty($this->mBySecond)) {
			$result['bysecond'] = true;
		}
		if (!empty($this->mByMinute)) {
			$result['byminute'] = true;
		}
		if (!empty($this->mByHour)) {
			$result['byhour'] = true;
		}
		if (!empty($this->mByDay)) {
			$result['byday'] = true;
		}
		if (!empty($this->mByMonthDay)) {
			$result['bymonthday'] = true;
		}
		if (!empty($this->mByYearDay)) {
			$result['byyearday'] = true;
		}
		if (!empty($this->mByWeekNo)) {
			$result['byweekno'] = true;
		}
		if (!empty($this->mByMonth)) {
			$result['bymonth'] = true;
		}
		if (!empty($this->mBySetPos)) {
			$result['bysetpos'] = true;
		}
		if (!empty($this->mByTerm)) {
			$result['byterm'] = true;
		}
		if (!empty($this->mByTermDay)) {
			$result['bytermday'] = true;
		}
		if (!empty($this->mByTermWeek)) {
			$result['bytermweek'] = true;
		}
		if (!empty($this->mByEasterDay)) {
			$result['byeaster'] = true;
		}
		if (1 !== $this->mWkSt) {
			$result['wkst'] = true;
		}
		return $result;
	}
	
	/// Set the frequency type.
	/**
	 * @param $Frequency string Frequency of recurrence (see @a $sFrequencies)
	 */
	function SetFrequency($Frequency)
	{
		assert('in_array($Frequency, self::$sFrequencies)');
		$this->mFrequency = $Frequency;
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
			$this->mUntil = NULL;
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
			$this->mCount = NULL;
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
			$param = 'mBy'.$simple;
			if (!empty($this->$param)) {
				$by = strtolower($simple);
				foreach ($this->$param as $item => $dummy) {
					$results[] = array(
						'by' => $by,
						'primary' => $item,
						'secondary' => NULL
					);
				}
			}
		}
		if (!empty($this->mByDay)) {
			foreach ($this->mByDay as $item => $data) {
				if (is_int($data)) {
					$results[] = array(
						'by' => 'day',
						'primary' => $item,
						'secondary' => NULL
					);
				} elseif (is_array($data)) {
					foreach ($data as $day => $dummy) {
						$results[] = array(
							'by' => 'day',
							'primary' => $item,
							'secondary' => $day
						);
					}
				}
			}
		}
		return $results;
	}
	
	/// Set the matching seconds.
	/**
	 * @param $Value array[int],int Array of matching seconds.
	 */
	function SetBySecond($Value = array())
	{
		if (is_int($Value)) {
			$this->mBySecond[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mBySecond = array_flip($Value);
		}
	}
	
	/// Get an array of bysecond records.
	function GetBySecond()
	{
		return array_keys($this->mBySecond);
	}
	
	/// Set the matching minutes.
	/**
	 * @param $Value array[int] Array of matching minutes or NULL.
	 */
	function SetByMinute($Value = array())
	{
		if (is_int($Value)) {
			$this->mByMinute[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByMinute = array_flip($Value);
		}
	}
	
	/// Get an array of byminute records.
	function GetByMinute()
	{
		return array_keys($this->mByMinute);
	}
	
	/// Set the matching hours.
	/**
	 * @param $Value array[int] Array of matching hours.
	 */
	function SetByHour($Value = array())
	{
		if (is_int($Value)) {
			$this->mByHour[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByHour = array_flip($Value);
		}
	}
	
	/// Get an array of byhour records.
	function GetByHour()
	{
		return array_keys($this->mByHour);
	}
	
	/// Set the matching days.
	/**
	 * @param $Value int,NULL Day.
	 * @param $Optional int,NULL Optional value.
	 */
	function SetByDay($Value = NULL, $Optional = NULL)
	{
		if (is_int($Value)) {
			if (NULL === $Optional) {
				$this->mByDay[$Value] = 1;
			} elseif (isset($this->mByDay[$Value])) {
				if (is_array($this->mByDay[$Value])) {
					$this->mByDay[$Value][$Optional] = 1;
				}
			} else {
				$this->mByDay[$Value] = array($Optional => 1);
			}
		} else {
			/// @pre @a $Value must be NULL if not int.
			assert('NULL === $Value');
			$this->mByDay = array();
		}
	}
	
	/// Get an array of byday records in form day => array(weeks)/0.
	function GetByDay()
	{
		return $this->mByDay;
	}
	
	/// Get the days specified in a single week.
	function GetByDayWeekless()
	{
		$days = array();
		foreach ($this->mByDay as $day => $weeks) {
			if (is_numeric($weeks) || (is_array($weeks) && (isset($weeks[1]) || isset($weeks[-1])))) {
				$days[] = $day;
			}
		}
		return $days;
	}
	
	/// Set the matching month days.
	/**
	 * @param $Value array[int] Array of matching month days.
	 */
	function SetByMonthDay($Value = array())
	{
		if (is_int($Value)) {
			$this->mByMonthDay[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByMonthDay = array_flip($Value);
		}
	}
	
	/// Get an array of bymonthday records.
	function GetByMonthDay()
	{
		return array_keys($this->mByMonthDay);
	}
	
	/// Set the matching year days.
	/**
	 * @param $Value array[int] Array of matching year days.
	 */
	function SetByYearDay($Value = array())
	{
		if (is_int($Value)) {
			$this->mByYearDay[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByYearDay = array_flip($Value);
		}
	}
	
	/// Get an array of byyearday records.
	function GetByYearDay()
	{
		return array_keys($this->mByYearDay);
	}
	
	/// Set the matching week numbers.
	/**
	 * @param $Value array[int] Array of matching week numbers.
	 */
	function SetByWeekNo($Value = array())
	{
		if (is_int($Value)) {
			$this->mByWeekNo[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByWeekNo = array_flip($Value);
		}
	}
	
	/// Get an array of byweekno records.
	function GetByWeekNo()
	{
		return array_keys($this->mByWeekNo);
	}
	
	/// Set the matching months.
	/**
	 * @param $Value array[int] Array of matching months.
	 */
	function SetByMonth($Value = array())
	{
		if (is_int($Value)) {
			$this->mByMonth[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByMonth = array_flip($Value);
		}
	}
	
	/// Get an array of bymonth records.
	function GetByMonth()
	{
		return array_keys($this->mByMonth);
	}
	
	/// Set the matching occurrence positions.
	/**
	 * @param $Value array[int] Array of matching occurrence positions.
	 */
	function SetBySetPos($Value = array())
	{
		if (is_int($Value)) {
			$this->mBySetPos[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints or NULL if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mBySetPos = array_flip($Value);
		}
	}
	
	/// Get an array of bysetpos records.
	function GetBySetPos()
	{
		return array_keys($this->mBySetPos);
	}
	
	/// Set the matching term.
	/**
	 * @param $Value array[int] Array of matching occurrence positions.
	 */
	function SetByTerm($Value = array())
	{
		if (is_int($Value)) {
			$this->mByTerm[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByTerm = array_flip($Value);
		}
	}
	
	/// Set the matching days of term.
	/**
	 * @param $Value array[int] Array of matching occurrence positions.
	 */
	function SetByTermDay($Value = array())
	{
		if (is_int($Value)) {
			$this->mByTermDay[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByTermDay = array_flip($Value);
		}
	}
	
	/// Set the matching weeks of term.
	/**
	 * @param $Value array[int] Array of matching occurrence positions.
	 */
	function SetByTermWeek($Value = array())
	{
		if (is_int($Value)) {
			$this->mByTermWeek[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByTermWeek = array_flip($Value);
		}
	}
	
	/// Set the matching days relative to easter.
	/**
	 * @param $Value array[int] Array of matching occurrence positions.
	 */
	function SetByEaster($Value = array())
	{
		if (is_int($Value)) {
			$this->mByEaster[$Value] = 1;
		} else {
			/// @pre @a $Value must be an array of ints if not int.
			assert('is_array($Value) && !in_array(FALSE, array_map(\'is_int\',$Value))');
			$this->mByEaster = array_flip($Value);
		}
	}
	
	/// Set the start day of the week.
	/**
	 * @param $Value int New week start.
	 */
	function SetWkSt($Value = 1)
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
	
	
	/// Get the day of the week, where 0 is the first day of the week.
	/**
	 * @param $dayofweek int Day of week in [0,6] ([sunday,saturday])
	 * @note Uses @a $this->mWkSt.
	 */
	protected function RecurDayOfWeek($dayofweek)
	{
		return ($dayofweek-$this->mWkSt+7)%7;
	}
	
	/// Get the day of the week, where 0 is the first day of the week.
	/**
	* @param $clock &Academic_time Academic time to get the day of the week of.
	*/
	protected function RecurDayOfWeekTimestamp(&$clock)
	{
		return $this->RecurDayOfWeek($clock->DayOfWeek());
	}
	
	/// Get occurrences in a minute.
	/**
	* @param $result     &array     Output array.
	* @param $default    &Academic_time     Default date information.
	* @param $base       &Academic_time Start of the minute.
	*/
	protected function MinuteInner(&$result, &$default, &$base)
	{
		if (empty($this->mBySecond)) {
			// If no seconds are explicitly specified, use the default
			$result[] = strtotime($default->Second().'seconds', $base->Timestamp());
		} else {
			// If seconds specified, use each of those seconds past the base
			foreach ($this->mBySecond as $second => $dummy) {
				$result[] = strtotime($second.'seconds', $base->Timestamp());
			}
		}
	}
	
	/// Get occurrences in an hour.
	/**
	* @param $result     &array     Output array.
	* @param $default    &Academic_time  Default date.
	* @param $base       &Academic_time  Start of the hour.
	*  - ['byminute'], ['bysecond']
	*/
	protected function HourInner(&$result, &$default, &$base)
	{
		if (empty($this->mByMinute)) {
			// If no minutes are explicitly specified, use the default
			$this->MinuteInner($result, $default, $base->Adjust($default->Minute().'min', 'hour'));
		} else {
			// If minutes specified, use each of those minutes past the base
			foreach ($this->mByMinute as $minute => $dummy) {
				$this->MinuteInner($result, $default, $base->Adjust($minute.'min', 'hour'));
			}
		}
	}
	
	/// Get occurrences in a day.
	/**
	* @param $result     &array     Output array.
	* @param $default    &Academic_time  Default date.
	* @param $base       &Academic_time  Start of the day.
	*  - ['byhour'], ['byminute'], ['bysecond']
	*/
	protected function DayInner(&$result, &$default, &$base)
	{
		if (empty($this->mByHour)) {
			// If no hours are explicitly specified, use the default
			$this->HourInner($result, $default, $base->Adjust($default->Hour().'hour', 'day'));
		} else {
			// If hours specified, use each of those hours in the day
			foreach ($this->mByHour as $hour => $dummy) {
				$this->HourInner($result, $default, $base->Adjust($hour.'hour', 'day'));
			}
		}
	}
	
	protected function WeekInnerDayMatch(&$clock)
	{
		return	$this->MatchRruleBymonth($clock) &&
				$this->MatchRruleByyearday($clock) &&
				$this->MatchRruleBymonthday($clock) &&
				$this->MatchRruleByday($clock);
	}
	
	/// Get occurrences in a week.
	/**
	* @param $result     &array     Output array.
	* @param $default    &Academic_time  Default date.
	* @param $base       &Academic_time  Start of the day.
	* @param $week       int        Week number.
	* @param $negweek    int        Negative week number.
	*/
	protected function WeekInner(&$result, &$default, &$base, $week = 1, $negweek = -1)
	{
		$start_dayofweek = $base->DayOfWeek();
		if (empty($this->mByDay)) {
			// If no days are explicitly specified, use the default
			$clock = $base->Adjust($default->DayOfWeek($start_dayofweek).'day');
			if ($this->WeekInnerDayMatch($clock)) {
				$this->DayInner($result, $default, $clock);
			}
		} else {
			for ($i = 0; $i < 7; ++$i) {
				$dayofweek = ($start_dayofweek + $i) % 7;
				if (array_key_exists($dayofweek, $this->mByDay)) {
					$dummy = $this->mByDay[$dayofweek];
					if (is_int($dummy) ||
						(is_array($dummy) && (array_key_exists($week, $dummy) || array_key_exists($negweek, $dummy))))
					{
						$clock = $base->Adjust($i.'day');
						if ($this->WeekInnerDayMatch($clock)) {
							$this->DayInner($result, $default, $clock);
						}
					}
				}
			}
		}
	}
	
	protected function MonthlyInnerDayMatch(&$clock, $byday = true) 
	{
		return ($this->MatchRruleByyearday($clock) &&
				$this->MatchRruleBymonthday($clock) &&
				(!$byday || $this->MatchRruleByday($clock)));
	}
	
	/// Get occurrences in a month.
	/**
	* @param $result     &array     Output array.
	* @param $default    &Academic_time  Default date.
	* @param $base       &Academic_time  Start of the day.
	*
	* @pre month is acceptable
	*/
	protected function MonthInner(&$result, &$default, &$base)
	{
		if (empty($this->mByMonthDay) && empty($this->mByDay)) {
			// If no monthdays are explicitly specified, use the default
			$clock = $base->Adjust(($default->DayOfMonth()-1).'day', 'month');
			if ($this->MonthlyInnerDayMatch($clock)) {
				$this->DayInner($result, $default, $clock);
			}
		} else {
			if (!empty($this->mByDay)) {
				$daysinmonth = (int)$base->Adjust('+1month-1day')->Format('j');
				$firstday = $base->DayOfWeek();
				for ($i = 0; $i < $daysinmonth; ++$i) {
					$dayofweek = ($firstday + $i) % 7;
					if (array_key_exists($dayofweek, $this->mByDay)) {
						if (is_int($this->mByDay[$dayofweek])) {
							$clock = $base->Adjust($i.'day', 'month');
							if ($this->MonthlyInnerDayMatch($clock)) {
								$this->DayInner($result, $default, $clock);
							}
						} elseif (is_array($this->mByDay[$dayofweek])) {
							$week = (int)($i/7) + 1;
							$negweek = -(int)(($daysinmonth-$i+6)/7);
							if (array_key_exists($week, $this->mByDay[$dayofweek]) ||
								array_key_exists($negweek, $this->mByDay[$dayofweek]))
							{
								$clock = $base->Adjust($i.'day', 'month');
								if ($this->MonthlyInnerDayMatch($clock, true)) {
									$this->DayInner($result, $default, $clock);
								}
							}
						}
					}
				}
			} else {
				// If monthdays specified, use each of those monthdays in the month
				$daysinmonth = (int)$base->Adjust('+1month-1day')->Format('j');
				$firstday = $base->DayOfWeek();
				for ($i = 0; $i < $daysinmonth; ++$i) {
					$negdayofmonth = $i-$daysinmonth;
					if (array_key_exists($i+1,           $this->mByMonthDay) ||
						array_key_exists($negdayofmonth, $this->mByMonthDay))
					{
						$clock = $base->Adjust($i.'day', 'month');
						if ($this->MonthlyInnerDayMatch($clock)) {
							$this->DayInner($result, $default, $clock);
						}
					}
				}
			}
		}
	}
	
	
	protected function YearlyInnerDayMatch(&$clock, $primary = '') 
	{
		return (($primary == 'byyearday' || $this->MatchRruleByyearday($clock)) &&
				($primary == 'bymonth' || $this->MatchRruleBymonth($clock)) &&
				$this->MatchRruleBymonthday($clock) &&
				($primary == 'byday' || $this->MatchRruleByday($clock)) &&
				$this->MatchRruleByweekno($clock));
	}
	
	/// Get occurrences in a year.
	/**
	* @param $result     &array     Output array.
	* @param $default    &Academic_time  Default date.
	* @param $base       &Academic_time  Start of the day.
	*
	* @pre month is acceptable
	*/
	protected function YearInner(&$result, &$default, &$base)
	{
		if (empty($this->mByYearDay) && empty($this->mByDay) && empty($this->mByMonth)) {
			// If no yeardays are explicitly specified, use the default
			$clock = $base->Adjust($default->DayOfYear().'day', 'year');
			if ($this->YearlyInnerDayMatch($clock)) {
				$this->DayInner($result, $default, $clock);
			}
		} elseif (!empty($this->mByMonth)) {
			for ($i = 1; $i <= 12; ++$i) {
				if (array_key_exists($i, $this->mByMonth)) {
					if ($i > 1) {
						$monthstart = $base->Adjust('+'.($i-1).'months', 'year');
					} else {
						$monthstart = $base;
					}
					$this->MonthInner($result, $default, $monthstart);
				}
			}
		} elseif (!empty($this->mByYearDay)) {
			// If yeardays specified, use each of those yeardays in the month
			/// @todo Fix byyearday in YearInner
			foreach ($this->mByYearDay as $yearday => $dummy) {
				$clock = $base->Adjust(($yearday-1).'day', 'month');
				if ($this->YearlyInnerDayMatch($clock, 'byyearday')) {
					$this->DayInner($result, $default, $clock);
				}
			}
		} elseif (!empty($this->mByDay)) {
			$daysinyear = 265+(int)$base->Format('L');
			$firstday = $base->DayOfWeek();
			for ($i = 0; $i <= $daysinyear; ++$i) {
				$dayofweek = ($firstday + $i) % 7;
				if (array_key_exists($dayofweek, $this->mByDay)) {
					if (is_int($this->mByDay[$dayofweek])) {
						$clock = $base->Adjust($i.'day', 'year');
						if ($this->YearlyInnerDayMatch($clock, 'byday')) {
							$this->DayInner($result, $default, $clock);
						}
					} elseif (is_array($this->mByDay[$dayofweek])) {
						$week = (int)($i/7) + 1;
						$negweek = -(int)(($daysinyear-$i+6)/7);
						if (array_key_exists($week, $this->mByDay[$dayofweek]) ||
							array_key_exists($negweek, $this->mByDay[$dayofweek]))
						{
							$clock = $base->Adjust($i.'day', 'year');
							if ($this->YearlyInnerDayMatch($clock, 'byday')) {
								$this->DayInner($result, $default, $clock);
							}
						}
					}
				}
			}
		}
	}
	
	/*
	secondly
	
	start with day
	day valid?, start with hour of start
	*/
	
	protected function RecurDailyDayMatch(&$clock) 
	{
		return	$this->MatchRruleBymonth($clock) &&
				$this->MatchRruleByyearday($clock) &&
				$this->MatchRruleBymonthday($clock) &&
				$this->MatchRruleByday($clock);
	}
	
	protected function RecurDaily(&$results, &$default)
	{
		// Start at the beginning of the day of base
		$day = $default->Midnight();
		
		$count = NULL;
		if (NULL !== $this->mCount) {
			$count = $this->mCount;
		}
		while ((NULL === $count || $count > 0) &&
			(NULL === $this->mEffectiveUntil || $day->Timestamp() <= $this->mEffectiveUntil))
		{
			if ($this->RecurDailyDayMatch($day)) {
				$dayresults = array();
				$this->DayInner($dayresults, $default, $day);
				$this->HandleInners($results, $dayresults, $default, $count);
			}
			$day = $day->Adjust('+'.$this->mInterval.'day');
		}
		
	}
	
	protected function RecurWeekly(&$results, &$default)
	{
		// Start at the beginning of the week of base
		$day = $default->Midnight();
		$dayofweek = $this->RecurDayOfWeekTimestamp($day);
		if ($dayofweek) {
			$weekstart = $day->Adjust('-'.$dayofweek.'day');
		} else {
			$weekstart = $day;
		}
		
		$count = NULL;
		if (NULL !== $this->mCount) {
			$count = $this->mCount;
		}
		while ((NULL === $count || $count > 0) &&
			(NULL === $this->mEffectiveUntil || $weekstart->Timestamp() <= $this->mEffectiveUntil))
		{
			$weekresults = array();
			$this->WeekInner($weekresults, $default, $weekstart);
			$this->HandleInners($results, $weekresults, $default, $count);
			$weekstart = $weekstart->Adjust('+'.$this->mInterval.'week');
		}
		
	}
	
	protected function RecurMonthly(&$results, &$default)
	{
		// Start at the beginning of the month of base
		$monthstart = new Academic_time(mktime(0, 0, 0, $default->Month(), 1, $default->Year()));
		
		$count = NULL;
		if (NULL !== $this->mCount) {
			$count = $this->mCount;
		}
		while ((NULL === $count || $count > 0) &&
			(NULL === $this->mEffectiveUntil || $monthstart->Timestamp() <= $this->mEffectiveUntil))
		{
			if ($this->MatchRruleBymonth($monthstart)) {
				$monthresults = array();
				$this->MonthInner($monthresults, $default, $monthstart);
				$this->HandleInners($results, $monthresults, $default, $count);
			}
			$monthstart = $monthstart->Adjust('+'.$this->mInterval.'months');
		}
		
	}
	
	protected function RecurYearly(&$results, &$default)
	{
		// Start at the beginning of the year of base
		$yearstart = new Academic_time(mktime(0, 0, 0, 1, 1, $default->Year()));
		
		$count = NULL;
		if (NULL !== $this->mCount) {
			$count = $this->mCount;
		}
		while ((NULL === $count || $count > 0) &&
			(NULL === $this->mEffectiveUntil || $yearstart->Timestamp() <= $this->mEffectiveUntil))
		{
			$yearresults = array();
			$this->YearInner($yearresults, $default, $yearstart);
			$this->HandleInners($results, $yearresults, $default, $count);
			$yearstart = $yearstart->Adjust('+'.$this->mInterval.'years');
		}
		
	}
	
	/**
	* @pre @a $inner_results is sorted
	* @note result is not necessarily sorted
	*/
	protected function HandleInners(&$results, &$inner_results, &$default, &$count)
	{
		$size = count($inner_results);
		if (!empty($this->mBySetPos)) {
			// Setpos, include only specifics
			foreach ($this->mBySetPos as $setpos => $dummy) {
				if (NULL !== $count && $count <= 0) {
					break;
				}
				if ($setpos > 0) {
					if ($setpos <= $size) {
						$inner_result = $inner_results[$setpos-1];
						if ($inner_result >= $default->Timestamp()) {
							if (NULL === $this->mEffectiveUntil || $inner_result <= $this->mEffectiveUntil) {
								$results[] = $inner_result;
								--$count;
							}
						}
					}
				} elseif ($setpos < 0 && $setpos >= -$size) {
					$index = $size + $setpos;
					if (!array_key_exists($index+1, $this->mBySetPos)) {
						$inner_result = $inner_results[$index];
						if ($inner_result >= $default->Timestamp()) {
							if (NULL === $this->mEffectiveUntil || $inner_result <= $this->mEffectiveUntil) {
								$results[] = $inner_result;
								--$count;
							}
						}
					}
				}
			}
		} else {
			// No setpos, just include as many dates as possible
			foreach ($inner_results as $inner_result) {
				if ($inner_result >= $default->Timestamp()) {
					if (NULL !== $count && $count <= 0) {
						break;
					}
					if (NULL === $this->mEffectiveUntil || $inner_result <= $this->mEffectiveUntil) {
						$results[] = $inner_result;
						--$count;
					} else {
						break;
					}
				}
			}
		}
	}
	
	protected function MatchRruleBymonth(&$clock)
	{
		// by month
		if (!empty($this->mByMonth)) {
			if (!array_key_exists($clock->Month(), $this->mByMonth)) {
				return false;
			}
		}
		return true;
	}
	
	protected function MatchRruleByweekno(&$clock)
	{
		// by weekno
		if (!empty($this->mByWeekNo)) {
			$yearday = (int)$clock->Format('z')+1;
			$dayofstdweek = $clock->DayOfWeek();
			$dayofweek = $clock->DayOfWeek($this->mWkSt);
			
			// week 1 is first week of year with >= 4 days
			$weekno = (int)(($yearday-$dayofweek + 9)/7);
			if ($weekno > 52) {
				$yeardayoflastdayofyear = 264+(int)$clock->Format('L');
				$negyeardayofstartofweek = ($yearday-$dayofweek) - $yeardayoflastdayofyear - 2;
				if ($negyeardayofstartofweek > -4) {
					$weekno -= 52;
				}
			} elseif ($weekno < 1) {
				$startofweek = $clock->Adjust('-'.$dayofweek.'days');
				$yearday2 = (int)$startofweek->Format('z')+1;
				$dayofweek2 = $this->RecurDayOfWeekTimestamp($startofweek);
				$weekno = (int)(($yearday2-$dayofweek2 + 9)/7);
			}
			
			// week -1 is the last week of year with >= 4 days
	// 		$negweekno = (int)(($yearday-$dayofweek + 9)/7);
			
			/// @todo Implement negative week numbers
			
			if (($weekno < 1     || !array_key_exists($weekno, $this->mByWeekNo)) /*&&
				($negweekno > -1 || !array_key_exists($negweekno, $this->mByWeekNo))*/)
			{
				return false;
			}
		}
		return true;
	}
	
	protected function MatchRruleByyearday(&$clock)
	{
		// by yearday
		if (!empty($this->mByYearDay)) {
			$yearday = (int)$clock->Format('z')+1;
			if (!array_key_exists($yearday, $this->mByYearDay)) {
				$yeardayoflastdayofyear = 264+(int)$clock->Format('L');
				$negyearday = $yearday - $yeardayoflastdayofyear - 2;
				if (!array_key_exists($negyearday, $this->mByYearDay))
				{
					return false;
				}
			}
		}
		return true;
	}
	
	protected function MatchRruleBymonthday(&$clock)
	{
		// by monthday
		if (!empty($this->mByMonthDay)) {
			$year = $clock->Year();
			$month = $clock->Month();
			$monthday = $clock->DayOfMonth();
			if (!array_key_exists($monthday, $this->mByMonthDay)) {
				$megmonthday = $monthday - (int)date('j', strtotime('+1month-1day', mktime(0,0,0,$month, 1, $year))) - 1;
				if (!array_key_exists($megmonthday, $this->mByMonthDay))
				{
					return false;
				}
			}
		}
		return true;
	}
		
	protected function MatchRruleByday(&$clock)
	{
		// by day
		if (!empty($this->mByDay))
		{
			$dayofstdweek = $clock->DayOfWeek();
			if (array_key_exists($dayofstdweek, $this->mByDay)) {
				switch ($this->mFrequency) {
					case 'monthly':
						return $this->MatchRruleBydayMonth($clock, $this->mByDay[$dayofstdweek]);
					case 'yearly':
						return $this->MatchRruleBydayYear($clock, $this->mByDay[$dayofstdweek]);
					case 'termly':
						return $this->MatchRruleBydayTerm($clock, $this->mByDay[$dayofstdweek]);
					case 'weekly':
					default:
						return $this->MatchRruleBydayWeek($clock, $this->mByDay[$dayofstdweek]);
							
				}
			} else {
				return false;
			}
		}
		
		return true;
	}
	
	protected function MatchRruleBydayWeek(&$clock, &$dayrule)
	{
		return is_int($dayrule) ||
			(is_array($dayrule) && (
				array_key_exists(1, $dayrule) ||
				array_key_exists(-1, $dayrule)
				)
			);
	}
	
	protected function MatchRruleBydayMonth(&$clock, &$dayrule)
	{
		if (is_array($dayrule)) {
			$dayofmonth = $clock->DayOfMonth();
			// Positive week of month
			$weekofmonth = (int)(($dayofmonth+6) / 7);
			// Negative week of month
			$daysinmonth = (int)$clock->Adjust('-'.($dayofmonth-1).'days+1month-1day')->Format('j');
			$negweekofmonth = -(int)(($daysinmonth-$dayofmonth)/7) - 1;
			if (!array_key_exists($weekofmonth, $dayrule) &&
				!array_key_exists($negweekofmonth, $dayrule))
			{
				return false;
			}
		}
		return true;
	}
	
	protected function MatchRruleBydayYear(&$clock, &$dayrule)
	{
		if (is_array($dayrule)) {
			$dayofyear = $clock->DayOfYear();
			// Positive week of year
			$weekofyear = (int)(($dayofyear) / 7) + 1;
			// Negative week of year
			$daysinyear_minus1 = 264+(int)$clock->Format('L');
			$negweekofyear = -(int)(($daysinyear_minus1-$dayofyear)/7) - 1;
			if (!array_key_exists($weekofyear, $dayrule) &&
				!array_key_exists($negweekofyear, $dayrule))
			{
				return false;
			}
		}
		return true;
	}
	
	protected function MatchRruleBydayTerm(&$clock, &$dayrule)
	{
		return true;
	}
	
	function Recur(&$results, &$default, $effective_until)
	{
		$this->mEffectiveUntil = $effective_until;
		if (NULL !== $this->mUntil && $this->mUntil < $this->mEffectiveUntil) {
			$this->mEffectiveUntil = $this->mUntil;
		}
		static $frequency_handlers = array(
			'daily' => 'RecurDaily',
			'weekly' => 'RecurWeekly',
			'monthly' => 'RecurMonthly',
			'yearly' => 'RecurYearly',
		);
		if (array_key_exists($this->mFrequency, $frequency_handlers)) {
			$handler = $frequency_handlers[$this->mFrequency];
			$this->$handler($results, $default);
		}
	}
	
	/// Get occurrences.
	/**
	 * @param $StartDate	timestamp Start date.
	 * @param $MaxDate		timestamp Max date to return.
	 * @return array['YYYYMMDD' => array[{'HHMMSS',NULL} => duration] ] Matching occurrences in range.
	 *
	 */
	function GetOccurrences($start_date_time, $first_duration, $start_range_time, $end_range_time)
	{
		$results = array();
		$this->Recur($results, new Academic_time($start_date_time), $end_range_time);
		$hash = array();
		foreach ($results as $result) {
			if ($result >= $start_range_time && $result < $end_range_time) {
				$Ymd = date('Ymd', $result);
				$His = date('His', $result);
				$hash[$Ymd][$His] = $first_duration;
			}
		}
		return $hash;
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
	
	/// Find whether the recurrence set is made up of a simple rrule and no exrule.
	/**
	 * @return RecurrenceRule,NULL The one rule or NULL.
	 */
	function GetSimpleRrule()
	{
		if (count($this->mRRules) == 1 &&
			empty($this->mExRules))
		{
			$first = array_keys($this->mRRules);
			return $this->mRRules[$first[0]];
		} else {
			return NULL;
		}
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
		if (NULL === $MinDate) {
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
	/// Constructor.
	function __construct()
	{
		parent::model();
		$this->load->library('academic_calendar');
	}
	
	/// Insert just the date bits.
	function InsertRecurrenceSetDates($Set, $EventId)
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
		return $dates_added;
	}
	
	/// Insert the rdates, exdates, rrules, exrules in a set into the db.
	/**
	 * @param $Dates array['YYYYMMDD' => array[{'HHMMSS',NULL} => {duration,NULL}]] 
	 * @param $EventId int Event identifier.
	 */
	function InsertRecurrenceSet($Set, $EventId)
	{
		$dates_added = $this->InsertRecurrenceSetDates($Set, $EventId);
		
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
			
			$rule_id = $recur->GetRecurId();
			if (is_int($rule_id)) {
				// is_int so doesn't need escaping
				$this->db->where("event_recur_rule_id = $rule_id");
				$this->db->update('event_recur_rules', $fields);
			} else {
				$this->db->insert('event_recur_rules', $fields);
			}
			$affected = $this->db->affected_rows();
			
			if ($affected > 0) {
				if (!is_int($rule_id)) {
					$rule_id = $this->db->insert_id();
				}
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
	function UpdateRecurrenceSet($Set, $EventId)
	{
		// Get the list of known recurrence rules.
		$rules = $Set->GetRulesArray();
		$rule_ids = array();
		foreach ($rules as $rule_info) {
			$id = $rule_info['rule']->GetRecurId();
			if (is_int($id)) {
				// ensure they're ints, so we don't have to escape them.
				$rule_ids[] = $id;
			} else {
				// non int recurrence rule id: '$id'
				assert('NULL === $id');
			}
		}
		// Escape the event id
		$event_id = $this->db->Escape($EventId);
		
		// Delete all attached dates.
		/// @todo check if "has permission" security is required in this context.
		$this->db->delete('event_dates', "event_date_event_id = $event_id");
		// Delete all recurrence rule by's
		// CI Active record doesn't support join with delete
		$sql = 'DELETE event_recur_rule_by FROM event_recur_rule_by,event_recur_rules '.
			'WHERE event_recur_rule_id = event_recur_rule_by_event_recur_rule_id AND '.
				"event_recur_rule_event_id = $event_id";
		$this->db->query($sql);
		
		// Delete rules not in the list
		if (empty($rule_ids)) {
			$this->db->delete('event_recur_rules', "event_recur_rule_event_id = $event_id");
		} else {
			$rule_id_list = join(',',$rule_ids);
			$this->db->delete('event_recur_rules', "event_recur_rule_event_id = $event_id AND event_recur_rule_id NOT IN ($rule_id_list)");
		}
		
		// Let InsertRecurrenceSet handle the adding again.
		return $this->InsertRecurrenceSet($Set, $EventId);
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