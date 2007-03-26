<?php

/**
 *	Provides the Yorker Office - Photo Request Functionality
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Photos extends Controller
{
	/**
	 *	@brief Default constructor
	 */
	function __construct()
	{
		parent::Controller();

	}

	/**
	 *	@brief Load photo request pool page
	 */
	function index()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		$data['test'] = 'test';

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/photos/home', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	/**
	 *	@brief Load photo request details
	 */
	function view()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		$data['test'] = 'test';

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/photos/view', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

}

?>