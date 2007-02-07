<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file mainframe_helper.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Simple helper functions for choosing and setting up the main frame.
 */


/// Set up the main frame.
/**
 * @param $Frame string levels:
 *	- 'public'
 *	- 'student'
 *	- 'vip'
 *	- 'office'
 *	- 'editor'
 *	- 'admin'
 * @param $Override bool Override any previous calls to SetupMainFrame.
 */
function SetupMainFrame($Frame='public', $Override=TRUE)
{
	// User level doesn't matter
	//$user_level = GetUserLevel();
	
	static $frames = array(
		'public'  => 'frame_public',
		'student' => 'frame_public',
		'vip'     => 'frame_organisation',
		'organisation' => 'frame_organisation',
		'office'  => 'frame_office',
		'editor'  => 'frame_office',
		'admin'   => 'frame_office',
	);
	
	assert('array_key_exists($Frame,$frames)');
	$frame_library = $frames[$Frame];
	$CI = &get_instance();
	if (!isset($CI->$frame_library)) {
		if (!isset($CI->main_frame) || $Override) {
			// Load the corresponding library and create an alias called main_frame
			$CI->load->library($frame_library);
			$CI->main_frame = $CI->$frame_library;
		}
	}
}

?>