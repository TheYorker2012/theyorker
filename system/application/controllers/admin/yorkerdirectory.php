<?php

/// Yorker directory admin controller.
/**
 * @author Owen Jones (oj502@york.ac.uk)
 * 
 * The URI /admin/directory maps to this controller this will be an admin menu when it is made
 */
class Yorkerdirectory extends Controller
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
		// Set up the public frame
		$this->frame_public->SetTitle('Directory Admin');
		$this->frame_public->SetContentSimple('directory/admin_directory');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	function view()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Directory Admin');
		$this->frame_public->SetContentSimple('directory/admin_directory_view');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>