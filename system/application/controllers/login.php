<?php

class Login extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}

	function index()
	{
		$data = array(
			'username' => array('name' => 'username', 'id' => 'username'),
			'password' => array('name' => 'password', 'id' => 'password'),
			'target' => '/login',
		);
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$successfully_logged_in = FALSE;
		if (FALSE !== $username) {
			$data['initial_username'] = $username;
			try {
				$this->user_auth->login($username, $password, false);
				$successfully_logged_in = TRUE;
				$this->frame_public->AddMessage('success','You have successfully logged in');
				$this->frame_public->DeferMessages();
				redirect('home/main');
			} catch (Exception $e) {
				$this->frame_public->AddMessage('error',$e->getMessage());
			}
		} else {
			$data['initial_username'] = '';
		}
		
		// Set up the public frame
		$this->frame_public->SetTitle('Log in');
		
		if (!$successfully_logged_in) {
			$this->frame_public->SetContentSimple('login/login', $data);
		}
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
		
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
}
?>
