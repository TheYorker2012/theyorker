<?php

/// Logout controller
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Logout extends Controller
{
	/// Default constructor
	function __construct()
	{
		parent::Controller();

		session_start();
		
		$this->load->library('messages');

		// CheckPermissions is not used so user_auth needs loading
		$this->load->model('user_auth');
		$this->load->helper('uri_tail');
	}

	/// Logout from main account
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	logging out.
	 */
	function main()
	{
		try {
			$this->user_auth->logout();
			$this->messages->AddMessage('success','You have successfully logged out');
		} catch (Exception $e) {
			$CI->messages->AddMessage('error',$e->getMessage());
		}
		
		RedirectUriTail(2);
	}
	
	/// Logout from vip area
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	logging out.
	 */
	function vip()
	{
		try {
			$this->user_auth->logoutOrganisation();
			$this->messages->AddMessage('success','You have successfully left the VIP area');
		} catch (Exception $e) {
			$CI->messages->AddMessage('error',$e->getMessage());
		}
		
		RedirectUriTail(2);
	}

	/// Logout from office
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	logging out.
	 */
	function office()
	{
		try {
			$this->user_auth->logoutOffice();
			$this->messages->AddMessage('success','You have successfully left the office');
		} catch (Exception $e) {
			$CI->messages->AddMessage('error',$e->getMessage());
		}
		
		RedirectUriTail(2);
	}
	
	/// Logout from facebook api
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	logging out.
	 */
	function facebook()
	{
		$this->load->library('facebook');
		$this->facebook->Disable();
		
		RedirectUriTail(2);
	}
}
?>
