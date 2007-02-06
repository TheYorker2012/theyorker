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
		
		SetupMainFrame('vip');
		
		$this->load->library('organisations');
		$this->load->helpers('images');
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

		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('contacts', 'Contacts',
				'/viparea/directory/'.$DirectoryEntry.'/contacts');
		$navbar->AddItem('map', 'Map',
				'/viparea/directory/'.$DirectoryEntry.'/map');
		$navbar->AddItem('photos', 'Photos',
				'/viparea/directory/'.$DirectoryEntry.'/photos');
		$navbar->AddItem('information', 'Information',
				'/viparea/directory/'.$DirectoryEntry.'/information');
	}

	/// Directory index page.
	// this is blank lol
	function index()
	{
	}

	/// Directory organisation page.
	function information($organisation)
	{
		if (CheckPermissions('vip')) {
			$this->pages_model->SetPageCode('vip_area_directory_information');
			
			//Get Data And toolbar
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupOrganisationFrame($organisation);
			
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			
			// Set up the directory view
			$the_view = $this->frames->view('directory/viparea_directory_information', $data);
			
			// Set up the public frame
			$this->main_frame->SetTitleParameters(
					array('organisation' => $data['organisation']['name']));
			$this->main_frame->SetContent($the_view);
		}
		// Load the public frame view
		$this->main_frame->Load();
	}
	function photos($organisation)
	{
		if (CheckPermissions('vip')) {
			$this->pages_model->SetPageCode('vip_area_directory_photos');
			
			//Get Data And toolbar
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupOrganisationFrame($organisation);

			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['disclaimer_text'] = $this->pages_model->GetPropertyWikitext('disclaimer_text');
			$data['oraganisation'] = $organisation;
			$data['images'] = array ( //data sent in order
								array(
									'id' => 32,
									'url' => photoLocation(32),
								),
								array(
									'id' => 32,
									'url' => photoLocation(32),
								),
								array(
									'id' => 32,
									'url' => photoLocation(32),
								),
							);
			
			// Set up the directory view
			$the_view = $this->frames->view('directory/viparea_directory_photos', $data);
			
			// Set up the public frame
			$this->main_frame->SetTitleParameters(
					array('organisation' => $data['organisation']['name']));
			$this->main_frame->SetContent($the_view);
		}
		// Load the public frame view
		$this->main_frame->Load();
	}
	function map($organisation)
	{
		if (CheckPermissions('vip')) {
			$this->pages_model->SetPageCode('vip_area_directory_map');
			
			//Get Data And toolbar
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupOrganisationFrame($organisation);
			
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['map_text'] = $this->pages_model->GetPropertyWikitext('map_text');
			
			// Set up the directory view
			$the_view = $this->frames->view('directory/viparea_directory_map', $data);
			
			// Set up the public frame
			$this->main_frame->SetTitleParameters(
					array('organisation' => $data['organisation']['name']));
			$this->main_frame->SetContent($the_view);
		}
			
		// Load the public frame view
		$this->main_frame->Load();
	}
	function contacts($organisation,$business_card_group=-1)
	{
		if (CheckPermissions('vip')) {
			$this->pages_model->SetPageCode('vip_area_directory_contacts');
			
			//Get Data And toolbar
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupOrganisationFrame($organisation);

			// Business Card Groups
			$groups = $this->directory_model->GetDirectoryOrganisationCardGroups($organisation);
			// translate into nice names for view
			$data['organisation']['groups'] = array();
			foreach ($groups as $group) {
				$data['organisation']['groups'][] = array(
					'name' => $group['business_card_group_name'],
					'id' => $group['business_card_group_id']
				);
				if ($business_card_group==-1) $business_card_group = $group['business_card_group_id'];
			}
					
			// Members data
			$members = $this->directory_model->GetDirectoryOrganisationCardsByGroupId($business_card_group);
			// translate into nice names for view
			$data['organisation']['cards'] = array();
			foreach ($members as $member) {
				$data['organisation']['cards'][] = array(
					'id' => $member['business_card_id'],
					'name' => $member['business_card_name'],
					'title' => $member['business_card_title'],
					'course' => $member['business_card_course'],
					'blurb' => $member['business_card_blurb'],
					'email' => $member['business_card_email'],
					'phone_mobile' => $member['business_card_mobile'],
					'phone_internal' => $member['business_card_phone_internal'],
					'phone_external' => $member['business_card_phone_external'],
					'postal_address' => $member['business_card_postal_address']
				);
			}
			
			//Put the view in edit mode
			$data['organisation']['editmode'] = true;
			
			// Set up the directory view
			$the_view = $this->frames->view('directory/directory_view_members', $data);
			
			// Set up the public frame
			$this->main_frame->SetTitleParameters(
					array('organisation' => $data['organisation']['name']));
			$this->main_frame->SetContent($the_view);
		}
		// Load the public frame view
		$this->main_frame->Load();
	}
	function editcontact($organisation,$business_card)
	{
		if (CheckPermissions('vip')) {
			$this->pages_model->SetPageCode('vip_area_directory_editcontact');
				
			$organisation = ''; // Throw this away and retrieve it from the $business_card instead for security
			
			// Members data
			$members = $this->directory_model->GetDirectoryOrganisationCardsById($business_card);
			// translate into nice names for view
			foreach ($members as $member) {
				$data['editmember'] = array(
					'name' => $member['business_card_name'],
					'title' => $member['business_card_title'],
					'course' => $member['business_card_course'],
					'group_id' => $member['business_card_business_card_group_id'],
					'blurb' => $member['business_card_blurb'],
					'email' => $member['business_card_email'],
					'phone_mobile' => $member['business_card_mobile'],
					'phone_internal' => $member['business_card_phone_internal'],
					'phone_external' => $member['business_card_phone_external'],
					'postal_address' => $member['business_card_postal_address']
				);
				$organisation = $member['organisation_directory_entry_name'];
			}
			
			//Get Data And toolbar
			$data += $this->organisations->_GetOrgData($organisation);
			$this->_SetupOrganisationFrame($organisation);

			// Business Card Groups
			$groups = $this->directory_model->GetDirectoryOrganisationCardGroups($organisation);
			// translate into nice names for view
			$data['business_card']['groups'] = array();
			foreach ($groups as $group) {
				$data['business_card']['groups'][] = array(
					'name' => $group['business_card_group_name'],
					'id' => $group['business_card_group_id'],
					'href' => '/directory/'.$organisation.'/members/'.$group['business_card_group_id']
				);
			}
			
			// Set up the directory view
			$the_view = $this->frames->view('directory/viparea_directory_contacts', $data);
			
			// Set up the public frame
			$this->main_frame->SetTitleParameters(
					array('organisation' => $data['organisation']['name']));
			$this->main_frame->SetContent($the_view);
		}
		// Load the public frame view
		$this->main_frame->Load();
	}

}
?>
