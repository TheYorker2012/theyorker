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
		/// Load requests office model
		$this->load->model('requests_model');
	}

	function index()
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_index');

		$data = array(
			'main_text'		=>	$this->pages_model->GetPropertyWikitext('main_text'),
			'my_requests'	=>	$this->requests_model->GetMyRequests($this->user_auth->entityId)
		);
		// Set up the content
		$this->main_frame->SetContentSimple('office/index', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>