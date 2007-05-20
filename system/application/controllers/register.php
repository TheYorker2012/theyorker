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
		$this->load->model('pages_model');
		// Get changeable page content
		$this->pages_model->SetPageCode('preferences');
	}

	function index()
	{
		// TODO: Check if this is the first time they've logged in or not
		if (!CheckPermissions('student')) return;
		
		$this->load->library('account_personal');
		
		// Get page content
		$data['intro_heading'] = $this->pages_model->GetPropertyText('intro_heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		
		$this->account_personal->Validate(true,'/register','/register/academic');
		
		//$directory_view = $this->frames->view('directory/directory', $data);

		$data['bigcontent'] = $this->account_personal;
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
			$CI->load->model('calendar/events_model');
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
						$CI->events_model->FeedSubscribe($module_id);
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
			$CI->load->model('slideshow');
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
				$get_slideshow = $CI->slideshow->getPhotos($soc_id);
				foreach ($get_slideshow->result() as $photo) {
					$xajax_response->addScriptCall('Slideshow.add', '/photos/slideshow/'.$slide->photo_id);
				}
				$xajax_response->addScriptCall('Slideshow.load');
				}
			return $xajax_response;
		}

		function societySubscription ($soc_id)
		{
			$CI = &get_instance();
			$CI->load->model('prefs_model');
			$CI->load->model('calendar/events_model');
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
						$CI->events_model->FeedSubscribe($soc_id);
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
			$CI = & get_instance();
			$CI->load->model('calendar/events_model');
			
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
						$sql = 'INSERT INTO subscriptions SET subscription_organisation_entity_id = ' . $soc_id . ', subscription_user_entity_id = ' . $_SESSION['ua_entityId'] . ',  subscription_user_confirmed = 1';
						$query = mysql_query($sql);
						$CI->events_model->FeedSubscribe($soc_id);
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
