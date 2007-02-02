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
		
		// Load the public frame
		$this->load->library('frame_organisation');
		$this->main_frame = $this->frame_organisation;
	}
	
	function index()
	{
		// Set up the public frame
		if (SetupMainFrame('organisation')) {
			$this->main_frame->SetTitle('Admin');
			$this->main_frame->SetContentSimple('admin/admin');
		}
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>