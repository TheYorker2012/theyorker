<?php

/// Main admin controller.
class Index extends Controller
{
	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
		if (!CheckPermissions('admin')) return;
		
		$this->pages_model->SetPageCode('admin_index');

		$this->load->model('feedback_model');
		$data['feedback_count'] = $this->feedback_model->GetFeedbackCount();

		$this->main_frame->SetContentSimple('admin/admin',$data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>