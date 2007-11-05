<?php

/**
 * @file libraries/Calendar_view_edit_simple.php
 * @brief Calendar view for the agenda.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(frames)
 *
 * Allows editing/creation of an event using a cut down recurrence interface.
 *
 * Not to know anything of calendar_backend or specific models.
 * Knows about recurrence rules, academic_calendar.
 */
 
 /*
 initial data -> view -> post -> view -> post -> output data
              ^^              ^^              ^^
 */
$CI = & get_instance();
$CI->load->library('frames');

/// Agenda calendar view class.
class CalendarViewEditSimple extends FramesView
{
	/// string Prefix for form data.
	public $mPrefix;
	
	/// RecurrenceSet Recurrence information.
	public $mRecurrenceSet;
	
	/// Default constructor.
	function __construct($prefix)
	{
		parent::__construct('calendar/event_edit');
		
		$this->mPrefix = 'eved';//$prefix;
	}
	
	/// Set the ajax validate path.
	function SetPathAjaxValidate($path)
	{
		$this->mDataArray['paths']['ajax_validate'] = $path;
	}
	
	/// Set the event categories.
	function SetEventCategories($categories)
	{
		$this->SetData('EventCategories', $categories);
	}
	
	/// Set initial data.
	/**
	 * @param $post array Raw post data.
	 */
	function SetInitialPost($post)
	{
		// Validate the post data.
// 		$rset = Calendar_view_edit_simple::validate_recurrence_set_data(
	}
	
	/// Set the event information for this event.
	/**
	 * @param $info array Event information:
	 *  - 
	 */
	function SetEventInfo($info)
	{
		
	}
	
	/// Set the recurrence set of the event.
	/**
	 * @param $rset RecurrenceSet Recurrence information including start/end time.
	 */
	function SetRecurrenceSet($rset)
	{
		$this->mRecurrenceSet = $rset;
	}
	
	/// Validate the input data.
	function Validate()
	{
		
		// Ready output data
		$duration = Academic_time::Difference($event->StartTime, $event->EndTime, array('days', 'hours', 'minutes'));
		$start = new Academic_time(time());
		$eventinfo = array(
			'summary' => '',
			'description' => '',
			'location' => '',
			'category' => '',
			'timeassociated' => true,
			'start' => array(
				'monthday' => $start->DayOfMonth(),
				'month' => $start->Month(),
				'year' => $start->Year(),
				'time' => $start->Hour().':'.$start->Minute(),
				'yearday' => $start->DayOfYear(),
				'day' => $start->DayOfWeek(),
				'monthweek' => (int)(($start->DayOfMonth()+6)/7),
			),
			'duration' => array(
				'days' => $duration['days'],
				'time' => $duration['hours'].':'.$duration['minutes'],
			),
		);
		$data = array(
			'SimpleRecur' => $rset_arr,
			'FailRedirect' => '/'.$tail,
			'Path' => $this->mPaths,
			'EventCategories' => $categories,
			'FormPrefix' => $prefix,
			'EventInfo' => $eventinfo,
		);
	}
	
}

/// Validate simple recurrence by ajax.
class CalendarViewEditSimpleValidate
{
	/// List of errors.
	private $mErrors = array();
	
	/// List of result dates.
	private $mResults = array();
	
	/// Recurrence rule.
	private $mRset = NULL;
	
	/// Set the post data
	function SetData($data)
	{
		if (isset($data['prefix'])) {
			$prefix = $data['prefix'];
			if (isset($data[$prefix.'_recur_simple']) and
				isset($data[$prefix.'_start']) and
				isset($data[$prefix.'_duration']))
			{
				$this->mRset = Calendar_view_edit_simple::validate_recurrence_set_data(isset($data[$prefix.'_timeassociated']) && $data[$prefix.'_timeassociated'], $data[$prefix.'_start'], $data[$prefix.'_duration'], $data[$prefix.'_recur_simple'], $data[$prefix.'_inex'], $this->mErrors);
				
				$this->mResults = $this->mRset->Resolve(strtotime('-1month'), strtotime('1year'));
			} else {
				$this->mErrors[] = array('field' => '', 'text' => 'No simple recurrence data.');
			}
		} else {
			$this->mErrors[] = array('field' => '', 'text' => 'No GET data.');
		}
	}
	
	/// Produce XML results.
	function EchoXML()
	{
		// Return xml including
		// Any errors
		// List of dates in new and old recurrence set, each with whether in
		//     new and or old
		// Calendar html for calendar preview?
		//     could be extrapolated from date client side
		
		$data = array(
			'Errors' => $this->mErrors,
			'Results' => $this->mResults,
		);
		list($data['Start'], $data['End']) = $this->mRset->GetStartEnd();
		
		get_instance()->load->view('calendar/simple_recur_xml.php', $data);
	}
}

/// Dummy class.
class Calendar_view_edit_simple
{
	/// Default constructor
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->model('calendar/recurrence_model');
	}
	
	/// Create a duration array with days later and the end time.
	static function calculate_duration($start, $end)
	{
		$duration = Academic_time::Difference($start, $end, array('days'));
		return array(
			'days' => $duration['days'],
			'time' => date('H:i', $end->Timestamp()),
		);
	}
	
	/// Turn a recurrence set into data for the recurrence view.
	/**
	 * @return array,NULL,FALSE: NULL if no recurrence, FALSE if too complicated.
	 */
	static function transform_recur_for_view(& $recur, & $errors)
	{
		if (NULL === $recur) {
			return NULL;
		}
		
		/*
		 * check in following order:
		 * 	daily, interval, no bys
		 *  weekly, interval, byday multiple
		 *  monthly, interval
		 * 	  bymonthday singular
		 * 	  bydayn singular / byday singular, setpos / byday multiple special, setpos
		 *  yearly, interval
		 *    bymonthday singular, bymonth singular
		 *    bydayn singular, bymonth singular
		 *    byyearday
		 */
		/// @TODO allow implicit monthly/yearly bymonthday[, bymonth]
		
		$rrule = $recur->GetSimpleRrule();
		if (NULL !== $rrule) {
			$fields = $rrule->GetUsedFields();
			$freq = $rrule->GetFrequency();
			$result = array(
				'enable' => 'on',
				'freq' => $freq,
				'interval' => $rrule->GetInterval(),
			);
			if (isset($fields['count'])) {
				$result['range_method'] = 'count';
				$result['count'] = $rrule->GetCount();
			} elseif (isset($fields['until'])) {
				$result['range_method'] = 'until';
				$until = $rrule->GetUntil();
				$result['until'] = array(
					'monthday' => (int)date('d', $until),
					'month'    => (int)date('n', $until),
					'year'     => (int)date('Y', $until),
				);
			} else {
				$result['range_method'] = 'noend';
			}
			$match = true;
			if ('daily' === $freq) {
				foreach ($fields as $field => $dummy) {
					if (substr($field, 0, 2) === 'by') {
						$match = false;
						break;
					}
				}
			} elseif ('weekly' === $freq) {
				foreach ($fields as $field => $dummy) {
					if (substr($field, 0, 2) === 'by' && $field !== 'byday') {
						$errors[] = array('text' => "Recurrence rule too complicated for basic interface ($field detected)");
						$match = false;
						break;
					}
				}
				if ($match) {
					if (isset($fields['byday'])) {
						$days = $rrule->GetByDayWeekless();
						$result['weekly_byday'] = array();
						foreach ($days as $day) {
							$result['weekly_byday'][$day] = 'on';
						}
					} else {
						$result['weekly_byday'] = array();
					}
				}
			} elseif ('monthly' === $freq) {
				if (isset($fields['bymonthday'])) {
					$monthdays = $rrule->GetByMonthDay();
					if (count($monthdays) === 1) {
						foreach ($fields as $field => $dummy) {
							if (substr($field, 0, 2) === 'by' && $field !== 'bymonthday') {
								$errors[] = array('text' => "Recurrence rule too complicated for basic interface ($field detected)");
								$match = false;
								break;
							}
						}
						if ($match) {
							$result['monthly_method'] = 'monthday';
							$monthdays_output = array();
							$result['monthly_monthday'] = array('monthday' => $monthdays[0]);
						}
					} else {
						// Not one bymonthday field
						$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not one bymonthday)");
						$match = false;
					}
				} elseif (isset($fields['byday'])) {
					$bydays = $rrule->GetByDay();
					foreach ($fields as $field => $dummy) {
						if (substr($field, 0, 2) === 'by' && $field !== 'byday' && $field !== 'bysetpos') {
							$errors[] = array('text' => "Recurrence rule too complicated for basic interface ($field detected)");
							$match = false;
							break;
						}
					}
					if ($match) {
						// Either:
						//  1. bydays is individual day with week and no setpos
						//  2. bydays is individual day without week and setpos
						//  3. bydays is 1,2,3,4,5 without weeks and setpos
						$multiple_bydays = count($bydays);
						$setpos = $rrule->GetBySetPos();
						$dayweeks_specified = NULL;
						$weekdays = array();
						$has_setpos = isset($fields['bysetpos']);
						if (!$has_setpos or count($setpos) == 1) {
							foreach ($bydays as $day => $weeks) {
								if (is_array($weeks)) {
									if (count($weeks) === 1) {
										$dayweeks_specified = true;
										$actual_weeks = array_keys($weeks);
										$result['monthly_weekday'] = array(
											'week' => $actual_weeks[0],
											'day' => $day,
										);
									} else {
										// Not one week in byday field
										$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not one week in byday)");
										$match = false;
									}
								} else {
									$weekdays[] = $day;
								}
							}
							if ($multiple_bydays == 1) {
								if ($dayweeks_specified && !$has_setpos) {
									// case 1
									$result['monthly_method'] = 'weekday';
								} elseif (!$dayweeks_specified && $has_setpos) {
									// case 2
									$result['monthly_method'] = 'weekday';
									$result['monthly_weekday'] = array(
										'week' => $setpos[0],
										'day' => $weekdays[0],
									);
// 									var_dump($result['monthly_weekday']);
								} else {
									// Invalid combination of byday and bysetpos
									$errors[] = array('text' => "Recurrence rule too complicated for basic interface (unknown combination of byday and bysetpos)");
									$match = false;
								}
							} elseif (!$dayweeks_specified && $has_setpos) {
								// days = 1,2,3,4,5
								sort($weekdays);
								if (count($weekdays) == 5 and
									$weekdays[0] == 1 and
									$weekdays[1] == 2 and
									$weekdays[2] == 3 and
									$weekdays[3] == 4 and
									$weekdays[4] == 5)
								{
									// case 3
									$result['monthly_method'] = 'weekday';
									$result['monthly_weekday'] = array(
										'week' => $setpos[0],
										'day' => join(',', $weekdays),
									);
								} else {
									// Not standard combination of days
									$errors[] = array('text' => "Recurrence rule too complicated for basic interface (using setpos with multiple bydays but not a standard combination of days)");
									$match = false;
								}
							} else {
								// Multiple bydays without setpos
								$errors[] = array('text' => "Recurrence rule too complicated for basic interface (no bysetpos with multiple bydays)");
								$match = false;
							}
						} else {
							// Multiple setpos on monthly byday
							$errors[] = array('text' => "Recurrence rule too complicated for basic interface (multiple bysetpos on monthly byday)");
							$match = false;
						}
					}
				} else {
					// Not a recognised monthly method
					$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not recognised monthly method)");
					$match = false;
				}
			} elseif ('yearly' === $freq) {
				if (isset($fields['bymonthday']) && isset($fields['bymonth'])) {
					$monthdays = $rrule->GetByMonthDay();
					$months = $rrule->GetByMonth();
					if (count($monthdays) === 1 && count($months) === 1) {
						foreach ($fields as $field => $dummy) {
							if (substr($field, 0, 2) === 'by' && $field !== 'bymonthday' && $field !== 'bymonth') {
								$errors[] = array('text' => "Recurrence rule too complicated for basic interface ($field detected)");
								$match = false;
								break;
							}
						}
						if ($match) {
							$result['yearly_method'] = 'monthday';
							$monthdays_output = array();
							$result['yearly_monthday'] = array(
								'monthday' => $monthdays[0],
								'month' => $months[0]
							);
						}
					} else {
						// Not one bymonthday field or bymonth field
						$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not one bymonthday or bymonth)");
						$match = false;
					}
				} elseif (isset($fields['byday']) && isset($fields['bymonth'])) {
					$bydays = $rrule->GetByDay();
					$months = $rrule->GetByMonth();
					if (count($bydays) === 1 && count($months) === 1) {
						foreach ($fields as $field => $dummy) {
							if (substr($field, 0, 2) === 'by' && $field !== 'byday' && $field !== 'bymonth') {
								$match = false;
								break;
							}
						}
						if ($match) {
							foreach ($bydays as $day => $weeks) {
								if (is_array($weeks) && count($weeks) === 1) {
									$actual_weeks = array_keys($weeks);
									$result['yearly_method'] = 'weekday';
									$result['yearly_weekday'] = array(
										'week' => $actual_weeks[0],
										'day' => $day,
										'month' => $months[0],
									);
								} else {
									// Not one week in byday field
									$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not one week in byday)");
									$match = false;
								}
							}
						}
					} else {
						// Not one byday field or bymonth field
						$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not one byday or bymonth)");
						$match = false;
					}
				} elseif (isset($fields['byyearday'])) {
					$yeardays = $rrule->GetByYearDay();
					if (count($yeardays) === 1) {
						foreach ($fields as $field => $dummy) {
							if (substr($field, 0, 2) === 'by' && $field !== 'byyearday') {
								$errors[] = array('text' => "Recurrence rule too complicated for basic interface ($field detected)");
								$match = false;
								break;
							}
						}
						if ($match) {
							$result['yearly_method'] = 'yearday';
							$monthdays_output = array();
							$result['yearly_yearday'] = array('yearday' => $yeardays[0]);
						}
					} else {
						// Not one byyearday field
						$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not one byyearday)");
						$match = false;
					}
				} else {
					// Not a recognised yearly method
					$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not recognised yearly method)");
					$match = false;
				}
			} else {
				// Not a recognised simple frequency
				$errors[] = array('text' => "Recurrence rule too complicated for basic interface (not recognised simple frequency)");
				$match = false;
			}
			if ($match) {
				return $result;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/// Turn a recurrence set into data for the include exclude dates view.
	/**
	 * @return array(includes,excludes => array[date=>date]).
	 */
	static function transform_inex_for_view(& $recur, & $errors)
	{
		$inex_dates = $recur->GetSimpleDatesList(true);
		$result = array();
		foreach ($inex_dates as $date => $exclude) {
			if ($exclude) {
				$result['excludes'][$date] = $date;
			} else {
				$result['includes'][$date] = $date;
			}
		}
		return $result;
	}
	
	/// Turn array data into a recurrence set.
	static function validate_recurrence_set_data($time_associated, & $start, & $duration, & $simple, & $inex, & $errors)
	{
		$rset = new RecurrenceSet();
		
		// Start
		if (isset($start['year']) && is_numeric($start['year'])) {
			$start['year'] = (int)$start['year'];
			if ($start['year'] < 1970 or $start['year'] > 2030) {
				$errors[] = array('field' => 'start', 'text' => "Start year out of range.");
			}
		} else {
			$errors[] = array('field' => 'start', 'text' => "Start year missing or invalid.");
		}
		if (isset($start['month']) && is_numeric($start['month'])) {
			$start['month'] = (int)$start['month'];
			if ($start['month'] < 1 or $start['month'] > 12) {
				$errors[] = array('field' => 'start', 'text' => "Start month out of range.");
			}
		} else {
			$errors[] = array('field' => 'start', 'text' => "Start month missing or invalid.");
		}
		if (isset($start['monthday']) && is_numeric($start['monthday'])) {
			$start['monthday'] = (int)$start['monthday'];
			if ($start['monthday'] < 1 or $start['month'] > 31) {
				$errors[] = array('field' => 'start', 'text' => "Start month day out of range.");
			}
		} else {
			$errors[] = array('field' => 'start', 'text' => "Start month day missing or invalid.");
		}
		if ($time_associated) {
			if (isset($start['time']))	 {
				$time = split(':', $start['time']);
				if (count($time) != 2) {
					$errors[] = array('field' => 'start', 'text' => "Start time invalid.");
				} elseif (!is_numeric($time[0]) or !is_numeric($time[1])) {
					$errors[] = array('field' => 'start', 'text' => 'Start time not correctly numeric.');
				} else {
					$time[0] = (int)$time[0];
					$time[1] = (int)$time[1];
					if ($time[0] < 0 or $time[0] > 24 or
						$time[1] < 0 or $time[1] > 60)
					{
						$errors[] = array('field' => 'start', 'text' => 'Start time out of range.');
					}
				}
			} else {
				$errors[] = array('field' => 'start', 'text' => "Start month day missing or invalid.");
			}
		} else {
			$time = array(0,0);
		}
		// Duration
		if (isset($duration['days']) && is_numeric($duration['days'])) {
			$duration['days'] = (int)$duration['days'];
			if ($duration['days'] < 0 or $duration['days'] > 7) {
				$errors[] = array('field' => 'duration', 'text' => "Duration days out of range.");
			}
		} else {
			$errors[] = array('field' => 'duration', 'text' => "Duration days missing or invalid.");
		}
		if ($time_associated) {
			if (isset($duration['time'])) {
				$duration_time = split(':', $duration['time']);
				if (count($duration_time) != 2) {
					$errors[] = array('field' => 'duration', 'text' => "Duration time invalid.");
				} elseif (!is_numeric($duration_time[0]) or !is_numeric($duration_time[1])) {
					$errors[] = array('field' => 'duration', 'text' => 'Duration time not correctly numeric.');
				} else {
					// check time itself is valid
					$duration_time[0] = (int)$duration_time[0];
					$duration_time[1] = (int)$duration_time[1];
					if ($duration_time[0] < 0 or $duration_time[0] > 24 or
						$duration_time[1] < 0 or $duration_time[1] > 60)
					{
						$errors[] = array('field' => 'duration', 'text' => 'Duration time out of range.');
					}
				}
			} else {
				$errors[] = array('field' => 'duration', 'text' => "Duration time missing or invalid.");
			}
		} else {
			$duration_time = array(0,0);
		}
		// Set if all seems ok
		if (empty($errors)) {
			$start_ts = mktime(
				$time[0],          $time[1],           0,
				$start['month'],   $start['monthday'], $start['year']
			);
			$end_ts = mktime(
				$duration_time[0], $duration_time[1],  0,
				$start['month'],   $start['monthday'], $start['year']
			);
			$days_later = $duration['days'];
			if ($duration_time[0] < $time[0] ||
				($duration_time[0] == $time[0] && $duration_time[1] < $time[1]))
			{
				++$days_later;
			}
			if ($days_later) {
				$end_ts = strtotime($days_later.'days', $end_ts);
			}
			if ($start_ts == $end_ts) {
				$errors[] = array('field' => 'duration', 'text' => "Duration of zero not allowed.");
			}
			$rset->SetStartEnd($start_ts, $end_ts);
		} else {
			$rset->SetStartEnd(time(), strtotime('1hour'));
		}
		
		if ($simple !== NULL) {
			$recur = self::validate_recurrence_rule_data($simple, $errors);
			if ($recur !== NULL) {
				$rset->AddRRules($recur);
			}
		}
		if ($inex !== NULL) {
			$dates = self::validate_inex_dates_data($inex, $errors);
			if ($dates !== NULL) {
				$rset->AddExDates($dates['excludes']);
				$rset->AddRDates($dates['includes']);
			}
		}
		
		return $rset;
	}
	
	/// Turn simple recurrence array data into a recurrence rule.
	static function validate_recurrence_rule_data(& $simple, & $errors)
	{
		// If no recurrence is enabled, just return NULL
		if (!isset($simple['enable'])) {
			return NULL;
		}
		
		// Otherwise, create the recurrence rule.
		$recur = new CalendarRecurRule();
		// Interval
		if (isset($simple['interval'])) {
			$interval = $simple['interval'];
			if (is_numeric($interval)) {
				$recur->SetInterval((int)$interval);
			} else {
				$errors[] = array('field' => 'interval', 'text' => "Non-numeric interval: $interval");
			}
		}
		
		// Frequency dependent
		if (isset($simple['freq'])) {
			switch ($simple['freq']) {
				case 'daily':
					$recur->SetFrequency('daily');
					break;
					
				case 'weekly':
					$recur->SetFrequency('weekly');
					if (isset($simple['weekly_byday']) && is_array($simple['weekly_byday'])) {
						foreach ($simple['weekly_byday'] as $day => $dummy) {
							if (is_numeric($day) && $day >= 0 && $day < 7) {
								$recur->SetByDay($day);
							}
						}
					}
					break;
					
				case 'monthly':
					$recur->SetFrequency('monthly');
					if (isset($simple['monthly_method'])) {
						switch ($simple['monthly_method']) {
							case 'monthday':
								if (isset($simple['monthly_monthday']) && is_array($simple['monthly_monthday'])) {
									$monthday = & $simple['monthly_monthday'];
									$monthday_fail = false;
									if (!isset($monthday['monthday']) || !is_numeric($monthday['monthday'])) {
										$monthday_fail = true;
										$errors[] = array('field' => 'monthly_monthday', 'text' => 'Invalid monthly day.');
									} elseif ($monthday['monthday'] < -31 || $monthday['monthday'] > 31 || !$monthday['monthday']) {
										$monthday_fail = true;
										$errors[] = array('field' => 'monthly_monthday', 'text' => 'Monthly day out of range');
									}
									
									if (!$monthday_fail) {
										$recur->SetByMonthDay((int)$monthday['monthday']);
									}
								}
								break;
							case 'weekday':
								if (isset($simple['monthly_weekday']) && is_array($simple['monthly_weekday'])) {
									$weekday = & $simple['monthly_weekday'];
									$weekday_fail = false;
									if (!isset($weekday['day'])) {
										$weekday_fail = true;
										$errors[] = array('field' => 'monthly_weekday', 'text' => 'Invalid monthly day.');
									} else {
										$days_split = split(',', $weekday['day']);
										$monthly_weekday_days = array();
										foreach ($days_split as $day) {
											if (!is_numeric($day) || $day < 0 || $day > 6) {
												$weekday_fail = true;
											} else {
												$monthly_weekday_days[] = (int)$day;
											}
										}
										if ($weekday_fail) {
											$errors[] = array('field' => 'monthly_weekday', 'text' => 'Monthly day(s) out of range');
										}
									}
									if (!isset($weekday['week']) || !is_numeric($weekday['week'])) {
										$weekday_fail = true;
										$errors[] = array('field' => 'monthly_weekday', 'text' => 'Invalid monthly week.');
									} elseif ($weekday['week'] < -5 || $weekday['week'] > 5 || !$weekday['week']) {
										$weekday_fail = true;
										$errors[] = array('field' => 'monthly_weekday', 'text' => 'Monthly week out of range');
									}
									
									if (!$weekday_fail) {
										if (count($monthly_weekday_days) == 1) {
											$recur->SetByDay($monthly_weekday_days[0], (int)$weekday['week']);
										} else {
											foreach ($monthly_weekday_days as $day) {
												$recur->SetByDay($day);
											}
											$recur->SetBySetPos((int)$weekday['week']);
										}
									}
								}
								
								break;
						}
					}
					break;
					
				case 'yearly':
					$recur->SetFrequency('yearly');
					if (isset($simple['yearly_method'])) {
						switch ($simple['yearly_method']) {
							case 'monthday':
								if (isset($simple['yearly_monthday']) && is_array($simple['yearly_monthday'])) {
									$monthday = & $simple['yearly_monthday'];
									$monthday_fail = false;
									if (!isset($monthday['monthday']) || !is_numeric($monthday['monthday'])) {
										$monthday_fail = true;
										$errors[] = array('field' => 'yearly_monthday', 'text' => 'Invalid yearly day.');
									} elseif ($monthday['monthday'] < -31 || $monthday['monthday'] > 31 || !$monthday['monthday']) {
										$monthday_fail = true;
										$errors[] = array('field' => 'yearly_monthday', 'text' => 'yearly day out of range');
									}
									if (!isset($monthday['month']) || !is_numeric($monthday['month'])) {
										$monthday_fail = true;
										$errors[] = array('field' => 'yearly_monthday', 'text' => 'Invalid yearly month.');
									} elseif ($monthday['month'] < 1 || $monthday['month'] > 12) {
										$monthday_fail = true;
										$errors[] = array('field' => 'yearly_monthday', 'text' => 'yearly month out of range');
									}
									
									if (!$monthday_fail) {
										$recur->SetByMonthDay((int)$monthday['monthday']);
										$recur->SetByMonth((int)$monthday['month']);
									}
								}
								break;
							case 'weekday':
								if (isset($simple['yearly_weekday']) && is_array($simple['yearly_weekday'])) {
									$weekday = & $simple['yearly_weekday'];
									$weekday_fail = false;
									if (!isset($weekday['week']) || !is_numeric($weekday['week'])) {
										$weekday_fail = true;
										$errors[] = array('field' => 'yearly_weekday', 'text' => 'Invalid yearly week.');
									} elseif ($weekday['week'] < -5 || $weekday['week'] > 5 || !$weekday['week']) {
										$weekday_fail = true;
										$errors[] = array('field' => 'yearly_weekday', 'text' => 'yearly week out of range');
									}
									if (!isset($weekday['day']) || !is_numeric($weekday['day'])) {
										$weekday_fail = true;
										$errors[] = array('field' => 'yearly_weekday', 'text' => 'Invalid yearly day.');
									} elseif ($weekday['day'] < 0 || $weekday['day'] > 6) {
										$weekday_fail = true;
										$errors[] = array('field' => 'yearly_weekday', 'text' => 'yearly day out of range');
									}
									if (!isset($weekday['month']) || !is_numeric($weekday['month'])) {
										$weekday_fail = true;
										$errors[] = array('field' => 'yearly_monthday', 'text' => 'Invalid yearly month.');
									} elseif ($weekday['month'] < 1 || $weekday['month'] > 12) {
										$weekday_fail = true;
										$errors[] = array('field' => 'yearly_monthday', 'text' => 'yearly month out of range');
									}
									
									if (!$weekday_fail) {
										// use monthly with bymonth and byday
										/// @note jh559: using monthly frequency here doesn't work coz yearly interval would be wrong
// 										$recur->SetFrequency('monthly');
// 										$recur->SetByMonth((int)$weekday['month']);
// 										$recur->SetByDay((int)$weekday['day'], (int)$weekday['week']);
										
										// use yearly with bymonth and bysetpos
										$recur->SetByDay((int)$weekday['day'], (int)$weekday['week']);
										$recur->SetByMonth((int)$weekday['month']);
										//$recur->SetBySetpos((int)$weekday['week']);
									}
								}
								
								break;
							case 'yearday':
								if (isset($simple['yearly_yearday']) && is_array($simple['yearly_yearday'])) {
									$yearday = & $simple['yearly_yearday'];
									if (!isset($yearday['yearday']) || !is_numeric($yearday['yearday'])) {
										$errors[] = array('field' => 'yearly_yearday', 'text' => 'Invalid yearly day of year.');
									} elseif ($yearday['yearday'] < -365 || $yearday['yearday'] > 365 || !$yearday['yearday']) {
										$errors[] = array('field' => 'yearly_weekday', 'text' => 'yearly day of year out of range');
									} else {
										$recur->SetByYearday((int)$yearday['yearday']);
									}
								}
								break;
						}
					}
					break;
					
				default:
					$errors[] = array('field' => 'freq', 'text' => 'Unrecognised frequency');
					break;
			}
		} else {
			$errors[] = array('text' => 'Recurrence frequency missing');
		}
		
		// Range
		if (isset($simple['range_method'])) {
			switch ($simple['range_method']) {
				case 'count':
					if (isset($simple['count']) && is_numeric($simple['count'])) {
						if ($simple['count'] > 0) {
							$recur->SetCount((int)$simple['count']);
						} else {
							$recur->SetCount(1);
							$errors[] = array('field' => 'count', 'text' => 'Occurrence count should be positive and non-zero');
						}
					} else {
						$recur->SetCount(10);
						$errors[] = array('field' => 'count', 'text' => 'Non-numeric occurrence count');
					}
					break;
					
				case 'until':
					if (isset($simple['until']) && is_array($simple['until'])) {
						$until = &$simple['until'];
						$until_fail = false;
						if (!isset($until['year']) || !is_numeric($until['year'])) {
							$until_fail = true;
							$errors[] = array('field' => 'until', 'text' => 'Invalid until year');
						} elseif ($until['year'] < 2000 || $until['year'] > 2100) {
							$until_fail = true;
							$errors[] = array('field' => 'until', 'text' => 'Until year out of range');
						}
						if (!isset($until['month']) || !is_numeric($until['month'])) {
							$until_fail = true;
							$errors[] = array('field' => 'until', 'text' => 'Invalid until month');
						} elseif ($until['month'] < 1 || $until['month'] > 12) {
							$until_fail = true;
							$errors[] = array('field' => 'until', 'text' => 'Until month out of range');
						}
						if (!isset($until['monthday']) || !is_numeric($until['monthday'])) {
							$until_fail = true;
							$errors[] = array('field' => 'until', 'text' => 'Invalid until day of month');
						} elseif ($until['monthday'] < 1 || $until['monthday'] > 31) {
							$until_fail = true;
							$errors[] = array('field' => 'until', 'text' => 'Year out of range');
						}
						
						if (!$until_fail) {
							$until_date = mktime(23, 59, 59, (int)$until['month'], (int)$until['monthday'], (int)$until['year']);
							$recur->SetUntil($until_date);
						}
					} else {
						$errors[] = array('field' => 'until', 'text' => 'Invalid until date');
					}
					break;
			}
		}
		
		return $recur;
	}
	
	/// Turn simple inex array data into inex date lists.
	/**
	 * @return array['{in,ex}cludes' => array['Ymd' => array['His' => $duration]]]
	 *  where first is exclude, second is include
	 */
	static function validate_inex_dates_data(& $simple, & $errors)
	{
		$result = array();
		foreach (array('exclude', 'include') as $inex) {
			$inexes = $inex.'s';
			$result[$inexes] = array();
			if (isset($simple[$inexes])) {
				// Existing ones
				if (is_array($simple[$inexes])) {
					foreach ($simple[$inexes] as $date => $dummydate) {
						// Exclude if the remvove button exists.
						if (!isset($simple[$inex.'_remove_btns'][$date])) {
							$result[$inexes][$date] = array(NULL => NULL);
						}
					}
				} else {
					$errors[] = array('field' => $inexes, 'text' => "$inexes must be list");
				}
			}
			if (isset($simple["add_$inex"])) {
				// A new one
				if (isset($simple['new_date']) && is_array($simple['new_date'])) {
					$new_date = $simple['new_date'];
					$new_date_failed = false;
					if (isset($new_date['year']) && is_numeric($new_date['year'])) {
						$new_date['year'] = (int)$new_date['year'];
						if ($new_date['year'] < 1970 or $new_date['year'] > 2030) {
							$errors[] = array('field' => $inexes, 'text' => "New $inex date year out of range.");
							$new_date_failed = true;
						}
					} else {
						$errors[] = array('field' => $inexes, 'text' => "New $inex date year missing or invalid.");
						$new_date_failed = true;
					}
					if (isset($new_date['month']) && is_numeric($new_date['month'])) {
						$new_date['month'] = (int)$new_date['month'];
						if ($new_date['month'] < 1 or $new_date['month'] > 12) {
							$errors[] = array('field' => $inexes, 'text' => "New $inex date month out of range.");
							$new_date_failed = true;
						}
					} else {
						$errors[] = array('field' => $inexes, 'text' => "New $inex date month missing or invalid.");
						$new_date_failed = true;
					}
					if (isset($new_date['monthday']) && is_numeric($new_date['monthday'])) {
						$new_date['monthday'] = (int)$new_date['monthday'];
						if ($new_date['monthday'] < 1 or $new_date['month'] > 31) {
							$errors[] = array('field' => 'start', 'text' => "New $inex date month day out of range.");
							$new_date_failed = true;
						}
					} else {
						$errors[] = array('field' => $inexes, 'text' => "New $inex date month day missing or invalid.");
						$new_date_failed = true;
					}
					if (!$new_date_failed) {
						// No problems with the new date, so add to the list.
						$new_date_ts = mktime(
							0,                    0,                     0,
							$new_date['month'],   $new_date['monthday'], $new_date['year']
						);
						$date = date('Ymd', $new_date_ts);
						$result[$inexes][$date] = array(NULL => NULL);
					}
				} else {
					$errors[] = array('field' => $inexes, 'text' => "New $inex date information missing or invalid");
				}
			}
		}
		return $result;
	}
}

?>