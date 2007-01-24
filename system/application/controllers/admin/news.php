<?php

/// News admin controller.
class News extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	function index()
	{
		$admin_view_name = 'news/admin_news';
		
		// Set up the public frame
		$this->frame_public->SetTitle('News Admin');
		$this->frame_public->SetContentSimple($admin_view_name);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	function request($Segment4)
	{
		switch ($Segment4) {
			case 'view':
				$admin_view_name = 'news/admin_request_view';
				break;
			default:
				$admin_view_name = 'news/admin_request_new';
				break;
		}
		
		// Set up the public frame
		$this->frame_public->SetTitle('News Admin');
		$this->frame_public->SetContentSimple($admin_view_name);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>