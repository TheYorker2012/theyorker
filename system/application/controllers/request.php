<?php

class Request extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
	}
	
    function index()
    {
		if (!CheckPermissions('public')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('Request');
		$this->main_frame->SetContentSimple('request/request');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
    }

    function upload()
    {
		if (!CheckPermissions('public')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('Upload');
		$this->main_frame->SetContentSimple('request/upload');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
    }

    function crop()
    {
		if (!CheckPermissions('public')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('Crop');
		$this->main_frame->SetContentSimple('request/crop');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
    }

}
?>
