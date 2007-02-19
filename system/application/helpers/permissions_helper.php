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

/// Get the vip mode that the user is in.
/**
 * @return string Mode string:
 *	- 'none' Not in vip mode
 *	- 'office' VIP mode through office
 *	- 'viparea' VIP mode through viparea
 */
function VipMode($SetMode = FALSE)
{
	static $vip_mode = 'none';
	
	if (is_string($SetMode)) {
		$vip_mode = $SetMode;
	}
	
	return $vip_mode;
}


/// Check the access permissions.
/**
 * @param $Permission string or array of the following levels (in the order that
 *	they are to be obtained:
 *	- 'public' - anyone
 *	- 'student' - must be logged on
 *	- 'vip' - must be logged on as a vip
 *	- 'vip+pr' - must be logged on as a vip or a pr rep
 *	- 'office' - must be in the office
 *	- 'pr' - must be in the office as a pr rep
 *	- 'editor' - must be in the office as an editor
 *	- 'admin' - must be in the office as an administrator
 * @param $LoadMainFrame bool Whether to load the mainframe if permision hasn't
 *	 yet been acquired (for the login screen).
 * @return bool Whether enough privilages.
 */
function CheckPermissions($Permission = 'public', $LoadMainFrame = TRUE, $NoPost = FALSE)
{
	$CI = &get_instance();
	$CI->load->library('messages');
	$CI->load->model('pages_model');

	$user_level = GetUserLevel(); // loads the user_auth library
	
	// Check whether the page is accessed in a special way
	$thru_viparea		=	(	($CI->uri->total_segments() >= 1)
							&&	($CI->uri->segment(1) === 'viparea'));
	$thru_office_vip	= 	(	($CI->uri->total_segments() >= 3)
							&&	($CI->uri->segment(1) === 'office')
							&&	($CI->uri->segment(2) === 'vip'));
	
	$organisation_specified = FALSE;
	if ($thru_viparea) {
		if ($CI->uri->total_segments() > 1) {
			$organisation_shortname = $CI->uri->segment(2);
			$organisation_specified = TRUE;
		} else {
			$organisation_shortname = $CI->user_auth->organisationShortName;
		}
		vip_url('viparea/'.$organisation_shortname.'/', TRUE);
	} elseif ($thru_office_vip) {
		$organisation_shortname = $CI->uri->segment(3);
		$organisation_specified = TRUE;
		vip_url('office/vip/'.$organisation_shortname.'/', TRUE);
	} else {
		$organisation_shortname = '';
	}
	VipOrganisation($organisation_shortname);
	
	
	// Login actions for student/vip/office logins
	$student_login_action = array(
		'redirect+url','login/main',
		'post' => TRUE
	);
	if ($organisation_specified) {
		$vip_login_action = array(
			'redirect+url','login/vipswitch/'.$organisation_shortname,
			'post' => TRUE
		);
	} else {
		$vip_login_action = array(
			'redirect+url','login/vip',
			'post' => TRUE
		);
	}
	$office_login_action = array(
		'redirect+url','login/office',
		'post' => TRUE
	);
	
	// If vip+pr, use URI to decide which
	if ($Permission === 'vip+pr') {
		$Permission =	($thru_viparea		? 'vip'	:
						($thru_office_vip	? 'pr'	: ''));
	}
	// Ensure that:
	//	$thru_office_vip => 'pr'
	//	$thru_viparea => 'vip'
	elseif (	($thru_office_vip	&& $Permission !== 'pr')
			||	($thru_viparea		&& $Permission !== 'vip')) {
		$Permission = '';
	}
	
	// Matrix indexed by user level, then page level, of behaviour
	// Possible values:
	//	NULL/notset	http error 404
	//	TRUE		allowed
	//	array		specially handled
	//	otherwise	access denied
	if ($user_level === 'public') {
		$action_levels = array(
			'public'	=> TRUE,
			'student'	=> $student_login_action,
			'vip'		=> $student_login_action,
			'office'	=> $student_login_action,
			'pr'		=> $student_login_action,
			'editor'	=> $student_login_action,
			'admin'		=> $student_login_action,
		);
	} elseif ($user_level === 'student') {
		$action_levels = array(
			'public'	=> TRUE,
			'student'	=> TRUE,
			'vip'		=> $vip_login_action,
			'office'	=> $office_login_action,
			'pr'		=> $office_login_action,
			'editor'	=> $office_login_action,
			'admin'		=> $office_login_action,
		);
	} elseif ($user_level === 'organisation') {
		// Logged in from public as organisation
		$action_levels = array(
			'public'	=> TRUE,
			'student'	=> TRUE,
			'vip'		=> ($CI->user_auth->organisationShortName == $organisation_shortname),
			'office'	=> FALSE,
			'pr'		=> FALSE,
			'editor'	=> FALSE,
			'admin'		=> FALSE,
		);
	} elseif ($user_level === 'vip') {
		// Logged in as student and in VIP area
		$vip_door_open_action = array(
			'message','warning',
			HtmlButtonLink(site_url('logout/vip'.$CI->uri->uri_string()),'Leave VIP Area')
			. $CI->pages_model->GetPropertyText('login:warn_open_vip', TRUE),
			TRUE
		);
		
		if ($CI->user_auth->organisationShortName == $organisation_shortname) {
			$vip_accessible = TRUE;
		} else {
			// check permissions to access this organisation
			$vip_organisations = $CI->user_auth->getOrganisationLogins();
			foreach ($vip_organisations as $organisation) {
				if ($organisation['organisation_directory_entry_name'] == $organisation_shortname) {
					$vip_accessible = $vip_login_action;
					break;
				}
			}
			if (!isset($vip_accessible)) {
				$vip_accessible = FALSE;
			}
		}
		
		$action_levels = array(
			'public'	=> $vip_door_open_action,
			'student'	=> $vip_door_open_action,
			'vip'		=> $vip_accessible,
			'office'	=> $office_login_action,
			'pr'		=> $office_login_action,
			'editor'	=> $office_login_action,
			'admin'		=> $office_login_action,
		);
	} else {
		// Office
		// Door left open actions
		$office_door_open_action = array(
			'message','warning',
			HtmlButtonLink(site_url('logout/office'.$CI->uri->uri_string()),'Leave Office')
			. $CI->pages_model->GetPropertyText('login:warn_open_office', TRUE),
			TRUE
		);
		$admin_door_open_action = $office_door_open_action;
		
		// Change an office user to pr if they rep for the organisation
		if ($user_level === 'office' && $Permission === 'pr') {
			// Check user is PR Rep for this organisation
			$rep_organisations = $CI->user_auth->getPrRepOrganisations();
			foreach ($rep_organisations as $organisation) {
				if ($organisation['organisation_directory_entry_name']
						== $organisation_shortname) {
					// Yes, match, PR Rep, set stuff and change user level to PR
					$user_level = 'pr';
					break;
				}
			}
		}
		
		// Refine further
		if ($user_level === 'office') {
			$action_levels = array(
				'public'		=> $office_door_open_action,
				'student'		=> $office_door_open_action,
				'vip'			=> $vip_login_action,
				'office'		=> TRUE,
				'pr'			=> FALSE,
				'editor'		=> FALSE,
				'admin'			=> FALSE,
			);
		} elseif ($user_level === 'pr') {
			$action_levels = array(
				'public'		=> $office_door_open_action,
				'student'		=> $office_door_open_action,
				'vip'			=> $vip_login_action,
				'office'		=> TRUE,
				'pr'			=> TRUE,
				'editor'		=> FALSE,
				'admin'			=> FALSE,
			);
		} elseif ($user_level === 'editor') {
			$action_levels = array(
				'public'		=> $office_door_open_action,
				'student'		=> $office_door_open_action,
				'vip'			=> $vip_login_action,
				'office'		=> TRUE,
				'pr'			=> TRUE,
				'editor'		=> TRUE,
				'admin'			=> FALSE,
			);
		} elseif ($user_level === 'admin') {
			$action_levels = array(
				'public'		=> $admin_door_open_action,
				'student'		=> $admin_door_open_action,
				'vip'			=> $vip_login_action,
				'office'		=> TRUE,
				'pr'			=> TRUE,
				'editor'		=> TRUE,
				'admin'			=> TRUE,
			);
		}
	}

	$access_allowed = FALSE;

	// No permission set or NULL indicates page doesn't exist at this URI
	if (!array_key_exists($Permission, $action_levels)
			|| NULL === $action_levels[$Permission]) {
		return show_404();
	} else {
		
		$action = $action_levels[$Permission];
		// True is allow
		if (TRUE === $action) {
			$access_allowed = TRUE;
		} elseif (is_array($action)) {
			// Array is special decider
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
			// Anything else is disallow
			$CI->messages->AddMessage('warning', 'You do not have '.$Permission.' privilages required!');
			//redirect('');
		}
		
		// Restore post data
		if ((TRUE === $action || is_array($action)) && !$NoPost) {
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
 *	- 'pr'
 *	- 'editor'
 *	- 'admin'
 * @param $RedirectDestination string URI to redirect to on success.
 * @param $Organisation string Organisation codename to force.
 * @return Whether successfully logged in yet
 *
 * @pre CheckPermissions has already been called.
 */
function LoginHandler($Level, $RedirectDestination, $Organisation = FALSE)
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
		if (is_string($Organisation)) {
			// Default organisation is $Organisation
			foreach ($logins as $login) {
				$data['usernames'][$login['organisation_entity_id']] = $login['organisation_name'];
				if ($login['organisation_directory_entry_name'] === $Organisation) {
					$data['default_username'] = $login['organisation_entity_id'];
				}
			}
		} else {
			// Don't specify a default
			foreach ($logins as $login) {
				$data['usernames'][$login['organisation_entity_id']] = $login['organisation_name'];
			}
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