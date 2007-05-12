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

	}

	/**
	 *	@brief Load photo request pool page
	 */
	function index()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		$data['test'] = 'test';

		/// Get custom page content
		$this->pages_model->SetPageCode('office_photos');

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/photos/home', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	/**
	 *	@brief Load photo request details
	 */
	//TODO Security!!!
	function view()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;
		
		$this->load->helper(array('images', 'entity'));

		$requestID = $this->uri->segment(4);
		$viewer = $this->uri->segment(5);

		$data['photoRequest'] = $this->requests_model->GetPhotoRequest($requestID);
		$data['photos'] = $this->requests_model->GetAllPhotosForRequest($requestID);
		$data['article'] = $this->article_model->GetArticleDetails($data['photoRequest']->photo_request_article_id);
		
		if ($data['photoRequest']->photo_count == 0) {
			$this->messages->AddMessage('info', 'There are no photos for this request yet...');
		}
		if ($data['photoRequest']->photo_request_chosen_photo_id != null) {
			//TODO convert to username
			$this->messages->AddMessage('info', 'The final photo for this request has been chosen by '.$data['photoRequest']->photo_request_approved_user_entity_id);
		}
		if ($data['photoRequest']->photo_request_deleted = 1) {
			$this->messages->AddMessage('error', 'This photo request is marked as deleted.');
		}

   		/// Get custom page content
		$this->pages_model->SetPageCode('office_photos');

		/// Set up the main frame
		/// Using seperate views for mockups to make it more clear what options should be
		/// displayed/hidden for each user
		if ($viewer == 'editor') {
			$this->main_frame->SetContentSimple('office/photos/view-editor', $data);
		} elseif ($viewer == 'all') {
			$this->main_frame->SetContentSimple('office/photos/view-everyone', $data);
		} elseif ($viewer == 'reporter') {
			//TODO this should be setup properly, one view only for now
			if(isset($_SESSION['img']['list'])) {
				$data['suggestion'] = $_SESSION['img']['list'];
				unset($_SESSION['img']['list']);
			}
			$this->main_frame->SetContentSimple('office/photos/view-reporter', $data);
		} elseif ($viewer == 'photographer') {
			$this->main_frame->SetContentSimple('office/photos/view-photographer', $data);
		} elseif ($viewer == 'flagged') {
			$this->main_frame->SetContentSimple('office/photos/view-flagged', $data);
		} elseif ($viewer == 'completed') {
			$this->main_frame->SetContentSimple('office/photos/view-completed', $data);
		} else {
			$this->main_frame->SetContentSimple('office/photos/view', $data);
		}

		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

}

?>