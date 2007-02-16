<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file permissions_helper.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Helper functions for checking and obtaining permissions.
 */

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


/// VIP URL similar to site_url.
function vip_url($Path = '', $Set = FALSE)
{
	static $base = '/viparea/';
	
	if ($Set) {
		$base = $Path;
	} else {
		return site_url($base.$Path);
	}
}

/// Get the vip organisation.
function VipOrganisation($SetOrganisation = FALSE)
{
	static $organisation = '';
	
	if (is_string($SetOrganisation)) {
		$organisation = $SetOrganisation;
	}
	return $organisation;
}


/// Check the access permissions.
/**
 * @param $Permission string or array of the following levels (in the order that
 *	they are to be obtained:
 *	- 'public'
 *	- 'student'
 *	- 'vip'
 *	- 'vip+office'
 *	- 'office'
 *	- 'editor'
 *	- 'admin'
 * @param $LoadMainFrame bool Whether to load the mainframe if permision hasn't
 *	 yet been acquired (for the login screen).
 * @return bool Whether enough privilages.
 */
function CheckPermissions($Permission = 'public', $LoadMainFrame = TRUE, $NoPost = FALSE)
{
	$CI = &get_instance();
	
	// Check if vip
	$in_viparea = 	(		($CI->uri->total_segments() >= 2)
						&&	($CI->uri->segment(1) === 'viparea'));
	$in_office_vip = (		($CI->uri->total_segments() >= 3)
						&&	($CI->uri->segment(1) === 'office')
						&&	($CI->uri->segment(2) === 'vip'));
	
	if ($in_office_vip) {
		// /office/vip/...
		// $Permission IN {office, vip+office, admin}
		$office_vip_allowed_permissions = array(
			'office'		=> 'office',
			'vip+office'	=> 'office',
			'admin'			=> 'admin',
		);
		if (!array_key_exists($Permission, $office_vip_allowed_permissions)) {
			// This page isn't even valid
			show_404();
			return FALSE;
		}
		
		$Permission = $office_vip_allowed_permissions[$Permission];
		VipOrganisation($CI->uri->segment(3));
		vip_url('office/vip/'.$CI->uri->segment(3).'/', TRUE);
		
		/// @todo check permissions to access this organisation
		
	}
	
	if ($in_viparea) {
		// /viparea/...
		// $Permission IN {vip, vip+office}
		$viparea_allowed_permissions = array(
			'vip'			=> 'vip',
			'vip+office'	=> 'vip',
		);
		if (!array_key_exists($Permission, $viparea_allowed_permissions)) {
			// This page isn't even valid
			show_404();
			return FALSE;
		}
		
		$Permission = $viparea_allowed_permissions[$Permission];
		
		if ($CI->uri->total_segments() >= 2) {
			VipOrganisation($CI->uri->segment(2));
		}
		vip_url('viparea/'.$CI->uri->segment(2).'/', TRUE);
		
		/// @todo check permissions to access this organisation
		
	}

	$CI->load->model('pages_model');

	$CI->load->library('messages');

	$student_login_action = array(
			'redirect+url','login/main',
			'post' => TRUE
		);
	$vip_login_action = array(
			'redirect+url','login/vip',
			'post' => TRUE
		);
	$office_login_action = array(
			'redirect+url','login/office',
			'post' => TRUE
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
				HtmlButtonLink(site_url('logout/vip'.$CI->uri->uri_string()),'Leave VIP Area')
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
			// Perform action
			switch ($action[0]) {
				case 'handle':
					$access_allowed = $action[1]($action[2], $Permission);
					if (array_key_exists(3,$action)) {
						$CI->messages->AddMessage($action[3], $action[4], FALSE);
					}
					break;
					
				case 'redirect+url':
					$action[1] .= $CI->uri->uri_string();
				case 'redirect':
					if (array_key_exists(2,$action)) {
						$CI->messages->AddMessage($action[2], $action[3]);
					}
					if (array_key_exists('post',$action) && $action['post']) {
						// store post data
						if (!empty($_POST)) {
							SetRedirectData($action[1], serialize($_POST));
						}
					}
					redirect($action[1]);
					return FALSE;

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
		
		if ((TRUE === $action || is_array($action)) && !$NoPost) {
			// Restore post data
			$post_data = GetRedirectData();
			if (NULL !== $post_data) {
				$post_data = @unserialize($post_data);
				if (is_array($post_data)) {
					if (!isset($_POST)) {
						$_POST = array();
					}
					foreach ($post_data as $key => $value) {
						$_POST[$key] = $value;
					}
				}
			}
		}
	}

	SetupMainFrame($Permission, FALSE);

	if (!$access_allowed && $LoadMainFrame) {
		$CI->main_frame->Load();
	}

	return $access_allowed;
}

/// Save the redirect data.
/**
 * @param $PageId string Uri to store redirect data for.
 * @param $Data any data to be retrieved after the redirect with GetRedirectData.
 */
function SetRedirectData($PageId, $Data)
{
	$PageId = '/'.$PageId;
	if (!array_key_exists('posts',$_SESSION)) {
		$_SESSION['posts'] = array();
	}
	// save the post data and the time
	if (!array_key_exists($PageId, $_SESSION['posts'])) {
		$_SESSION['posts'][$PageId] = array();
	}
	array_push($_SESSION['posts'][$PageId], array(time()+600, $Data));
}

/// Get the redirect data.
/**
 * @return Stored data or NULL on failure.
 */
function GetRedirectData()
{
	$CI = & get_instance();
	$PageId = $CI->uri->uri_string();
	static $result = NULL;
	if (NULL === $result) {
		if (array_key_exists('posts',$_SESSION)) {
			if (array_key_exists($PageId,$_SESSION['posts'])) {
				$found = FALSE;
				while (!$found && !empty($_SESSION['posts'][$PageId])) {
					$post_data = array_shift($_SESSION['posts'][$PageId]);
					// Check not expired
					if ($post_data[0] > time()) {
						$result = $post_data[1];
						$found = TRUE;
					}
				}
				if (empty($_SESSION['posts'][$PageId])) {
					unset($_SESSION['posts'][$PageId]);
				}
			}
		}
	}
	return $result;
}

/// Return an html button link
function HtmlButtonLink($Link, $Caption)
{
	return '
<form action="'.$Link.'" method="link" class="form">
	<input type="submit" class="button" value="'.$Caption.'" />
</form>';
}

/// Handles the login view.
/**
 * @param $Level string:
 *	- 'public'
 *	- 'student'
 *	- 'organisation'
 *	- 'vip'
 *	- 'office'
 *	- 'editor'
 *	- 'admin'
 * @param $RedirectDestination string URI to redirect to on success.
 * @return Whether successfully logged in yet
 *
 * @pre CheckPermissions has already been called.
 */
function LoginHandler($Level, $RedirectDestination)
{
	$CI = &get_instance();
	$CI->load->library('messages');

	$data = array(
		'target' => $CI->uri->uri_string(),
	);

	$login_id = '';
	if ($Level === 'office') {
		$page_code = 'login_office';
		$login_id = 'office';
		$success_msg = $CI->pages_model->GetPropertyText('login:success_office', TRUE);
		$data['no_keep_login'] = TRUE;

	} elseif ($Level === 'vip') {
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
	if (($CI->input->post('login_button') === 'Login') &&
		($CI->input->post('login_id') === $login_id)) {
		if ($login_id === 'student') {
			$username = $CI->input->post('username');
		} elseif ($login_id === 'vip') {
			$entity_id = $CI->input->post('username');
		}
		$password = $CI->input->post('password');
		$post_data = $CI->input->post('previous_post_data');
		if (FALSE !== $post_data) {
			$data['previous_post_data'] = $post_data;
		}
		try {
			if ($Level === 'vip') {
				// if office access say have been logged out of vip
				if ($CI->user_auth->officeType !== 'None') {
					$CI->user_auth->logoutOffice();
					$left_office_message = $CI->pages_model->GetPropertyMessage('msg_left_office_message', $page_code);
					if (FALSE !== $left_office_message) {
						$CI->messages->AddMessage(new Message($left_office_message));
					}
				}
				$CI->user_auth->loginOrganisation($password, $entity_id);
			} elseif ($Level === 'office') {
				// if vip access say have been logged out of office
				if ($CI->user_auth->organisationLogin >= 0) {
					$CI->user_auth->logoutOrganisation();
					$left_vip_message = $CI->pages_model->GetPropertyMessage('msg_left_vip_message', $page_code);
					if (FALSE !== $left_vip_message) {
						$CI->messages->AddMessage(new Message($left_vip_message));
					}
				}
				$CI->user_auth->loginOffice($password);
			} else {
				$CI->user_auth->login($username, $password, false);
			}
			
			$CI->messages->AddMessage('success',$success_msg);
			
			if (FALSE !== $post_data) {
				SetRedirectData($RedirectDestination, $post_data);
			}
			redirect($RedirectDestination);
			return TRUE;
		} catch (Exception $e) {
			$CI->messages->AddMessage('error',$e->getMessage());
		}
	} else {
		$post_data = GetRedirectData();
		if (NULL !== $post_data) {
			$data['previous_post_data'] = $post_data;
		}
		$data['initial_username'] = '';
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

	$CI->main_frame->Load();

	return FALSE;
}


?>