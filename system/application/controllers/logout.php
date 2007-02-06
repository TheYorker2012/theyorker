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
		
		SetupMainFrame('public');
	}
	
	/// Redirect to the uri given after the initial logout/.../
	function _redirect($FirstSegment = 3)
	{
		$uri_target = '';
		for ($segment_counter = $FirstSegment; $segment_counter <= $this->uri->total_segments(); ++$segment_counter) {
			$uri_target .= $this->uri->slash_segment($segment_counter);
		}
		redirect($uri_target);
	}

	/// Logout from main account
	function main()
	{
		$this->user_auth->logout();
		$this->main_frame->AddMessage('success','You have successfully logged out');
		
		$this->_redirect();
	}

	/// Logout from office
	function office()
	{
		$this->user_auth->logoutOffice();
		$this->main_frame->AddMessage('success','You have successfully left the office');
		
		$this->_redirect();
	}

	/// Logout from vip area
	function viparea()
	{
		$this->user_auth->logoutOrganisation();
		$this->main_frame->AddMessage('success','You have successfully left the VIP area');
		
		$this->_redirect();
	}
}
?>
