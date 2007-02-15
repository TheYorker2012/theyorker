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
		
		$this->load->library('messages');
		$this->load->library('user_auth');
	}
	
	/// Redirect to the uri given after the initial logout/.../
	/**
	 * @note Duplicated from login
	 */
	function _redirect($FirstSegment = 3)
	{
		$uri_target = '';
		for ($segment_counter = $FirstSegment; $segment_counter <= $this->uri->total_segments(); ++$segment_counter) {
			$uri_target .= $this->uri->slash_segment($segment_counter);
		}
		redirect($uri_target);
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
		
		$this->_redirect();
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
		
		$this->_redirect();
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
		
		$this->_redirect();
	}
}
?>
