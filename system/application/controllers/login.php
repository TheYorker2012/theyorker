<?php

class Login extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		SetupMainFrame('public');
	}

	function index()
	{
		if (CheckPermissions('student')) {
			$this->frame_public->DeferMessages();
			redirect('home/main');
		};
		
		$this->main_frame->Load();
	}

	function resetpassword()
	{
		$data = array();
		
		// Set up the public frame
		$this->main_frame->SetTitle('Reset My Password');
		$this->main_frame->SetContentSimple('login/resetpassword', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
