<?php

class Logout extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		SetupMainFrame('public');
	}

	function index()
	{
		$this->user_auth->logout();
		$this->main_frame->AddMessage('success','You have successfully logged out');
		$this->main_frame->DeferMessages();
		redirect('');
	}
}
?>
