<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_source_icalendar.php
 * @brief Calendar source for the iCal file format.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library calendar_backend)
 * @pre loaded(library academic_calendar)
 *
 * Event source class for obtaining events from an icalendar file.
 *
 * @version 12-04-2007 James Hogan (jh559)
 *	- Created.
 */

/// Calendar source for icalendar files.
class CalendarSourceICalendar extends CalendarSource
{
	/// array[string => array] Format arrays for block types.
	static $sBlockFormat = array(
		// ****************************************************************** //
		// Entire file block
		// ****************************************************************** //
		NULL => array(
			'flags' => array(
				'strict',
			),
			'block' => array(
				'VCALENDAR',
			),
			'require' => array(
				'VCALENDAR',
			),
		),
		
		// ****************************************************************** //
		// Main calendar container
		// ****************************************************************** //
		'VCALENDAR' => array(
			'block' => array(
				'VALARM',
				'VEVENT',
				'VFREEBUSY',
				'VJOURNAL',
				'VTIMEZONE',
				'VTODO',
			),
		),
		
		// ****************************************************************** //
		// Event block
		// ****************************************************************** //
		'VEVENT' => array(
			'flags' => array(
				//'debug',
			),
			'single' => array(
				'CLASS' => 'TEXT_TOKEN',
				'CREATED' => 'DATE-TIME',
				'DESCRIPTION' => 'TEXT',
				'DTSTART' => array('DATE-TIME','DATE'),
				//'GEO' => '',
				'LAST-MODIFIED' => 'DATE-TIME',
				'LOCATION' => 'TEXT',
				'ORGANIZER' => 'TEXT',
				//'PRIORITY' => '',
				'DTSTAMP' => 'DATE-TIME',
				//'SEQ' => '',
				'STATUS' => array(array('TENTATIVE','CONFIRMED','CANCELLED'),'TEXT-TOKEN'),
				'SUMMARY' => 'TEXT',
				'TRANSP' => array(array('OPAQUE','TRANSPARENT'),'TEXT-TOKEN'),
				'UID' => 'TEXT',
				//'URL' => '',
				//'RECURID' => '',
				'DTEND' => array('DATE-TIME','DATE'),
				//'DURATION' => '',
			),
			'multiple' => array(
				//'ATTACH' => '',
				//'ATTENDEE' => '',
				//'CATEGORIES' => '',
				'COMMENT' => 'TEXT',
				//'CONTACT' => '',
				'EXDATE' => array(TRUE, 'DATE-TIME', 'DATE'),
				'EXRULE' => 'RECUR',
				//'REQUEST-STATUS' => '',
				//'RELATED' => '',
				//'RESOURSES' => '',
				'RDATE' => array(TRUE, 'DATE-TIME', 'DATE', 'PERIOD'),
				'RRULE' => 'RECUR',
			),
			'required' => array(
				'DTSTART',
				'UID',
			),
			'mutex' => array(
				'DTEND' => array('DURATION'),
				'DURATION' => array('DTEND'),
			),
		),
		
		// ****************************************************************** //
		// Free busy time block
		// ****************************************************************** //
		'VFREEBUSY' => array(
			'required' => array(
				'UID',
			),
		),
		
		// ****************************************************************** //
		// Journal block
		// ****************************************************************** //
		'VJOURNAL' => array(
			'required' => array(
				'UID',
			),
			'single' => array(
				'STATUS' => array(array('DRAFT','FINAL','CANCELLED'),'TEXT-TOKEN'),
			),
		),
		
		// ****************************************************************** //
		// Timezone block
		// ****************************************************************** //
		'VTIMEZONE' => array(
		),
		
		// ****************************************************************** //
		// Todo item block
		// ****************************************************************** //
		'VTODO' => array(
			'required' => array(
				'UID',
			),
			'single' => array(
				'STATUS' => array(array('NEEDS-ACTION','COMPLETED','IN-PROGRESS', 'CANCELLED'),'TEXT-TOKEN'),
			),
		),
		
		// ****************************************************************** //
		// Alarm block
		// ****************************************************************** //
		'VALARM' => array(
		),
	);
	
	/// array[lines] iCalendar structure.
	protected $mStructure = array();
	/// array[properties] iCalendar organised data.
	protected $mData = array();
	/// array Array of timezones.
	protected $mTimezones = array();
	
	// Temporary variables.
	/// int Line number.
	protected $mLine = NULL;
	/// string Message prefix stack.
	protected $mMessagePrefixStack = array();
	/// array[string] Array of warnings.
	protected $mWarnings = array();
	/// array[string] Array of errors.
	protected $mErrors = array();
	
	/// Default constructor.
	function __construct($ICalendarData = '', $SourceId)
	{
		parent::__construct();
		
		$this->SetSourceId($SourceId);
		$this->mName = 'iCalendar file';
		//$this->mCapabilities[] = 'rsvp';
		//$this->mCapabilities[] = 'refer';
		
		$this->SetIcalData($ICalendarData);
	}
	
	/// Set the icalendar data.
	/**
	 * @param $ICalendarData string iCalendar data.
	 * @return bool Whether any errors.
	 */
	function SetIcalData($ICalendarData = '')
	{
		$lines = $this->GetContentLines($ICalendarData);
		
		// Extract the structure of the file.
		$lines = $this->ExtractStructure($lines);
		if (NULL === $lines) {
			$this->mStructure = array();
			$this->mData = array();
			return FALSE;
		} else {
			$this->mStructure = $lines;
			$this->mData = $this->ReadBlock($this->mStructure, self::$sBlockFormat[NULL]);
			return TRUE;
		}
	}
	
	/// Split ical text into content lines.
	/**
	 * @param $Text string
	 * @return array[string].
	 */
	function GetContentLines($Text)
	{
		// Take the first 77 octets (each line can be up to 75)
		// Find the first \n and ensure it is preceeded by a \r
		if (FALSE !== ($fnl = strpos(substr($Text,0,77), "\n"))) {
			$previous = substr($Text, $fnl-1,1);
			if ($previous !== "\r") {
				$this->Warning('The file does not appear to use windows style line endings as the iCal standard requires.');
			}
		} else {
			$this->Warning('No line feed found in first 77 bytes');
		}
		$CRLF = '/\r?\n/';
		// Split into content lines
		$lines = preg_split($CRLF,$Text);
		// Unfold any line breaks (\r\n[space,tab])
		$last_nonfolded = NULL;
		foreach ($lines as $key => $line) {
			if (preg_match('/^[ \t](.*)$/',$line, $matches) &&
					NULL !== $last_nonfolded) {
				$lines[$last_nonfolded] .= $matches[1];
				unset($lines[$key]);
			} else {
				$last_nonfolded = $key;
			}
		}
		// Process each line
		foreach ($lines as $key => $value) {
			if (empty($value)) {
				unset($lines[$key]);
			} else {
				$this->mLine = $key+1;
				$lines[$key] = $this->ReadContentLine($value);
				if (NULL === $lines[$key]) {
					unset($lines[$key]);
				}
			}
		}
		$this->mLine = NULL;
		return $lines;
	}
	
	/// Fedge the events of the source.
	/**
	 * @param $Data CalendarData Data object to add events to.
	 */
	protected function _FetchEvents(&$Data)
	{
		//echo('<pre align="left">');
		//print_r($this->ClearMessages());
		$occurrence_uid = 0;
		if (is_array($this->mData) && array_key_exists('VCALENDAR', $this->mData)) {
			foreach ($this->mData['VCALENDAR'] as $calendar) {
				if (array_key_exists('VEVENT', $calendar)) {
					foreach ($calendar['VEVENT'] as $event) {
						//print_r($event);
						$event_obj = &$Data->NewEvent();
						$event_obj->SourceEventId = $event['UID'];
						if (array_key_exists('SUMMARY', $event)) {
							$event_obj->Name = $event['SUMMARY'];
						}
						if (array_key_exists('DESCRIPTION', $event)) {
							$event_obj->Description = $event['DESCRIPTION'];
						}
						if (array_key_exists('LAST-MODIFIED', $event)) {
							$event_obj->LastUpdate = $event['LAST-MODIFIED'];
						}
						$start = $event['DTSTART'];
						if (array_key_exists('DTEND', $event)) {
							$end = $event['DTEND'];
						} elseif (array_key_exists('DURATION', $event)) {
							$end = $start + $event['DURATION'];
						} else {
							$end = $start;
						}
						
						$event_obj->Recur = new RecurrenceSet;
						$event_obj->Recur->SetStartEnd($start, $end);
						if (array_key_exists('RDATE', $event)) {
							var_dump($event['RDATE']);
							$event_obj->Recur->AddRDates();
						}
						if (array_key_exists('RRULE', $event)) {
							$event_obj->Recur->AddRRules($event['RRULE']);
						}
						if (array_key_exists('EXDATE', $event)) {
							var_dump($event['EXDATE']);
							$event_obj->Recur->AddExDates();
						}
						if (array_key_exists('EXRULE', $event)) {
							$event_obj->Recur->AddExRules($event['EXRULE']);
						}
						$occurrences = $event_obj->Recur->Resolve($this->mRange[0], $this->mRange[1]);
						
						$state = 'published';
						if (array_key_exists('STATUS', $event)) {
							switch ($event['STATUS']) {
								case 'TENTATIVE':
									$state = 'draft';
									break;
								case 'CONFIRMED':
									$state = 'published';
									break;
								case 'CANCELLED':
									$state = 'cancelled';
									break;
							}
						}
						$time_associated = (
							date('His', $start) === '000000' &&
							date('His', $end  ) === '000000'
						);
						
						foreach ($occurrences as $date => $times) {
							foreach ($times as $time => $duration) {
								$occurrence_obj = &$Data->NewOccurrence($event_obj);
								$occurrence_obj->SourceOccurrenceId = $occurrence_uid++;
								$occurrence_obj->Origin = 'recur';
								$occurrence_obj->State = $state;
								$occurrence_obj->StartTime = new Academic_time(strtotime($date.$time));
								$occurrence_obj->EndTime   = $occurrence_obj->StartTime->Adjust($duration.'seconds');
								$occurrence_obj->TimeAssociated = $time_associated;
								//$occurrence_obj->$LocationDescription = ;
								//$occurrence_obj->$LocationLink = ;
								unset($occurrence_obj);
							}
						}
						unset($event_obj);
					}
				}
			}
		}
		//echo('</pre>');
	}
	
	/// Extract the file structure.
	/**
	 * @param $Lines array[string] Array of lines.
	 * @return array[line] Array of lines where each line can be:
	 *	- int => string Normal line indexed by line number.
	 *	- int => array Chunk with sub lines and 'name' property.
	 */
	function ExtractStructure($Lines)
	{
		// Stack of parent chunks.
		$chunk_stack = array();
		// Current chunk (not on stack).
		$chunk = array();
		// Go through the lines.
		foreach ($Lines as $line_number => $line) {
			// on a begin, start a new chunk.
			if ('BEGIN' === $line['name']) {
				array_push($chunk_stack, $chunk);
				$chunk = $line;
				$chunk['line'] = $line_number;
			// on an end, finish the last chunk and add to the parent chunk.
			} elseif ('END' === $line['name']) {
				if (array_key_exists('name',$chunk)) {
					if ($chunk['value'] === $line['value']) {
						$parent_chunk = array_pop($chunk_stack);
						$line_no = $chunk['line'];
						unset($chunk['line']);
						$parent_chunk[$line_no] = $chunk;
						$chunk = $parent_chunk;
					} else {
						$this->mLine = $line_number+1;
						$this->Error('END:'.$line['value'].
							' does not match BEGIN:'.$chunk['value'].' on line '.
							($chunk['line']+1));
						return NULL;
					}
				} else {
					$this->mLine = $line_number+1;
					$this->Error('Unmatched END');
					return NULL;
				}
			// on any other text, add to the current chunk.
			} elseif (!empty($line['value']) || array_key_exists('params',$line)) {
				$chunk[$line_number] = $line;
			}
		}
		// check for unended chunks.
		if (array_key_exists('name',$chunk)) {
			$this->Error('BEGIN:'.$chunk['name'].' on line '.
				$chunk['line'].' not matched by an END');
			return NULL;
		} else {
			return $chunk;
		}
	}
	
	/// Reads the property bits.
	/**
	 * @param $Line string Line of text in form ID*(;ID*=.*(,.*)*)*:.*
	 * @return array,NULL:
	 *	- 'name'	string Name of property
	 *	- 'params'  array[string => array[string]]
	 *	- 'value'   string
	 *
	 * @see http://www.ietf.org/rfc/rfc2445.txt page 14
	 */
	function ReadContentLine($Line)
	{
		$paramvalue = '([^";:,]*|"[^"]*")';
		$token = '[a-zA-Z\-]+';
		$content_line = '/^('.$token.')((;'.$token.'='.$paramvalue.'(,'.$paramvalue.')*)*):(.*)$/';
		if (preg_match($content_line, $Line, $matches)) {
			$result = array(
				'name'   => strtoupper($matches[1]),
				'value'  => $matches[7],
			);
			if (array_key_exists(2,$matches) && !empty($matches[2])) {
				$paramvalue = '(([^";:,]+)|"([^"]*)")';
				preg_match_all('/'.$token.'=('.$paramvalue.')(,'.$paramvalue.')*/', $matches[2], $params);
				foreach ($params[0] as $key => $param) {
					$equal_pos = strpos($param,'=');
					$name = strtoupper(substr($param,0,$equal_pos));
					$values = substr($param,$equal_pos+1);
					// Values can be in quotes in which case commas are ignored
					preg_match_all('/'.$paramvalue.'/', $values, $value_list);
					// Not in quotes:
					foreach ($value_list[2] as $key => $value) {
						if (!empty($value)) {
							$result['params'][$name][$key] = strtoupper($value);
						}
					}
					// In quotes:
					foreach ($value_list[3] as $key => $value) {
						if (!empty($value)) {
							$result['params'][$name][$key] = $value;
						}
					}
				}
			}
			return $result;
		} elseif (!empty($Line)) {
			$this->Error('Invalid content line: '.$Line);
			return NULL;
		}
	}
	
	function Read_TEXT_TOKEN($Property, $Extra = NULL)
	{
		if (!preg_match('/^[A-Za-z\-]*$/',$Property['value'])) {
			$this->Error('Invalid value "'.$Property['value'].'"');
			return NULL;
		} else {
			$value = strtoupper($Property['value']);
			if (NULL !== $Extra && !in_array($value, $Extra)) {
				$this->Error('Invalid value "'.$value.'", should be in {'.implode(', ',$Extra).'}.');
				$value = NULL;
			}
			return $value;
		}
	}
	
	function Read_TEXT($Property, $Flags = array())
	{
		static $escapings = array(
			'\\\\' => '\\',
			'\\;' => ';',
			'\\,' => ',',
			'\\N' => "\n",
			'\\n' => "\n",
		);
		// Non backslash or escape string
		if (!preg_match('/^([^\\\\]|\\\\[\\\\;,Nn])*$/',$Property['value'])) {
			$this->Error('Invalid text value (unescaped \\) "'.$Property['value'].'"');
			return NULL;
		} else {
			preg_match_all('/([^\\\\\\,]|\\\\[\\\\;,Nn])+/',$Property['value'],$matches);
			$values = array();
			if (array_key_exists('params', $Property)) {
				if (array_key_exists('LANGUAGE', $Property['params'])) {
					$values['LANGUAGE'] = $Property['params']['LANGUAGE'];
				}
			}
			foreach ($matches[0] as $key => $match) {
				$values[] = str_replace(
					array_keys($escapings),
					array_values($escapings),
					$match
				);
			}
			if (in_array('list',$Flags)) {
				return $values;
			} elseif (count($values) > 1) {
				$this->Error('Invalid unlisted text value (unescaped ,) "'.$Property['value'].'"');
				return NULL;
			} elseif (empty($values)) {
				return '';
			} else {
				return $values[0];
			}
		}
	}
	
	function ReadMultiple($Property, $Function)
	{
		$values = explode(',',$Property['value']);
		$temp_property = $Property;
		foreach ($values as $key => $value) {
			$temp_property['value'] = $value;
			$values[$key] = $this->$Function($temp_property);
			if (NULL === $values[$key]) {
				unset($values[$key]);
			}
		}
		return $values;
	}
	
	//TZIDs in calendars often contain leading information that should be stripped
	//Example: TZID=/mozilla.org/20050126_1/Europe/Berlin
	//Need to return the last part only
	function parse_tz($data){
		$fields = explode('/',$data);
		$tz = array_pop($fields);
		$tmp = array_pop($fields);
		if (isset($tmp) && !empty($tmp)) $tz = $tmp.'/'.$tz;
		return $tz;
	}
	
	/// @todo Standardise Read_(DATE|DATE_TIME|PERIOD) output
	function Read_DATE($Property)
	{
		$datetime_regex = '/^((\d{4})(\d{2})(\d{2}))$/';
		
		// check format of date-time value
		if (!preg_match($datetime_regex, $Property['value'], $matches)) {
			$this->Error('Invalid date-time value: "'.$Property['value'].'"');
			return NULL;
		}
		
		// check the date part
		$year  = (int)$matches[2];
		$month = (int)$matches[3];
		$day   = (int)$matches[4];
		if (!checkdate($month,$day,$year)) {
			$this->Error('Invalid date value: "'.$matches[1].'"');
			return NULL;
		}
		// Restrict to 1970, the epoch of the unix timestamp
		if ($year < 1970) {
			$year = 1970;
		}
		
		/// @todo Check timezone when reading time value
		$unixtime = gmmktime(0, 0, 0, $month, $day, $year);
		
		return $unixtime;
	}
	
	function Read_DATE_TIME($Property)
	{
		$datetime_regex = '/^((\d{4})(\d{2})(\d{2}))T(\d{2})(\d{2})(\d{2})(Z?)$/';
		
		// check format of date-time value
		if (!preg_match($datetime_regex, $Property['value'], $matches)) {
			$this->Error('Invalid date-time value: "'.$Property['value'].'"');
			return NULL;
		}
		
		// check the date part
		$year  = (int)$matches[2];
		$month = (int)$matches[3];
		$day   = (int)$matches[4];
		if (!checkdate($month,$day,$year)) {
			$this->Error('Invalid date value: "'.$matches[1].'"');
			return NULL;
		}
		// Restrict to 1970, the epoch of the unix timestamp
		if ($year < 1970) {
			$year = 1970;
		}
		
		// check the time part
		$hour   = (int)$matches[5];
		$minute = (int)$matches[6];
		$second = (int)$matches[7];
		$is_zulu = !empty($matches[8]);
		
		// Pull out the timezone, or use GMT if zulu time was indicated.
		if (isset($Property['params']['TZID'])) {
			$tzs = $Property['params']['TZID'];
			if (count($tzs) > 1) {
				$this->Warning('Parameter TZID has multiple values, first value used.');
			}
			$tz_dt = $this->parse_tz($tzs[0]);
		} elseif ($is_zulu) {
			$tz_dt = 'GMT';
		}
		
		$unixtime = gmmktime($hour, $minute, $second, $month, $day, $year);

		// Check for daylight savings time.
		/// @todo Check timezone when reading time value
		/*$dlst = date('I', $unixtime);
		$server_offset_tmp = chooseOffset($unixtime);
		if (isset($tz_dt)) {
			if (array_key_exists($tz_dt, $tz_array)) {
				$offset_tmp = $tz_array[$tz_dt][$dlst];
			} else {
				$offset_tmp = '+0000';
			}
		} elseif (isset($calendar_tz)) {
			if (array_key_exists($calendar_tz, $tz_array)) {
				$offset_tmp = $tz_array[$calendar_tz][$dlst];
			} else {
				$offset_tmp = '+0000';
			}
		} else {
			$offset_tmp = $server_offset_tmp;
		}
		
		// Set the values.
		$unixtime = calcTime($offset_tmp, $server_offset_tmp, $unixtime);
		$date = date('Ymd', $unixtime);
		$time = date('Hi', $unixtime);*/
			
		// get the timezone
		// put into utc
		
		return $unixtime;
	}
	
	function ReadProperty($Value, $DefaultType, $Types = NULL, $List = FALSE, $ExtraData = NULL)
	{
		// See if there's an explicitly specified type
		$type = $DefaultType;
		if (isset($Value['params']['VALUE'])) {
			if (!is_array($Types)) {
				$this->Warning('Parameter VALUE not necessary in this context');
			} else {
				$type = $Value['params']['VALUE'];
				if (count($type) !== 1) {
					$this->Error('Parameter VALUE="'.implode(',',$type).'" must be a single value');
					return NULL;
				}
				$type = $type[0];
				if (!in_array($type, $Types)) {
					$this->Error('Parameter VALUE="'.$type.'" is not a valid type for this property, valid types are: '.implode(', ',array_keys($Types)));
					return NULL;
				}
			}
		}
		$function_name = 'Read_'.str_replace('-','_',$type);
		if ($List) {
			$values = explode(',', $Value['value']);
			foreach ($values as $key => $value) {
				$values[$key] = $Value;
				$values[$key]['value'] = $value;
			}
		} else {
			$values = array($Value);
		}
		$results = array();
		foreach ($values as $value) {
			if (NULL === $ExtraData) {
				$results[] = $this->$function_name($value);
			} else {
				$results[] = $this->$function_name($value, $ExtraData);
			}
		}
		if ($List) {
			return $results;
		} else {
			return $results[0];
		}
	}
	
	/// Read a block given the types of the properties.
	/**
	 * @param $Value array Array of properties.
	 * @param $Format array with the following optional fields
	 *	- 'single' array[string => {string,array(string)}]
	 *		Those properties that can only appear once and their type(s).
	 *	- 'multiple' array[string => {string,array(string)}]
	 *		Those properties that can appear multiple times and their type(s).
	 *	- 'block' array[string] Sub block types.
	 *	- 'required' array[string] Array of compulsory properties.
	 *	- 'mutex' array[string => array(string)]
	 *		Indexed by property name, the properties that cannot appear with the index property.
	 *	' 'flags' array[string]
	 *		Flag string:	'strict' (all properties must be recognised,
	 *						'wall'   (warn about stuff),
	 *						'debug'  (warn about extra stuff)
	 */
	function ReadBlock($Value, $Format)
	{
		$Singles   = (array_key_exists('single',   $Format) ? $Format['single']   : array());
		$Multiples = (array_key_exists('multiple', $Format) ? $Format['multiple'] : array());
		$Blocks    = (array_key_exists('block',    $Format) ? $Format['block']    : array());
		$Required  = (array_key_exists('required', $Format) ? $Format['required'] : array());
		$Mutex     = (array_key_exists('mutex',    $Format) ? $Format['mutex']    : array());
		$Flags     = (array_key_exists('flags',    $Format) ? $Format['flags']    : array());
		
		$first_line = $this->mLine;
		$object = array();
		foreach ($Value as $line => $value) {
			if (is_int($line)) {
				$name = $value['name'];
				// Read subblocks
				if ('BEGIN' === $name && in_array($value['value'],$Blocks)) {
					$blocktype = $value['value'];
					$this->mLine = $line.' ('.$blocktype.')';
					if (!array_key_exists($blocktype, $object)) {
						$object[$blocktype] = array();
					}
					if (array_key_exists($blocktype, self::$sBlockFormat)) {
						$store_value = $this->ReadBlock($value, self::$sBlockFormat[$blocktype]);
					} else {
						$store_value = $this->ReadProperty($value, $blocktype);
					}
					if (NULL !== $store_value) {
						$object[$blocktype][] = $store_value;
					}
					continue;
				}
				$this->mLine = $line.' ('.$name.')';
				$ignore = FALSE;
				// Check mutual exclusions
				if (array_key_exists($name, $Mutex)) {
					foreach ($Mutex[$name] as $not_allowed) {
						if (array_key_exists($not_allowed, $object)) {
							$this->Error('Property '.$name.' not allowed with existing property '.$not_allowed);
							$ignore = TRUE;
							break;
						}
					}
				}
				if (!$ignore) {
					// Read single properties
					if (array_key_exists($name, $Singles)) {
						if (array_key_exists($name, $object)) {
							$this->Warning('occurs more than once, later occurrences are ignored.');
						} else {
							$type = $Singles[$name];
							$list = FALSE;
							$extra = NULL;
							if (is_array($type)) {
								$types = $type;
								$type = 0;
								while (!is_string($types[$type])) {
									if (is_bool($types[$type])) {
										$list = $types[$type];
									} elseif (is_array($types[$type])) {
										$extra = $types[$type];
									}
									++$type;
								}
								$type = $types[$type];
							} else {
								$types = NULL;
							}
							$store_value = $this->ReadProperty($value, $type, $types, $list, $extra);
							if (NULL !== $store_value) {
								$object[$name] = $store_value;
							}
						}
					// Read multiple properties
					} elseif (array_key_exists($name, $Multiples)) {
						if (!array_key_exists($name, $object)) {
							$object[$name] = array();
						}
						$type = $Multiples[$name];
						$list = FALSE;
						$extra = NULL;
						if (is_array($type)) {
							$types = $type;
							$type = 0;
							while (!is_string($types[$type])) {
								if (is_bool($types[$type])) {
									$list = $types[$type];
								} elseif (is_array($types[$type])) {
									$extra = $types[$type];
								}
								++$type;
							}
							$type = $types[$type];
						} else {
							$types = NULL;
						}
						$store_value = $this->ReadProperty($value, $type, $types, $list, $extra);
						if (NULL !== $store_value) {
							$object[$name][] = $store_value;
						}
					} elseif ('X-' !== substr($name, 0,2)) {
						if ('BEGIN' === $name) {
							$name = $value['value'];
							$type = 'block';
						} else {
							$type = 'property';
						}
						if (in_array('strict', $Flags)) {
							$this->Error('Unrecognised '.$type.': '.$name.' in this context');
							return NULL;
						} elseif (in_array('wall', $Flags) || in_array('debug', $Flags)) {
							$this->Warning('Unrecognised '.$type.': '.$name.' in this context');
						}
					}
				}
			}
		}
		// Check that the required properties exist
		$this->mLine = $first_line;
		foreach ($Required as $require) {
			if (is_array($require)) {
				// Disjunction of requirements (any match will satisfy)
				$found = FALSE;
				foreach ($require as $disjunct) {
					if (array_key_exists($disjunct, $object)) {
						$found = TRUE;
						break;
					}
				}
				if (!$found) {
					$this->Error('All of the compulsory property group {'.implode(', ',$require).'} is missing or could not be read.');
					return NULL;
				}
			} else {
				// Single required match
				if (!array_key_exists($require, $object)) {
					$this->Error('Compulsory property '.$require.' is missing or could not be read.');
					return NULL;
				}
			}
		}
		$this->mLine = NULL;
		
		return $object;
	}
	
	/// Read a VEvent structure.
	function ReadTodo($Structure)
	{
		$todo = array(
			'rrules'	=> array(),
		);
		foreach ($Structure as $line => $value) {
			if (is_int($line)) {
				$this->mLine = $line.' ('.$value['name'].')';
				switch ($value['name']) {
					case 'RRULE':
						$todo['rrules'][] = $this->Read_RECUR($value['value']);
						break; 
				}
			}
		}
		return $event;
	}
	
	
	/// Read a date time value after regex validation.
	/**
	 * @param $Matches Array Matches from the regular expression function.
	 * @return mixed New mapping.
	 */
	function CheckDateTime($Matches)
	{
		// Check that the dat is valid.
		if (!checkdate($Matches[3],$Matches[4],$Matches[2])) {
			$this->Error('UNTIL date: '.$Matches[1].' not valid');
		} elseif (array_key_exists(5, $Matches)) {
			// Check that the time is valid.
			$num_errors = count($this->mErrors);
			$Matches = $this->ValidateMatches($Matches, array(
				6 => array(0,23),
				7 => array(0,59),
				8 => array(0,59), /// @todo Allow leap second until time.
			));
			if (count($this->mErrors)-$num_errors === 0) {
				$Matches = mktime(
					$Matches[6], $Matches[8], $Matches[7],
					$Matches[3], $Matches[4], $Matches[2]
				);
			}
		} else {
			$Matches = mktime(
				23, 59, 59,
				$Matches[3], $Matches[4], $Matches[2]
			);
		}
		return $Matches;
	}
	
	
	
	/// Read an iCal recurrence rule.
	/**
	 * @param $Rule string iCal RecurGetMessageMarker definition.
	 * @return Recur,NULL Recurrence object or NULL on failure.
	 */
	function Read_RECUR($Rule)
	{
		static $RecurClass = 'CalendarRecurRule';
		// Case insensitive, from now on its all assumed upper case.
		$marker = $this->GetMessageMarker();
		
		// Format data
		$weekday = implode('|',array_keys(CalendarRecurRule::$sWeekdays));
		$date = '(\d{4})(\d{2})(\d{2})';
		$time = '(\d{2})(\d{2})(\d{2})';
		$valid_frequencies = array(
			'SECONDLY' => 'secondly',
			'MINUTELY' => 'minutely',
			'HOURLY' => 'hourly',
			'DAILY' => 'daily',
			'WEEKLY' => 'weekly',
			'MONTHLY' => 'monthly',
			'YEARLY' => 'yearly',
			//'TERMLY' => 'termly',	// non standard
			//'ACTERMLY' => 'acyearly',	// non standard
		);
		$valid_properties = array(
			'FREQ'		=> array('[A-Z]+',	array(0=>$valid_frequencies)),
			'UNTIL'		=> array('('.$date.')(T'.$time.')?', 'CheckDateTime'),
			'COUNT'		=> array('\d+',		array(0=>array(1,NULL))),
			'INTERVAL'	=> array('\d+',		array(0=>array(1,NULL))),
			'WKST'		=> array($weekday, ),
		);
		$valid_list_properties = array(
			'BYSECOND'	=> array('\d{1,2}', array(0=>array(0,59))),
			'BYMINUTE'	=> array('\d{1,2}', array(0=>array(0,59))),
			'BYHOUR'	=> array('\d{1,2}', array(0=>array(0,23))),
			'BYDAY'		=> array('([+-]?\d{1,2})?('.$weekday.')', array(1=>array(NULL,-1,1,NULL))),
			'BYMONTHDAY'=> array('[+-]?\d{1,2}', array(0=>array(-32,1,1,32))),
			'BYYEARDAY'	=> array('[+-]?\d{1,3}', array(0=>array(-355,-1,1,355))),
			'BYWEEKNO'	=> array('[+-]?\d{1,2}', array(0=>array(-53,-1,1,53))),
			'BYMONTH'	=> array('\d{1,2}', array(0=>array(1,12))),
			'BYSETPOS'	=> array('\d{1,3}', array(0=>array(1,NULL))),
		);
		// Split into properties
		$properties = array();
		$order = 0;
		$values = explode(';', $Rule['value']);
		foreach ($values as $property) {
			$split_by_equals = explode('=', $property);
			if (2 !== count($split_by_equals)) {
				$this->Error('Badly formed property "'.$property.'"');
				continue;
			}
			$name = $split_by_equals[0];
			$values = explode(',',$split_by_equals[1]);
			$this->PushMessagePrefix('Property "'.$name.'" ');
			if ($order === 0 && $name !== 'FREQ') {
				$this->Warning('RRule did not start with item FREQ');
			}
			if (array_key_exists($name, $properties)) {
				$this->Warning('occurs multiple times, later occurrences ignored.');
			} else {
				// Check property is valid
				if (array_key_exists($name, $valid_properties)) {
					$regex = $valid_properties[$name][0];
					if (array_key_exists(0,$values) && 1 === count($values)) {
						$value = $values[0];
						if (!preg_match('/^'.$regex.'$/', $value, $matches)) {
							$this->Error('value: "'.$value.'" does not match regular expression "'.$regex.'"');
						} else {
							if (array_key_exists(1,$valid_properties[$name])) {
								$matches = $this->ValidateMatches($matches, $valid_properties[$name][1]);
							}
							$properties[$name] = $matches;
						}
					} else {
						$this->Error('Single value expected');
					}
				} elseif (array_key_exists($name, $valid_list_properties)) {
					$regex = $valid_list_properties[$name];
					foreach ($values as $item_index => $item) {
						if (!preg_match('/^'.$regex[0].'$/', $item, $matches)) {
							$this->Error('['.($item_index+1).'] value "'.$item.'" does not match regular expression "'.$regex[0].'"');
						} else {
							if (array_key_exists(1,$regex)) {
								$matches = $this->ValidateMatches($matches, $regex[1]);
							}
							$values[$item_index] = $matches;
						}
					}
					$properties[$name] = $values;
				} else {
					$this->Error('unrecognised');
				}
			}
			$this->PopMessagePrefix();
			++$order;
		}
		// Check some rules
		if (!array_key_exists('FREQ', $properties)) {
			$this->Error('FREQ item not found');
		}
		if (array_key_exists('COUNT', $properties) && array_key_exists('UNTIL', $properties)) {
			$this->Error('COUNT and UNTIL items found together');
		}
		
		$new_errors = $this->GetErrorsSinceMarker($marker);
		
		if (!$new_errors) {
			$recur = new $RecurClass();
			
			// Get the data into the structure, validating as we go
			$recur->SetFrequency($properties['FREQ'][0]);
			
			if (array_key_exists('UNTIL', $properties)) {
				$recur->SetUntil($properties['UNTIL'][0]);
			} elseif (array_key_exists('COUNT', $properties)) {
				$recur->SetCount($properties['COUNT'][0]);
			} else {
				$recur->SetUntil();
				$recur->SetCount();
			}
			if (array_key_exists('INTERVAL', $properties)) {
				$recur->SetInterval($properties['INTERVAL'][0]);
			} else {
				$recur->SetInterval();
			}
			$by_lists = array(
				'BYSECOND'		=> 'SetBySecond',
				'BYMINUTE'		=> 'SetByMinute',
				'BYHOUR'		=> 'SetByHour',
				'BYMONTHDAY'	=> 'SetByMonthDay',
				'BYYEARDAY'		=> 'SetByYearDay',
				'BYWEEKNO'		=> 'SetByWeekNo',
				'BYMONTH'		=> 'SetByMonth',
				'BYSETPOS'		=> 'SetBySetPos',
			);
			foreach ($by_lists as $ical_name => $mutator_name) {
				if (array_key_exists($ical_name, $properties)) {
					$temp_array = array();
					foreach ($properties[$ical_name] as $key => $value) {
						$temp_array[$key] = $value[0];
					}
					$recur->$mutator_name($temp_array);
					unset($temp_array);
				} else {
					$recur->$mutator_name();
				}
			}
			if (array_key_exists('BYDAY', $properties)) {
				$temp_array = array();
				foreach ($properties['BYDAY'] as $key => $value) {
					if (!array_key_exists(1,$value) || empty($value[1])) {
						$value[1] = NULL;
					}
					$temp_array[$key] = array($value[1], CalendarRecurRule::$sWeekdays[$value[2]]);
				}
				$recur->SetByDay($temp_array);
				unset($temp_array);
			} else {
				$recur->SetByDay();
			}
			if (array_key_exists('WKST', $properties)) {
				$recur->SetWkSt(CalendarRecurRule::$sWeekdays[$properties['WKST'][0]]);
			} else {
				$recur->SetWkSt();
			}
			return $recur;
		} else {
			return NULL;
		}
	}
	
	
	/// Clear and return the messages.
	/**
	 * @return array Message categories:
	 *	- 'error' array[string] Fatal errors.
	 *	- 'warning' array[string] Warnings.
	 */
	function ClearMessages()
	{
		$warnings = $this->mWarnings;
		$errors = $this->mErrors;
		$this->mWarnings = array();
		$this->mErrors = array();
		$this->mMessagePrefixStack = array();
		$this->mLine = NULL;
		return array('error' => $errors, 'warning' => $warnings);
	}
	
	/// Post a new fatal error message.
	/**
	 * @param $Message string Error message.
	 */
	function Error($Message)
	{
		$this->mErrors[] = (NULL !== $this->mLine ? 'Line '.$this->mLine.': ' : '').$this->GetMessagePrefix().$Message;
	}
	
	
	/// Post a new warning message.
	/**
	 * @param $Message string Warning message.
	 */
	function Warning($Message)
	{
		$this->mWarnings[] = (NULL !== $this->mLine ? 'Line '.$this->mLine.': ' : '').$this->GetMessagePrefix().$Message;
	}
	
	/// Get the current imploding of message prefixes.
	/**
	 * @return string Current message prefix.
	 */
	protected function GetMessagePrefix()
	{
		$num_prefixes = count($this->mMessagePrefixStack);
		if ($num_prefixes) {
			return $this->mMessagePrefixStack[$num_prefixes-1];
		} else {
			return '';
		}
	}
	
	/// Add a new message prefix onto the stack.
	/**
	 * @param $Prefix string Prefix to add to the existing prefixes.
	 */
	function PushMessagePrefix($Prefix)
	{
		array_push($this->mMessagePrefixStack, $this->GetMessagePrefix().$Prefix);
	}
	
	/// Remove and return the last pushed message prefix.
	/**
	 * @return string Removed prefix.
	 */
	function PopMessagePrefix()
	{
		return array_pop($this->mMessagePrefixStack);
	}
	
	/// Get an object representing a marker relating to messages.
	function GetMessageMarker()
	{
		return array(
			count($this->mErrors),
			count($this->mWarnings),
		);
	}
	
	/// Get the number of error messages since a given marker.
	function GetErrorsSinceMarker($Marker)
	{
		return count($this->mErrors)-$Marker[0];
	}
	
	/// Get the number of warning messages since a given marker.
	function GetWarningsSinceMarker($Marker)
	{
		return count($this->mWarnings)-$Marker[1];
	}
	
	/// Validate the items in an array.
	/**
	 * @param $Matches array[key=>value] Array to validate.
	 * @param $Validator array[key=>validator] Array of validators.
	 *	- Ranges e.g. array(-5,-1,1,NULL) for [-5,-1]+[1,inf].
	 *	- Array of valid values (non int) mapping to what to replace it with.
	 * @return @a Matches Modified matches.
	 */
	function ValidateMatches($Matches, $Validator)
	{
		if (is_array($Validator)) {
			foreach ($Validator as $key => $validation) {
				if (array_key_exists($key, $Matches) && !empty($Matches[$key])) {
					if (array_key_exists(0, $validation)) {
						// Set of ranges
						$Matches[$key] = (int)$Matches[$key];
						$index = 0;
						$valid = FALSE;
						while (array_key_exists($index, $validation) && array_key_exists($index+1, $validation)) {
							if ((NULL === $validation[$index] || $Matches[$key] >= $validation[$index]) &&
								(NULL === $validation[$index+1] || $Matches[$key] <= $validation[$index+1])) {
								$valid = TRUE;
								break;
							}
							$index += 2;
						}
						if (!$valid) {
							$this->Error('value '.$Matches[$key].' in "'.$Matches[0].'" out of range ');
						}
						unset($valid);
					} elseif (array_key_exists($Matches[$key], $validation)) {
						// Mapping of valid values: found
						$Matches[$key] = $validation[$Matches[$key]];
					} else {
						// Mapping of valid values: not found
						$this->Error('value '.$Matches[$key].' in "'.$Matches[0].'" not a valid value ('.implode(',',array_keys($validation)).')');
					}
				}
			}
		} else {
			assert('is_string($Validator)');
			$Matches = $this->$Validator($Matches);
		}
		return $Matches;
	}
}



/// Dummy class
class Calendar_source_icalendar
{
	/// Default constructor.
	function __construct()
	{
		$CI = & get_instance();
		// models to be loaded
	}
}

?>