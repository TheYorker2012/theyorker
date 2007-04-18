<?php

/// Main login controller.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 * 
 * Used for logging in and related functions.
 */
class Login extends Controller
{
	/// Default constructor
	function __construct()
	{
		parent::Controller();
	}
	
	/// Redirect to the uri given after the initial login/.../
	/**
	 * @note Duplicated from logout
	 */
	function _redirect($FirstSegment = 2)
	{
		return implode('/', array_slice($this->uri->rsegment_array(), $FirstSegment));
	}

	/// Main login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function main()
	{
		if (!CheckPermissions('public', FALSE, TRUE)) return;
		
		LoginHandler('student', $this->_redirect());
	}

	/// VIP login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function vip()
	{
		if (!CheckPermissions('student', FALSE, TRUE)) return;
		
		LoginHandler('vip', $this->_redirect());
	}

	/// VIP login screen for specific organisation.
	/**
	 * @param $Organisation string Organisation code.
	 *
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function vipswitch($Organisation)
	{
		if (!CheckPermissions('student', FALSE, TRUE)) return;
		
		LoginHandler('vip', $this->_redirect(3), $Organisation);
	}

	/// Office login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function office()
	{
		if (!CheckPermissions('student', FALSE, TRUE)) return;
		
		LoginHandler('office', $this->_redirect());
	}
	
	/// Facebook login.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function facebook()
	{
		$this->load->library('facebook');
		$this->facebook->Enable();
		
		redirect($this->_redirect());
	}
}
?>
