<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Uri_tree_controller.php
 * @brief General controller for complex uri nestings.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

/// Complex controller.
class UriTreeSubcontroller extends model
{
	/// array Recognition structure.
	protected $mStructure = NULL;
	
	/// array Data container.
	protected $mData = array();
	
	/// bool Whether finished yet.
	protected $mFinished = FALSE;
	
	/// Default constructor.
	/**
	 * @param $Structure array URI processing structure.
	 */
	function __construct($Structure)
	{
		parent::model();
		
		// Initialise
		$this->mStructure = $Structure;
	}
	
	/// virtual Set the base path.
	function SetPath($Path) {}
	
	/// Main mapping page.
	function _map($args)
	{
		// Find the path up to the first argument.
		$segs = $this->uri->segment_array();
		$this->SetPath(implode('/',array_slice($segs, 0, count($segs)-count($args))));
		// Process the rest of the path.
		$this->mFinished = FALSE;
		$rest = $this->_processUriStructure($this->mStructure, $args);
		if (!$this->mFinished) {
			show_404();
		}
	}
	
	/// Process a portion of the structure with a given uri array.
	/**
	 * This recursive structure has a number of key-value pairs.
	 * Given a uri segment, the following sequence of checks is performed.
	 *	- if a _store element exists, store the top into that value.
	 *	- if a _call element exists, call the function with the rest of the input and break.
	 *	- if the segment is blank, process the value of value[value['']] with the rest of the segments.
	 *	- if a value[segment] exists, process the value with the rest of the segments.
	 *	- check for a _in element, find an element with a matching key, process element 0 of that, then the matching element.
	 *	- if a _match element exists, try each key as a function until a match is found ('$this->', 'self::' is understood).
	 *	- if a match all wildcard (*) exists, use that.
	 *	- break.
	 * 
	 * @param $Structure array URI Structure to process.
	 * @param $Segments array[string] Array of input segments to process.
	 * @param $Last string Last accepted segment.
	 * @return array[string] Any unprocessed segments.
	 * 
	 * @note Sets @a $mFinished to TRUE when a callback is used.
	 */
	protected function _processUriStructure(&$Structure, $Segments, $Last = NULL)
	{
		//var_dump($Structure, $Segments);
		if ($this->mFinished) {
			return array();
		}
		$segments = $Segments;
		if (empty($segments)) {
			$top = '';
		} else {
			$top = $Segments[0];
			array_shift($segments);
		}
		$effective_top = $top;
		
		// Store the last value?
		if (array_key_exists('_store', $Structure)) {
			$this->mData[$Structure['_store']] = $Last;
		}
		// Call a function?
		if (array_key_exists('_call', $Structure)) {
			call_user_func_array(array(&$this, $Structure['_call']), $Segments);
			$this->mFinished = TRUE;
			return array();
		}
		// Use the default function?
		if ('' === $effective_top) {
			if (array_key_exists('', $Structure)) {
				$effective_top = $Structure[''];
			} else {
				return array();
			}
		}
		// Explicit key?
		if (substr($effective_top, 0, 1) !== '_' && array_key_exists($effective_top, $Structure)) {
			$rest_structure = $Structure[$effective_top];
			if (is_string($rest_structure)) {
				call_user_func_array(array(&$this, $rest_structure), $segments);
				$this->mFinished = TRUE;
				return array();
			} elseif (is_array($rest_structure)) {
				return $this->_processUriStructure($rest_structure, $segments, $top);
			} else {
				return $segments;
			}
		}
		// _in key?
		if (array_key_exists('_in', $Structure)) {
			foreach ($Structure['_in'] as $in_set) {
				if (array_key_exists($effective_top, $in_set)) {
					$rest_segments = $this->_processUriStructure($in_set[0], $segments, $top);
					$rest_structure = $in_set[$effective_top];
					if (is_string($rest_structure)) {
						call_user_func_array(array(&$this, $rest_structure), $rest_segments);
						$this->mFinished = TRUE;
						return array();
					} elseif (is_array($rest_structure)) {
						return $this->_processUriStructure($rest_structure, $rest_segments, $top);
					} else {
						return $rest_segments;
					}
				}
			}
		}
		// _match key?
		if (array_key_exists('_match', $Structure)) {
			foreach ($Structure['_match'] as $matcher => $substruct) {
				if ($matcher($effective_top)) {
					return $this->_processUriStructure($substruct, $segments, $top);
				}
			}
		}
		// match everything?
		if (array_key_exists('*', $Structure)) {
			if (NULL === $Structure['*']) {
				return $segments;
			} else {
				return $this->_processUriStructure($Structure['*'], $segments, $top);
			}
		}
		return $Segments;
	}
}

class Uri_tree_subcontroller extends model {}

?>
