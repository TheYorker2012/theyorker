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
				/// Get suggested photos for request
				$data['photos'] = $this->photos_model->GetSuggestedPhotos($request_id);

				/// Load main frame with view
				$this->main_frame->SetContentSimple('office/photos/view', $data);
				$this->main_frame->Load();
			}
		}

/*
		$this->load->helper(array('images', 'entity'));

		if ($this->input->post('r_assign') == 'Suggest' and $this->input->post('imgid_number')) {
			for ($i=0; $i<$this->input->post('imgid_number'); $i++) {
				if ($this->input->post('imgid_'.$i.'_allow') == 'y'){
					$this->requests_model->SuggestPhoto($requestID,
						                                $this->input->post('imgid_'.$i.'_number'),
						                                $this->input->post('imgid_'.$i.'_comment'));
				}
			}
		}

		$data['photos'] = $this->requests_model->GetAllPhotosForRequest($requestID);
*/

	}

	/**
	 *	@brief	Allow users to upload photos
	 */
	function upload()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		///@TODO: Check request is open / accepting photos / exists
		if (!$this->uri->segment(4)) {
			redirect('/office/photos');
		} else {
			$this->load->library('image_upload');
			$this->image_upload->automatic('/office/photos/uploaded/' . $this->uri->segment(4), array('small','medium'), false, true);
		}
	}

	/**
	 *	@brief	Adds selected/uploaded photos to the photo request
	 */
	function uploaded()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		///@TODO: Check request is open / accepting photos / exists
		if (!$this->uri->segment(4)) {
			redirect('/office/photos');
		} else {
			///@TODO: Check imglist isn't empty/null/exists
			$new_photos = array_unique($_SESSION['img']['list']);
			foreach ($new_photos as $photo) {
				$this->photos_model->SuggestPhoto($this->uri->segment(4),$photo,'',$this->user_auth->entityId);
			}
			/// Reset list of uploaded photos
			unset($_SESSION['img']);
			/// Take user back to photo request
			redirect('/office/photos/view/'.$this->uri->segment(4));
		}
	 }

}

?>