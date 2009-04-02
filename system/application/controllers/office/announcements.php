<?php

/**
 *	@brief	Control panel allowing priviledged users to send notifications
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */
class Announcements extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('notifications_model');
	}

	function index()
	{
		if (!CheckPermissions('office')) return;
		if (!CheckRolePermissions('ANNOUNCEMENT_VIEW')) return;

		$this->pages_model->SetPageCode('office_announcements');

		$data['announcements'] = $this->notifications_model->getAllAnnouncements();

		// Set up the content
		$this->main_frame->SetContentSimple('office/announcements/index', $data);
		$this->main_frame->Load();
	}

	function add()
	{
		if (!CheckPermissions('office')) return;
		if (!CheckRolePermissions('ANNOUNCEMENT_SEND')) return;

		// AJAX
		$this->load->library('xajax');
        $this->xajax->registerFunction(array('_getRecipients', &$this, '_getRecipients'));
        $this->xajax->processRequests();

		$this->pages_model->SetPageCode('office_announcements');

		$data['roles'] = $this->notifications_model->getAllUserRoles();
		$data['bylines'] = $this->notifications_model->getUserBylines();

		if (isset($_POST['preview']) || isset($_POST['post'])) {

        	if (!empty($_POST['sender'])) {
				foreach ($data['bylines'] as $byline) {
					if ($byline->id == $_POST['sender']) {
						$data['preview']['byline'] = $byline;
						break;
					}
				}
			}
			if (empty($data['preview']['byline'])) {
				$this->main_frame->AddMessage('error','You need to have a byline to post an announcement. Go and make one now!');
			} else {
				$this->load->library('wikiparser');
				$data['preview']['content'] = $this->wikiparser->parse($_POST['content']);
	
				if (isset($_POST['post'])) {
					if (empty($_POST['subject']) || empty($_POST['content'])) {
						$this->main_frame->AddMessage('error','Please make sure you have provided a subject and message for this announcement.');
					} elseif (empty($_POST['sendto'])) {
						$this->main_frame->AddMessage('error','Please select which group of users you wish to send the announcement to.');
					} elseif (empty($_POST['sender'])) {
						$this->main_frame->AddMessage('error','Please choose the byline you wish to post the announcement with.');
					} else {
						$this->notifications_model->add($_POST['subject'], $_POST['content'], $data['preview']['content'], $_POST['sendto'], $_POST['sender']);
						$this->main_frame->AddMessage('success','New announcement has been posted.');
						redirect('/office/announcements');
					}
				}
			}
		}

		// Set up the content
		$this->main_frame->IncludeCss('/stylesheets/office_interface.css');
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->SetContentSimple('office/announcements/post', $data);
		$this->main_frame->Load();
	}
	
	function _getRecipients ($role = NULL) {
		$xajax_response = new xajaxResponse();
		if (!empty($role)) {
			$query = $this->notifications_model->getAllUsersWithRole($role);
			$users = array();
			foreach ($query as $user) {
				$users[] = $user->firstname . ' ' . $user->surname;
			}
			$xajax_response->addScriptCall('recipientList', $users);
		}
		return $xajax_response;
	}
}

?>