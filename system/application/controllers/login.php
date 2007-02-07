<?php

class Login extends Controller
{

	function __construct()
	{
		parent::Controller();
	}
	
	/// Redirect to the uri given after the initial logout/.../
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

	function main()
	{
		if (!CheckPermissions('student')) return;
		
		$this->_redirect();
	}

	function resetpassword()
	{
		if (!CheckPermissions('public')) return;
		
		$data = array();
		
		// Set up the public frame
		$this->main_frame->SetTitle('Reset My Password');
		$this->main_frame->SetContentSimple('login/resetpassword', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
