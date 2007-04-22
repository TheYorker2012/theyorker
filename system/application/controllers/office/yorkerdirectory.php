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

			$organisation = VipOrganisation();
			$this->pages_model->SetPageCode('viparea_directory_information');	
		
		//test to allow a person to view deleted revisions
		$show_all_revisions = false;
		if (PermissionsSubset('office', GetUserLevel())){
			$show_show_all_revisions_option = true;
		}else{
			$show_show_all_revisions_option = false;
		}
		if($action=='viewall'){
			if (PermissionsSubset('office', GetUserLevel())){
				$show_all_revisions = true;
			}else{
				$this->main_frame->AddMessage('error','You do not have permission to view deleted revisions');
			}
			$action='view';
		}
		
		if($action=='delete'){
			$result = $this->directory_model->FlagEntryRevisionAsDeletedById($organisation, $revision);
			if($result == 1){
				$this->main_frame->AddMessage('success','Directory revision successfully removed.');
			}else{
				$this->main_frame->AddMessage('error','Directory revision was not removed, revision does not exist or is live.');
			}
			$action='view';
		}
		if($action=='restore'){
			//Check Permissions
			if (PermissionsSubset('office', GetUserLevel())){
				//Send and get data
				$result = $this->directory_model->FlagEntryRevisionAsDeletedById($organisation, $revision, false);
				if($result == 1){
					$this->main_frame->AddMessage('success','Directory revision was restored successfully.');
				}else{
					$this->main_frame->AddMessage('error','Directory revision was not restored it does not exist or it is not deleted.');
				}
			}else{
				$this->main_frame->AddMessage('error','You do not have permission to restore revisions');
			}
			$action='view';
		}
		
		if($action=='publish'){
			//Check Permissions
			if (PermissionsSubset('office', GetUserLevel())){
				//Send and get data
				$result = $this->directory_model->PublishDirectoryEntryRevisionById($organisation, $revision);
				if($result == 1){
					$this->main_frame->AddMessage('success','Directory revision was published successfully.');
				}else{
					$this->main_frame->AddMessage('error','Directory revision was not published it does not exist or is already live.');
				}
			}else{
				$this->main_frame->AddMessage('error','You do not have permission to publish revisions');
			}
			$action='view';
		}
		
		if ($action=='view'){
			//Get Organisation Data
			$data = $this->organisations->_GetOrgData($organisation, $revision);
			
			//Send data if given
			if(!empty($_POST['submitbutton'])){
				$this->main_frame->AddMessage('success','A new directory entry revision has been created.');
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
				$data['revisions_information_text'] = $this->pages_model->GetPropertyWikitext('revisions_information_text');
				
				//Page Revisions
				$data['revisions'] = $this->directory_model->GetRevisonsOfDirectoryEntry($organisation, $show_all_revisions);
				$data['show_all_revisions'] = $show_all_revisions;
				$data['show_show_all_revisions_option'] = $show_show_all_revisions_option;
				
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
		
		if($action=='preview'){
			
			//Show a toolbar in a message for the preview.
			$published = $this->directory_model->IsRevisionPublished($organisation, $revision);
			$user_level = GetUserLevel();
			$is_deleted = $this->directory_model->IsRevisionDeleted($revision);
			if($published){
				$message = 'This is a preview of the current published directory revision.<br />';
			}else{
				if($is_deleted){
					$message = 'This is a preview of a <span class="red">deleted</span> directory revision.<br />';
				}else{
					$message = 'This is a preview of a directory revision.<br />';
				}
			}
			$message .= '<a href="'.vip_url('directory/information/view/'.$revision).'">Go Back</a>';
			
			if($published == false){
				if (PermissionsSubset('office', GetUserLevel())){
					$message .= ' | <a href="'.vip_url('directory/information/publish/'.$revision).'">Publish This Revision</a>';
				}
				
				if ($is_deleted) {
					if (PermissionsSubset('office', GetUserLevel())){
						$message .= ' | <a href="'.vip_url('directory/information/restore/'.$revision).'">Restore This Revision</a>';
					}
				} else {
					$message .= ' | <a href="'.vip_url('directory/information/delete/'.$revision).'">Delete This Revision</a>';
				}
			}
			
			$this->main_frame->AddMessage('information',$message);
			
			$data = $this->organisations->_GetOrgData($organisation, $revision);
			
			if (!empty($data)) {
				$this->_SetupNavbar();
				
				// Set up the directory view
				$the_view = $this->frames->view('directory/directory_view', $data);
				
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
	
	function photos($action = 'default', $photoID = FALSE, $operation = FALSE)
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$organisation = VipOrganisation();
		$this->pages_model->SetPageCode('viparea_directory_photos');
		$this->load->model('slideshow');
		$this->load->helper(array('images', 'url'));
		$this->load->library('image_upload');
		
		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);

		if (!empty($data)) {
			$this->_SetupNavbar();
			if ($action == 'move') { // Switch hates me, this should be case switch but i won't do it
				if ($operation == 'up') {
					$this->slideshow->pushUp($photoID, $data['organisation']['id']);
				} elseif ($operation == 'down') {
					$this->slideshow->pushDown($photoID, $data['organisation']['id']);
				}
			} elseif ($action == 'delete') {
				if ($operation == 'confirm') {
					$this->slideshow->deletePhoto($photoID, $data['organisation']['id']);
					$this->messages->AddMessage('info', 'Photo Deleted');
				} else {
					$this->messages->AddMessage('info', 'Are you sure? <a href="'.$photoID.'/confirm">Click to delete</a>');
				}
			} elseif ($action == 'upload') {
				$this->xajax->processRequests();
				return $this->image_upload->recieveUpload(vip_url('directory/photos'), array('slideshow'));
			} elseif (isset($_SESSION['img']['list'])) {
				foreach ($_SESSION['img']['list'] as $newID) {
					$this->slideshow->addPhoto($newID, $data['organisation']['id']);
				}
				unset($_SESSION['img']['list']);
			}
			
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['disclaimer_text'] = $this->pages_model->GetPropertyWikitext('disclaimer_text');
			$data['oraganisation'] = $organisation; // why its spelt wrong? but def don't correct it!
			$data['images'] = $this->slideshow->getPhotos($data['organisation']['id']);
			
			// Set up the directory view
			$the_view = $this->frames->view('directory/viparea_directory_photos', $data);
			
			// Set up the public frame
			$this->main_frame->SetTitleParameters(
					array('organisation' => $data['organisation']['name']));
			$this->main_frame->SetPage('photos');
			$this->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
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
	
		$this->load->library('maps');
		$organisation = VipOrganisation();
		$this->pages_model->SetPageCode('viparea_directory_map');
		
		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);
		
		if (!empty($data)) {
			if (isset($_POST['0_lat'])) {
				$this->directory_model->UpdateDirectoryEntryLocation(
					$organisation, 
					$data['organisation']['location'],
					$_POST['0_lat'], 
					$_POST['0_lng']
				);
				$data['organisation']['location_lat'] = $_POST['0_lat'];
				$data['organisation']['location_lng'] = $_POST['0_lng'];
			}
			$this->_SetupNavbar();
			
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['map_text'] = $this->pages_model->GetPropertyWikitext('map_text');
			
			// Set up the directory view
			$the_view = $this->frames->view('directory/viparea_directory_map', $data);
			
			$map = &$this->maps->CreateMap('Test Map', 'googlemaps');
			if ($data['organisation']['location_lat'] !== NULL) {
				$map->WantLocation($data['organisation']['name'], $data['organisation']['location_lat'], $data['organisation']['location_lng']);
			} else {
				$map->WantLocation($data['organisation']['name']);
			}
			$this->maps->SendMapData();

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
		
		$organisation = VipOrganisation();
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
	
	/// Set up the navigation bar.
	/**
	 * @param $DirectoryEntry Directory entry of organisation.
	 */
	private function _SetupNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('information', 'Information',
				vip_url('directory/information'));
		$navbar->AddItem('photos', 'Photos',
				vip_url('directory/photos'));
		$navbar->AddItem('map', 'Map',
				vip_url('directory/map'));
		$navbar->AddItem('contacts', 'Contacts',
				vip_url('directory/contacts'));
	}

}
?>
