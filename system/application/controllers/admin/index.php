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
		if (!CheckPermissions('office')) return;
		$this->pages_model->SetPageCode('admin_index');
		$data = array();

		$this->load->model('feedback_model');
		$data['feedback_count'] = $this->feedback_model->GetFeedbackCount();

		// Set up the content
		$this->main_frame->SetData('menu_tab', 'admin');
		$this->main_frame->SetContentSimple('admin/admin',$data);
		$this->main_frame->Load();
	}
}

?>
