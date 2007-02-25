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
		
		$this->load->library('organisations');
		$this->load->helpers('images');
		$this->load->model('directory_model');

		$this->load->helper('text');
		$this->load->helper('images');
		$this->load->helper('wikilink');
	}

	/// Directory index page.
	/**
	 * @note Shows error 404 when accessed from viparea
	 */
	function index()
	{
		if (!CheckPermissions('office')) return;
		
		$this->_office();
	}
	
	/// office/directory
	function _office()
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
		$directory_view = $this->frames->view('directory/viparea_directory', $data);

		// Set up the public frame to use the directory view
		$this->main_frame->SetContent($directory_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	/// Directory organisation page.
	function information($action='view', $revision=false)
	{
		if (!CheckPermissions('vip+pr')) return;
		if ($action=='view'){
			$organisation = $this->user_auth->organisationShortName;
			$this->pages_model->SetPageCode('viparea_directory_information');
			//Get Organisation Data
			$data = $this->organisations->_GetOrgData($organisation, $revision);
			
			//Send data if given
			if(!empty($_POST['submitbutton'])){
				$this->main_frame->AddMessage('success','Directory entry updated.');
				if($_POST['description']==null){
					$this->main_frame->AddMessage('information','About field is blank we advise you add some detail.');
				}
				$this->directory_model->AddDirectoryEntryRevision($organisation, $_POST);
			}
			//Show hide directory entry information and form detection
			if(!empty($_POST['directory_visibility'])){
				if($_POST['directory_visibility']=="Show Entry"){
					$this->directory_model->MakeDirectoryEntryVisible($organisation);
					$this->main_frame->AddMessage('success','Directory entry made visible.');
				}
				if($_POST['directory_visibility']=="Hide Entry"){
					$this->directory_model->MakeDirectoryEntryVisible($organisation, false);
					$this->main_frame->AddMessage('success','Directory entry hidden.');
				}
			}
			
			//Find out if the directory entry is currently visable.
			$data['directory_visibility'] = $this->directory_model->IsEntryShownInDirectory($organisation);
			if($data['directory_visibility']){
				$data['directory_visibility_text'] = $this->pages_model->GetPropertyText('directory_visible_true');
			}else{
				$data['directory_visibility_text'] = $this->pages_model->GetPropertyText('directory_visible_false');
			}
			
			if (!empty($data)) {
				$this->_SetupNavbar();
				
				// Insert main text from pages information
				$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
				
				//Page Revisions
				$data['revisions'] = $this->directory_model->GetRevisonsOfDirectoryEntry($organisation);
				
				// Set up the directory view
				$the_view = $this->frames->view('directory/viparea_directory_information', $data);
				
				// Set up the public frame
				$this->main_frame->SetTitleParameters(
						array('organisation' => $data['organisation']['name']));
				$this->main_frame->SetPage('information');
				$this->main_frame->SetContent($the_view);
			} else {
				$this->load->library('custom_pages');
				$this->main_frame->SetContent(new CustomPageView('directory_notindirectory','error'));
			}
			
			// Load the public frame view
			$this->main_frame->Load();
		}
		if($action=='publish'){
			$organisation = $this->user_auth->organisationShortName;
			$this->pages_model->SetPageCode('viparea_directory_publish');
			
			//Send and get data
			$this->directory_model->PublishDirectoryEntryRevisionById($organisation, $revision);
			
			$data = $this->organisations->_GetOrgData($organisation, $revision);
			
			if (!empty($data)) {
				$this->_SetupNavbar();
				
				// Set up the directory view
				$the_view = $this->frames->view('directory/viparea_directory_publish', $data);
				
				// Set up the public frame
				$this->main_frame->SetTitleParameters(
						array('organisation' => $data['organisation']['name']));
				$this->main_frame->SetPage('information');
				$this->main_frame->SetContent($the_view);
			} else {
				$this->load->library('custom_pages');
				$this->main_frame->SetContent(new CustomPageView('directory_notindirectory','error'));
			}
			
			// Load the public frame view
			$this->main_frame->Load();
		}
		if($action=='delete'){
			$organisation = $this->user_auth->organisationShortName;
			$this->pages_model->SetPageCode('viparea_directory_delete');
			
			$result = $this->directory_model->DeleteEntryRevisionById($organisation, $revision);
			
			//Delete entry
			$data = $this->organisations->_GetOrgData($organisation, $revision);
			
			if (!empty($data)) {
			$this->_SetupNavbar();
			
				$data['result']=$result;
				
				// Set up the directory view
				$the_view = $this->frames->view('directory/viparea_directory_delete', $data);
				
				// Set up the public frame
				$this->main_frame->SetTitleParameters(
						array('organisation' => $data['organisation']['name']));
				$this->main_frame->SetPage('information');
				$this->main_frame->SetContent($the_view);
			} else {
				$this->load->library('custom_pages');
				$this->main_frame->SetContent(new CustomPageView('directory_notindirectory','error'));
			}
			
			// Load the public frame view
			$this->main_frame->Load();
		}
	}
	
	function photos()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$organisation = $this->user_auth->organisationShortName;
		$this->pages_model->SetPageCode('viparea_directory_photos');
		
		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);

		if (!empty($data)) {
			$this->_SetupNavbar();
			
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
				$this->main_frame->SetPage('photos');
			$this->main_frame->SetContent($the_view);
		} else {
			$this->load->library('custom_pages');
			$this->main_frame->SetContent(new CustomPageView('directory_notindirectory','error'));
		}
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function map()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$organisation = $this->user_auth->organisationShortName;
		$this->pages_model->SetPageCode('viparea_directory_map');
		
		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);
		
		if (!empty($data)) {
			$this->_SetupNavbar();
			
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['map_text'] = $this->pages_model->GetPropertyWikitext('map_text');
			
			// Set up the directory view
			$the_view = $this->frames->view('directory/viparea_directory_map', $data);
			
			// Set up the public frame
			$this->main_frame->SetTitleParameters(
					array('organisation' => $data['organisation']['name']));
			$this->main_frame->SetPage('map');
			$this->main_frame->SetContent($the_view);
		} else {
			$this->load->library('custom_pages');
			$this->main_frame->SetContent(new CustomPageView('directory_notindirectory','error'));
		}
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function contacts($business_card_group=-1)
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$organisation = $this->user_auth->organisationShortName;
		$this->pages_model->SetPageCode('viparea_directory_contacts');
		
		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);
		
		if (!empty($data)) {
			$this->_SetupNavbar();
			
			// Business Card Groups
			$groups = $this->directory_model->GetDirectoryOrganisationCardGroups($organisation);
			// translate into nice names for view
			$data['organisation']['groups'] = array();
			foreach ($groups as $group) {
				$data['organisation']['groups'][] = array(
					'name' => $group['business_card_group_name'],
					'href' => vip_url('directory/contacts/'.$group['business_card_group_id']),
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
					'user_id' => $member['business_card_user_entity_id'],
					'id' => $member['business_card_id'],
					'name' => $member['business_card_name'],
					'title' => $member['business_card_title'],
					'course' => $member['business_card_course'],
					'blurb' => $member['business_card_blurb'],
					'email' => $member['business_card_email'],
					'image_id' => $member['business_card_image_id'],
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
			$this->main_frame->SetPage('contacts');
			$this->main_frame->SetContent($the_view);
		} else {
			$this->load->library('custom_pages');
			$this->main_frame->SetContent(new CustomPageView('directory_notindirectory','error'));
		}
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editcontact($business_card)
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$organisation = $this->user_auth->organisationShortName;
		$this->pages_model->SetPageCode('viparea_directory_editcontact');
			
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
		$this->_SetupNavbar();

		// Business Card Groups
		$groups = $this->directory_model->GetDirectoryOrganisationCardGroups($organisation);
		// translate into nice names for view
		$data['business_card']['groups'] = array();
		foreach ($groups as $group) {
			$data['business_card']['groups'][] = array(
				'name' => $group['business_card_group_name'],
				'id' => $group['business_card_group_id'],
				'href' => vip_url('directory/'.$organisation.'/contacts/'.$group['business_card_group_id'])
			);
		}
		
		// Set up the directory view
		$the_view = $this->frames->view('directory/viparea_directory_contacts', $data);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->main_frame->SetPage('contacts');
		$this->main_frame->SetContent($the_view);
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	/// Set up the navigation bar.
	/**
	 * @param $DirectoryEntry Directory entry of organisation.
	 */
	private function _SetupNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('contacts', 'Contacts',
				vip_url('directory/contacts'));
		$navbar->AddItem('map', 'Map',
				vip_url('directory/map'));
		$navbar->AddItem('photos', 'Photos',
				vip_url('directory/photos'));
		$navbar->AddItem('information', 'Information',
				vip_url('directory/information'));
	}

}
?>
