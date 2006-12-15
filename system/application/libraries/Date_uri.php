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
 * The URI's can be of four formats:
 *
 * 'gregorian-single':
 *	- /2006-apr-15
 *	- /2006-september-15
 *
 * 'gregorian-multiple':
 *	- /2006/apr/15
 *	- /2006/september/15
 *
 * 'academic-single':
 *	- /2006-xmas-2
 *	- /2006-spring-1-monday
 *	- /2006-au-5-wed
 *
 * 'academic-multiple':
 *	- /2006-2007/xmas/2
 *	- /2006-2007/spring/1/monday
 *	- /2006-2007/au/5/wed
 *
 * @todo Possible to choose whether to include the day for academic URI generation.
 *	- This may mean splitting the academic formats into with/without day.
 *
 * @todo Test fully with invalid URIs.
 */
class Date_uri
{
	/// Default URI formatting mode.
	private static $sDefaultFormat = 'academic-multiple';
	
	/// Get the default URI formatting mode.
	/**
	 * @return Format string Default formatting mode.
	 */
	static function DefaultFormat()
	{
		return self::$sDefaultFormat;
	}
	
	/// Generate a URI from a date in a particular format.
	/**
	 * @param $Date Academic_time Date to generate URI for.
	 * @param $Format Format string like part of return value of ReadUri().
	 * @return string (empty on failure):
	 *	- URI string from date without leading or trailing '/'.
	 */
	function GenerateUri($Date, $Format)
	{
		switch ($Format) {
			case 'gregorian-single':
				return strtolower($Date->Format('Y-M-j'));
				
			case 'gregorian-multiple':
				return strtolower($Date->Format('Y/M/j'));
				
			case 'academic-single':
				return
					$Date->AcademicYear().'-'.
					$Date->AcademicTermNameUnique().'-'.
					$Date->AcademicWeek().'-'.
					strtolower($Date->Format('D'));
				
			case 'academic-multiple':
				return
					$Date->AcademicYear().'-'.
					(1+$Date->AcademicYear()).'/'.
					$Date->AcademicTermNameUnique().'/'.
					$Date->AcademicWeek().'/'.
					strtolower($Date->Format('D'));
				
			default:
				return '';
		}
	}

	/**
	 * @param $FirstSegment integer Number of first segment.
	 * @return array Return structure:
	 *	- 'valid' (bool Whether successful)
	 *	- 'date' (Academic_time Date of URI)
	 *	- 'format' (string Format of URI)
	 *
	 * Format strings:
	 *	- 'gregorian-single' (e.g. 2006-aug-8)
	 *	- 'gregorian-multiple' (e.g. 2006/aug/8)
	 *	- 'academic-single' (e.g. 2006-xmas-2[-monday])
	 *	- 'academic-multiple' (e.g. 2006-2007/xmas/2[/mon])
	 */
	function ReadUri($FirstSegment)
	{
		$CI = &get_instance();
		$uri = &$CI->uri;
		
		// Load the Academic_calendar library
		$CI->load->library('frames');
		
		static $months = array(
				'jan'=>1, 'feb'=>2, 'mar'=>3,
				'apr'=>4, 'may'=>5, 'jun'=>6,
				'jul'=>7, 'aug'=>8, 'sep'=>9,
				'oct'=>10,'nov'=>11,'dec'=>12,
				'january'=>1,  'february'=>2,  'march'=>3,
				'april'=>4,                    'june'=>6,
				'july'=>7,     'august'=>8,    'september'=>9,
				'october'=>10, 'november'=>11, 'december'=>12,
			);
		static $terms = array(
				'au'=>0, 'ch'=>1, 'sp'=>2, 'ea'=>3, 'su'=>4, 'hol'=>5,
				'autumn'=>0, 'christmas'=>1, 'xmas'   =>1, 'spring'=>2,
				'easter'=>3, 'summer'   =>4, 'holiday'=>5,
			);
		static $days = array(
				'mon'=>1, 'tue'=>2, 'wed'=>3, 'thu'=>4,
				'fri'=>5, 'sat'=>6, 'sun'=>7,
				'monday'=>1, 'tuesday'=>2,  'wednesday'=>3, 'thursday'=>4,
				'friday'=>5, 'saturday'=>6, 'sunday'=>7,
			);
		
		$regexes = array(
				'gregorian-multiple' => '(\d{4})',
				'academic-multiple' => '(\d{4})-(\d{4})',
				'gregorian-single' => '(\d{4})-('.implode('|',array_keys($months)).')-(\d{1,2})',
				'academic-single' => '(\d{4})-('.implode('|',array_keys($terms)).')-(\d{1,2})(-('.implode('|',array_keys($days)).'))?',
			);
			
		$segments = array(0 => $uri->segment($FirstSegment));
		$format = '';
		$valid = FALSE;
		foreach ($regexes as $dateformat => $regex) {
			preg_match('/^'.$regex.'$/i', $segments[0], $matches);
			if (!empty($matches)) {
				$format = $dateformat;
				$valid = TRUE;
				break;
			}
		}
		if ($valid) {
			switch ($format) {
				case 'gregorian-multiple':
					$year = (int)$matches[1];
					
					$month = strtolower($uri->segment($FirstSegment+1));
					$day_of_month = $uri->segment($FirstSegment+2);
					if (!is_numeric($day_of_month) ||
						!array_key_exists($month,$months)) {
						$valid = FALSE;
						$format = '';
					} else {
						$month = $months[$month];
						$day_of_month = (int)$day_of_month;
						$date = $CI->academic_calendar->Gregorian($year,$month,$day_of_month);
					}
					break;
					
				case 'academic-multiple':
					$year = (int)$matches[1];
					
					$term = strtolower($uri->segment($FirstSegment+1));
					if (array_key_exists($term, $terms)) {
						$term = $terms[$term];
						
						// And check that the term in question exists
						if (!Academic_time::ValidateAcademicTerm($year, $term)) {
							$valid = FALSE;
							$format = '';
							break;
						}
					} else {
						$valid = FALSE;
						$format = '';
						break;
					}
					$week = $uri->segment($FirstSegment+2);
					if (is_numeric($week)) {
						$week = (int)$week;
					} else {
						$valid = FALSE;
						$format = '';
						break;
					}
					
					$day_of_week = $uri->segment($FirstSegment+3);
					if ($day_of_week !== FALSE) {
						$day_of_week = strtolower($day_of_week);
						if (array_key_exists($day_of_week, $days)) {
							$day_of_week = $days[$day_of_week];
						} else {
							$valid = FALSE;
							$format = '';
							break;
						}
					} else {
						$day_of_week = 1;
					}
					
					$date = $CI->academic_calendar->Academic($year,$term,$week,$day_of_week);
					break;
					
				case 'gregorian-single':
					$year = (int)$matches[1];
					$month = $months[strtolower($matches[2])];
					$day_of_month = (int)$matches[3];
					$date = $CI->academic_calendar->Gregorian($year,$month,$day_of_month);
					break;
					
				case 'academic-single':
					$year = (int)$matches[1];
					$term = $terms[strtolower($matches[2])];
						
					// And check that the term in question exists
					if (!Academic_time::ValidateAcademicTerm($year, $term)) {
						$valid = FALSE;
						$format = '';
						break;
					}
					
					$week = (int)$matches[3];
					if (array_key_exists(5,$matches)) {
						$day_of_week = $days[strtolower($matches[5])];
					} else {
						$day_of_week = 1;
					}
					$date = $CI->academic_calendar->Academic($year,$term,$week,$day_of_week);
					break;
			}
		}
		return array(
				'valid' => $valid,
				'format' => $format,
				'date' => $valid ? $date : NULL,
			);
		
	}
}

?>