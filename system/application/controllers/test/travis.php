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

		$data['startdate'] = mktime(0,0,0,2,25,2007);

		// Assuming ordered by date/time ASC
		$data['events'][] = array(
			'day' => 'sat',
			'title' => 'Meeting martians!',
			'start_date' => mktime(14,30,0,2,28,2007),
			'end_date' => mktime(17,00,0,2,28,2007), 
			'location' => 'Mars'		
		);

		// Set up the public frame
		$this->main_frame->SetTitle('Travis\' Ideas Page :)');
		$this->main_frame->SetContentSimple('test/travis', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>