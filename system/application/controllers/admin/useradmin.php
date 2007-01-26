<?php

/// user admin controller.
class Useradmin extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	/// admin page to edit user information
	function edit()
	{
	
		// Set up the public frame
		$this->frame_public->SetTitle('Administrate User');
		$this->frame_public->SetContentSimple('login/user');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>