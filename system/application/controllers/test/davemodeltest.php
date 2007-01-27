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
		$league  = $this->review->GetLeague('romance');
		
		// Set up the public frame
		$this->frame_public->SetTitle('I am going to cry if I can\'t make this work!');
		$this->frame_public->SetContentSimple('test/davemodeltest',$reviews);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
		
	}
}
?>
