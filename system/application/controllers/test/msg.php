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
	}
	
	/**
	 * @brief Messages test page.
	 */
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->messages->AddMessage('success','Something has worked');
		$this->messages->AddMessage('information','This is the case');
		$this->messages->AddMessage('warning','Be careful');
		$this->messages->AddMessage('error','You did it wrong or we did it wrong');
		
		// Set up the public frame
		$this->main_frame->SetTitle('Messages test');
		$this->main_frame->SetContentSimple('general/home');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
