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
	function _redirect($FirstSegment = 3)
	{
		$segments = $this->uri->segment_array();
		while ($FirstSegment > 1) {
			array_shift($segments);
			--$FirstSegment;
		}
		return implode('/',$segments);
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
		if (!CheckPermissions('public', FALSE, TRUE)) return;
		
		LoginHandler('vip', $this->_redirect());
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

	/// Page for resetting password.
	function resetpassword()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('reset_password');
		
		$data = array();
		
		// Set up the public frame
		$this->main_frame->SetContentSimple('login/resetpassword', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
