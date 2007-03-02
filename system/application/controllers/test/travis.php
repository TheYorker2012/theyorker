<?php

/**
 *	@brief My testing page :)
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Travis extends Controller {

	/**
	 *	@brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
	}

	/**
	 * @brief Testing testing 1...2...3 ;)
	 */
	function index()
	{
		if (!CheckPermissions('office')) return;
		

		$data['height_hour'] = 40;
		$data['width_time_col'] = 30;
		$data['width_day_col'] = 85;

		// Set up the public frame
		$this->main_frame->SetTitle('Travis\' Ideas Page :)');
		$this->main_frame->SetContentSimple('test/travis', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>