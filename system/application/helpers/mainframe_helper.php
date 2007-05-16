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
 *	- 'pr'
 *	- 'editor'
 *	- 'admin'
 * @param $Override bool Override any previous calls to SetupMainFrame.
 */
function SetupMainFrame($Frame='public', $Override=TRUE)
{
	// User level doesn't matter
	//$user_level = GetUserLevel();
	
	static $frames = array(
		'public'		=> 'frame_public',
		'student'		=> 'frame_public',
		'vip'			=> 'frame_vip',
		'organisation'	=> 'frame_vip',
		'office'		=> 'frame_office',
		'pr'			=> 'frame_office',
		'editor'		=> 'frame_office',
		'manage'		=> 'frame_office',
		'admin'			=> 'frame_office',
	);
	
	assert('array_key_exists($Frame,$frames)');
	$frame_library = $frames[$Frame];
	$CI = &get_instance();
	if (!isset($CI->$frame_library)) {
		if (!isset($CI->main_frame) || $Override) {
			// Load the corresponding library and create an alias called main_frame
			// Could also be done by adding to loader->_ci_varmap but this isn't
			// very flexible as it depends on the internals of code igniter.
			//  $CI->load->_ci_varmap[$frame_library] = 'main_frame';
			$CI->load->library($frame_library);
			$CI->main_frame = &$CI->$frame_library;
			
			$CI->main_frame->SetData('toplinks',
					GenerateToplinks($Frame)
				);
		}
	}
}

/// Generate array of top links to go in the main frame.
/**
 * @param $Permission string Permission level of page
 * @pre user_auth library loaded.
 */
function GenerateToplinks($Permission)
{
	$CI = &get_instance();
	$UserLevel = GetUserLevel();

	$top_links = array();
	
	$log_out = array('log out', site_url('logout/main'.$CI->uri->uri_string()));
	$username = $CI->user_auth->username;
	$enter_office = array('enter office',site_url('office'));
	$go_office = array('office',site_url('office'));
	$enter_vip = array('enter VIP area',site_url('viparea'));
	$go_vip = array('VIP area',site_url('viparea'));
	
	switch ($UserLevel) {
		case 'public':
			if ($CI->uri->segment(1) !== 'login') {
				//$top_links[] = array('log in',  site_url('login/main'.$CI->uri->uri_string()));
			}
			//$top_links[] = array('register',site_url('register'));
			break;
		
		case 'student':
			$top_links[] = 'logged in as ' . $username;
			if ($CI->user_auth->officeLogin) {
				$top_links[] = $enter_office;
			}
			if ($CI->user_auth->officeLogin) {
				$top_links[] = $enter_vip;
			}
			$top_links[] = $log_out;
			break;
		
		case 'organisation':
		case 'vip':
			if ($Permission === 'public' || $Permission === 'student') {
				$top_links[] = 'logged in as ' . $username;
				$top_links[] = $go_vip;
				if ($UserLevel === 'vip') {
					$top_links[] = array('leave VIP area',
							site_url('logout/vip'.$CI->uri->uri_string()));
				}
			} elseif ($Permission === 'vip') {
				$top_links[] = 'in VIP area of ' . VipOrganisationName(TRUE).' as ' . $username;
				if ($UserLevel === 'vip') {
					$top_links[] = array('leave VIP area',
							site_url('logout/vip'));
				}
			}
			$top_links[] = $log_out;
			break;
		
		case 'office':
		case 'editor':
		case 'manage':
		case 'admin':
			if ($Permission === 'public' || $Permission === 'student') {
				$top_links[] = 'logged in as ' . $username;
				$top_links[] = $go_office;
				$top_links[] = array('leave office',
						site_url('logout/office'.$CI->uri->uri_string()));
			} elseif (	$Permission === 'office' ||
						$Permission === 'editor' ||
						$Permission === 'admin') {
				$top_links[] = 'in office as ' . $username;
				$top_links[] = array('leave office',
						site_url('logout/office'));
			} elseif (	$Permission === 'pr') {
				$top_links[] = 'in PR area of ' . VipOrganisationName(TRUE).' as ' . $username;
				$top_links[] = array('office',
						site_url('office'));
				$top_links[] = array('leave office',
						site_url('logout/office'));
			} elseif (	$Permission === 'manage') {
				$top_links[] = 'in management area of ' . VipOrganisationName(TRUE).' as ' . $username;
				$top_links[] = array('office',
						site_url('office'));
				$top_links[] = array('leave office',
						site_url('logout/office'));
			}
			$top_links[] = $log_out;
			break;
	}
	
	return $top_links;
/*
	office | editor | admin
		[public | student]
			>enter office
			if (office)
				!you're still in office
		'in office as %%username%%'
		>leave office
*/
}

?>
