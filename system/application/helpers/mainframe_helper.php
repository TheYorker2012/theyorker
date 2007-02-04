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
 */
function SetupMainFrame($Frame='public')
{
	static $frames = array(
		'public'  => 'frame_public',
		'student' => 'frame_public',
		'vip'     => 'frame_organisation',
		'office'  => 'frame_office',
		'editor'  => 'frame_office',
		'admin'   => 'frame_office',
	);
	
	assert('array_key_exists($Frame,$frames)');
	$frame_library = $frames[$Frame];
	
	// Load the corresponding library and create an alias called main_frame
	$CI = &get_instance();
	$CI->load->library($frame_library);
	$CI->main_frame = $CI->$frame_library;
}

?>