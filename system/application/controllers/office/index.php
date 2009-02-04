<?php

/// Main office controller.
class Index extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		$this->load->model('notifications_model');
		/// Load requests office model
		$this->load->model('requests_model');
		/// Load photos office model
		$this->load->model('photos_model');
	}

	function index()
	{
		if (!CheckPermissions('office')) return;

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
		$this->main_frame->IncludeCss('/stylesheets/office_interface.css');
		$this->main_frame->IncludeJs('/javascript/office_interface.js');
		$this->main_frame->SetContentSimple('office/index', $data);
		$this->main_frame->Load();
	}
}

?>