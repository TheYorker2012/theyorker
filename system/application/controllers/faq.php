<?php

class Faq extends Controller {

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
    function index()
    {
		// Set up the public frame
		$this->frame_public->SetTitle('Frequently Asked Questions');
		$this->frame_public->SetContentSimple('faq/faq');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }
}
?>
