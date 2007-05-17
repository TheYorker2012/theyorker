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
		$this->load->model('businesscards_model');

		$this->load->helper('text');
		$this->load->helper('images');
		$this->load->helper('wikilink');
	}

	private function _CreateDirectoryEntryName($long_name)
	{
		//strip non alpha-numerical symbols
		$new_string = preg_replace("/[^a-zA-Z0-9s]/", "", $long_name);
		//replace spaces with an underscore
		return str_replace(" ", "_", $new_string);
	}

	/// Set up the navigation bar for a specific organisation.
	private function _SetupOrganisationNavbar()
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

	/// Directory index page.
	/**
	 * @note Shows error 404
	 */
	function index()
	{
		if (!CheckPermissions('office')) return;

		show_404();
	}

	/// Directory organisation page.
	function information($action='view', $revision=true)
	{
		if (!CheckPermissions('vip+pr')) return;

		$organisation = VipOrganisation();
		$this->pages_model->SetPageCode('viparea_directory_information');

		$editor_level = PermissionsSubset('editor', GetUserLevel());

		//test to allow a person to view deleted revisions
		$show_all_revisions = false;
		if ($action=='viewall') {
			if ($editor_level) {
				$show_all_revisions = true;
			} else {
				$this->messages->AddMessage('error','You do not have permission to view deleted revisions');
			}
			$action='view';
		}

		if ($action=='delete') {
			if ($editor_level) {
				$result = $this->directory_model->FlagEntryRevisionAsDeletedById($organisation, $revision);
				if ($result == 1) {
					$this->messages->AddMessage('success','Directory revision successfully removed.');
				} else {
					$this->messages->AddMessage('error','Directory revision was not removed, revision does not exist or is live.');
				}
			} else {
				$this->messages->AddMessage('error','You do not have permission to remove revisions.');
			}
			$action='view';
		}
		if ($action=='changename') {
			//Check Permissions
			if ($editor_level) {
				if ($_POST['organisation_name']=="") {
					$this->messages->AddMessage('error','Please enter an organisation name.');
				} else {
					$new_directory_entry_name = $this->_CreateDirectoryEntryName($_POST['organisation_name']);
					$result = $this->directory_model->UpdateDirctoryEntryType($organisation, $_POST['organisation_type']);
					$result2 = $this->directory_model->UpdateDirctoryEntryNames($organisation, $_POST['organisation_name'], $organisation);
					if ($result==1 && $result2==1) {
						$this->messages->AddMessage('success','Organisation name was successfully changed.');
					} else {
						$this->messages->AddMessage('error','Update did not work, please try again.');
					}
				}
			} else {
				$this->messages->AddMessage('error','You do not have permission to change the organisations name.');
			}
			$action='view';
		}
		if ($action=='restore') {
			//Check Permissions
			if ($editor_level) {
				//Send and get data
				$result = $this->directory_model->FlagEntryRevisionAsDeletedById($organisation, $revision, false);
				if ($result == 1) {
					$this->messages->AddMessage('success','Directory revision was restored successfully.');
				} else {
					$this->messages->AddMessage('error','Directory revision was not restored it does not exist or it is not deleted.');
				}
			} else {
				$this->messages->AddMessage('error','You do not have permission to restore revisions');
			}
			$action='view';
		}

		if ($action=='publish') {
			//Check Permissions
			if ($editor_level) {
				//Send and get data
				$result = $this->directory_model->PublishDirectoryEntryRevisionById($organisation, $revision);
				if ($result == 1) {
					$this->messages->AddMessage('success','Directory revision was published successfully.');
				} else {
					$this->messages->AddMessage('error','Directory revision was not published it does not exist or is already live.');
				}
			} else {
				$this->messages->AddMessage('error','You do not have permission to publish revisions');
			}
			$action='view';
		}

		if ($action=='view') {
			//Send data if given
			if (!empty($_POST['description'])) {
				$this->directory_model->AddDirectoryEntryRevision($organisation, $_POST);
				$this->messages->AddMessage('success','A new directory entry revision has been created.');
				if ($_POST['description']==null) {
					$this->messages->AddMessage('information','About field is blank we advise you add some detail.');
				}
			}
			//Show hide directory entry information and form detection
			if (!empty($_POST['directory_visibility'])) {
				if ($_POST['directory_visibility']=="Show Entry") {
					$this->directory_model->MakeDirectoryEntryVisible($organisation);
					$this->messages->AddMessage('success','Directory entry made visible.');
				}
				if ($_POST['directory_visibility']=="Hide Entry") {
					$this->directory_model->MakeDirectoryEntryVisible($organisation, false);
					$this->messages->AddMessage('success','Directory entry hidden.');
				}
			}

			//Accept/reject directory entry information and form detection
			if (!empty($_POST['directory_acceptance']) && $_POST['directory_acceptance']=='Accept') {
				if ($editor_level) {
					$this->directory_model->AcceptDirectoryEntry($organisation);
					$this->messages->AddMessage('success','Directory entry accepted');
				} else {
					$this->messages->AddMessage('error','You do not have permission to accept directory entries.');
				}
			} elseif (!empty($_POST['directory_deletion']) && ($_POST['directory_deletion']=='Reject' || $_POST['directory_deletion']=='Delete')) {
				if ($editor_level) {
					$this->directory_model->DeleteDirectoryEntry($organisation);
					$this->messages->AddMessage('success','Directory entry removed');
					redirect('/office/prlist/');
				} else {
					$this->messages->AddMessage('error','You do not have permission to remove directory entries.');
				}
			}

			//Get Organisation Data
			$data = $this->organisations->_GetOrgData($organisation, $revision);

			$organisation_details = $this->directory_model->GetOrganisation($organisation);

			//Find out if the directory entry is currently visable.
			$data['show_visibility'] = (
				$organisation_details->organisation_needs_approval == 0
				&& $organisation_details->organisation_type_directory == 1
				&& $organisation_details->organisation_has_live_content == 1
			);
			$data['directory_visibility'] = (!$organisation_details->organisation_show_in_directory);

			$data['show_acceptance'] = $organisation_details->organisation_needs_approval;

			if ($organisation_details->organisation_needs_approval) {
				$data['directory_visibility_text'] = 'This directory entry is <b>not visible</b> as it has not yet been accepted by an editor.';

			} elseif (!$organisation_details->organisation_type_directory) {
				$data['directory_visibility_text'] = 'This directory entry is <b>not visible</b> to the public, as it is of a type that is hidden.';

			} elseif (!$organisation_details->organisation_has_live_content) {
				$data['directory_visibility_text'] = 'This directory entry is <b>not visible</b> to the public, as no revisions have been <b>published</b> by an editor.';

			} elseif (!$organisation_details->organisation_show_in_directory) {
				$data['directory_visibility_text'] = 'This directory entry is <b>not visible</b> to the public, as it is set to a <b>hidden</b> state.';

			} else {
				$data['directory_visibility_text'] = 'This directory entry is <b>visible</b> to the public';
			}

			if (!empty($data)) {
				$this->_SetupOrganisationNavbar();

				// Insert main text from pages information
				$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
				$data['revisions_information_text'] = $this->pages_model->GetPropertyWikitext('revisions_information_text');

				//Page Revisions
				$data['revisions'] = $this->directory_model->GetRevisonsOfDirectoryEntry($organisation, $show_all_revisions);
				$data['show_all_revisions'] = $show_all_revisions;
				$data['show_show_all_revisions_option'] = $editor_level;
				$data['user_is_editor'] = $editor_level;
				$data['organisation']['types'] = $this->directory_model->GetOrganisationTypes();
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

		if ($action=='preview') {

			//Show a toolbar in a message for the preview.
			$published = $this->directory_model->IsRevisionPublished($organisation, $revision);
			$user_level = GetUserLevel();
			$is_deleted = $this->directory_model->IsRevisionDeleted($revision);
			if ($published) {
				$message = 'This is a preview of the current published directory revision.<br />';
			} else {
				if ($is_deleted) {
					$message = 'This is a preview of a <span class="red">deleted</span> directory revision.<br />';
				} else {
					$message = 'This is a preview of a directory revision.<br />';
				}
			}
			$message .= '<a href="'.vip_url('directory/information/view/'.$revision).'">Go Back</a>';

			if ($published == false) {
				if ($editor_level) {
					$message .= ' | <a href="'.vip_url('directory/information/publish/'.$revision).'">Publish This Revision</a>';
				}

				if ($is_deleted) {
					if ($editor_level) {
						$message .= ' | <a href="'.vip_url('directory/information/restore/'.$revision).'">Restore This Revision</a>';
					}
				} else {
					$message .= ' | <a href="'.vip_url('directory/information/delete/'.$revision).'">Delete This Revision</a>';
				}
			}

			$this->messages->AddMessage('information',$message);

			$data = $this->organisations->_GetOrgData($organisation, $revision);

			if (!empty($data)) {
				$this->_SetupOrganisationNavbar();

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
			$this->_SetupOrganisationNavbar();
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
			$this->_SetupOrganisationNavbar();

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

	function contacts($action="viewgroup", $business_card_group=-1)
	{
		if (!CheckPermissions('vip+pr')) return;

		$organisation = VipOrganisation();
		$this->pages_model->SetPageCode('viparea_directory_contacts');

		$editor_level = PermissionsSubset('editor', GetUserLevel());

		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);

		//Deletegroup
		if ($action=="deletegroup") {
			$cards = $this->directory_model->GetDirectoryOrganisationCardsByGroupId($business_card_group, true);
			if (empty($cards)) {
				$result = $this->businesscards_model->RemoveOrganisationCardGroupById($business_card_group);
				if ($result == 1) {
					$this->messages->AddMessage('success','Group was successfully removed.');
				} else {
					$this->messages->AddMessage('error','Group was not removed, the group does not exist.');
				}
			} else {
				$this->messages->AddMessage('error','Group was not removed, you cannot remove groups with cards.');
			}
		//set things back to normal
		$action="viewgroup";
		$business_card_group=-1;
		}

		if ($action=="deletecard") {//business_card_group is actually the card id for this action
			if ($editor_level) {
			$result = $this->businesscards_model->DeleteBusinessCard($business_card_group);
			if ($result) {
				$this->messages->AddMessage('success','The contact card was successfully deleted.');
			} else {
				$this->messages->AddMessage('error','The contact card was not removed, it does not exist.');
			}
			//set things back to normal
			$action="viewgroup";
			$business_card_group=-1;
			} else {
				$this->messages->AddMessage('error','You do not have permission to delete contact cards.');
			}
		}
		if ($action=="approvecard") {//business_card_group is actually the card id for this action
			if ($editor_level) {
			$result = $this->businesscards_model->ApproveBusinessCard($business_card_group);
			if ($result) {
				$this->messages->AddMessage('success','The contact card was successfully approved.');
			} else {
				$this->messages->AddMessage('error','The contact card was not approved, it does not exist.');
			}
			//set things back to normal
			$action="viewgroup";
			$business_card_group=-1;
			} else {
				$this->messages->AddMessage('error','You do not have permission to approve contact cards.');
			}
		}

		//Add Groups
		if (!empty($_POST["group_name"])) {
			$max_order = $this->businesscards_model->SelectMaxGroupOrderById($data['organisation']['id']);
			$post_data = array(
				'group_name' => $_POST["group_name"],
				'organisation_id' => $data['organisation']['id'],
				'group_order' => $max_order+1,
			);
			$this->businesscards_model->AddOrganisationCardGroup($post_data);
			$this->messages->AddMessage('success','Group was successfully added.');

		}
		if (!empty($_POST["card_addbutton"])) {
			if (empty($_POST["card_name"]) || empty($_POST["card_title"]))
			{
			$this->messages->AddMessage('error','Please include a name and a title for your contact card');
			//add failed send the data back into the form
			$data['card_form']=$_POST;
			} else {
				//find user id if exist
				if (!empty($_POST["card_username"])) {
					//find user id from username
					$user_id = $this->businesscards_model->GetUserIdFromUsername($_POST["card_username"]);
				} else {
					$user_id = "";
				}

				//Send message if username was given and no id found
				if ($user_id==""&&!empty($_POST["card_username"])) {
					$this->messages->AddMessage('error','The user '.$_POST["card_username"].' was not found, you may have spelt the username incorrectly or the user is not on the yorker. You may wish to leave that field blank.');
				//add failed send the data back into the form
				$data['card_form']=$_POST;
				} else {

					//add contact card
					//@note start time, end time, order, and image id are all currently null and not in use.
					$this->businesscards_model->NewBusinessCard($user_id, $_POST["group_id"], null, $_POST["card_name"],
			$_POST["card_title"], $_POST["card_about"], $_POST["card_course"], $_POST["email"], $_POST["phone_mobile"],
			$_POST["phone_internal"], $_POST["phone_external"], $_POST["postal_address"],
			0, null, null);
					$this->messages->AddMessage('success','The contact card was successfully added.');

				}
			}
		}

		if (!empty($data)) {
			$this->_SetupOrganisationNavbar();

			// Business Card Groups
			$groups = $this->directory_model->GetDirectoryOrganisationCardGroups($organisation);
			// translate into nice names for view
			$data['organisation']['groups'] = array();
			foreach ($groups as $group) {
				$data['organisation']['groups'][] = array(
					'name' => $group['business_card_group_name'],
					'href' => vip_url('directory/contacts/viewgroup/'.$group['business_card_group_id']),
					'id' => $group['business_card_group_id']
				);
				if ($business_card_group==-1) $business_card_group = $group['business_card_group_id'];
				$data['current_group']['id'] = $business_card_group;
				if ($group['business_card_group_id'] == $business_card_group) $data['current_group']['name'] = $group['business_card_group_name'];
			}

			// Members data
			$members = $this->directory_model->GetDirectoryOrganisationCardsByGroupId($business_card_group, true);
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
					'postal_address' => $member['business_card_postal_address'],
					'approved' => $member['business_card_approved']
				);
			}

			//Put the view in edit mode
			$data['organisation']['editmode'] = true;

			// Set up the directory view
			$the_view = $this->frames->view('directory/viparea_directory_view_members', $data);

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

}
?>
