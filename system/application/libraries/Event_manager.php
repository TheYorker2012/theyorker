<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Event_manager.php
 * @brief Event Manager Library.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

/**
 * @brief Event Manager Library.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Provides an interface between the controller classes and the model classes.
 * Contains most of the logic relating to event management.
 *
 */
class Event_manager {
	
	/**
	 * @brief Get the next timestamp at a particular time of day.
	 * @author James Hogan (jh559@cs.york.ac.uk)
	 *
	 * Get the next timestamp after @a $Time that is @a $SliceTime minutes past
	 * midnight.
	 *
	 * @param $Time Timestamp.
	 * @param $SliceTime Number of minutes past midnight to slices.
	 *
	 * @return Timestamp of the next time after @a $Time that is @a $SliceTime
	 *	minutes past midnight.
	 *
	 * @pre 0 <= @a $SliceTime < 24*60
	 */
	function GetNextSlice($Time, $SliceTime)
	{
		// Get $SliceTime minutes after midnight on day of $Time
		$return_time = mktime(
			(int)($SliceTime/60), // hours
			$SliceTime%60,        // minutes
			0,                    // seconds
			date('m', $Time),     // month
			date('d', $Time),     // day
			date('Y', $Time));    // year
		
		// If before $Time, do the same on the next day
		if ($return_time <= $Time) {
			$return_time = strtotime('+1 day', $return_time);
		}
		return $return_time;
	}
	
	/**
	 * @brief Slice up event occurrences at a particular time of day.
	 * @author James Hogan (jh559@cs.york.ac.uk)
	 *
	 * Slice up each occurrence in a set of occurrences at a particular time of
	 * day.
	 *
	 * @param $OccurrencesArray Array of occurrences.
	 * @param $SliceTime The time that events are sliced (measured in minutes
	 *	past midnight).
	 *
	 * @return Array of occurrence slices, structured similarly to
	 *	@a $OccurrencesArray with the following changes:
	 *	- Field ['slice_start'] added with start time of slice (timestamp).
	 *	- Field ['slice_end'] added with end time of slice (timestamp).
	 *
	 * @pre Every occurrence in @a $OccurrenceArray must contain these fields:
	 *	- ['start']: Start time of occurrence (timestamp).
	 *	- ['end']: End time of occurrence (timestamp).
	 *
	 * @pre 0 <= @a $SliceTime < 24*60.
	 */
	function SliceOccurrences($OccurrencesArray, $SliceTime)
	{
		// Prepare a return array
		$return_array = array();
		$next_return_array_index = 0;
		// Go through occurrences, copying slices to end of $return_array
		foreach ($OccurrencesArray as $occurrence_info) {
			$next_slice_time  = $occurrence_info['start'];
			$slice_end_time   = $occurrence_info['end'  ];
			// Go through slices until we get to the last slice
			do {
				// Calculate start time of slice in $slice_start_time
				$slice_start_time  = $next_slice_time;
				// Calculate end time of slice in $next_slice_time
				$next_slice_time = $this->GetNextSlice($next_slice_time, $SliceTime);
				$last_slice      = ($next_slice_time >= $slice_end_time);
				if ($last_slice === TRUE) {
					$next_slice_time = $slice_end_time;
				}
				
				// Add slice to end of $return_array
				$return_array[$next_return_array_index] = $occurrence_info;
				$return_array[$next_return_array_index]['slice_start'] = $slice_start_time;
				$return_array[$next_return_array_index]['slice_end'  ] = $next_slice_time;
				++$next_return_array_index;
			} while ($last_slice === FALSE);
		}
		
		// Now we're done
		return $return_array;
	}
}

?>