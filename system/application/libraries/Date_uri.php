<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Date_uri.php
 * @brief Library for using dates in URIs.
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @see Date_uri
 */
 
/// Library class for using dates in URIs.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Basically reads and generates dates in URI's.
 *
 * The URI segment can be in several formats:
 *	- SEGMENT => START
 *	- SEGMENT => START ":" END (only when $AllowRange === TRUE)
 *
 * Where:
 *	- START  => ACADEMIC | GREGORIAN | RELATIVE
 *	- END    => ACADEMIC | GREGORIAN | RELATIVE
 *
 *	- ACADEMIC => [AC_YEAR "-"] AC_TERM ["-" AC_WEEK ["-" DAY]]
 *	- GREGORIAN => [YEAR "-"] MONTH ["-" DATE]
 *	- RELATIVE => "today" | "tomorrow" | "yesterday" | NUMBER OFFSET_UNIT ["s"]
 *
 *	- OFFSET_UNIT => "day" | "week" | "month" | "year"
 *	- MONTH  => "jan" ["uary"] | "feb" ["ruary"] | "mar" ["ch"] | "apr" ["il"] | "may" | "jun" ["e"]
 *	- MONTH  => "jul" ["y"] | "aug" ["ust"] | "sep" ["tember"] | "oct" ["ober"] | "nov" ["ember"] | "dec" ["ember"]
 *	- AC_TERM => "au" ["tumn"] | "ch" ["ristmas"] | "xmas" | "sp" ["ring"]
 *	- AC_TERM => "ea" ["ster"] | "su" ["mmer"] | "hol" ["iday"]
 *
 *	- AC_YEAR => YEAR
 *	- AC_WEEK=> digit {1,2}
 *	- YEAR => digit {4}
 *	- DATE => digit | "1" digit | "2" digit | "30" | "31"
 *
 * For example, the following are valid dates (which have implicit ranges):
 *	- "xmas"
 *	- "xmas-2-tuesday"
 *	- "jan-5"
 *	- "tomorrow"
 *
 * And the following are valid ranges:
 *	- "xmas:sp"
 *	- "tomorrow:dec-25"
 *	- "5weeks:1month"
 *	- "today:2weeks"
 *	- "2006-dec-25:4days"
 *
 * @todo Test fully with invalid URIs.
 */
class Date_uri
{
	/// Default URI formatting mode.
	private static $sDefaultFormat = 'ac-re';
	
	/// Get the default URI formatting mode.
	/**
	 * @return Format string Default formatting mode.
	 */
	static function DefaultFormat()
	{
		return self::$sDefaultFormat;
	}
	
	/// Array of accepted months mapping onto month numbers.
	private $mMonths;
	/// Array of accepted terms mapping onto term numbers.
	private $mTerms;
	/// Array of accepted days mapping onto day numbers (1-7, mon-sun).
	private $mDays;
	
	/// Default constructor
	function __construct()
	{
		$this->mMonths = array(
				'jan'=>1, 'feb'=>2, 'mar'=>3,
				'apr'=>4, 'may'=>5, 'jun'=>6,
				'jul'=>7, 'aug'=>8, 'sep'=>9,
				'oct'=>10,'nov'=>11,'dec'=>12,
				'january'=>1,  'february'=>2,  'march'=>3,
				'april'=>4,                    'june'=>6,
				'july'=>7,     'august'=>8,    'september'=>9,
				'october'=>10, 'november'=>11, 'december'=>12,
			);
		$this->mTerms = array(
				'au'=>0, 'ch'=>1, 'sp'=>2, 'ea'=>3, 'su'=>4, 'hol'=>5,
				'autumn'=>0, 'christmas'=>1, 'xmas'   =>1, 'spring'=>2,
				'easter'=>3, 'summer'   =>4, 'holiday'=>5,
			);
		$this->mDays = array(
				'mon'=>1, 'tue'=>2, 'wed'=>3, 'thu'=>4,
				'fri'=>5, 'sat'=>6, 'sun'=>7,
				'monday'=>1, 'tuesday'=>2,  'wednesday'=>3, 'thursday'=>4,
				'friday'=>5, 'saturday'=>6, 'sunday'=>7,
			);
	}
	
	/// Get the regular expression to read the date range
	/**
	 * @param $AllowRange bool Whether to allow a range of dates.
	 */
	private function GetRegex($AllowRange = TRUE)
	{
		// Set to true when editing the regex (not using cached regex)
		if (FALSE) {
			// months: keys of $this->mMonths
			// terms: keys of $this->mTerms
			// days: keys of $this->mDays
			
			$re_months = implode('|',array_keys($this->mMonths));
			$re_terms = implode('|',array_keys($this->mTerms));
			$re_days = implode('|',array_keys($this->mDays));
			
			// relative_bases: today, tomorrow etc.
			// duration_units: days, weeks, months, years
			
			$re_relative_bases = 'now|today|yesterday|tomorrow';
			$re_duration_units = 'days?|weeks?|months?|years?';
			
			// dayofmonth: 1-31
			// year: 4 digits
			// week: 2 digits
			
			$re_dayofmonth = '[1-9]|1\d|2\d|3[01]';
			$re_year = '\d{4}';
			$re_week = '\d\d?';
			
			// academic:  [(year)-](term)[-(week)[-(day)]]
			// gregorian: [(year)-](month)[-(date)]
			// relative:  (base)|[(num)(unit)]
			
			$re_start_ac = '((?P<ac_year>'.$re_year.')-)?'.
					'(?P<ac_term>'.$re_terms.')'.
					'(-(?P<week>'.$re_week.')(-(?P<day>'.$re_days.'))?)?';
			$re_start_greg = '((?P<year>'.$re_year.')-)?'.
					'(?P<month>'.$re_months.')'.
					'(-?(?P<dom>'.$re_dayofmonth.'))?';
			$re_start_rel = '(?P<base>'.$re_relative_bases.')|'.
					'((?P<offset>-?\d+)(?P<offset_unit>'.$re_duration_units.'))';
			
			$re_end_ac = '((?P<end_ac_year>' . $re_year . ')-)?'.
					'(?P<end_ac_term>' . $re_terms . ')'.
					'(-(?P<end_week>' . $re_week . ')(-(?P<end_day>' . $re_days . '))?)?';
			$re_end_greg = '((?P<end_year>' . $re_year . ')-)?'.
					'(?P<end_month>' . $re_months . ')'.
					'(-?(?P<end_dom>' . $re_dayofmonth . '))?';
			$re_end_rel = '(?P<end_base>'.$re_relative_bases.')|'.
					'((?P<end_offset>-?\d+)(?P<end_offset_unit>'.$re_duration_units.'))';
			
			// start, end: academic, gregorian, relative
			
			$re_start =
					'(?P<start_ac>'.$re_start_ac.')|'.
					'(?P<start_greg>'.$re_start_greg.')|'.
					'(?P<start_rel>'.$re_start_rel.')';
			$re_end =
					'(?P<end_ac>'.$re_end_ac.')|'.
					'(?P<end_greg>'.$re_end_greg.')|'.
					'(?P<end_rel>'.$re_end_rel.')';
			
			// single date regex: ^start$
			// multiple date range: ^(start[(:(end)]$
			
			$single_regex   = '/^('.$re_start.')$/i';
			$multiple_regex = '/^('.$re_start.')(:('.$re_end.'))?$/i';
			
			if (TRUE) {
				// Echo the regular expression
				echo '<h2>Debug output<h2><pre>';
				echo '			if ($AllowRange) {'."\n";
				echo '				return \''.$multiple_regex."';\n";
				echo '			} else {'."\n";
				echo '				return \''.$single_regex."';\n";
				echo '			}'."\n";
				echo '</pre><h2>End of Debug output</h2>';
			}
			
			return ($AllowRange
					? $multiple_regex
					: $single_regex);
			
		} else {
			if ($AllowRange) {
				return '/^((?P<start_ac>((?P<ac_year>\d{4})-)?(?P<ac_term>au|ch|sp|ea|su|hol|autumn|christmas|xmas|spring|easter|summer|holiday)(-(?P<week>\d\d?)(-(?P<day>mon|tue|wed|thu|fri|sat|sun|monday|tuesday|wednesday|thursday|friday|saturday|sunday))?)?)|(?P<start_greg>((?P<year>\d{4})-)?(?P<month>jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|june|july|august|september|october|november|december)(-?(?P<dom>[1-9]|1\d|2\d|3[01]))?)|(?P<start_rel>(?P<base>now|today|yesterday|tomorrow)|((?P<offset>-?\d+)(?P<offset_unit>days?|weeks?|months?|years?))))(:((?P<end_ac>((?P<end_ac_year>\d{4})-)?(?P<end_ac_term>au|ch|sp|ea|su|hol|autumn|christmas|xmas|spring|easter|summer|holiday)(-(?P<end_week>\d\d?)(-(?P<end_day>mon|tue|wed|thu|fri|sat|sun|monday|tuesday|wednesday|thursday|friday|saturday|sunday))?)?)|(?P<end_greg>((?P<end_year>\d{4})-)?(?P<end_month>jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|june|july|august|september|october|november|december)(-?(?P<end_dom>[1-9]|1\d|2\d|3[01]))?)|(?P<end_rel>(?P<end_base>now|today|yesterday|tomorrow)|((?P<end_offset>-?\d+)(?P<end_offset_unit>days?|weeks?|months?|years?)))))?$/i';
			} else {
				return '/^((?P<start_ac>((?P<ac_year>\d{4})-)?(?P<ac_term>au|ch|sp|ea|su|hol|autumn|christmas|xmas|spring|easter|summer|holiday)(-(?P<week>\d\d?)(-(?P<day>mon|tue|wed|thu|fri|sat|sun|monday|tuesday|wednesday|thursday|friday|saturday|sunday))?)?)|(?P<start_greg>((?P<year>\d{4})-)?(?P<month>jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|june|july|august|september|october|november|december)(-?(?P<dom>[1-9]|1\d|2\d|3[01]))?)|(?P<start_rel>(?P<base>now|today|yesterday|tomorrow)|((?P<offset>-?\d+)(?P<offset_unit>days?|weeks?|months?|years?))))$/i';
			}
		}
	}
	
	/// Reads an academic date from regex results and produces some useful data.
	/**
	 * @param $Relative Academic_time Date to use if the year isn't specified.
	 * @param $Year string Academic year string from regex.
	 * @param $Term string Term string from regex.
	 * @param $Week string Week string from regex.
	 * @param $Day string Day string from regex.
	 * @return array with the following elements:
	 *	- 0: Academic_time Start date.
	 *	- 1: Academic_time End date.
	 *	- 2: string Description of date.
	 */
	protected function ReadAcademic($Relative, $Year, $Term, $Week, $Day)
	{
		$result_start = $Relative;
		$result_end = $Relative;
		$result_description = '';
		if (empty($Year)) {
			$Year = $result_start->AcademicYear();
		} else {
			$duration = 'year';
		}
		if (empty($Term)) {
			$Term = 0;
		} else {
			$Term = $this->mTerms[strtolower($Term)];
			$duration = 'term';
		}
		if (empty($Week)) {
			$Week = 1;
			$result_had_week = FALSE;
		} else {
			$result_had_week = TRUE;
			$duration = 'week';
		}
		if (empty($Day)) {
			$Day = 1;
			$result_had_day = FALSE;
		} else {
			$result_had_day = TRUE;
			$Day = $this->mDays[strtolower($Day)];
			$duration = 'day';
		}
		
		$CI = &get_instance();
		$result_start = $CI->academic_calendar->Academic($Year, $Term, $Week, $Day);
		
		if ($result_had_day) {
			$result_description .= $result_start->Format('l').' ';
		}
		if ($result_had_week) {
			$result_description .= 'week ' . $result_start->AcademicWeek() . ' of ';
		}
		$result_description .= 'the ' .
				$result_start->AcademicTermName() . ' ' .
				$result_start->AcademicTermTypeName() . ' ' .
				$result_start->AcademicYearName();
		
		// Set the implicit duration
		if ('year' === $duration) {
			$result_end = $CI->academic_calendar->Academic(
				$Year+1, 0, 1, 1);
		} elseif ('term' === $duration) {
			$result_end = $CI->academic_calendar->Academic(
				$Year + (int)(($Term+1) / 6),
				($Term + 1)%6, 1, 1);
		} else {
			$result_end = $result_start->Adjust('1'.$duration);
		}
		
		return array(
				$result_start,
				$result_end,
				$result_description,
			);
	}
	
	/// Reads a gregorian date from regex results and produces some useful data.
	/**
	 * @param $Relative Academic_time Date to use if the year isn't specified.
	 * @param $Year string Year string from regex.
	 * @param $Month string Month string from regex.
	 * @param $Dom string Day of month string from regex.
	 * @return array with the following elements:
	 *	- 0: Academic_time Start date.
	 *	- 1: Academic_time End date.
	 *	- 2: string Description of date.
	 */
	protected function ReadGregorian($Relative, $Year, $Month, $Dom)
	{
		$result_start = $Relative;
		$result_end = $Relative;
		if (!empty($Year)) {
			$duration = 'year';
		}
		if (empty($Month)) {
			$Month = $result_start->Month();
		} else {
			$Month = $this->mMonths[strtolower($Month)];
			$duration = 'month';
		}
		if (empty($Year)) {
			if (empty($Month) ||
					$Month >= $result_start->Month()) {
				$Year = $result_start->Year();
			} else {
				$Year = $result_start->Year()+1;
			}
		}
		if (empty($Dom)) {
			$Dom = 1;
			$result_had_dom = FALSE;
		} else {
			$result_had_dom = TRUE;
			$duration = 'day';
		}
		
		$CI = &get_instance();
		$result_start = $CI->academic_calendar->Gregorian(
				$Year,
				$Month,
				$Dom);
		
		$result_description = $result_start->Format(
				($result_had_dom?
					'F jS Y':
					'F Y'
				));
		
		// Add on the duration so that e.g. jan:feb includes all of
		// january and february
		$result_end = $result_start->Adjust('1'.$duration);
		
		return array(
				$result_start,
				$result_end,
				$result_description,
			);
	}

	/**
	 * @param $UriSegment string URI segment.
	 * @param $AllowRange bool Whether to allow date ranges (using ':' in between).
	 * @return array Return structure:
	 *	- 'valid' (bool Whether successful)
	 *	- 'start' (Academic_time Start of range specified by @a $UriSegment)
	 *	- 'end'  (Academic_time End of range specified by @a $UriSegment)
	 *	- 'format' (string Format string which can be passed to GenerateUri())
	 *	- 'description' (string Description of the date [range])
	 */
	function ReadUri($UriSegment, $AllowRange = TRUE)
	{
		// Use GenerateRegex to get regex
		$regex = $this->GetRegex($AllowRange);
		
		$format = self::DefaultFormat();
		$description = '';
		$valid = (preg_match($regex,$UriSegment, $results) > 0);
		if ($valid) {
			
			if (FALSE) {
				echo '<br/><br/>Result of regular expression process:<br/>'.
					str_replace("\n","<br/>\n",var_export($results,true));
			}
			
			
			// Initial information extraction
			$start_academic = !empty($results['start_ac']);
			$start_gregorian = !empty($results['start_greg']);
			$start_relative = !empty($results['start_rel']);
			$end_academic = !empty($results['end_ac']);
			$end_gregorian = !empty($results['end_greg']);
			$end_relative = !empty($results['end_rel']);
			
			$has_start = ($start_academic || $start_gregorian || $start_relative);
			$has_end   = ($end_academic   || $end_gregorian   || $end_relative);
			
			if ($has_end) {
				$description .= 'from ';
			}
			
			$start = Academic_time::NewToday();
			$end = $start;
			
			// Find the start, its either academic, gregorian, or relative
			if ($start_academic) {
				$format = 'ac';
				if ($end_academic) {
					$format .= ':ac';
				}
				list($start,$end,$temp_description) = $this->ReadAcademic(
						$start,
						$results['ac_year'],
						$results['ac_term'],
						empty($results['week']) ? '' : $results['week'],
						empty($results['day'])  ? '' : $results['day']
					);
				$description .= $temp_description;
				
			} elseif ($start_gregorian) {
				$format = 'gr';
				if ($end_gregorian) {
					$format .= ':gr';
				}
				list($start,$end,$temp_description) = $this->ReadGregorian(
						$start,
						$results['year'],
						$results['month'],
						empty($results['dom'])  ? '' : $results['dom']
					);
				$description .= $temp_description;
				
			} elseif ($start_relative) {
				$format = 'gr';
				if ($end_gregorian) {
					$format .= ':gr';
				}
				$offset = '';
				if (!empty($results['base'])) {
					switch ($results['base']) {
						case 'yesterday':
							$offset = '-1day';
							break;
						case 'tomorrow':
							$offset = '1day';
							break;
					}
					$description .= strtolower($results['base']);
				} else {
					if (!empty($results['offset'])) {
						if (!empty($results['offset_unit'])) {
							$offset .= $results['offset'].$results['offset_unit'];
						} else {
							$offset .= $results['offset'].'day';
						}
					
						$results['offset_unit'] = strtolower($results['offset_unit']);
						if ('s' !== substr($results['offset_unit'],-1,1)) {
							$results['offset_unit'] .= 's';
						}
						$description .=
								$results['offset'] . ' ' .
								$results['offset_unit'] . ' time';
					}
				}
				if (!empty($offset)) {
					$start = $start->Adjust($offset);
				}
				// Set the implicit duration if no end is specified
				if (!$has_end) {
					$end = $start->Adjust('1day');
				}
			}
			
			// Find the end, its either academic, gregorian, or relative
			if ($end_academic) {
				$description .= ' until ';
				
				list($dummy,$end,$temp_description) = $this->ReadAcademic(
						$start,
						$results['end_ac_year'],
						$results['end_ac_term'],
						empty($results['end_week']) ? '' : $results['end_week'],
						empty($results['end_day'])  ? '' : $results['end_day']
					);
				$description .= $temp_description;
				
			} elseif ($end_gregorian) {
				$description .= ' until ';
				
				list($dummy,$end,$temp_description) = $this->ReadGregorian(
						$start,
						$results['end_year'],
						$results['end_month'],
						empty($results['end_dom'])  ? '' : $results['end_dom']
					);
				$description .= $temp_description;
				
			} elseif ($end_relative) {
				$format .= ':re';
				$end = $start;
				$offset = '';
				if (!empty($results['end_base'])) {
					$end = Academic_time::NewToday()->Adjust('1day');
					switch ($results['end_base']) {
						case 'yesterday':
							$offset .= '-1day';
							break;
						case 'tomorrow':
							$offset .= '1day';
							break;
					}
					$description .= ' until ' . strtolower($results['end_base']);
				} else {
					if (!empty($results['end_offset'])) {
						if (!empty($results['end_offset_unit'])) {
							$offset .= $results['end_offset'].$results['end_offset_unit'];
						} else {
							$results['end_offset_unit'] = 'day';
							$offset .= $results['end_offset'].'day';
						}
						$results['end_offset_unit'] = strtolower($results['end_offset_unit']);
						if ('s' === substr($results['end_offset_unit'],-1,1)) {
							if (1 == $results['end_offset']) {
								$results['end_offset_unit'] = substr($results['end_offset_unit'],0,-1);
							}
						} else {
							if (1 != $results['end_offset']) {
								$results['end_offset_unit'] .= 's';
							}
						}
						$description .=
								' for ' . $results['end_offset'] .
								' ' . $results['end_offset_unit'];
					}
				}
				if (!empty($offset)) {
					$end = $start->Adjust($offset);
				}
			}
			// If the end is before the start, use the start for 1 day
			if ($start->Timestamp() >= $end->Timestamp()) {
				$end = $start->Adjust('1day');
			}
		} else {
			// Invalid, so puke back saying it was unrecognised
			$description = 'Unrecognised '.($AllowRange?'date range':'date');
		}
		
		// Return the final array
		return array(
				'valid'    => $valid,
				'format'   => $format,
				'start'    => $valid ? $start : NULL,
				'end'      => $valid ? $end   : NULL,
				'description' => $description,
			);
		
	}
	
	
	
	/// Generate a URI from a date in a particular format.
	/**
	 * @param $Start Academic_time Start date to generate URI for.
	 * @param $End Academic_time End date to generate URI for.
	 * @param $Format Format string like part of return value of ReadUri().
	 *	- 'gr'	YEAR-MON-DD
	 *	- 'gr:gr'	YEAR-MON-DD:YEAR-MON-DD
	 *	- 'gr:re'	YEAR-MON-DD:XXUNIT
	 *	- 'ac'	YEAR-TERM-WW[-DAY]
	 *	- 'ac:ac'	YEAR-TERM-WW[-DAY]:YEAR-TERM-WW[-DAY]
	 *	- 'ac:re'	YEAR-TERM-WW[-DAY]:XXUNIT
	 * @return string (empty on failure):
	 *	- URI string from date without leading or trailing '/'.
	 */
	function GenerateUri($Format, $Start, $End = FALSE)
	{
		$start_format = substr($Format,0,2);
		if (FALSE === $End) {
			$end_format = '';
		} else {
			$end_format = substr($Format,3,2);
		}
		
		$days = (int)(($End->Timestamp()-$Start->Timestamp() + 12*60*60)/(24*60*60));
		$unit = 'day';
		if ($days % 7 === 0) {
			$days /= 7;
			$unit = 'week';
		}
		
		if ($start_format === 'gr') {
			$result = $Start->Format('Y-M-j');
			if ($end_format === 'gr') {
				$result .= ':'.$End->Format('Y-M-j');
			}
			$valid = TRUE;
		} elseif ($start_format === 'ac') {
			$result = $Start->AcademicYear();
			$result .= '-'.$Start->AcademicTermNameUnique();
			$put_end = ($end_format === 'ac');
			$by_term = (0 === $Start->AcademicDay() && 0 === $End->AcademicDay());
			if (!$by_term) {
				$result .= '-'.$Start->AcademicWeek();
				$dow = $Start->Format('D');
				if ($dow !== 1 && $unit === 'day') {
					$result .= '-'.$dow;
				} else {
					$put_end = FALSE;
				}
			} else {
				$End = $End->Adjust('-1day');
			}
			if ($put_end) {
				$result .= ':'.$End->AcademicYear();
				$result .= '-'.$End->AcademicTermNameUnique();
				if (!$by_term) {
					$result .= '-'.$End->AcademicWeek();
					$result .= '-'.$End->Format('D');
				}
			}
			$valid = TRUE;
		} else {
			$result = '';
			$valid = FALSE;
		}
		
		if ($valid && $end_format === 're') {
			$result .= ':'.$days.$unit;
		}
		return $result;
	}
}

?>