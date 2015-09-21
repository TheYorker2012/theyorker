<?php

/// Main admin controller.
class UserProfile extends Controller
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

		// Set up the content
		$this->main_frame->SetData('menu_tab', 'admin');
		$this->main_frame->SetContentSimple('admin/admin',$data);
		$this->main_frame->Load();
	}
}

?>