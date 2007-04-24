<?php

/// Review Leagues List
/**
 * @author Mark Goodall
 * @author Nick Evans
 *
 */
class Reviewlist extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->library('organisations');

		$this->load->model('directory_model');
		$this->load->model('pr_model');
		$this->load->model('review_model');
		$this->load->model('prefs_model');

		$this->load->helper('text');
		$this->load->helper('images');
		$this->load->helper('wikilink');
	}

	//Shows list of leagues with "Last updated" and "Assigned to" columns
	function showleagues($content_type_codename) {
		if (!CheckPermissions('office')) return;
		
		$content_type_id = $this->pr_model->GetContentTypeId($content_type_codename);
		if ($content_type_id == 0) {
			show_404();
		}
		
		$this->pages_model->SetPageCode('office_leagues_show');

		$data = array();

		//$data['leagues'] = $this->pr_model->GetReviewContextListFromId($content_type_id,'office/reviews/','/'.$content_type_codename.'/review'); //$this->organisations->_GetOrgs($search_pattern);
	
		// Set up the  frame
		$this->main_frame->SetContentSimple('office/reviews/showleagues', $data);

		// Load the frame view
		$this->main_frame->Load();
	}
	
	//List leagues allowing editing of an individual league
	function editleagues($content_type_codename) {
		if (!CheckPermissions('office')) return;
		
		$content_type_id = $this->pr_model->GetContentTypeId($content_type_codename);
		if ($content_type_id == 0) {
			show_404();
		}
		
		$this->pages_model->SetPageCode('office_leagues_edit');

		$data = array();

		//$data['leagues'] = $this->pr_model->GetReviewContextListFromId($content_type_id,'office/reviews/','/'.$content_type_codename.'/review'); //$this->organisations->_GetOrgs($search_pattern);
	
		// Set up the  frame
		$this->main_frame->SetContentSimple('office/reviews/reviewlist', $data);

		// Load the frame view
		$this->main_frame->Load();
	}
	
	//Edit details of a particular league
	function editleaguedetails($content_type_codename, $league_codename) {
		if (!CheckPermissions('office')) return;
		
		$content_type_id = $this->pr_model->GetContentTypeId($content_type_codename);
		if ($content_type_id == 0) {
			show_404();
		}
		
		
		
		$this->pages_model->SetPageCode('office_league_edit_details');

		$data = array();

		//$data['leagues'] = $this->pr_model->GetReviewContextListFromId($content_type_id,'office/reviews/','/'.$content_type_codename.'/review'); //$this->organisations->_GetOrgs($search_pattern);
	
		// Set up the  frame
		$this->main_frame->SetContentSimple('office/reviews/reviewlist', $data);

		// Load the frame view
		$this->main_frame->Load();
	}

	//Edit contents of a particular league
	function editleaguecontents($content_type_codename, $league_codename) {
		if (!CheckPermissions('office')) return;
		
		$content_type_id = $this->pr_model->GetContentTypeId($content_type_codename);
		if ($content_type_id == 0) {
			show_404();
		}
		
		$this->pages_model->SetPageCode('office_league_edit_contents');

		$data = array();

		//$data['leagues'] = $this->pr_model->GetReviewContextListFromId($content_type_id,'office/reviews/','/'.$content_type_codename.'/review'); //$this->organisations->_GetOrgs($search_pattern);
	
		// Set up the  frame
		$this->main_frame->SetContentSimple('office/reviews/league_content', $data);

		// Load the frame view
		$this->main_frame->Load();
	}

	/// Review list index page.
	/**
	 * @note POST data:
	 *	- 'search' (search pattern, optional)
	 */
	function attentionlist($content_type_codename) {
		if (!CheckPermissions('office')) return;
		
		$content_type_id = $this->pr_model->GetContentTypeId($content_type_codename);
		if ($content_type_id == 0) {
			//show_404();
		}
		
		$this->pages_model->SetPageCode('office_review_attention_list');

		$data = array();

		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well
		$data['organisations'] = $this->pr_model->GetReviewContextListFromId($content_type_id,'office/reviews/','/'.$content_type_codename.'/review'); //$this->organisations->_GetOrgs($search_pattern);
		$data['search'] = $search_pattern;
		
		// Set up the directory view
		$directory_view = $this->frames->view('office/reviews/reviewlist', $data);

		// Set up the public frame to use the directory view
		$this->main_frame->SetContent($directory_view);

		// Include the javascript
		$this->main_frame->SetExtraHead('<script src="/javascript/directory.js" type="text/javascript"></script>');

		$this->main_frame->SetExtraCss('/stylesheets/directory.css');

		// Load the public frame view
		$this->main_frame->Load();
	}

}
?>
