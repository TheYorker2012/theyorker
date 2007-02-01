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

	/// Set up the directory frame
	/**
	 * @param $OrganisationData Organisation data array.
	 * @pre @a $OrganisationData is valid organisation array.
	 * @post Frame_directory frame is loaded and ready to use.
	 */
	private function _SetupOrganisationFrame($DirectoryEntry)
	{
		$this->load->library('frame_directory');
	}

	/// Directory index page.
	/**
	 * @note POST data:
	 *	- 'search' (search pattern, optional)
	 */
	function index()
	{
		$this->pages_model->SetPageCode('directory_index');
		
		$data = array();
		
		$data['maintext'] = $this->pages_model->GetPropertyText('maintext');

		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well
		$data['organisations'] = $this->organisations->_GetOrgs($search_pattern);
		$data['search'] = $search_pattern;
		
		// Get organisation types
		$data['organisation_types'] = $this->organisations->_GetOrganisationTypes($data['organisations']);

		//Libary for AtoZ system
		$this->load->library('character_lib'); //This character libary is used by the view, so load it here

		// Set up the directory view
		$directory_view = $this->frames->view('directory/directory', $data);

		// Set up the public frame to use the directory view
		$this->frame_public->SetTitle('Admin Directory');
		$this->frame_public->SetContent($directory_view);

		// Load the public frame view
		$this->frame_public->Load();
	}

	/// Directory organisation page.
	function view($organisation)
	{
		$this->pages_model->SetPageCode('admin_directory_view');
		
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($organisation);

		$subpageview='directory/admin_directory_view';

		// Members data
		$members = $this->directory_model->GetDirectoryOrganisationCardsByEntryName($organisation);
		// translate into nice names for view
		$data['organisation']['cards'] = array();
		foreach ($members as $member) {
			$data['organisation']['cards'][] = array(
				'name' => $member['business_card_name'],
				'title' => $member['business_card_title'],
				#'course' => $member['business_card_course'],
				'blurb' => $member['business_card_blurb'],
				'email' => $member['business_card_email'],
				'phone_mobile' => $member['business_card_mobile'],
				'phone_internal' => $member['business_card_phone_internal'],
				'phone_external' => $member['business_card_phone_external'],
				'postal_address' => $member['business_card_postal_address'],
				'colours' => array(
					'background' => $member['business_card_colour_background'],
					'foreground' => $member['business_card_colour_foreground'],
				),
				'type' => $member['business_card_type_name'],
			);
		}

		// Set up the directory view
		$directory_view = $this->frames->view($subpageview, $data);

		// Set up the directory frame to use the directory events view
		$this->frame_directory->SetOrganisation($data['organisation']);
		$this->frame_directory->SetContent($directory_view);

		// Set up the public frame to use the directory view
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->frame_public->SetContent($this->frame_directory);

		// Load the public frame view
		$this->frame_public->Load();
	}
}
?>
