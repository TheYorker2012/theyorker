<?php
/**
 * This controller displays the A to Z page.
 * It uses the Atoz_model, and the character_lib to display an interactive A to Z page.
 *
 * @author Nick Evans
 */
class Atoz extends Controller {
	
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
		
		$this->load->model('Atoz_model'); //Load the model

		$this->load->library('character_lib'); //This character libary is used by the view, so load it here
	}
	
	/**
	 * Displays the AtoZ for organisations of a particular type
	 * @param organisation_type_id specifies the filter on organisation_type_id.
	 */
	function type($organisation_type_id)
	{
		$data = array(
			'organisation_array' => $this->Atoz_model->get_all_organisations_of_type($organisation_type_id)
		);
		
		// Set up the public frame
		$this->frame_public->SetTitle('A to Z');
		$this->frame_public->SetContentSimple('general/atoz', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/**
	 * Displays the AtoZ for all organisations
	 */
	function directory()
	{
		$data = array(
			'organisation_array' => $this->Atoz_model->get_all_organisations()
		);
		// Set up the public frame
		$this->frame_public->SetTitle('A to Z');
		$this->frame_public->SetContentSimple('general/atoz', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

}

?>