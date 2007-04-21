<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Filter_uri.php
 * @brief Library for using filters in URIs.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

class FilterDefinition
{
	/*
		array[
			subsegid =. array(
				'name' => string = key,
				[part definitions]
				part_index => array(
					'name' => string = parent.name,
					'type' => enum('enum','string','int') = 'enum',
					'match' => regex string,
					'range' => array(array[int min, int max]),
					'count' => NULL,int,array(n,m) = 1,
					int => enumeration,
				)
			)
		]
	*/
	protected $mSubSegments;
	/*
		// category
		'cat' => array(
			'name' => 'category',
			array(
				'no-social',
				'no-academic',
				'no-meeting',
			),
		),
		'att' => array(
			'name' => 'attending',
			array(
				'no-no',
				'no-maybe',
				'no-yes',
			)
		),
		'search' => array(
			array(
				'name' => 'field',
				'all',
				'name',
				'description',
			),
			array(
				'name' => 'criteria'
				'type' => 'string',
			),
			array(
				'name' => 'flags',
				'count' => NULL,
				'regex',
				'case',
			),
		),
	*/
	
	function __construct($SubSegments)
	{
		$this->mSubSegments = $SubSegments;
	}
	
	/**
	 * @param $Definition array(
			'name' => string = parent.name,
			'type' => enum('enum','string','int') = 'enum',
			'match' => regex string,
			'range' => array(array[int min, int max]),
			'count' => int,array(n,m) = 1,
			int => enumeration,
		) Definition of the part.
	 * @param $Part string The actual part.
	 * @return mixed Value result.
	 */
	protected function MatchPart($Definition, $Part)
	{
		if (array_key_exists('type', $Definition)) {
			$type = $Definition['type'];
		} else {
			$type = 'enum';
		}
		switch ($type) {
			case 'enum':
				// not valid
				if (!is_int(array_search($Part, $Definition))) {
					return NULL;
				}
				break;
			
			case 'string':
				// regular expression matcher exists
				if (array_key_exists('match', $Definition)) {
					// valid? return the results
					if (preg_match($Definition['match'], $Part, $matches)) {
						$Part = $matches;
					} else {
						return NULL;
					}
				}
				break;
			
			case 'int':
				// not numeric
				if (!is_numeric($Part)) {
					return NULL;
				}
				$Part = (int)$Part;
				// range checker
				if (array_key_exists('range', $Definition)) {
					$in_range = FALSE;
					foreach ($Definition['range'] as $range) {
						if ($Part >= $range[0] && $Part <= $range[1]) {
							$in_range = TRUE;
						}
					}
					if (!$in_range) {
						// not matched range
						return NULL;
					}
				}
				break;
			
			default:
				// the definition is invalid, this is a programmer error
				assert('FALSE===\'Invalid part type:'.$type);
				return NULL;
		}
		return $Part;
	}
	
	function ReadUri($UriSegment)
	{
		if (NULL === $UriSegment) {
			return NULL;
		}
		// Split by colons
		$subsegments = explode('.', $UriSegment);
		$results = array();
		
		// go through subsegments
		foreach ($subsegments as $subsegment) {
			// get parts and check that the part id is valid
			$parts = explode(':', $subsegment);
			$part_id = array_shift($parts);
			$result = array();
			if (array_key_exists($part_id, $this->mSubSegments)) {
				// keep record of which input part we're on.
				$part_index = 0;
				// go through definition parts, matching with given parts
				foreach ($this->mSubSegments[$part_id] as $key => $part_definition) {
					if (is_int($key)) {
						if (array_key_exists('count', $part_definition)) {
							$count_range = $part_definition['count'];
							if (is_int($count_range)) {
								$min = $count_range;
								$max = $count_range;
							} else {
								assert('is_array($count_range)');
								$min = $count_range[0];
								if (array_key_exists(1, $count_range)) {
									$max = $count_range[1];
								} else {
									$max = NULL;
								}
							}
						} else {
							$min = 1;
							$max = 1;
						}
						// number of matches of each part
						$count = 0;
						$result[$key] = array();
						while (($count < $max || NULL === $max) && array_key_exists($part_index, $parts)) {
							$part = $this->MatchPart($part_definition, $parts[$part_index]);
							if (NULL !== $part) {
								// valid
								$result[$key][] = $part;
								++$part_index;
								++$count;
							} else {
								break;
							}
						}
						if ($count < $min) {
							return FALSE;
						}
						if (!array_key_exists('count', $part_definition)) {
							$result[$key] = $result[$key][0];
						}
					}
				}
				if ($part_index < count($parts)) {
					// Not used up all the parts
					return FALSE;
				}
			}
			if (!array_key_exists($part_id, $results)) {
				$results[$part_id] = array();
			}
			$results[$part_id][] = $result;
		}
		
		return $results;
	}
}

/// Library class for using filters in URIs.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 */
class Filter_uri
{
}

?>