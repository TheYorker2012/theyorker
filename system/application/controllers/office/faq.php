<?php

/// FAQ admin controller.
class Faq extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
	}
	
	function add()
	{
		if (!CheckPermissions('office')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('FAQ Admin');
		$this->main_frame->SetContentSimple('faq/addfaq');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	/// admin page to edit entry in faq
	function edit()
	{
		if (!CheckPermissions('office')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('FAQ Admin');
		$this->main_frame->SetContentSimple('faq/editfaq');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>