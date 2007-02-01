<?php

/**
 * @brief message test controller.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Msg extends Controller {
	
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	/**
	 * @brief Messages test page.
	 */
	function index()
	{
		$this->frame_public->AddMessage('success','Something has worked');
		$this->frame_public->AddMessage('information','This is the case');
		$this->frame_public->AddMessage('warning','Be careful');
		$this->frame_public->AddMessage('error','You did it wrong or we did it wrong');
		
		// Set up the public frame
		$this->frame_public->SetTitle('Messages test');
		$this->frame_public->SetContentSimple('general/home');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
