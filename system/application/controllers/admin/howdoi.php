<?php

/// How Do I? admin controller.
class Howdoi extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	/// admin page to add entry to howdoi
	function add()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('How Do I? Admin');
		$this->frame_public->SetContentSimple('howdoi/addhowdoi');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>