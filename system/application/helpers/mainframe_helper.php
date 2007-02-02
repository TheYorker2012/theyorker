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
 *	- 'writer'
 *	- 'editor'
 *	- 'webmaster'
 * @return bool Whether enough privilages.
 */
function SetupMainFrame($Permission = 'public')
{
	static $access_matrix = array(
		'public'       => array('public'),
		'student'      => array('public', 'student'),
		'organisation' => array('public', 'student', 'organisation'),
		'writer'       => array('public', 'student',                 'writer'),
		'editor'       => array('public', 'student',                 'writer', 'editor'),
		'webmaster'    => array('public', 'student', 'organisation', 'writer', 'editor', 'webmaster'),
	);
	
	$CI = &get_instance();
	$CI->load->library('user_auth');
	
	$user_levels = array('public');
	if ($CI->user_auth->isLoggedIn) {
		$user_levels[] = 'student';
	}
	if (FALSE) {
		$user_levels[] = 'organisation';
	}
	if (FALSE) {
		$user_levels[] = 'writer';
	}
	if (FALSE) {
		$user_levels[] = 'editor';
	}
	if (FALSE) {
		$user_levels[] = 'webmaster';
	}
	$action_levels = array();
	foreach ($user_levels as $user_level) {
		$action_levels = array_merge($action_levels, $access_matrix[$user_level]);
	}
	
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
	$CI->load->library($frame_library);
	$CI->main_frame = $CI->$frame_library;
	
	if (!array_key_exists($Permission, $action_levels)) {
		$CI->main_frame->AddMessage('information', '(the mainframe_helper thinks) You do not have '.$Permission.' privilages required to use this page.');
		return TRUE;
	} else {
		return TRUE;
	}
}

?>