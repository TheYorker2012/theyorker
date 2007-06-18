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
	public $Platform = NULL;
	public $Client = NULL;
	public $Uid = NULL;
	
	function __construct()
	{
		$CI = & get_instance();
		
		$CI->load->helper('facebook');
		$CI->load->config('facebook');
		$this->Config = $CI->config->Item('facebook');
		
		$this->Platform = new FacebookPlatform($this->Config['api_key'], $this->Config['secret']);
		$this->Client = & $this->Platform->api_client;
		if (array_key_exists('auth_token', $_GET)) {
			redirect($CI->uri->uri_string());
		}
		$links = array();
		if ($this->InUse()) {
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
		//return array_key_exists('facebook', $_SESSION);
		return isset($_SESSION['facebook']['enable']);
	}
	
	function Enable()
	{
		if (!$this->InUse()) {
			$_SESSION['facebook'] = array('enable' => true);
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
		$CI->messages->AddMessage('information',
			'You have logged out of facebook (reason: '.$E->getMessage().')'.
			HtmlButtonLink(site_url('login/facebook'.$CI->uri->uri_string()),'Log back in')
		);
	}
	
	function Connect()
	{
		if (!array_key_exists('facebook', $_SESSION)) {
			$_SESSION['facebook'] = array('enable' => true);
		}
		
		// Ensure that facebook is switched on
		$this->Uid = $this->Platform->require_login();
		
	}
}

?>
