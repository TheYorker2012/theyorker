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
		'target' => $CI->uri->uri_string(),
	);
	
	$login_id = '';
	if ($Data[0] === 'office' ||
		$Data[0] === 'editor' ||
		$Data[0] === 'admin')
	{
		$page_code = 'login_office';
		$login_id = 'office';
		$data['no_keep_login'] = TRUE;
		$success_msg = 'you have successfully entered the office';
		
	} elseif ($Data[0] === 'vip') {
		$page_code = 'login_vip';
		$login_id = 'vip';
		$data['usernames'] = array();
		$logins = $CI->user_auth->getOrganisationLogins();
		foreach ($logins as $login) {
			$data['usernames'][$login['organisation_entity_id']] = $login['organisation_name'];
		}
		$success_msg = 'you have successfully entered the VIP area';
		
	} else {
		$page_code = 'login_public';
		$login_id = 'student';
		$data['username'] = '';
		$data['keep_login'] = '0';
		$success_msg = 'you have successfully logged in';
	}
	$data['login_id'] = $login_id;
	
	$input_login_id = $CI->input->post('login_id');
	$successfully_logged_in = FALSE;
	if ($input_login_id === $login_id) {
		if ($login_id === 'student') {
			$username = $CI->input->post('username');
		} elseif ($login_id === 'vip') {
			$entity_id = $CI->input->post('username');
		}
		$password = $CI->input->post('password');
		try {
			if ($Data[0] === 'student') {
				$CI->user_auth->login($username, $password, false);
			} elseif ($Data[0] === 'vip') {
				$CI->user_auth->loginOrganisation($password, $entity_id);
			} elseif ($Data[0] === 'office') {
				$CI->user_auth->loginOffice($password);
			} elseif ($Data[0] === 'editor') {
				$CI->user_auth->loginOffice($password);
			} elseif ($Data[0] === 'admin') {
				$CI->user_auth->loginOffice($password);
			}
			$successfully_logged_in = TRUE;
			$CI->main_frame->AddMessage('success',$success_msg);
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
		$CI->pages_model->SetPageCode($page_code);
		
		$login_message = $CI->pages_model->GetPropertyText('login_message');
		if (!empty($login_message)) {
			$data['login_message'] = $login_message;
		}
		
		$section_title = $CI->pages_model->GetPropertyText('section_title');
		if (!empty($section_title)) {
			$data['title'] = $section_title;
		}
		
		/// @todo Move cunning array page properties into pages model.
		$data['rightbar'] = array();
		$index_counter = 0;
		while (TRUE) {
			$title = $CI->pages_model->GetPropertyText(
					'rightbar['.$index_counter.'].title');
			if (empty($title)) {
				break;
			}
			$text = $CI->pages_model->GetPropertyWikitext(
					'rightbar['.$index_counter.'].text');
			
			$data['rightbar'][] = array(
				'title' => $title,
				'text' => $text,
			);
			
			++$index_counter;
		}
		
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
			array('student','Please log in to your account')
		);
	$vip_login_action = array(
			'handle','login_handler',
			array('vip','Please log in to your VIP account')
		);
	$office_login_action = array(
			'handle','login_handler',
			array('office','Please enter the office')
		);
	$editor_login_action = array(
			'handle','login_handler',
			array('editor','Please enter the office')
		);
	$admin_login_action = array(
			'handle','login_handler',
			array('admin','Please enter the office')
		);
	
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
				'vip'			=> $student_login_action,
				'office'		=> $student_login_action,
				'editor'		=> $student_login_action,
				'admin'			=> $student_login_action,
			),
		'student'      => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'vip'			=> $vip_login_action,
				'office'		=> $office_login_action,
				'editor'		=> $editor_login_action,
				'admin'			=> $admin_login_action,
			),
		'vip' => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'vip'			=> TRUE,
			),
		'office'       => array(
				'public'		=> TRUE,
				'vip'			=> $vip_login_action,
				'student'		=> TRUE,
				'office'		=> TRUE,
				'editor'		=> FALSE,
			),
		'editor'       => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'vip'			=> $vip_login_action,
				'office'		=> TRUE,
				'editor'		=> TRUE,
			),
		'admin'    => array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'vip'			=> $vip_login_action,
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
	if ($CI->user_auth->organisationLogin >= 0) {
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