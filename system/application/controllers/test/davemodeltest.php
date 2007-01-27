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
		$reviews = $this->review->GetReview('boxing_club','lifestyle');
		
		// Set up the public frame
		$this->frame_public->SetTitle('Review...');
		$this->frame_public->SetContentSimple('test/davemodeltest',$reviews);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
		
	}
}
?>
