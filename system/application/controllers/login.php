<?php

class Login extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
		$this->load->library('User_auth');
	}

	function index()
	{
		$data = array(
			'username' => array('name' => 'username', 'id' => 'username'),
			'password' => array('name' => 'password', 'id' => 'password')
		);
		
		// Set up the public frame
		$this->frame_public->SetTitle('Log in');
		$this->frame_public->SetContentSimple('login/login', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function loginsubmit() {
		$this->user_auth->login($this->input->post('username'), $this->input->post('password'), false);
	}

	function resetpassword()
	{
		$data = array();
		
		// Set up the public frame
		$this->frame_public->SetTitle('Reset My Password');
		$this->frame_public->SetContentSimple('login/resetpassword', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function register()
	{
		$data = array();
		
		// Set up the public frame
		$this->frame_public->SetTitle('Register An Account');
		$this->frame_public->SetContentSimple('login/register', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
