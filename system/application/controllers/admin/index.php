<?php

/// Main admin controller.
class Index extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		SetupMainFrame('admin');
	}
	
	function index()
	{
		// Set up the public frame
		if (CheckPermissions('admin')) {
			$this->main_frame->SetTitle('Admin');
			$this->main_frame->SetContentSimple('admin/admin');
		}
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>