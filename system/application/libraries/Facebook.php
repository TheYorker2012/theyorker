<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	@file	libraries/Facebook.php
 *	@author	James Hogan (jh559@cs.york.ac.uk)
 *	@brief	Library for using the facebook API.
 */

/// Main library class
class Facebook
{
	public $Config;
	public $AuthToken = FALSE;
	public $Client = NULL;
	public $Session = NULL;
	public $Uid = NULL;
	
	function __construct()
	{
		$CI = & get_instance();
		
		$CI->load->helper('facebook_rest_client');
		$CI->load->config('facebook');
		$this->Config = $CI->config->Item('facebook');
		
		if (array_key_exists('auth_token', $_GET)) {
			$this->AuthToken = $_GET['auth_token'];
			$this->Connect();
			redirect($CI->uri->uri_string());
		}
		$links = array();
		if ($this->InUse()) {
			if (isset($_SESSION['facebook']['session'])) {
				$this->Session = $_SESSION['facebook']['session'];
			}
			$links[] = array('Disable Facebook', site_url('logout/facebook'.$CI->uri->uri_string()));
		} else {
			$links[] = array('Enable Facebook', site_url('login/facebook'.$CI->uri->uri_string()));
		}
		if (isset($CI->main_frame)) {
			$CI->main_frame->SetData('extra_menu_buttons',
				array_merge($CI->main_frame->GetData('extra_menu_buttons', array()),$links));
		}
	}
	
	function InUse()
	{
		return array_key_exists('facebook', $_SESSION);
	}
	
	function Enable()
	{
		if (!$this->InUse()) {
			$_SESSION['facebook'] = array();
		}
	}
	
	function Disable()
	{
		if ($this->InUse()) {
			unset($_SESSION['facebook']);
		}
	}
	
	function HandleException($E)
	{
		$this->Disable();
		$CI = & get_instance();
		$CI->messages->AddMessage('information', 'You have logged out of facebook. '. HtmlButtonLink(site_url('login/facebook'.$CI->uri->uri_string()),'Log back in'));
	}
	
	function Connect()
	{
		if (!array_key_exists('facebook', $_SESSION)) {
			$_SESSION['facebook'] = array();
		}
		
		// Already created client?
		if ($this->Client !== NULL) {
			return;
		}
		try {
			if (NULL !== $this->Session && array_key_exists('session_key', $this->Session)) {
				$session_key = $this->Session['session_key'];
			} else {
				$session_key = NULL;
				// Got an authentication token?
				if (!$this->AuthToken) {
					// redirect to the login page
					$this->RedirectLogin();
				}
			}
			
			// Create our client object.  
			// This is a container for all of our static information.
			$this->Client = new FacebookRestClient(
				$this->Config['rest_server_addr'],
				$this->Config['api_key'],
				$this->Config['secret'],
				$session_key,
				$this->Config['debug']
			);
			
			if (NULL === $session_key) {
				// The required call: Establish session 
				// The session key is saved in the client lib for the whole PHP instance.
				$this->Session = $this->Client->auth_getSession($this->AuthToken);
				$_SESSION['facebook']['session'] = $this->Session;
				$this->Uid = $this->Session['uid'];
			}
			
			//var_dump($this->Client->auth_createToken());
			
		} catch (FacebookRestClientException $ex) {
			if ($ex->getCode() == FacebookAPIErrorCodes::API_EC_PARAM) {
				// This will happen if auth_getSession fails, which generally means you
				// just hit "reload" on the page with an already-used up auth_token
				// parameter.  Bounce back to facebook to get a fresh auth_token.
				$this->RedirectLogin();
			} else {
				// Developers should probably handle other exceptions in a better way than this.
				throw $ex;
			}
		}
	}
	
	function GenLoginUrl($Next = NULL)
	{
		if (NULL === $Next) {
			$CI = & get_instance();
			$Next = substr($CI->uri->uri_string(), 1);
		}
		return $this->Config['login_server_base_url'].'/login.php?v=1.0' .
			'&next=' . $Next . '&api_key=' . $this->Config['api_key'];
	}
	
	function RedirectLogin()
	{
		header('Location: '.$this->GenLoginUrl());
		exit;
	}
}

?>
