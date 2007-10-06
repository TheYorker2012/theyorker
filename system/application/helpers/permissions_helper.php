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

/// Find whether the set of permissions for a level is a subset of another.
/**
 * This is analagous to @a $Subset <= @a $Superset.
 * @param $Subset string Permission level to search for.
 * @param $Superset string Permission level to search in.
 * @return bool Whether all permissions of @a $Subset are permissions of
 *	@a $Superset.
 */
function PermissionsSubset($Subset, $Superset)
{
	// Tree of which subsets of each permission level.
	static $permission_subsets = array(
		'public'		=> array(),
		'student'		=> array('public'	=> TRUE),
		'organisation'	=> array('student'	=> TRUE),
		'vip'			=> array('student'	=> TRUE),
		'office'		=> array('student'	=> TRUE),
		'pr'			=> array('office'	=> TRUE),
		'editor'		=> array('pr'		=> TRUE),
		'admin'			=> array('editor'	=> TRUE),
	);

	// Unknown superset, assume its empty, always fail.
	if (!array_key_exists($Superset, $permission_subsets)) {
		return FALSE;
	}
	// If the superset IS the subset allow.
	if ($Superset === $Subset) {
		return TRUE;
	}
	// If the subset is an explicit subset identifier of superset use it directly.
	if (array_key_exists($Subset, $permission_subsets[$Superset])) {
		return $permission_subsets[$Superset][$Subset];
	}
	// Go through the supersets subsets in a depth first manner to find subset.
	foreach ($permission_subsets[$Superset] as $superset_subset=>$enable) {
		$subset_found = PermissionsSubset($Subset, $superset_subset);
		if ($subset_found) {
			// $Subset is a subset of $Superset
			return $enable;
		}
		// Negative: try any other parent permissions before giving up.
	}
	// All explicit subsets checked. Must not be a subset.
	return FALSE;
}

/// Get the site path of the home page for the user.
function GetDefaultHomepage()
{
	switch (GetUserLevel()) {
		case 'student':
			return '';
			break;
		case 'vip':
			return '/viparea';
			break;
		case 'organisation':
			return '/viparea';
			break;
		case 'office':
			return '/office';
			break;
		case 'pr':
			return '/office';
			break;
		case 'editor':
			return '/office';
			break;
		case 'admin':
			return '/admin';
			break;
		default:
			return '';
			break;
	}
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
function VipOrganisation($TopOrganisation = FALSE, $SetOrganisation = FALSE)
{
	static $organisation = array('','');

	if (is_string($SetOrganisation)) {
		$organisation[$TopOrganisation?1:0] = $SetOrganisation;
	}
	return $organisation[$TopOrganisation?1:0];
}

/// Get the vip organisation name.
function VipOrganisationName($TopOrganisation = FALSE, $SetOrganisation = FALSE)
{
	static $organisation = array('','');

	if (is_string($SetOrganisation)) {
		$organisation[$TopOrganisation?1:0] = $SetOrganisation;
	}
	return $organisation[$TopOrganisation?1:0];
}

/// Get the vip organisation id.
function VipOrganisationId($TopOrganisation = FALSE, $SetOrganisation = FALSE)
{
	static $organisation_id = array(FALSE, FALSE);

	if (FALSE !== $SetOrganisation[$TopOrganisation?1:0] && is_numeric($SetOrganisation)) {
		$organisation_id[$TopOrganisation?1:0] = (int)$SetOrganisation;
	}
	return $organisation_id[$TopOrganisation?1:0];
}

/// Get the vip mode that the user is in.
/**
 * @return string Mode string:
 *	- 'none' Not in vip mode
 *	- 'office' VIP mode through office
 *	- 'viparea' VIP mode through viparea
 *	- 'manage' VIP mode through office/manage
 */
function VipMode($SetMode = FALSE)
{
	static $vip_mode = 'none';

	if (is_string($SetMode)) {
		$vip_mode = $SetMode;
	}

	return $vip_mode;
}

/// Get the vip/pr user level.
/**
 * @param $Permission Permission to retrieve ('read','rep','write').
 * @return bool Whether the user can access that part of PR/VIP.
 */
function VipLevel($Permission, $Set = FALSE)
{
	static $pr_levels = array(
		'none'  => 0,
		'read'  => 1,
		'rep'   => 2,
		'write' => 3,
	);
	static $pr_level = 0;

	if ($Set) {
		$pr_level = (int)$pr_levels[$Permission];
	}

	return $pr_level >= $pr_levels[$Permission];
}

/// Get the number of segments used up until the controller
function VipSegments($Set = NULL)
{
	static $vip_segments = 0;

	if (NULL !== $Set) {
		$vip_segments = $Set;
	}

	return $vip_segments;
}

/// Specify or get the output formats that can be produced by the controller.
/**
 * @param $Modes array[string],string,NULL = NULL Each element is a output mode identifier:
 *	- 'xhtml' - Standard XHTML
 *	- 'fbml' - Facebook markup language for facebook apps
 *
 * @param * string Alternatively a set of string can be provided as arguments directly.
 */
function OutputModes($Modes = NULL)
{
	static $output_modes = array('xhtml');
	if (NULL !== $Modes) {
		if (is_array($Modes)) {
			$output_modes = $Modes;
		} else {
			$output_modes = func_get_args();
		}
	}
	return $output_modes;
}

/// Specify or get the current output mode.
/**
 * @param $Set string,NULL = NULL Output mode identifier.
 */
function OutputMode($Set = NULL)
{
	static $output_mode = 'xhtml';
	if (NULL !== $Set) {
		$output_mode = $Set;
	}
	return $output_mode;
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
	// Start a session
	$CI = &get_instance();

	// Initialisation stuff
	$CI->load->library('messages');
	$CI->load->model('user_auth');
	$CI->load->model('pages_model');
	
	// Decide on output format
	if (array_key_exists('fb_sig', $_POST)) {
		/// @todo AUTHENTICATE FACEBOOK
		OutputMode('fbml');
		global $_SESSION;
		$_SESSION = array();
	} else {
		OutputMode('xhtml');
	}
	// If the output mode is not supported, show a 404
	if (!in_array(OutputMode(), OutputModes())) {
		show_404();
	}

	// Translate some auxilliary permissions
	$auxilliary_permissions = array(
		'moderator' => 'editor',
	);
	if (array_key_exists($Permission, $auxilliary_permissions)) {
		$Permission = $auxilliary_permissions[$Permission];
	}

	$user_level = GetUserLevel();

	// URL analysis regarding vip area
	$thru_viparea		=	(	($CI->uri->total_segments() >= 1)
							&&	($CI->uri->segment(1) === 'viparea'));
	$thru_office_pr		= 	(	($CI->uri->total_segments() >= 3)
							&&	($CI->uri->segment(1) === 'office')
							&&	($CI->uri->segment(2) === 'pr')
							&&	($CI->uri->segment(3) === 'org'));
	$thru_office_manage	=	(	($CI->uri->total_segments() >= 2)
							&&	($CI->uri->segment(1) === 'office')
							&&	($CI->uri->segment(2) === 'manage'));
	$company_short_name = $CI->config->Item('company_organisation_id');
	$organisation_specified = FALSE;
	if ($thru_viparea) {
		if ($CI->uri->total_segments() > 1) {
			$organisation_shortname = $CI->uri->segment(2);
			$organisation_specified = TRUE;
			VipSegments(2);
		} else {
			$organisation_shortname = $CI->user_auth->organisationShortName;
		}
		// don't allow access to vip area of the company, only through office/manage
		if ($organisation_shortname === $company_short_name) {
			$organisation_shortname = '';
			$CI->user_auth->logoutOrganisation();
			redirect('');
		}
		vip_url('viparea/'.$organisation_shortname.'/', TRUE);
	} elseif ($thru_office_pr) {
		$organisation_shortname = $CI->uri->segment(4);
		$organisation_specified = TRUE;
		VipSegments(4);
		vip_url('office/pr/org/'.$organisation_shortname.'/', TRUE);
	} elseif ($thru_office_manage) {
		$organisation_shortname = $company_short_name;
		$organisation_specified = TRUE;
		VipSegments(2);
		vip_url('office/manage/', TRUE);
	} else {
		$organisation_shortname = '';
	}
	VipOrganisation(FALSE, $organisation_shortname);
	VipOrganisation(TRUE, $CI->user_auth->organisationShortName);

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
		$Permission =	($thru_viparea			? 'vip'	:
						($thru_office_pr		? 'pr'	:
						($thru_office_manage	? 'manage'	: '')));
	}
	// if vip, also allow manage as that is viparea alias
	elseif ($Permission === 'vip') {
		$Permission =	($thru_viparea			? 'vip'	:
						($thru_office_manage	? 'manage'	: ''));
	}
	// Ensure that:
	//	$thru_office_pr => 'pr'
	//	$thru_viparea => 'vip'
	//	$thru_office_manage => 'vip'
	elseif (	($thru_office_pr		&& $Permission !== 'pr')
			||	($thru_viparea			&& $Permission !== 'vip')
			||	($thru_office_manage	&& $Permission !== 'manage')) {
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
			'manage'	=> $student_login_action,
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
			'manage'	=> $office_login_action,
			'admin'		=> $office_login_action,
		);
	} elseif ($user_level === 'organisation') {
		// Logged in from public as organisation
		$allow_vip = array_key_exists($organisation_shortname, $CI->user_auth->allTeams);
		$action_levels = array(
			'public'	=> TRUE,
			'student'	=> TRUE,
			'vip'		=> $allow_vip,
			'office'	=> FALSE,
			'pr'		=> FALSE,
			'editor'	=> FALSE,
			'manage'	=> FALSE,
			'admin'		=> FALSE,
		);
		if ($allow_vip) {
			VipOrganisationId(FALSE, $CI->user_auth->allTeams[$organisation_shortname][0]);
			VipOrganisationName(FALSE, $CI->user_auth->allTeams[$organisation_shortname][1]);
			VipOrganisationId(TRUE, $CI->user_auth->organisationLogin);
			VipOrganisationName(TRUE, $CI->user_auth->organisationName);
			VipMode('viparea');
			VipLevel('write', TRUE);
		}
	} elseif ($user_level === 'vip') {
		// Logged in as student and in VIP area
		$vip_door_open_action = array(
			'message','warning',
			HtmlButtonLink(site_url('logout/vip'.$CI->uri->uri_string()),'Leave VIP Area')
			. $CI->pages_model->GetPropertyText('login:warn_open_vip', TRUE),
			TRUE
		);

		$allow_vip = array_key_exists($organisation_shortname, $CI->user_auth->allTeams);
		if ($allow_vip) {
			$vip_accessible = TRUE;
			VipOrganisationId(FALSE, $CI->user_auth->allTeams[$organisation_shortname][0]);
			VipOrganisationName(FALSE, $CI->user_auth->allTeams[$organisation_shortname][1]);
			VipOrganisationId(TRUE, $CI->user_auth->organisationLogin);
			VipOrganisationName(TRUE, $CI->user_auth->organisationName);
			VipMode('viparea');
			VipLevel('write', TRUE);
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
			'manage'	=> $office_login_action,
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

		// Refine further
		if ($user_level === 'office') {
			$action_levels = array(
				'public'		=> $office_door_open_action,
				'student'		=> $office_door_open_action,
				'vip'			=> $vip_login_action,
				'office'		=> TRUE,
				'pr'			=> 'pr',
				'editor'		=> FALSE,
				'manage'		=> FALSE,
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
				'manage'		=> TRUE,
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
				'manage'		=> TRUE,
				'admin'			=> TRUE,
			);
		}

		// Change an office user to pr if they rep for the organisation
		static $vipModes = array(
			'pr' => 'office',
			'manage' => 'manage',
		);
		if (array_key_exists($Permission, $vipModes)) {
			// Get organisation information
			$CI->db->select('organisation_entity_id AS id,'.
							'organisation_name AS name,'.
							'organisation_pr_rep AS rep');
			$CI->db->join('entities', 'organisation_entity_id = entity_id', 'inner');
			$CI->db->where(array(
				'organisation_directory_entry_name' => $organisation_shortname,
				'entity_deleted = FALSE',
			));
			$matching_org = $CI->db->get('organisations')->result_array();

			if (empty($matching_org)) {
				$action_levels[$Permission] = FALSE;
			} else {
				$matching_org = $matching_org[0];
				if ($action_levels[$Permission] === 'pr') {
					$action_levels[$Permission] = TRUE;
					$rep = ($matching_org['rep'] == $CI->user_auth->entityId);
					if ($rep) {
						VipLevel('rep', TRUE);
					} else {
						VipLevel('read', TRUE);
					}
				} elseif ($action_levels[$Permission]) {
					VipLevel('write', TRUE);
				}
				VipOrganisationId(FALSE, $matching_org['id']);
				VipOrganisationName(FALSE, $matching_org['name']);
				VipOrganisationId(TRUE, $matching_org['id']);
				VipOrganisationName(TRUE, $matching_org['name']);
				VipMode($vipModes[$Permission]);
			}
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
					// Before redirecting, forward on the redirected post data
					$post_data = GetRedirectData();
					if (NULL !== $post_data) {
						SetRedirectData($action[1], $post_data);
					}
					// Do the redirect
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
						global $_POST;
						$_POST = array();
					}
					foreach ($post_data as $key => $value) {
						$_POST[$key] = $value;
					}
				}
			}
		}
	}
	if ('fbml' === OutputMode()) {
		$Permission = 'facebookapp';
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
<form action="'.$Link.'" method="post" class="form">
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
		//'target' => 'https://'.$_SERVER['HTTP_HOST'].$CI->uri->uri_string(),
		'target' => $CI->uri->uri_string(),
	);

	$login_id = '';
	if ($Level === 'office') {
		$page_code = 'login_office';
		$login_id = 'office';
		$success_msg = $CI->pages_model->GetPropertyText('login:success_office', TRUE);
		// Find whether to fail
		$data['failure'] = !$CI->user_auth->officeLogin;
		if ($data['failure']) {
			$data['failure_text'] = $CI->pages_model->GetPropertyWikitext('nooffice_text', $page_code);
		}

	} elseif ($Level === 'vip') {
		$page_code = 'login_vip';
		$login_id = 'vip';
		$success_msg = $CI->pages_model->GetPropertyText('login:success_vip', TRUE);
		$data['usernames'] = array();
		$logins = $CI->user_auth->getOrganisationLogins();
		// Find whether to fail
		$data['failure'] = empty($logins);
		if ($data['failure']) {
			$data['failure_text'] = $CI->pages_model->GetPropertyWikitext('novip_text', $page_code);
		}
		// Default to an organisation?
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
		$data['username'] = $CI->user_auth->username;
		$data['keep_login'] = !empty($CI->user_auth->username);
		$data['failure'] = false;
	}
	$data['login_id'] = $login_id;
	if (($CI->input->post('login_id') === $login_id)) {
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
				$keep_login = (FALSE !== $CI->input->post('keep_login'));

				$CI->user_auth->login($username, $password, $keep_login);

				if($RedirectDestination == '' || $RedirectDestination == '/')
				{
					$RedirectDestination = GetDefaultHomepage();
				}

			}

			$CI->messages->AddMessage('success','<p>'.$success_msg.'</p>');

			if (FALSE !== $post_data) {
				SetRedirectData($RedirectDestination, $post_data);
			}
			redirect($RedirectDestination);
			return TRUE;
		} catch (Exception $e) {
			$CI->messages->AddMessage('error','<p>'.$e->getMessage().'</p>');
		}
	} else {
		$post_data = GetRedirectData();
		if (NULL !== $post_data) {
			$data['previous_post_data'] = $post_data;
			$CI->messages->AddMessage('information', '<p>The form data you submitted will be sent after you log in from this page.  </p>');
		}
		$data['initial_username'] = '';
	}

	// Get various page properties used for displaying the login screen
	$CI->pages_model->SetPageCode($page_code);

	// Show "please log in" message if not failed
	/*
	if (!$data['failure']) {
		$permission_message = $CI->pages_model->GetPropertyMessage('msg_permission_message');
		if (FALSE !== $permission_message) {
			$CI->messages->AddMessage(new Message($permission_message), FALSE);
		}
	}*/

	// Title of login section of page
	$section_title = $CI->pages_model->GetPropertyText('section_title');
	if (!empty($section_title)) {
		$data['title'] = $section_title;
	}

	// Main login message
	$login_message = $CI->pages_model->GetPropertyText('login_message');
	if (!empty($login_message)) {
		$data['login_message'] = '<p>'.$login_message.'</p>';
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
