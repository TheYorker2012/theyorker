<?php

class Faq extends Controller {

	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('Frequently Asked Questions');
		$this->main_frame->SetContentSimple('faq/faq');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
    }
}
?>
