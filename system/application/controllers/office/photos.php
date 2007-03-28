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

		/// Get custom page content
		$this->pages_model->SetPageCode('office_photos');

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

   		/// Get custom page content
		$this->pages_model->SetPageCode('office_photos');

		/// Set up the main frame
		/// Using seperate views for mockups to make it more clear what options should be
		/// displayed/hidden for each user
		if ($this->uri->segment(4) == 'editor') {
			$this->main_frame->SetContentSimple('office/photos/view-editor', $data);
		} elseif ($this->uri->segment(4) == 'all') {
			$this->main_frame->SetContentSimple('office/photos/view-everyone', $data);
		} elseif ($this->uri->segment(4) == 'reporter') {
			$this->main_frame->SetContentSimple('office/photos/view-reporter', $data);
		} elseif ($this->uri->segment(4) == 'photographer') {
			$this->main_frame->SetContentSimple('office/photos/view-photographer', $data);
		} elseif ($this->uri->segment(4) == 'flagged') {
			$this->main_frame->SetContentSimple('office/photos/view-flagged', $data);
		} elseif ($this->uri->segment(4) == 'completed') {
			$this->main_frame->SetContentSimple('office/photos/view-completed', $data);
		} else {
			$this->main_frame->SetContentSimple('office/photos/view', $data);
		}

		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

}

?>