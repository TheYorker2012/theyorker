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
		$uri_target = '';
		for ($segment_counter = $FirstSegment; $segment_counter <= $this->uri->total_segments(); ++$segment_counter) {
			$uri_target .= $this->uri->slash_segment($segment_counter);
		}
		redirect($uri_target);
	}

	/// Main login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function main()
	{
		if (!CheckPermissions('public')) return;
		
		if (LoginHandler('student')) {
			$this->_redirect();
		}
	}

	/// VIP login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function vip()
	{
		if (!CheckPermissions('public')) return;
		
		if (LoginHandler('vip')) {
			$this->_redirect();
		}
	}

	/// Office login screen.
	/**
	 * Any additional uri segments are used as the redirect address after
	 *	successful login.
	 */
	function office()
	{
		if (!CheckPermissions('student')) return;
		
		if (LoginHandler('office')) {
			$this->_redirect();
		}
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
