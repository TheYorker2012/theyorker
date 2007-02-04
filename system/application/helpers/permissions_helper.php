<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file permissions_helper.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Helper functions for checking and obtaining permissions.
 */

function login_handler($Data, $Permission)
{
	$CI = &get_instance();
	
	$data = array(
		'username' => array('name' => 'username', 'id' => 'username'),
		'password' => array('name' => 'password', 'id' => 'password'),
		'target' => $CI->uri->uri_string(),
	);
	
	$login_id = '';
	if ($Data[0] === 'office' ||
		$Data[0] === 'editor' ||
		$Data[0] === 'admin')
	{
		$login_id = 'office';
		$data['no_username'] = TRUE;
		$data['no_keep_login'] = TRUE;
	} else {
		$login_id = 'student';
	}
	$data['login_id'] = $login_id;
	
	$input_login_id = $CI->input->post('login_id');
	$successfully_logged_in = FALSE;
	if ($input_login_id === $login_id) {
		if ($login_id !== 'office') {
			$username = $CI->input->post('username');
		} else {
			$username = '';
		}
		$password = $CI->input->post('password');
		$data['initial_username'] = $username;
		try {
			if ($Data[0] === 'student') {
				$CI->user_auth->login($username, $password, false);
			} elseif ($Data[0] === 'vip') {
				//$CI->user_auth->loginOrganisation($username, $password);
				//$CI->main_frame->AddMessage('warning','VIP login system hasn\'t been implemented');
				throw new Exception('VIP login hasn\'t been implemented');
			} elseif ($Data[0] === 'office') {
				$CI->user_auth->loginOffice($password);
			} elseif ($Data[0] === 'editor') {
				$CI->user_auth->loginOffice($password);
			} elseif ($Data[0] === 'admin') {
				$CI->user_auth->loginOffice($password);
			}
			$successfully_logged_in = TRUE;
			$CI->main_frame->AddMessage('success','You have successfully logged in to your '.$login_id.' account');
			//$CI->main_frame->DeferMessages();
			unset($_POST);
			return CheckPermissions($Permission);
			//redirect('');
			//redirect($CI->uri->uri_string());
		} catch (Exception $e) {
			$CI->main_frame->AddMessage('information',$Data[1]);
			$CI->main_frame->AddMessage('error',$e->getMessage());
		}
	} else {
		$data['initial_username'] = '';
		$CI->main_frame->AddMessage('information',$Data[1]);
	}
	
	if (!$successfully_logged_in) {
		$CI->main_frame->SetTitle('Log in');
		$CI->main_frame->SetContentSimple('login/login', $data);
	}
	
	return $successfully_logged_in;
}

/// Check the access permissions.
/**
 * @param $Permission string or array of the following levels (in the order that
 *	they are to be obtained:
 *	- 'public'
 *	- 'student'
 *	- 'vip'
 *	- 'office'
 *	- 'editor'
 *	- 'admin'
 * @return bool Whether enough privilages.
 */
function CheckPermissions($Permission = 'public')
{
	$student_login_action = array(
			'handle','login_handler',
			array('student','You need to log in to your student account to access this page')
		);
	$vip_login_action = array(
			'handle','login_handler',
			array('vip','You need to log in to your VIP account to access this page')
		);
	$office_login_action = array(
			'handle','login_handler',
			array('office','You need to log in to your office account to access this page')
		);
	$editor_login_action = array(
			'handle','login_handler',
			array('editor','You need to log in to your office account to access this page')
		);
	$admin_login_action = array(
			'handle','login_handler',
			array('admin','You need to log in to your admin account to access this page')
		);
	
	$vip_login_action = TRUE;
	
	// Matrix indexed by user level, then page level, of behaviour
	// Possible values:
	//	<not set>	http error 404
	//	TRUE		allowed
	//	array		specially handled
	//	otherwise	access denied
	$access_matrix = array(
		'public'       => array(
				'public'		=> TRUE,
				'student'		=> $student_login_action,
				'office'		=> $student_login_action,
				'editor'		=> $student_login_action,
				'admin'			=> $student_login_action,
				'vip'			=> $vip_login_action,
			),
		'student'      => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'office'		=> $office_login_action,
				'editor'		=> $editor_login_action,
				'vip'			=> $vip_login_action,
				'admin'			=> $admin_login_action,
			),
		'vip' => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'vip'			=> TRUE,
			),
		'office'       => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'office'		=> TRUE,
				'editor'		=> FALSE,
			),
		'editor'       => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'office'		=> TRUE,
				'editor'		=> TRUE,
			),
		'admin'    => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'vip'			=> TRUE,
				'office'		=> TRUE,
				'editor'		=> TRUE,
				'admin'			=> TRUE,
			),
	);
	
	$CI = &get_instance();
	$CI->load->library('user_auth');
	
	$user_level = 'public';
	if ($CI->user_auth->isLoggedIn) {
		$user_level = 'student';
	}
	if (FALSE) {
		$user_level = 'vip';
	}
	if ($CI->user_auth->officeType === 'Low') {
		$user_level = 'office';
	} elseif ($CI->user_auth->officeType === 'High') {
		$user_level = 'editor';
	} elseif ($CI->user_auth->officeType === 'Admin') {
		$user_level = 'admin';
	}
	$action_levels = $access_matrix[$user_level];
	
	if (!is_array($Permission)) {
		$Permission = array($Permission);
	}
	
	
	$access_allowed = FALSE;
	
	foreach ($Permission as $permission) {
		if (!array_key_exists($permission, $action_levels)) {
			show_404();
		} else {
			$action = $action_levels[$permission];
			if (TRUE === $action) {
				$access_allowed = TRUE;
			} elseif (is_array($action)) {
				switch ($action[0]) {
					case 'handle':
						$access_allowed = $action[1]($action[2], $Permission);
						break;
						
					case 'redirect':
						if (array_key_exists(2,$action)) {
							$CI->main_frame->AddMessage($action[2], $action[3]);
						}
						$CI->main_frame->DeferMessages();
						redirect($action[1]);
						break;
				}
			} else {
				// Access denied
				$CI->main_frame->AddMessage('warning', 'You do not have '.$permission.' privilages required to use this page.');
			}
		}
		if (!$access_allowed)
			break;
	}
	
	return $access_allowed;
}

?>