<?php

class Logout extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}

	function index()
	{
		$this->user_auth->logout();
		redirect('/');
	}
}
?>
