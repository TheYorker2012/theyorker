<?php

class DaveModelTest extends Controller {

	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();

		// Load the public frame
		$this->load->library('frame_public');
	}

	/**
	 * @brief ModelTest test page.
	 */
	function index()
	{
		$this->load->model('review_model','review');

		//Load data from model

		$reviews['areview'] = $this->review->GetReview('evil_eye_lounge','food');
		$reviews['league'] = $this->review->GetLeague('splashing_out');

		// Set up the public frame
		$this->frame_public->SetTitle('Reviews...');
		$this->frame_public->SetContentSimple('test/davemodeltest',$reviews);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();

	}
}
?>
