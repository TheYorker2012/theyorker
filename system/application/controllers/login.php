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
			'previous_username' => '',
		);
		
		// Use post data to log in
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$keeplogin = $this->input->post('keep_login');
		
		if (FALSE !== $username &&
			FALSE !== $password &&
			is_bool($keeplogin))
		{
			try {
				$this->user_auth->login($username,$password,$keeplogin);
				$this->frame_public->AddMessage(
					new InformationMsg('Login successfully', 'You are now successfully logged in.')
				);
				//redirect('');
			} catch (Exception $e) {
				$data['previous_username'] = $username;
				$this->frame_public->AddMessage(
					new ErrorMsg('Login error', $e->getMessage())
				);
			}
		}
		
		// Set up the public frame
		$this->frame_public->SetTitle('Log in');
		$this->frame_public->SetContentSimple('login/login', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	function resetpassword()
	{
		$data = array(
			'test' => 'I set this variable from the controller!',
		);
		
		// Set up the public frame
		$this->frame_public->SetTitle('Reset My Password');
		$this->frame_public->SetContentSimple('login/resetpassword', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	function register()
	{
		$data = array(
			'test' => 'I set this variable from the controller!',
		);
		
		// Set up the public frame
		$this->frame_public->SetTitle('Register An Account');
		$this->frame_public->SetContentSimple('login/register', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
