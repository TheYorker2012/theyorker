<?php
/**
 *	This provides the wizard for setting up a user's
 *	preferences when they first login to the site.
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Register extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		// Load data model
		$this->load->model('prefs_model');
		// Load the main frame
		SetupMainFrame('student');
		// Get changeable page content
		$this->pages_model->SetPageCode('preferences');
	}

	function index()
	{
		// TODO: Check if this is the first time they've logged in or not
		if (!CheckPermissions('student')) return;
		
		// Get page content
		$data['intro_heading'] = $this->pages_model->GetPropertyText('intro_heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		$data['colleges'] = $this->prefs_model->GetColleges();
		$data['years'] = $this->prefs_model->GetYears();

		// Perform validation checks on submitted data
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<li>','</li>');
		// Validation rules
		$rules['fname'] = 'trim|required|alpha|xss_clean';
		$rules['sname'] = 'trim|required|alpha|xss_clean';
		$rules['email'] = 'trim|required|valid_email';
		$rules['nick'] = 'trim|required|alpha_numeric|xss_clean';
		$rules['gender'] = 'trim|required';
		$rules['college'] = 'trim|required|numeric';
		$rules['year'] = 'trim|required|numeric';
		$rules['time'] = 'trim|required|numeric';
		$this->validation->set_rules($rules);
		// names of fields for error msgs
		$fields['fname'] = 'first name';
		$fields['sname'] = 'surname';
		$fields['email'] = 'e-mail address';
		$fields['nick'] = 'nickname';
		$fields['gender'] = 'gender';
		$fields['college'] = 'college';
		$fields['year'] = 'year of study';
		$fields['time'] = 'time format';
		$this->validation->set_fields($fields);
		// Run validation checks, if they pass proceed to conduct db integrity checks
		$errors = array();
		if ($this->validation->run()) {
			if (!$this->prefs_model->genderCheck($_POST['gender'])) {
				array_push($errors, 'You must select your gender');
			}
			if (!$this->prefs_model->collegeExists($_POST['college'])) {
				array_push($errors, 'Please select the college you are a member of');
			}
			if (!$this->prefs_model->yearValid($_POST['year'])) {
				array_push($errors, 'You didn\'t choose the year you enrolled at university');
			}
			if (!$this->prefs_model->timeValid($_POST['time'])) {
				array_push($errors, 'Please decide whether you wish to view 12hr or 24hr time');
			}

			// If no db integrity errors then save and move onto next section
			if (count($errors) == 0) {
				$info = array(
					$this->validation->college,
					$this->validation->fname,
					$this->validation->sname,
					$this->validation->email,
					$this->validation->nick,
					$this->validation->gender,
					$this->validation->year,
					$this->validation->time
					);
				$this->prefs_model->updateUserInfo($this->user_auth->entityId,$info);
				redirect('/register/academic');
			}
		}

		// Validation errors occured
		if ($this->validation->error_string != "") {
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
		} elseif (count($errors) > 0) {
			$temp_msg = '';
			foreach ($errors as $error) {
				$temp_msg .= '<li>' . $error . '</li>';
			}
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
		} else {
			// If there were no errors then this is the first time the form has been loaded
			// so load default/preset values from db
			$userInfo = $this->prefs_model->getUserInfo($this->user_auth->entityId);
			$this->validation->fname = $userInfo['user_firstname'];
			$this->validation->sname = $userInfo['user_surname'];
			$this->validation->nick = $userInfo['user_nickname'];
			$this->validation->gender = $userInfo['user_gender'];
			$this->validation->college = $userInfo['user_college'];
			$this->validation->year = $userInfo['user_enrolled_year'];
			$this->validation->time = $userInfo['user_time_format'];
			if ($userInfo['user_email'] != '') {
				$this->validation->email = $userInfo['user_email'];
			} else {
				$this->validation->email = $this->user_auth->username . '@york.ac.uk';
			}
		}
		$this->main_frame->SetContentSimple('account/preferences', $data);
		
		// Set up the main frame
		$this->main_frame->SetTitleParameters(
			array('section' => 'General')
		);
		// Load the main frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function academic ()
	{
		if (!CheckPermissions('student')) return;
		
		$this->load->library('xajax');

		function getModules ($subject_id)
		{
			$CI = &get_instance();
			$CI->load->model('prefs_model');
			$xajax_response = new xajaxResponse();
			$modules_output = '';
			if ((is_numeric($subject_id)) && ($CI->prefs_model->isDepartment($subject_id))) {
				$modules = $CI->prefs_model->getModules($subject_id);
				$module_subscriptions = $CI->prefs_model->getModuleSubscriptions($_SESSION['ua_entityId'], $subject_id);
				foreach ($modules as $module) {
					$modules_output .= '<div id=\'soc' . $module['module_id'] . '\' class=\'';
					if (array_search($module['module_id'],$module_subscriptions) !== FALSE) {
						$modules_output .= 'selected';
					} else {
						$modules_output .= 'unselected';
					}
					$modules_output .= '\'><a href=\'/register/academic/' . $module['module_id'] . '/\' onclick="return update_subscriptions(\'' . $module['module_id'] . '\');">' . $module['module_name'] . '</a></div>';
				}
			}
			$xajax_response->addAssign('soc_container','innerHTML', $modules_output);
			return $xajax_response;
		}

		function updateModules ($module_id)
		{
			$CI = &get_instance();
			$CI->load->model('prefs_model');
			$xajax_response = new xajaxResponse();
			if ((is_numeric($module_id)) && ($CI->prefs_model->isModule($module_id))) {
				if ($CI->prefs_model->isSubscribed($_SESSION['ua_entityId'],$module_id)) {
					// Is subscribed, so delete subscription
					$CI->prefs_model->deleteSubscription($_SESSION['ua_entityId'], $module_id);
					$xajax_response->addScript('document.getElementById(\'soc' . $module_id . '\').className=\'unselected\';');
				} else {
					if ($CI->prefs_model->isDeletedSubscription($_SESSION['ua_entityId'],$module_id)) {
						// User used to be subscribed, so just activate subscription again
						$CI->prefs_model->reactivateSubscription($_SESSION['ua_entityId'], $module_id);
					} else {
						// New subscription required
						$CI->prefs_model->addSubscription($_SESSION['ua_entityId'], $module_id);
					}
					$xajax_response->addScript('document.getElementById(\'soc' . $module_id . '\').className=\'selected\';');
				}
			} else {
				$xajax_response->addAlert('Please select a valid module.');
			}
			$subscribed_modules = $CI->prefs_model->getModuleSubscriptions($_SESSION['ua_entityId']);
			$modules = '<ul>';
			foreach ($subscribed_modules as $module) {
				$modules .= '<li>' . $module['module_name'] . '</li>';
			}
			$modules .= '</ul>';
			$xajax_response->addAssign('current_modules','innerHTML', $modules);
			return $xajax_response;
		}

		$this->xajax->registerFunction('getModules');
		$this->xajax->registerFunction('updateModules');
		$this->xajax->processRequests();

		// Get page content
		$data['heading'] = $this->pages_model->GetPropertyText('academic_heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('academic_intro');
		$data['courses'] = $this->prefs_model->getDepartments();
		$data['current'] = $this->prefs_model->getModuleSubscriptions($_SESSION['ua_entityId']);

		// Set up the public frame
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->SetContentSimple('account/academic', $data);
		
		$this->main_frame->SetTitleParameters(
			array('section' => 'Academic')
		);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function societies ()
	{
		if (!CheckPermissions('student')) return;
		
		$this->load->library('xajax');

		function getInfo ($soc_id)
		{
			$CI = &get_instance();
			$CI->load->model('prefs_model');
			$xajax_response = new xajaxResponse();
			if ((!is_numeric($soc_id)) || (!$CI->prefs_model->isSociety($soc_id))) {
				$xajax_response->addAlert('Invalid society selected, please try again.');
			} else {
				$soc_info = $CI->prefs_model->getOrganisationDescription($soc_id);
				$xajax_response->addAssign('socdesc','innerHTML', $soc_info['description']);
				if ($CI->prefs_model->isSubscribed($_SESSION['ua_entityId'],$soc_id)) {
					$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/societies/' . $soc_id . '/remove/\';');
					$xajax_response->addScript('document.getElementById(\'soc_subscribe\').value=\'Unsubscribe\';');
				} else {
					$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/societies/' . $soc_id . '/add/\';');
					$xajax_response->addScript('document.getElementById(\'soc_subscribe\').value=\'Subscribe\';');
				}
				$xajax_response->addScript('document.getElementById(\'soc_subscribe\').className=\'button show\';');
	
				$xajax_response->addScriptCall('Slideshow.reset');
				$get_slideshow = $CI->prefs_model->getSlideshowImages($soc_id);
				foreach ($get_slideshow as $photo) {
					$xajax_response->addScriptCall('Slideshow.add', '/images/photos/' . $photo['photo_id'] . '.jpg');
				}
				$xajax_response->addScriptCall('Slideshow.load');
				}
			return $xajax_response;
		}

		function societySubscription ($soc_id)
		{
			$CI = &get_instance();
			$CI->load->model('prefs_model');
			$xajax_response = new xajaxResponse();
			if ((!is_numeric($soc_id)) || (!$CI->prefs_model->isSociety($soc_id))) {
				$xajax_response->addAlert('Invalid society selected, please try again.');
			} else {
				if ($CI->prefs_model->isSubscribed($_SESSION['ua_entityId'],$soc_id)) {
					// Is subscribed, so delete subscription
					$CI->prefs_model->deleteSubscription($_SESSION['ua_entityId'], $soc_id);
					// Set form controls up ready for subscription
					$xajax_response->addScript('document.getElementById(\'soc\' + lastViewed).className=\'viewing\';');
					$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/societies/' . $soc_id . '/add/\';');
					$xajax_response->addScript('document.getElementById(\'soc_subscribe\').value=\'Subscribe\';');
				} else {
					if ($CI->prefs_model->isDeletedSubscription($_SESSION['ua_entityId'],$soc_id)) {
						// User used to be subscribed, so just activate subscription again
						$CI->prefs_model->reactivateSubscription($_SESSION['ua_entityId'], $soc_id);
					} else {
						// New subscription required
						$CI->prefs_model->addSubscription($_SESSION['ua_entityId'], $soc_id);
					}
					// Set form controls up for unsubscription
					$xajax_response->addScript('document.getElementById(\'soc\' + lastViewed).className=\'selected\';');
					$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/societies/' . $soc_id . '/remove/\';');
					$xajax_response->addScript('document.getElementById(\'soc_subscribe\').value=\'Unsubscribe\';');
				}
				$xajax_response->addScript('document.getElementById(\'sub_loading\').className=\'hide\';');
				$xajax_response->addScript('document.getElementById(\'soc_subscribe\').className=\'button show\';');
			}
			return $xajax_response;
		}
		$this->xajax->registerFunction('getInfo');
		$this->xajax->registerFunction('societySubscription');
		$this->xajax->processRequests();

		// Get page content
		$data['heading'] = $this->pages_model->GetPropertyText('societies_heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('societies_intro');
		$data['society_subscriptions'] = $this->prefs_model->getSocietySubscriptions($this->user_auth->entityId);
		$data['societies'] = $this->prefs_model->getAllSocieties();

		// Set up the public frame

		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->SetContentSimple('account/societies', $data);
		
		$this->main_frame->SetTitleParameters(
			array('section' => 'Societies')
		);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}




	function au ()
	{
		if (!CheckPermissions('student')) return;
		
		$this->load->library('xajax');

		function isSubscribed ($user_id, $org_id)
		{
			$sql =
				'SELECT'.
				' subscription_organisation_entity_id '.
				'FROM subscriptions '.
				'WHERE subscription_user_entity_id = '.$user_id.
				' AND subscription_organisation_entity_id = '.$org_id.
				' AND subscription_deleted = 0';
			$query = mysql_query($sql);
			return mysql_num_rows($query);
		}

		function isDeletedSubscription ($user_id, $org_id)
		{
			$sql =
				'SELECT'.
				' subscription_organisation_entity_id '.
				'FROM subscriptions '.
				'WHERE subscription_user_entity_id = '.$user_id.
				' AND subscription_organisation_entity_id = '.$org_id.
				' AND subscription_deleted = 1';
			$query = mysql_query($sql);
			return mysql_num_rows($query);
		}

		function isAUClub ($soc_id)
		{
			$sql =
				'SELECT'.
				' organisation_entity_id AS id '.
				'FROM organisations '.
				'WHERE organisation_organisation_type_id = 3'.
				' AND organisation_entity_id = ' . $soc_id;
			$query = mysql_query($sql);
			return mysql_num_rows($query);
		}

		function getInfo ($soc_id)
		{
			$xajax_response = new xajaxResponse();
			if ((!is_numeric($soc_id)) || (!isAUClub($soc_id))) {
				$xajax_response->addAlert('Invalid athletic union club selected, please try again.');
			} else {
				$dbquery = mysql_query('SELECT organisation_content_description AS description FROM organisations INNER JOIN organisation_contents ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id WHERE organisation_organisation_type_id = 3 AND organisation_entity_id = ' . $soc_id . ' ORDER BY organisation_name ASC');
				$dbres = mysql_fetch_array($dbquery);
				$info = $dbres['description'];
				$xajax_response->addAssign('socdesc','innerHTML', $info);
				// ERROR: $this->prefs_model->isSubscribed() doesn't work and i can't work out how to reference it
				if (isSubscribed($_SESSION['ua_entityId'],$soc_id)) {
					$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/au/' . $soc_id . '/remove/\';');
					$xajax_response->addScript('document.getElementById(\'soc_subscribe\').value=\'Unsubscribe\';');
				} else {
					$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/au/' . $soc_id . '/add/\';');
					$xajax_response->addScript('document.getElementById(\'soc_subscribe\').value=\'Subscribe\';');
				}
				$xajax_response->addScript('document.getElementById(\'soc_subscribe\').className=\'button show\';');
	
				$get_slideshow = mysql_query('SELECT photos.photo_title, photos.photo_id FROM photos, organisation_slideshows AS slideshow WHERE slideshow.organisation_slideshow_organisation_entity_id = ' . $soc_id . ' AND slideshow.organisation_slideshow_photo_id = photos.photo_id ORDER BY slideshow.organisation_slideshow_order ASC');
				$xajax_response->addScriptCall('Slideshow.reset');
				while ($dbres = mysql_fetch_array($get_slideshow)) {
					$xajax_response->addScriptCall('Slideshow.add', '/images/photos/' . $dbres['photo_id'] . '.jpg');
				}
				$xajax_response->addScriptCall('Slideshow.load');
				}
			return $xajax_response;
		}

		function societySubscription ($soc_id)
		{
			$xajax_response = new xajaxResponse();
			if ((!is_numeric($soc_id)) || (!isAUClub($soc_id))) {
				$xajax_response->addAlert('Invalid athletic union club selected, please try again.');
			} else {
				if (isSubscribed($_SESSION['ua_entityId'],$soc_id)) {
					// Is subscribed, so delete subscription
					$sql = 'UPDATE subscriptions SET subscription_deleted = 1, subscription_timestamp = CURRENT_TIMESTAMP WHERE subscription_organisation_entity_id = ' . $soc_id . ' AND subscription_user_entity_id = ' . $_SESSION['ua_entityId'];
					$query = mysql_query($sql);
					// Set form controls up ready for subscription
					$xajax_response->addScript('document.getElementById(\'soc\' + lastViewed).className=\'viewing\';');
					$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/au/' . $soc_id . '/add/\';');
					$xajax_response->addScript('document.getElementById(\'soc_subscribe\').value=\'Subscribe\';');
				} else {
					if (isDeletedSubscription($_SESSION['ua_entityId'],$soc_id)) {
						// User used to be subscribed, so just activate subscription again
						$sql = 'UPDATE subscriptions SET subscription_deleted = 0, subscription_timestamp = CURRENT_TIMESTAMP WHERE subscription_organisation_entity_id = ' . $soc_id . ' AND subscription_user_entity_id = ' . $_SESSION['ua_entityId'];
						$query = mysql_query($sql);
					} else {
						// New subscription required
						$sql = 'INSERT INTO subscriptions SET subscription_organisation_entity_id = ' . $soc_id . ', subscription_user_entity_id = ' . $_SESSION['ua_entityId'] . ', subscription_interested = 1, subscription_user_confirmed = 1';
						$query = mysql_query($sql);
					}
					// Set form controls up for unsubscription
					$xajax_response->addScript('document.getElementById(\'soc\' + lastViewed).className=\'selected\';');
					$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/au/' . $soc_id . '/remove/\';');
					$xajax_response->addScript('document.getElementById(\'soc_subscribe\').value=\'Unsubscribe\';');
				}
				$xajax_response->addScript('document.getElementById(\'sub_loading\').className=\'hide\';');
				$xajax_response->addScript('document.getElementById(\'soc_subscribe\').className=\'button show\';');
			}
			return $xajax_response;
		}
		$this->xajax->registerFunction('getInfo');
		$this->xajax->registerFunction('societySubscription');
		$this->xajax->processRequests();

		// Get page content
		$data['heading'] = $this->pages_model->GetPropertyText('au_heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('au_intro');
		$data['society_subscriptions'] = $this->prefs_model->getAUClubSubscriptions($this->user_auth->entityId);
		$data['societies'] = $this->prefs_model->getAllAUClubs();

		// Set up the public frame

		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->SetContentSimple('account/au', $data);

		$this->main_frame->SetTitleParameters(
			array('section' => 'Athletic Union Clubs')
		);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function end()
	{
		if (!CheckPermissions('student')) return;
		$this->main_frame->AddMessage('success','Thank you for completing the preferences wizard and welcome to The Yorker.');
		redirect('/home');
	}
}
?>
