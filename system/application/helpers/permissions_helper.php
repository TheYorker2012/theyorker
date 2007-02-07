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
	$CI->load->library('messages');
	
	$data = array(
		'target' => $CI->uri->uri_string(),
	);
	
	$login_id = '';
	if ($Data[0] === 'office') {
		$page_code = 'login_office';
		$login_id = 'office';
		$success_msg = $CI->pages_model->GetPropertyText('login:success_office', TRUE);
		$data['no_keep_login'] = TRUE;
		
	} elseif ($Data[0] === 'vip') {
		$page_code = 'login_vip';
		$login_id = 'vip';
		$success_msg = $CI->pages_model->GetPropertyText('login:success_vip', TRUE);
		$data['usernames'] = array();
		$logins = $CI->user_auth->getOrganisationLogins();
		foreach ($logins as $login) {
			$data['usernames'][$login['organisation_entity_id']] = $login['organisation_name'];
		}
		
	} else {
		$page_code = 'login_public';
		$login_id = 'student';
		$success_msg = $CI->pages_model->GetPropertyText('login:success_public', TRUE);
		$data['username'] = '';
		$data['keep_login'] = '0';
	}
	$data['login_id'] = $login_id;
	$successfully_logged_in = FALSE;
	if (($CI->input->post('login_button') === 'Login') &&
		($CI->input->post('login_id') === $login_id)) {
		if ($login_id === 'student') {
			$username = $CI->input->post('username');
		} elseif ($login_id === 'vip') {
			$entity_id = $CI->input->post('username');
		}
		$password = $CI->input->post('password');
		try {
			if ($Data[0] === 'vip') {
				// if office access say have been logged out of vip
				if ($CI->user_auth->officeType !== 'None') {
					$CI->user_auth->logoutOffice();
					$CI->messages->AddMessage('information','You have been logged out of the office');
				}
				$CI->user_auth->loginOrganisation($password, $entity_id);
			} elseif ($Data[0] === 'office') {
				// if vip access say have been logged out of office
				if ($CI->user_auth->organisationLogin >= 0) {
					$CI->user_auth->logoutOrganisation();
					$CI->messages->AddMessage('information','You have been logged out of the VIP area');
				}
				$CI->user_auth->loginOffice($password);
			} else {
				$CI->user_auth->login($username, $password, false);
			}
			$successfully_logged_in = TRUE;
			
			$CI->messages->AddMessage('success',$success_msg);
			
			foreach ($_POST as $key => $value) {
				unset($_POST[$key]);
			}
			
			// Store post data
			if (array_key_exists('posts',$_SESSION)) {
				if (array_key_exists($CI->uri->uri_string(),$_SESSION['posts'])) {
					foreach ($_SESSION['posts'][$CI->uri->uri_string()] as $key => $value) {
						$_POST[$key] = $value;
					}
					unset($_SESSION['posts'][$CI->uri->uri_string()]);
				}
			}
			return CheckPermissions($Permission);
			//redirect($CI->uri->uri_string());
		} catch (Exception $e) {
			$CI->messages->AddMessage('error',$e->getMessage());
		}
	} else {
		$data['initial_username'] = '';
		
		// Store post data
		if (!empty($_POST)) {
			if (!array_key_exists('posts',$_SESSION)) {
				$_SESSION['posts'] = array();
			}
			$_SESSION['posts'][$CI->uri->uri_string()] = $_POST;
		}
	}
	
	// Get various page properties used for displaying the login screen
	$CI->pages_model->SetPageCode($page_code);
	
	$permission_message = $CI->pages_model->GetPropertyMessage('msg_permission_message');
	if (FALSE !== $permission_message) {
		$CI->messages->AddMessage(new Message($permission_message), FALSE);
	}
	
	// Title of login section of page
	$section_title = $CI->pages_model->GetPropertyText('section_title');
	if (!empty($section_title)) {
		$data['title'] = $section_title;
	}
	
	// Main login message
	$login_message = $CI->pages_model->GetPropertyText('login_message');
	if (!empty($login_message)) {
		$data['login_message'] = $login_message;
	}
	
	// Items in the right bar
	$data['rightbar'] = $CI->pages_model->GetPropertyArray('rightbar', array(
		// First index is [int]
		array('pre' => '[', 'post' => ']', 'type' => 'int'),
		// Second index is [string]
		array('pre' => '.', 'type' => 'enum',
			'enum' => array(
				array('title',	'text'),
				array('text',	'wikitext'),
			),
		),
	));
	
	SetupMainFrame(GetUserLevel(), FALSE);
	
	$CI->main_frame->SetContentSimple('login/login', $data);
	
	return $successfully_logged_in;
}

/// Get the user level as a string
function GetUserLevel()
{
	$CI = &get_instance();
	$CI->load->library('user_auth');
	
	$user_level = 'public';
	if ($CI->user_auth->isLoggedIn) {
		$user_level = 'student';
	}
	if ($CI->user_auth->organisationLogin >= 0) {
		if ($CI->user_auth->isUser) {
			$user_level = 'vip';
		} else {
			$user_level = 'organisation';
		}
	}
	if ($CI->user_auth->officeType === 'Low') {
		$user_level = 'office';
	} elseif ($CI->user_auth->officeType === 'High') {
		$user_level = 'editor';
	} elseif ($CI->user_auth->officeType === 'Admin') {
		$user_level = 'admin';
	}
	return $user_level;
}

/// Return an html button link
function HtmlButtonLink($Link, $Caption)
{
	return '
<form action="'.$Link.'" method="link" class="form">
	<input type="submit" class="button" value="'.$Caption.'" />
</form>';
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
 * @param $LoadMainFrame bool Whether to load the mainframe if permision hasn't
 *	 yet been acquired (for the login screen).
 * @return bool Whether enough privilages.
 */
function CheckPermissions($Permission = 'public', $LoadMainFrame = TRUE)
{
	$CI = &get_instance();
	
	$CI->load->model('pages_model');
	
	$student_login_action = array(
			'handle','login_handler',
			array('public')
		);
	$vip_login_action = array(
			'handle','login_handler',
			array('vip')
		);
	$office_login_action = array(
			'handle','login_handler',
			array('office')
		);
	
	
	// Matrix indexed by user level, then page level, of behaviour
	// Possible values:
	//	<not set>	http error 404
	//	TRUE		allowed
	//	array		specially handled
	//	otherwise	access denied
	$user_level = GetUserLevel();
	if ($user_level === 'public') {
		$action_levels = array(
				'public'		=> TRUE,
				'student'		=> $student_login_action,
				'vip'			=> $student_login_action,
				'office'		=> $student_login_action,
				'editor'		=> $student_login_action,
				'admin'			=> $student_login_action,
			);
	} elseif ($user_level === 'student') {
		$action_levels = array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'vip'			=> $vip_login_action,
				'office'		=> $office_login_action,
				'editor'		=> $office_login_action,
				'admin'			=> $office_login_action,
			);
	} elseif ($user_level === 'organisation') {
		// Logged in from public as organisation
		$action_levels = array(
				'public'		=> TRUE,
				'student'		=> TRUE,
				'vip'			=> TRUE,
				'office'		=> FALSE,
				'editor'		=> FALSE,
				'admin'			=> FALSE,
			);
	} elseif ($user_level === 'vip') {
		// Logged in as student and in VIP area
		$vip_door_open_action = array(
				'message','warning',
				HtmlButtonLink(site_url('logout/viparea'.$CI->uri->uri_string()),'Leave VIP Area')
				. $CI->pages_model->GetPropertyText('login:warn_open_vip', TRUE),
				TRUE
			);
		$action_levels = array(
				'public'		=> $vip_door_open_action,
				'student'		=> $vip_door_open_action,
				'vip'			=> TRUE,
				'office'		=> $office_login_action,
				'editor'		=> $office_login_action,
				'admin'			=> $office_login_action,
			);
	} else {
		$office_door_open_action = array(
				'message','warning',
				HtmlButtonLink(site_url('logout/office'.$CI->uri->uri_string()),'Leave Office')
				. $CI->pages_model->GetPropertyText('login:warn_open_office', TRUE),
				TRUE
			);
		$admin_door_open_action = $office_door_open_action;
		if ($user_level === 'office') {
			$action_levels = array(
					'public'		=> $office_door_open_action,
					'student'		=> $office_door_open_action,
					'vip'			=> $vip_login_action,
					'office'		=> TRUE,
					'editor'		=> FALSE,
					'admin'			=> FALSE,
				);
		} elseif ($user_level === 'editor') {
			$action_levels = array(
					'public'		=> $office_door_open_action,
					'student'		=> $office_door_open_action,
					'vip'			=> $vip_login_action,
					'office'		=> TRUE,
					'editor'		=> TRUE,
					'admin'			=> FALSE,
				);
		} elseif ($user_level === 'admin') {
			$action_levels = array(
					'public'		=> $admin_door_open_action,
					'student'		=> $admin_door_open_action,
					'vip'			=> $vip_login_action,
					'office'		=> TRUE,
					'editor'		=> TRUE,
					'admin'			=> TRUE,
				);
		}
	}
	
	$access_allowed = FALSE;
	
	if (!array_key_exists($Permission, $action_levels)) {
		return show_404();
	} else {
		
		$action = $action_levels[$Permission];
		if (TRUE === $action) {
			$access_allowed = TRUE;
		} elseif (is_array($action)) {
			switch ($action[0]) {
				case 'handle':
					$access_allowed = $action[1]($action[2], $Permission);
					if (array_key_exists(3,$action)) {
						$CI->messages->AddMessage($action[3], $action[4], FALSE);
					}
					break;
					
				case 'redirect':
					if (array_key_exists(2,$action)) {
						$CI->messages->AddMessage($action[2], $action[3]);
					}
					redirect($action[1]);
					break;
					
				case 'message':
					$CI->messages->AddMessage($action[1], $action[2], FALSE);
					$access_allowed = $action[3];
					break;
					
				default:
					break;
			}
		} else {
			// Access denied
			$CI->messages->AddMessage('warning', 'You do not have '.$Permission.' privilages required!');
			//redirect('');
		}
	}
	
	SetupMainFrame($Permission, FALSE);
	
	if (!$access_allowed && $LoadMainFrame) {
		$CI->main_frame->Load();
	}
	
	return $access_allowed;
}

?>