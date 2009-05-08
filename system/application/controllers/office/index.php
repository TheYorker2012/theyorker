<?php

/**
 *	@brief	Office homepage 2.0
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Index extends Controller
{

	function __construct()
	{
		parent::Controller();
		$this->load->model('notifications_model');

		$this->load->model('requests_model');
		$this->load->model('photos_model');
	}

	function index()
	{
		if (!CheckPermissions('office')) return;
		$this->pages_model->SetPageCode('office_index');
		$data = array();

		// Notifications
		$data['notifications'] = array(
			array(
				'permission'	=>	'',
				'query'			=>	'checkAnnouncements',
				'title'			=>	'Announcement',
				'type'			=>	'announcement',
				'link'			=>	'/office/announcements'
			),
			array(
				'permission'	=>	'BYLINES_PENDING',
				'query'			=>	'checkPendingBylines',
				'title'			=>	'Pending byline',
				'type'			=>	'byline',
				'link'			=>	'/office/bylines/pending'
			),
		);

		foreach ($data['notifications'] as &$n) {
			if (empty($n['permission']) || $this->permissions_model->hasUserPermission($n['permission'])) {
				$n['count'] = $this->notifications_model->$n['query']();
			}
		}

		// Activity
		$data['activity'] = $this->notifications_model->getActivity();



		//$article_requests = $this->requests_model->GetMyRequests($this->user_auth->entityId);
		//$photos_requests = $this->photos_model->GetMyRequests($this->user_auth->entityId);

		//$all_requests = array();
		//while ((count($article_requests) > 0) || (count($photos_requests) > 0)) {
		//	if ((count($article_requests) > 0) && ((count($photos_requests) == 0) || ($article_requests[0]['deadline'] < $photos_requests[0]['deadline']))) {
		//		$all_requests[] = array_shift($article_requests);
		//	} else {
		//		$all_requests[] = array_shift($photos_requests);
		//	}
		//}

		//$data['announcements'] = $this->notifications_model->getAnnouncements();
		//$data['my_requests'] = $all_requests;

		// Set up the content
		$this->main_frame->SetContentSimple('office/index', $data);
		$this->main_frame->Load();
	}

	function index_old()
	{
		if (!CheckPermissions('office')) return;

		// AJAX
		$this->load->library('xajax');
        $this->xajax->registerFunction(array('_readAnnouncement', &$this, '_readAnnouncement'));
        $this->xajax->processRequests();

		$this->pages_model->SetPageCode('office_index');

		$article_requests = $this->requests_model->GetMyRequests($this->user_auth->entityId);
		$photos_requests = $this->photos_model->GetMyRequests($this->user_auth->entityId);

		$all_requests = array();
		while ((count($article_requests) > 0) || (count($photos_requests) > 0)) {
			if ((count($article_requests) > 0) && ((count($photos_requests) == 0) || ($article_requests[0]['deadline'] < $photos_requests[0]['deadline']))) {
				$all_requests[] = array_shift($article_requests);
			} else {
				$all_requests[] = array_shift($photos_requests);
			}
		}

		$data['announcements'] = $this->notifications_model->getAnnouncements();
		$data['my_requests'] = $all_requests;

		// Set up the content
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->IncludeCss('/stylesheets/office_interface.css');
		$this->main_frame->IncludeJs('/javascript/office_interface.js');
		$this->main_frame->SetContentSimple('office/index', $data);
		$this->main_frame->Load();
	}
	
	function _readAnnouncement ($notification_id = NULL) {
		$xajax_response = new xajaxResponse();
		if (!empty($notification_id)) {
			$this->notifications_model->markAsRead($notification_id, $this->user_auth->entityId);
		}
		return $xajax_response;
	}
}

?>