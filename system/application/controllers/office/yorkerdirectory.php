<?php

/// Yorker directory.
/**
 * @author Owen Jones (oj502@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The URI /directory maps to this controller (see config/routes.php).
 *
 * Any 2nd URI segment is sent to Yorkerdirectory::view (see config/routes.php).
 *
 * Any 3rd URI segment (e.g. events) is sent to the function with the same value.
 *	(see config/routes.php).
 */
class Yorkerdirectory extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		// Make use of the public frame
		$this->load->library('frame_public');
		$this->load->library('organisations');

		$this->load->model('directory_model');

		$this->load->helper('text');
		$this->load->helper('wikilink');
	}

	/// Directory index page.
	/**
	 * @note POST data:
	 *	- 'search' (search pattern, optional)
	 */
	function index()
	{
		$this->pages_model->SetPageCode('office_directory_index');
		
		$data = array();
		
		$data['maintext'] = $this->pages_model->GetPropertyText('maintext');

		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well
		$data['organisations'] = $this->organisations->_GetOrgs($search_pattern, 'office/reviews/');
		$data['search'] = $search_pattern;
		
		// Get organisation types
		$data['organisation_types'] = $this->organisations->_GetOrganisationTypes($data['organisations'], TRUE);

		//Libary for AtoZ system
		$this->load->library('character_lib'); //This character libary is used by the view, so load it here

		// Set up the directory view
		$directory_view = $this->frames->view('directory/directory', $data);

		// Set up the public frame to use the directory view
		$this->frame_public->SetContent($directory_view);

		// Load the public frame view
		$this->frame_public->Load();
	}
}
?>
