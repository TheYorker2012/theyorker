<?php
/**
 *	This provides the wizard for setting up a user's
 *	preferences when they first login to the site.
 *
 *	@author	Chris Travis	(cdt502 - ctravis@gmail.com)
 */

class Register extends Controller {

	/**
	 *	@brief	Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		// Load data model
		$this->load->model('prefs_model');
	}

	/**
	 *	@brief	Determines which function is used depending on url
	 */
	function _remap($method = 'index')
	{
		if (method_exists($this, $method)) {
			$this->$method();
		} else {
			$this->subscriptions($method);
		}
	}

	function index()
	{
		if (!CheckPermissions('public')) return;

		$this->load->library('account_personal');

		/// Get changeable page content
		$this->pages_model->SetPageCode('preferences');
		$data['email_prompt'] = $this->pages_model->GetPropertyWikitext('email_prompt');
		$data['intro_heading'] = $this->pages_model->GetPropertyText('intro_heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		$data['bigcontent'] = $this->account_personal;

		/// Validate form submission
		$this->account_personal->Validate(true,'/register','/register/college_campus');

		/// Set up the main frame
		$this->main_frame->SetTitleParameters(
			array('section' => 'General')
		);
		/// Load the main frame view (which will load the content view)
		$this->main_frame->SetContentSimple('account/preferences', $data);
		$this->main_frame->Load();
	}

	function subscriptions ($type = 'college_campus')
	{
		if (!CheckPermissions('student')) return;

		$type_info = $this->prefs_model->isOrganisationType($type);
		if (count($type_info) == 0) {
			$type = 'college_campus';
			$type_info = $this->prefs_model->isOrganisationType($type);
		}

		$this->load->library('xajax');
		$this->xajax->registerFunction(array('_getInfo',&$this,'_getInfo'));
		$this->xajax->registerFunction(array('_changeSub',&$this,'_changeSub'));
		$this->xajax->processRequests();

		// Get changeable page content
		$this->pages_model->SetPageCode('preferences');
		// Get page content
		$data['heading'] = $this->pages_model->GetPropertyText($type . '_heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext($type . '_intro');
		$data['organisation_subscriptions'] = $this->prefs_model->getOrganisationTypeSubscriptions($this->user_auth->entityId,$type);
		$data['organisations'] = $this->prefs_model->getAllOrganisations($type);
		$data['type'] = $type;
		$data['friendly_name'] = $type_info['friendlyname'];

		$wizard_step = array(
				'',
				'college_campus',
				'societies',
				'athletic_union',
				'departments',
				'venues',
				'organisations',
				'end'
		);
		$wizard_current = array_search($type,$wizard_step);
		if ($wizard_current === FALSE) {
			$data['button_back'] = '/account/';
            $data['button_next'] = '/account/';
		} else {
			$data['button_back'] = '/register/' . $wizard_step[$wizard_current - 1];;
			$data['button_next'] = '/register/' . $wizard_step[$wizard_current + 1];
		}
		/// Skip step if no organisations to subscribe to
		if (count($data['organisations']) == 0) {
			redirect($data['button_next']);
		} else {
			// Set up the public frame
			$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
			$this->main_frame->IncludeCss('stylesheets/account.css');
			$this->main_frame->SetTitleParameters(
				array('section' => $type_info['friendlyname'])
			);
			$this->main_frame->SetContentSimple('account/subscriptions', $data);
			// Load the public frame view (which will load the content view)
			$this->main_frame->Load();
		}
	}

	function _getInfo ($org_id,$type)
	{
		$this->load->model('slideshow');
		$xajax_response = new xajaxResponse();
		if ((!is_numeric($org_id)) || (!$this->prefs_model->isOfOrganisationType($org_id,$type))) {
			$xajax_response->addAlert('Invalid organisation selected, please try again.');
		} else {
			$org_info = $this->prefs_model->getOrganisationDescription($org_id);
			$xajax_response->addAssign('subscription_desc','innerHTML', $org_info['description']);
			if ($this->prefs_model->isSubscribed($this->user_auth->entityId,$org_id)) {
				$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/societies/' . $org_id . '/remove/\';');
				$xajax_response->addScript('document.getElementById(\'subscription_subscribe\').value=\'Unsubscribe\';');
			} else {
				$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/societies/' . $org_id . '/add/\';');
				$xajax_response->addScript('document.getElementById(\'subscription_subscribe\').value=\'Subscribe\';');
			}
			$xajax_response->addScript('document.getElementById(\'subscription_subscribe\').className=\'button show\';');

			$xajax_response->addScriptCall('Slideshow.reset');
			$get_slideshow = $this->slideshow->getPhotos($org_id);
			foreach ($get_slideshow->result() as $photo) {
				$xajax_response->addScriptCall('Slideshow.add', '/photos/slideshow/'.$photo->photo_id);
			}
			$xajax_response->addScriptCall('Slideshow.load');
		}
		return $xajax_response;
	}

	function _changeSub ($org_id,$type)
	{
		$xajax_response = new xajaxResponse();
		if ((!is_numeric($org_id)) || (!$this->prefs_model->isOfOrganisationType($org_id,$type))) {
			$xajax_response->addAlert('Invalid society selected, please try again.');
		} else {
			if ($this->prefs_model->isSubscribed($this->user_auth->entityId,$org_id)) {
				// Is subscribed, so delete subscription
				$this->prefs_model->deleteSubscription($this->user_auth->entityId, $org_id);
				// Set form controls up ready for subscription
				$xajax_response->addScript('document.getElementById(\'soc\' + lastViewed).className=\'viewing\';');
				$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/societies/' . $org_id . '/add/\';');
				$xajax_response->addScript('document.getElementById(\'subscription_subscribe\').value=\'Subscribe\';');
			} else {
				if ($this->prefs_model->isDeletedSubscription($this->user_auth->entityId,$org_id)) {
					// User used to be subscribed, so just activate subscription again
					$this->prefs_model->reactivateSubscription($this->user_auth->entityId,$org_id);
				} else {
					// New subscription required
					$this->prefs_model->addSubscription($this->user_auth->entityId, $org_id);
				}
				// Set form controls up for unsubscription
				$xajax_response->addScript('document.getElementById(\'soc\' + lastViewed).className=\'selected\';');
				$xajax_response->addScript('document.getElementById(\'form_subscribe\').action=\'/register/societies/' . $org_id . '/remove/\';');
				$xajax_response->addScript('document.getElementById(\'subscription_subscribe\').value=\'Unsubscribe\';');
			}
			$xajax_response->addScript('document.getElementById(\'subscription_loading\').className=\'hide\';');
			$xajax_response->addScript('document.getElementById(\'subscription_subscribe\').className=\'button show\';');
		}
		return $xajax_response;
	}

	function end()
	{
		if (!CheckPermissions('student')) return;
		$this->main_frame->AddMessage('success','Thank you for completing the preferences wizard and welcome to The Yorker.');
		redirect('/home');
	}

/*	Academic Subscriptions not being asked for due to lack of timetabling info - cdt502 @ 23rd May 2007

	function academic ()
	{
		if (!CheckPermissions('student')) return;
		
		$this->load->library('xajax');

		// Get changeable page content
		$this->pages_model->SetPageCode('preferences');

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
*/

}
?>
