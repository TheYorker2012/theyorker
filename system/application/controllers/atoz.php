<?php
/**
 * This controller displays the A to Z page.
 * It uses the Atoz_model, and the character_lib to display an interactive A to Z page.
 *
 * \author Nick Evans
 */
class Atoz extends Controller {
	/**
	 * Displays the AtoZ for organisations of a particular type
	 * \param organisation_type_id specifies the filter on organisation_type_id.
	 */
	function type($organisation_type_id)
	{
		$this->load->model('Atoz_model'); //Load the model
		
		$this->load->library('character_lib'); //This character libary is used by the view, so load it here

		$data = array(
			'content_view' => 'general/atoz',
			'organisation_array' => $this->Atoz_model->get_all_organisations_of_type($organisation_type_id)
		);
		$this->load->view('frames/student_frame',$data);
	}
}

?>