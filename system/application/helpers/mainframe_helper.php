<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file mainframe_helper.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Simple helper functions for choosing and setting up the main frame.
 */

/// Set up the main frame.
/**
 * @param $Permission string levels:
 *	- 'public'
 *	- 'student'
 *	- 'organisation'
 *	- 'office'
 *	- 'webmaster'
 */
function SetupMainFrame($Permission = 'public')
{
	// Choose a frame to use
	switch($Permission) {
		case 'public':
			$frame_library = 'frame_public';
			break;
			
		case 'student':
			$frame_library = 'frame_public';
			break;
			
		case 'organisation':
			$frame_library = 'frame_organisation';
			break;
			
		case 'office':
			$frame_library = 'frame_office';
			break;
			
		case 'webmaster':
			$frame_library = 'frame_public';
			break;
			
		default:
			throw Exception('Unknown permission type');
	}
	
	// Load the corresponding library and create an alias called main_frame
	$CI = &get_instance();
	$CI->load->library($frame_library);
	$CI->main_frame = $CI->$frame_library;
}

?>