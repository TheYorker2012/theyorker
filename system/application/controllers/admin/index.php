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
		
		$this->main_frame->SetContentSimple('admin/admin');
			
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>