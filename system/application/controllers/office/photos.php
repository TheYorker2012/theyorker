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
				/// @TODO: Get rid of POST to prevent refresh adding them again
				if (($this->input->post('r_suggest') == 'Suggest') && ($this->input->post('imgid_number'))) {
					for ($i=0; $i<$this->input->post('imgid_number'); $i++) {
						if ($this->input->post('imgid_'.$i.'_allow') == 'y'){
							$this->photos_model->SuggestPhoto($request_id,$this->input->post('imgid_'.$i.'_number'),$this->input->post('imgid_'.$i.'_comment'),$this->user_auth->entityId);
						}
					}
				}

            /// Get suggested photos for request
				$data['photos'] = $this->photos_model->GetSuggestedPhotos($request_id);

				/// Load main frame with view
				$this->main_frame->SetContentSimple('office/photos/view', $data);
				$this->main_frame->Load();
			}
		}
	}

}

?>