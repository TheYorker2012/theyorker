<?php

/// Review Leagues List
/**
 * @author Owen Jones
 *
 */
class Reviewlist extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->model('directory_model');
		$this->load->model('pr_model');
		$this->load->model('review_model');
		$this->load->model('leagues_model');
		$this->load->model('prefs_model');

		$this->load->helper('text');
		$this->load->library('image');
		$this->load->helper('wikilink');
	}

	private function _SetupNavbar($content_type_codename)
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('attentionlist', 'Attention List',
						 '/office/reviewlist/'.$content_type_codename);
		$navbar->AddItem('completelist', 'Complete List',
						 '/office/reviewlist/completelist/'.$content_type_codename);
	}

	/// Review list index page.
	/**
	 * @note POST data:
	 *	- 'search' (search pattern, optional)
	 */
	function attentionlist($content_type_codename) {
		//check permissions
		if (!CheckPermissions('office')) return;
		
		//load Navbar frame
		$this->_SetupNavbar($content_type_codename);
		$this->main_frame->SetPage('attentionlist');
		$content_type_fullname = $this->pr_model->GetContentTypeNiceName($content_type_codename);
		$this->main_frame->SetTitleParameters(array('content_type' => $content_type_fullname));
		
		//Load page properties stuff
		$this->pages_model->SetPageCode('office_review_attention_list');
		
		$data = array();
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['assigned_venues_text'] = $this->pages_model->GetPropertyWikiText('assigned_venues');
		$data['revisions_needing_approval'] = $this->pages_model->GetPropertyWikiText('revisions_needing_approval');
		$data['reviews_needing_approval'] = $this->pages_model->GetPropertyWikiText('reviews_needing_approval');
		$data['list_text_information'] = $this->pages_model->GetPropertyWikiText('list_text_information');
		$data['list_text_reviews'] = $this->pages_model->GetPropertyWikiText('list_text_reviews');
		$data['list_text_tags'] = $this->pages_model->GetPropertyWikiText('list_text_tags');
		$data['list_text_leagues'] = $this->pages_model->GetPropertyWikiText('list_text_leagues');
		$data['list_text_photos'] = $this->pages_model->GetPropertyWikiText('list_text_photos');
		$data['content_type_codename'] = $content_type_codename;
		
		///////////////Get Sidebar data
		//Get Assigned Venues
		$data['assigned_venues'] = $this->pr_model->GetUsersAssignedReviewVenues($this->user_auth->entityId, $content_type_codename);
		//Get Leagues
		$data['leagues'] = $this->leagues_model->getAllLeagues($content_type_codename);
		//Get Waiting Revisions, (only show if editor, otherwise they cant approve stuff anyway)
		if (CheckPermissions('editor')) $data['waiting_revisions'] = $this->pr_model->GetWaitingVenueInformationRevisions($content_type_codename);
		//Get Waiting Reviews, (only show if editor, otherwise they cant approve stuff anyway)
		if (CheckPermissions('editor')) $data['waiting_review_revisions'] = $this->pr_model->GetWaitingVenueReviewRevisions($content_type_codename);
		
		//////Get data for main page lists
		$data['information_venues'] = $this->pr_model->GetWorstVenuesForInformation($content_type_codename, 5);
		$data['reviews_venues'] = $this->pr_model->GetWorstVenuesForReviews($content_type_codename, 5);
		$data['tags_venues'] = $this->pr_model->GetWorstVenuesForTags($content_type_codename, 5);
		$data['leagues_venues'] = $this->pr_model->GetWorstVenuesForLeagues($content_type_codename, 5);
		$data['photos_venues'] = $this->pr_model->GetWorstVenuesForPhotos($content_type_codename, 5);
		
		
		// Set up the public frame to use the directory view
		$this->main_frame->SetContentSimple('office/reviews/reviewlist_overview', $data);
		$this->main_frame->Load();
	}
	
	function completelist($content_type_codename) {
		//check permissions
		if (!CheckPermissions('office')) return;
		
		//load Navbar frame
		$this->_SetupNavbar($content_type_codename);
		$this->main_frame->SetPage('completelist');
		$content_type_id = $this->pr_model->GetContentTypeId($content_type_codename);
		$content_type_fullname = $this->pr_model->GetContentTypeNiceName($content_type_codename);
		$this->main_frame->SetTitleParameters(array('content_type' => $content_type_fullname));
		
		//Load page properties stuff
		$this->pages_model->SetPageCode('office_review_complete_list');
		
		$data = array();
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		
		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well
		$data['organisations'] = $this->pr_model->GetReviewContextListFromId($content_type_id);
		$data['content_type_codename'] = $content_type_codename;
		$data['search'] = $search_pattern;

		// Set up the public frame to use the directory view
		$this->main_frame->SetContentSimple('office/reviews/reviewlist', $data);

		// Include the javascript
		$this->main_frame->IncludeJs('javascript/directory.js');
		$this->main_frame->IncludeCss('stylesheets/directory.css');

		// Load the public frame view
		$this->main_frame->Load();
	}

}
?>
