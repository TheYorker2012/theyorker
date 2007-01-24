<?php

/// FAQ admin controller.
class Faq extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	function add()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('FAQ Admin');
		$this->frame_public->SetContentSimple('faq/addfaq');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	/// admin page to edit entry in faq
	function edit()
	{
	
		// Set up the public frame
		$this->frame_public->SetTitle('FAQ Admin');
		$this->frame_public->SetContentSimple('faq/editfaq');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>