<?php

class Feedback extends Controller {

	function __construct()
	{
		parent::Controller();
		// Load feedback model
		$this->load->model('feedback_model');
	}

	function index()
	{
		if (!CheckPermissions('admin')) return;

		$this->pages_model->SetPageCode('admin_feedback');

		$data['entries'] = $this->feedback_model->GetAllFeedback();

		$this->main_frame->SetContentSimple('admin/feedback', $data);
		$this->main_frame->Load();
	}

}
?>
