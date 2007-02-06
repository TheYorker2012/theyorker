<?php

/// user admin controller.
class Useradmin extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
	}
	
	/// admin page to edit user information
	function edit()
	{
		if (!CheckPermissions('admin')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('Administrate User');
		$this->main_frame->SetContentSimple('login/user');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>