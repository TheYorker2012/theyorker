<?php

class Request extends Controller {

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
		$this->frame_public->SetTitle('Request');
		$this->frame_public->SetContentSimple('request/request');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }

    function upload()
    {
		// Set up the public frame
		$this->frame_public->SetTitle('Upload');
		$this->frame_public->SetContentSimple('request/upload');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }

    function crop()
    {
		// Set up the public frame
		$this->frame_public->SetTitle('Crop');
		$this->frame_public->SetContentSimple('request/crop');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }

}
?>
