<?php

class DaveModelTest extends Controller {

	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
	}

	/**
	 * @brief ModelTest test page.
	 */
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->model('review_model','review');

		//Load data from model

		$reviews['areview'] = $this->review->GetReview('evil_eye_lounge','food');
		$reviews['league'] = $this->review->GetLeague('splashing_out');

		// Set up the public frame
		$this->main_frame->SetTitle('Reviews...');
		$this->main_frame->SetContentSimple('test/davemodeltest',$reviews);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();

	}
}
?>
