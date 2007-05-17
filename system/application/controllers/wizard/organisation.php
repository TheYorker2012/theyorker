<?php

/**
 * @file organisation.php
 * @brief Wizard for organisation suggestion.
 */

/**
 */
class Organisation extends controller
{
	/// Default constructor
	function __construct()
	{
		parent::controller();

		$this->load->model('directory_model');
		$this->load->library('Image_upload');
	}

	private function CreateDirectoryEntryName ($long_name){
		//lower case
		$new_string = strtolower($long_name);
		//spaces to underscores
        $new_string = preg_replace('/\s/', '_', $new_string);
        //strip non alpha-numerical symbols
        $new_string = preg_replace('/[^\da-z_]/', '', $new_string);
        //replace double underscores
        $new_string = str_replace('__', '_', $new_string);

		return $new_string;
	}

	function photostep() {
		$_POST['r_stage'] = 4;
		$this->index(4);
	}

	function index($stage = false)
	{
		if (!CheckPermissions('public')) return;

		$this->pages_model->SetPageCode('wizard_organisation');

		$stage_count = 6; //total number of stages
		$skip_stages = array('3', '4'); //these stages are skipped when the user is not connected to the organisation
		$headings = array('1'=>'Start', '2'=>'Basic Details', '3'=>'More Details', '4'=>'Photos', '5'=>'Map', '6'=>'Finish');
		$data['session_var'] = 'org_wizard'; //variable in the session to store the data

		if (isset($_POST['r_stage']))
		{
			//No post occured, but so try to use existing session data
			if ($stage) {
				$data['stage'] = $stage;
				if (isset($_SESSION['img']['list'])) {
					foreach ($_SESSION['img']['list'] as $newImg) {
						$_SESSION['org_wizard']['img'][] = $newImg;
					}
					unset($_SESSION['img']['list']);
				}
			} else {
				//a post has occured but there is no session data, get the serialised data out of the form and put it back into the session
				/*
				echo '<pre>';
				echo print_r($_POST);
				echo '<pre>';
				*/
				if (isset($_SESSION[$data['session_var']]['a_connected']) == false)
				{
					$unserialized = stripslashes($_POST['r_dump']);
					$unserialized = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $unserialized );
					$_SESSION[$data['session_var']] = unserialize($unserialized);
				}
				//dump the post data into the session
				foreach ($_POST as $key => $postitem)
				{
					$_SESSION['org_wizard'][$key] = $postitem;
				}
			}
			$data['is_connected'] = $_SESSION['org_wizard']['a_connected'];
			//$data['post'][$_POST['r_stage']] = $_POST;
			//$data['post'][$_POST['r_stage']]['prev'] = htmlentities(serialize($_POST), ENT_QUOTES);
			if (isset($_POST['r_submit_finish']))
				if ($_POST['r_stage'] == $stage_count)
				{
					//finished
					//##TODO: actually process the form data for maps
					//Correctly creates organisation and details
					//all fields in session with a_ in front are submitted data
					//the maps and photos pages aren't working atm just the standard form submit pages
					if(empty($_SESSION['org_wizard']['a_name']) || empty($_SESSION['org_wizard']['a_description']) || empty($_SESSION['org_wizard']['a_user_name']))
					{
						$this->messages->AddMessage('error', 'Please include at least a name, description and your name with your suggestion.');
					} else {
						//Store post data, so other varibles can be added to the array.
						$post_data = array(
							'type_id' => $_SESSION['org_wizard']['a_type'],
							'name' => $_SESSION['org_wizard']['a_name'],
							'suggestors_name' => $_SESSION['org_wizard']['a_user_name'],
							'suggestors_email' => $_SESSION['org_wizard']['a_user_email'],
							'suggestors_notes' => $_SESSION['org_wizard']['a_user_notes'],
							'suggestors_position' => $_SESSION['org_wizard']['a_user_position'],
							'description' => $_SESSION['org_wizard']['a_description'],
							'postal_address' => $_SESSION['org_wizard']['a_address'],
							'postcode' => $_SESSION['org_wizard']['a_postcode'],
							'phone_external' => $_SESSION['org_wizard']['a_phone_external'],
							'phone_internal' => $_SESSION['org_wizard']['a_phone_internal'],
							'fax_number' => $_SESSION['org_wizard']['a_fax'],
							'email_address' => $_SESSION['org_wizard']['a_email_address'],
							'url' => $_SESSION['org_wizard']['a_website'],
							'opening_hours' => $_SESSION['org_wizard']['a_opening_times'],
						);

						//create a useable directory entry name and add the directory entry name to the post data
						$post_data['directory_entry_name'] = $this->CreateDirectoryEntryName($post_data['name']);
						$exists_already = $this->directory_model->GetDirectoryOrganisationByEntryName($post_data['directory_entry_name']);
						if(empty($exists_already)){
							//create directory entry
							$result = $this->directory_model->AddDirectoryEntry($post_data);
							$newOrgId = $this->db->insert_id();
							if($result == 1)
							{
							//create directory entry revision
							$this->directory_model->AddDirectoryEntryRevision($post_data['directory_entry_name'], $post_data);
							//Store the revision id of the revision just made
							$entry_revision_id = $this->db->insert_id();
							//Make the stored revision the live id for the created organisation.
							$this->directory_model->PublishDirectoryEntryRevisionById($post_data['directory_entry_name'], $entry_revision_id);
							if (isset($_SESSION['org_wizard']['img'])) {
								$this->load->model('slideshow');
								foreach ($_SESSION['org_wizard']['img'] as $img) {
									$this->slideshow->addPhoto($img, $newOrgId);
								}
							}

							if (isset($_SESSION['org_wizard']['0_lat'])) {
								$this->directory_model->UpdateDirectoryEntryLocation(
									$post_data['directory_entry_name'],
									null,
									$_SESSION['org_wizard']['0_lat'],
									$_SESSION['org_wizard']['0_lng']
								);
							}

							$this->main_frame->AddMessage('success','Your suggestion has been submitted.');

							//Reset wizard on success
							$_SESSION['org_wizard'] = array();

							} else {
							//Something went wrong so don't make a revision
							$this->messages->AddMessage('error', 'An error occurred when your details were submitted, please try again.');
							}
						}else{
						//Name has been taken already!
						$this->messages->AddMessage('error', 'The name of your suggestion already exists in the directory. If you still wish to submit your suggestion please change the name.');
						}
					}

					$data['stage'] = 1;
				}
				else
				{
					//send them to the final stage
					$data['stage'] = $stage_count;
				}
			else if (isset($_POST['r_submit_next']))
			{
				$data['stage'] = $_POST['r_stage'] + 1;
				while ($data['is_connected'] == 'No' && in_array($data['stage'], $skip_stages))
					$data['stage'] = $data['stage'] + 1;
			}
			else if (isset($_POST['r_submit_back']))
			{
				$data['stage'] = $_POST['r_stage'] - 1;
				while ($data['is_connected'] == 'No' && in_array($data['stage'], $skip_stages))
					$data['stage'] = $data['stage'] - 1;
			}

			if ($data['stage'] === 5) {
				$this->load->library('maps');
				$map = &$this->maps->CreateMap('Add Location', 'googlemaps');
				if (isset($_SESSION['org_wizard']['0_lat'])) {
					$map->WantLocation(
						$_SESSION['org_wizard']['a_name'],
						$_SESSION['org_wizard']['0_lat'],
						$_SESSION['org_wizard']['0_lng']
					);
				} else {
					$map->WantLocation($_SESSION['org_wizard']['a_name']);
				}
				$map->SetFormId('orgdetails');
				$this->maps->SendMapData();
			}
		}
		else
		{
			$data['stage'] = 1;
			$data['is_connected'] = 'yes';
			$data['prev'] = array();
		}

		$data['stage_list']['count'] = $stage_count;
		$data['stage_list']['skip'] = $skip_stages;
		$data['stage_list']['headings'] = $headings;
		$data['organisations'] = $this->directory_model->GetOrganisationTypes();

		//if (isset($_SESSION[$data['session_var']]) == true)
			$_SESSION[$data['session_var']]['r_dump'] = NULL;

		// Set up the public frame
		$this->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		$the_view = $this->frames->view('wizard/organisation', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}

	function upload($type) {
		if (!CheckPermissions('public')) return;
		$this->xajax->processRequests();
		if ($type == 'images') {
			$this->image_upload->recieveUpload('wizard/organisation/photostep', array('slideshow'));
		}
	}

	function photo($action, $id = -1, $direction = '') {
		$this->load->helper('url');
		if ($action == 'move') {
			$loc = array_search($id, $_SESSION['org_wizard']['img']);
			if ($direction == 'up') {
				if ($loc != 0) {
					$temp = $_SESSION['org_wizard']['img'][$loc-1];
					$_SESSION['org_wizard']['img'][$loc-1] = $_SESSION['org_wizard']['img'][$loc];
					$_SESSION['org_wizard']['img'][$loc] = $temp;
				}
			} else {
				if ($loc != count($_SESSION['org_wizard']['img'])-1) {
					$temp = $_SESSION['org_wizard']['img'][$loc+1];
					$_SESSION['org_wizard']['img'][$loc+1] = $_SESSION['org_wizard']['img'][$loc];
					$_SESSION['org_wizard']['img'][$loc] = $temp;
				}
			}
			header('Location:'.site_url('wizard/organisation/photostep'));
		} elseif ($action == 'delete') {
			//if php has a function to renumber the keys in an array, plz change this
			$oldImgList = $_SESSION['org_wizard']['img'];
			unset($_SESSION['org_wizard']['img']);
			foreach ($oldImgList as $img) {
				if ($img != $id) {
					$_SESSION['org_wizard']['img'][] = $img;
				}
			}
			header('Location:'.site_url('wizard/organisation/photostep'));
		}
	}
}
?>
