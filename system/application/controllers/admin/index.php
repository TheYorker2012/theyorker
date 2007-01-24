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
		$this->load->library('frame_public');
	}
	
	function index()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Admin');
		$this->frame_public->SetContentSimple('admin/admin');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>