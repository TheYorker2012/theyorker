<?php

/**
 *	Provides the Yorker Office - Photo Request Functionality
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Photos extends Controller
{
	/**
	 *	@brief Default constructor
	 */
	function __construct()
	{
		parent::Controller();

  		/// Load photo requests model
		$this->load->model('photos_model');
	}

	/**
	 *	@brief Load photo request pool page
	 */
	function index()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		/// Get custom page content
		$this->pages_model->SetPageCode('office_photos');

		/// Retrieve list of all open requests
		$data['requests'] = $this->photos_model->GetAllOpenPhotoRequests();

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/photos/home', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	/**
	 *	@brief Load photo request details
	 */
	function view()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		/// Get custom page content
		$this->pages_model->SetPageCode('office_photos');

		$request_id = $this->uri->segment(4);
		if ((!$request_id) || (!is_numeric($request_id))) {
			redirect('/office/photos/');
		} else {
			/// Get all the information about the specified photo request
			$data = $this->photos_model->GetPhotoRequestDetails($request_id);
			if (!$data) {
				/// If request doesn't exist then redirect
				redirect('/office/photos/');
			} else {
				/// Check if there are any new suggested photos - ask for confirmation
				/// @TODO:Ensure request is open for new suggestions
				if (isset($_SESSION['img']['list'])) {
					$data['suggestion'] = array_unique($_SESSION['img']['list']);
					$this->load->helper('images');
					/// Reset list of new suggestions
					unset($_SESSION['img']);
				}

				/// Add any confirmed suggestions
				/// @TODO: Don't allow duplicate suggestions
				if (($this->input->post('r_suggest') == 'Suggest') && ($this->input->post('imgid_number'))) {
					for ($i=0; $i<$this->input->post('imgid_number'); $i++) {
						if ($this->input->post('imgid_'.$i.'_allow') == 'y'){
							$this->photos_model->SuggestPhoto($request_id,$this->input->post('imgid_'.$i.'_number'),$this->input->post('imgid_'.$i.'_comment'),$this->user_auth->entityId);
						}
					}
					redirect('/office/photos/view/'.$request_id.'/');
				}

				/// Get suggested photos for request
				$data['photos'] = $this->photos_model->GetSuggestedPhotos($request_id);
				/// Get photographers that request can be assigned to
				$data['photographers'] = $this->photos_model->GetPhotographers();

				/// Get comments
				if (is_numeric($data['comments_thread'])) {
					$this->load->library('comments');
					$this->comments->SetUri('/office/photos/view/'.$request_id.'/');
					$data['comments'] = $this->comments->CreateStandard((int)$data['comments_thread'],1);
				}

				/// Get current user's access level
				$data['user_level'] = GetUserLevel();
				if ($data['user_level'] == 'admin') {
					/// Admin users are effectively editors
					$data['user_level'] = 'editor';
				}
				if ($data['user_level'] != 'editor') {
					if (($data['status'] == 'assigned') && ($data['reporter_id'] == $this->user_auth->entityId)) {
						$data['user_level'] = 'photographer';
					} elseif ($data['reporter_id'] == $this->user_auth->entityId) {
						$data['user_level'] = 'reporter';
					} else {
						$data['user_level'] = 'everyone';
					}
				}
				/* At this point $data['user_level'] should hold one of the following access levels:
				 *	-	editor
				 *	-	photographer
				 *	-	reporter
				 *	-	everyone
				 */

				/// Access matrix
				$data['access']['details'] = array(
					'editor'		=>	TRUE,
					'photographer'	=>	FALSE,
					'reporter'		=>	TRUE,
					'everyone'		=>	FALSE
				);
				$data['access']['ready'] = array(
					'editor'		=>	TRUE,
					'photographer'	=>	TRUE,
					'reporter'		=>	FALSE,
					'everyone'		=>	FALSE
				);
				$data['access']['complete'] = array(
					'editor'		=>	TRUE,
					'photographer'	=>	FALSE,
					'reporter'		=>	FALSE,
					'everyone'		=>	FALSE
				);
				$data['access']['cancel'] = array(
					'editor'		=>	TRUE,
					'photographer'	=>	FALSE,
					'reporter'		=>	TRUE,
					'everyone'		=>	FALSE
				);

				/// Check if user is trying to edit request's details
				if ($this->input->post('r_details') == 'Edit') {
					/// Check the have the necessary permissions to edit
					if ($data['access']['details'][$data['user_level']]) {
						$this->photos_model->ChangeDetails($request_id,$this->input->post('r_title'),$this->input->post('r_brief'));
						$this->main_frame->AddMessage('success','Photo request details successfully changed.');
						redirect('/office/photos/view/'.$request_id.'/');
					} else {
						$this->main_frame->AddMessage('error','You do not have the necessary permissions to edit the details for this photo request, or this request has been completed or cancelled.');
					}
				}

				/// Check if trying to change assigned photographer
				if ($this->input->post('r_assign') !== FALSE) {
					if ($data['status'] == 'unassigned') {
						if (($data['user_level'] == 'editor') && (is_numeric($this->input->post('r_assignuser')))) {
							$this->photos_model->AssignPhotographer($request_id,$this->input->post('r_assignuser'));
							redirect('/office/photos/view/'.$request_id);
						} else {
							$this->photos_model->AssignPhotographer($request_id,$this->user_auth->entityId,'accepted');
							redirect('/office/photos/view/'.$request_id);
						}
					} elseif ($data['status'] == 'assigned') {
						if ($data['user_level'] == 'photographer') {
							if ($data['assigned_status'] == 'requested') {
								$this->photos_model->AssignPhotographer($request_id,$this->user_auth->entityId,'accepted');
								redirect('/office/photos/view/'.$request_id);
							}
						} elseif ($data['user_level'] == 'editor') {
							$this->photos_model->UnassignPhotographer($request_id);
							redirect('/office/photos/view/'.$request_id);
						}
					}
				} elseif ($this->input->post('r_decline') !== FALSE) {
					if (($data['status'] == 'assigned') && ($data['user_level'] == 'photographer') && ($data['assigned_status'] == 'requested')) {
						$this->photos_model->AssignPhotographer($request_id,$this->user_auth->entityId,'declined');
						redirect('/office/photos/view/'.$request_id);
					}
				}

				/// Load main frame with view
				$this->main_frame->SetContentSimple('office/photos/view', $data);
				$this->main_frame->Load();
			}
		}
	}

}

?>