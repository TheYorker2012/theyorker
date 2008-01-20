<?php
/**
 *  @file tickets.php
 *  @author Richard Ingle (ri504)
 *  Contains public site controller class
 */

class Tickets extends Controller {

	function __construct()
	{
		parent::Controller();
		/// Used in all all page requests
	}
	
	/**
	 *  @brief Listing of events with tickets on sale
	 */
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('tickets');
		
		$data = array();
				
		// Set up the public frame
		$this->main_frame->SetContentSimple('tickets/listing', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
