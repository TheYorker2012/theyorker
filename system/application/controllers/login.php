<?php

class Login extends Controller
{

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		if (!CheckPermissions('student')) return;
		
		redirect('home/main');
	}

	function resetpassword()
	{
		if (!CheckPermissions('public')) return;
		
		$data = array();
		
		// Set up the public frame
		$this->main_frame->SetTitle('Reset My Password');
		$this->main_frame->SetContentSimple('login/resetpassword', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
