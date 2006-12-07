<?php

class Howdoi extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
    function index()
    {
		// Set up the public frame
		$this->frame_public->SetTitle('How do I?');
		$this->frame_public->SetContentSimple('howdoi/howdoi');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }

    function view()
    {
		// Set up the public frame
		$this->frame_public->SetTitle('How do I?');
		$this->frame_public->SetContentSimple('howdoi/view');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }

}
?>
