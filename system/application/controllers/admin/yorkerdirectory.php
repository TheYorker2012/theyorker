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

		$this->load->model('directory_model');

		$this->load->helper('text');
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
		$data['organisations'] = $this->_GetOrgs($search_pattern);
		$data['search'] = $search_pattern;
		
		// Get organisation types
		$data['organisation_types'] = $this->_GetOrganisationTypes($data['organisations']);

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
		
		$data = $this->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($organisation);

		$subpageview='directory/admin_directory_view';

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

	/// Get organisation types from organisations.
	/**
	 * @param $Organisations array Organisations as returned by _GetOrgs.
	 * @return array of organisation types.
	 */
	private function _GetOrganisationTypes($Organisations)
	{
		$types = array();
		foreach ($Organisations as $organisation) {
			$types[$organisation['type']] = TRUE;
		}
		$result = array();
		foreach ($types as $type => $enabled) {
			$result[] = array(
				'id' => $type,
				'name' => $type,
			);
		}
		return $result;
	}

	/// Temporary function get organisations.
	/**
	 * @param $Pattern string/bool Search pattern or FALSE if all.
	 * @return array of organisations matching pattern.
	 */
	private function _GetOrgs($Pattern)
	{
		$org_description_words = $this->pages_model->GetPropertyInteger('org_description_words', 5);
		
		$orgs = $this->directory_model->GetDirectoryOrganisations();
		$organisations = array();
		foreach ($orgs as $org) {
			$organisations[] = array(
				'name' => $org['organisation_name'],
				'shortname' => $org['organisation_directory_entry_name'],
				'link' => 'admin/directory/'.$org['organisation_directory_entry_name'],
				'description' => $org['organisation_description'],
				'shortdescription' => word_limiter(
					$org['organisation_description'], $org_description_words),
				'type' => $org['organisation_type_name'],
			);
		}
		if ($Pattern !== FALSE) {
			$organisations = array(
				array(
					'shortname'   => 'pole_dancing',
					'name'        => 'Pole Dancing',
					'description' => 'A fitness club',
					'type'        => 'Athletics Union',
				),
				array(
					'shortname'   => 'costcutter',
					'name'        => 'Costcutter',
					'description' => 'Campus shop',
					'type'        => 'College & Campus',
				),
			);
		}
		return $organisations;
	}

	/// Temporary function get organisation data.
	/**
	 * @param $OrganisationShortName Short name of organisation.
	 * @return Organisation data relating to specified organisation or FALSE.
	 */
	private function _GetOrgData($OrganisationShortName)
	{
		$data = array();

		$orgs = $this->directory_model->GetDirectoryOrganisationByEntryName($OrganisationShortName);
		if (1 === count($orgs)) {
			foreach ($orgs as $org) {
				$data['organisation'] = array(
					'id'          => $org['organisation_entity_id'],
					'name'        => $org['organisation_name'],
					'shortname'   => $org['organisation_directory_entry_name'],
					'description' => $org['organisation_description'],
					'type'        => $org['organisation_type_name'],
					'website'     => $org['organisation_url'],
					'location'    => $org['organisation_location'],
					'open_times'  => $org['organisation_opening_hours'],
					'email_address'   => $org['organisation_email_address'],
					'postal_address'  => $org['organisation_postal_address'],
					'postcode'    => $org['organisation_postcode'],
					'phone_internal'  => $org['organisation_phone_internal'],
					'phone_external'  => $org['organisation_phone_external'],
					'fax_number'  => $org['organisation_fax_number'],


					'blurb'       => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla lorem magna, tincidunt sed, feugiat nec, consectetuer vitae, nisl. Vestibulum gravida ipsum non justo. Vivamus sem. Quisque ut sem vitae elit luctus lobortis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
				);
				if (NULL === $org['organisation_yorkipedia_entry']) {
					$data['organisation']['yorkipedia'] = NULL;
				} else {
					$data['organisation']['yorkipedia'] = array(
							'url'   => 'http://yorkipedia.theyorker.co.uk',
							'title' => $org['organisation_yorkipedia_entry'],
						);
				}
			}
		} else {
			$data['organisation'] = array(
				'shortname'   => $OrganisationShortName,
				'name'        => 'FragSoc',
				'description' => 'The people who run this website',
				'type'        => 'Organisation',
				'blurb'       => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla lorem magna, tincidunt sed, feugiat nec, consectetuer vitae, nisl. Vestibulum gravida ipsum non justo. Vivamus sem. Quisque ut sem vitae elit luctus lobortis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
				'website'     => 'http://www.fragsoc.com',
				'location'    => 'Goodricke College',
				'open_times'  => 'Every Other Weekend',
			);
		}
		return $data;
	}
}
?>
