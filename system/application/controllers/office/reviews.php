<?php

/// Office Reviews
/**
 * @author Nick Evans (nse500@cs.york.ac.uk)
 * @author Frank Burton (frb501@cs.york.ac.uk)
 *@new_author Owen Jones (oj502@cs.york.ac.uk)
 *
 * The URI is mapped using config/routes.php
 *
 */
class Reviews extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->library('organisations');
		$this->load->model('directory_model');
		$this->load->model('review_model');
		$this->load->model('leagues_model');

		$this->load->helper('text');
		$this->load->helper('wikilink');
	}

	/// Set up the directory frame
	/**
	 * @param $OrganisationData Organisation data array.
	 * @pre @a $OrganisationData is valid organisation array.
	 * @post Frame_directory frame is loaded and ready to use.
	 */
	private function _SetupNavbar($DirectoryEntry, $ContextType)
	{
		$this->load->library('frame_directory');

		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('information', 'Information',
						 '/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/information');
		$navbar->AddItem('reviews', 'Reviews',
						 '/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/review');
		$navbar->AddItem('photos', 'Photos',
						 '/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/photos');
		$navbar->AddItem('tags', 'Tags',
						 '/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/tags');
		$navbar->AddItem('leagues', 'Leagues',
						 '/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/leagues');
		$navbar->AddItem('comments', 'Comments',
						 '/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/comments');
	}

	/// Main reviews page
	function index()
	{
		if (!CheckPermissions('office')) return;

		$this->main_frame->load();
	}

	/// Reviews Overview Page
	function overview($organisation = NULL)
	{
		if (NULL === $organisation) {
			show_404();
		}
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_reviews_overview');

		$data = $this->organisations->_GetOrgData($organisation);
		if (empty($data)) {
			show_404();
		}

		$data['page_information'] = $this->pages_model->GetPropertyWikitext('page_information');

		// Read any post data
		if ($this->input->post('create_confirm')) {
			$context = $this->input->post('create_context');
			if ($this->review_model->CreateReviewContext($organisation, $context)) {
				$this->messages->AddMessage('success', 'Review page successfully created');
			} else {
				$this->messages->AddMessage('error', 'Review page could not be created');
			}
		}
		if ($this->input->post('remove_confirm')) {
			$context = $this->input->post('remove_context');
			if ($this->review_model->DeleteReviewContext($organisation, $context)) {
				$this->messages->AddMessage('success', 'Review page successfully removed');
			} else {
				$this->messages->AddMessage('error', 'Review page could not be removed');
			}
		}

		// Fill the contexts array
		$data['contexts'] = array(
			'directory' => array(
				'name' => 'Directory',
				'exists' => TRUE,
				'editable' => TRUE,
				'creatable' => FALSE,
				'deletable' => FALSE,
				'edit' => site_url('office/pr/org/'.$organisation.'/directory/information'),
				'updated' => '',
				'deletable' => FALSE,
			),
		);
		// get the context types
		$content_types = $this->review_model->GetOrganisationReviewContextTypes($organisation);
		foreach ($content_types as $context_type) {
			$context = array();
			$context['name'] = $context_type['content_name'];
			$context['exists'] = (NULL !== $context_type['deleted']);
			$context['editable'] = $context['exists'];
			$context['creatable'] = !$context['exists'];
			$context['deletable'] = $context['exists'];
			if ($context['exists']) {
				$context['edit'] = site_url('office/reviews/'.$organisation.'/'.$context_type['content_codename'].'/information');
				$context['delete'] = site_url('office/reviews/'.$organisation);
				if (NULL !== $context_type['timestamp']) {
					$context['updated'] = date('d/m/Y h:i', $context_type['timestamp']);
				} else {
					$context['updated'] = '';
				}
			} else {
				$context['create'] = site_url('office/reviews/'.$organisation);
			}
			$data['contexts'][$context_type['content_codename']] = $context;
		}

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_overview', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	/// Reviews information page
	function information($ContextType, $organisation, $action = 'view', $revision_id = FALSE)
	{
		/// @todo add show all option backend
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_reviews_information');

		$editor_level = PermissionsSubset('editor', GetUserLevel());

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$data['page_information'] = $this->pages_model->GetPropertyWikitext('page_information');
		$data['context_type'] = $ContextType;
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('information');

		//test to allow a person to view deleted revisions
		$show_all_revisions = false;
		if ($action=='viewall') {
			if ($editor_level) {
				$show_all_revisions = true;
			} else {
				$this->messages->AddMessage('error','You do not have permission to view deleted revisions');
			}
			$action = 'view';
		}
		
		if ($action=='assign'){
			//There are two types of assignment. Url /assign/ where a user assigns themselfs. And by Posting a form, where an editor can assign anyone.
			$this->load->model('pr_model');
			$content_type_id = $this->pr_model->GetContentTypeId($ContextType);
			if(isset($_POST['assign_reporter'])){
				//There is form post, so treat and check as an editor
				if($editor_level){
					if($_POST['assign_reporter']=='unassign'){
						$this->pr_model->AssignReviewVenueToUser($data['organisation']['id'],$content_type_id);
						$this->messages->AddMessage('success','The assigned user has been removed.');
					}else{
						$user_id = (int) $_POST['assign_reporter'];//check for post
						$this->pr_model->AssignReviewVenueToUser($data['organisation']['id'],$content_type_id,$user_id);
						$this->messages->AddMessage('success','The user has been assigned to the venue.');
					}
				}else{
					$this->messages->AddMessage('error','Only aditors can assign someone else to a venue.');
				}
			}else{
				//there is no form post, so assume its a writer wanting to assign themselfs.
				$user_owns = $this->pr_model->IsUserAssignedToReviewVenue($ContextType, $organisation);
				if($user_owns){
					$this->pr_model->AssignReviewVenueToUser($data['organisation']['id'],$content_type_id, $this->user_auth->entityId);
					$this->messages->AddMessage('success','You have been assigned to this venue.');
				}else{
					$this->messages->AddMessage('error','This venue is already assigned to someone else!');
				}
			}
			$revision_id = FALSE;//have used this parameter for user id! Better clear it so other functions dont think i want a revision.
			$action = 'view';
		}
		
		if($action=='unassign'){
			//this action is only used by non editors wanting to unassign themselfs. Editors dont unassign people the reassign something to someone (inculding the null person)
			//Check the user is unassigning themselfs only!
			$this->load->model('pr_model');
			$content_type_id = $this->pr_model->GetContentTypeId($ContextType);
			$user_owns = $this->pr_model->IsUserAssignedToReviewVenue($ContextType, $organisation, $this->user_auth->entityId);
			if($user_owns){
				$this->pr_model->AssignReviewVenueToUser($data['organisation']['id'],$content_type_id);
				$this->messages->AddMessage('success','You have been unassigned from this venue.');
			}else{
				$this->messages->AddMessage('error','You can only unassign yourself from a venue.');
			}
			$revision_id = FALSE;
			$action = 'view';
		}
		
		if ($action=='delete') {
			if ($editor_level) {
				if (TRUE) {
					/// @todo Review context revision removal.
					$this->messages->AddMessage('error', 'Removal of revisions is not yet available');
				} else {
					$result = $this->directory_model->FlagEntryRevisionAsDeletedById($organisation, $revision);
					if ($result == 1) {
						$this->messages->AddMessage('success','Directory revision successfully removed.');
					} else {
						$this->messages->AddMessage('error','Directory revision was not removed, revision does not exist or is live.');
					}
				}
			} else {
				$this->messages->AddMessage('error','You do not have permission to remove revisions.');
			}
			$action='view';
		}

		if ($action=='restore') {
			//Check Permissions
			if ($editor_level) {
				if (TRUE) {
					/// @todo Review context revision restoration.
					$this->messages->AddMessage('error', 'Restoration of revisions is not yet available');
				} else {
					//Send and get data
					$result = $this->directory_model->FlagEntryRevisionAsDeletedById($organisation, $revision, false);
					if ($result == 1) {
						$this->messages->AddMessage('success','Directory revision was restored successfully.');
					} else {
						$this->messages->AddMessage('error','Directory revision was not restored it does not exist or it is not deleted.');
					}
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
				$result = $this->review_model->PublishContextContentRevision($organisation, $ContextType, $revision_id);
				if ($result) {
					$this->messages->AddMessage('success','Review page revision was published successfully.');
				} else {
					$this->messages->AddMessage('error','Review page revision was not published as it does not exist or is already live.');
				}
			} else {
				$this->messages->AddMessage('error','You do not have permission to publish revisions');
			}
			$action='view';
		}

		if ('preview' === $action) {
			$here = site_url('office/reviews/'.$organisation.'/'.$ContextType.'/information');

			$revision = $this->review_model->GetReviewContextContentRevisions($organisation, $ContextType, $revision_id===TRUE ? -1 : $revision_id);
			if (!array_key_exists(0, $revision)) {
				$action = 'view';
			} else {

				//Show a toolbar in a message for the preview.
				$published = $revision[0]['published'];
				$user_level = GetUserLevel();
				$is_deleted = $revision[0]['deleted'];
				if ($published) {
					$message = 'This is a preview of the current published review page.<br />';
				} else {
					if ($is_deleted) {
						$message = 'This is a preview of a <span class="red">deleted</span> review page revision.<br />';
					} else {
						$message = 'This is a preview of a review page revision.<br />';
					}
				}
				$message .= '<a href="'.$here.'/view/'.$revision_id.'">Go Back</a>';

				if ($published == false) {
					if ($editor_level) {
						$message .= ' | <a href="'.$here.'/publish/'.$revision_id.'">Publish This Revision</a>';
					}

					if ($is_deleted) {
						if ($editor_level) {
							$message .= ' | <a href="'.$here.'/restore/'.$revision_id.'">Restore This Revision</a>';
						}
					} else {
						$message .= ' | <a href="'.$here.'/delete/'.$revision_id.'">Delete This Revision</a>';
					}
				}

				$this->messages->AddMessage('information',$message);

				$this->load->library('Review_views');
				$this->review_views->SetRevision(is_numeric($revision_id) ? $revision_id : -1);
				$this->review_views->DisplayReview($ContextType,$organisation);
			}
		}

		if ('view' === $action) {
			$this->load->model('requests_model');
			$this->load->model('article_model');
			// Insert main text from pages information (sample)
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

			// Handle submitted data
			if ($this->input->post('reviewinfo_rating') != false)
			{
				// Set up validation library
				$this->load->library('validation');
				$this->validation->set_error_delimiters('<li>','</li>');

				// Specify validation rules
				$rules['reviewinfo_about'] = 'trim|required|xss_clean';
				$rules['reviewinfo_rating'] = 'trim|required|numeric';
				$rules['reviewinfo_js_rating'] = 'trim|required|numeric';
				$rules['reviewinfo_use_js_rating'] = 'trim|required|numeric';
				$rules['reviewinfo_quote'] = 'trim|required|xss_clean';
				$rules['reviewinfo_recommended'] = 'trim|xss_clean';
				$rules['reviewinfo_average_price'] = 'trim|xss_clean';
				$rules['reviewinfo_serving_hours'] = 'trim|xss_clean';
				$this->validation->set_rules($rules);

				// Set field names for displaying in error messages
				$fields['reviewinfo_about'] = 'blurb';
				$fields['reviewinfo_rating'] = 'rating';
				$fields['reviewinfo_js_rating'] = 'js_rating';
				$fields['reviewinfo_use_js_rating'] = 'use_js_rating';
				$fields['reviewinfo_quote'] = 'quote';
				$fields['reviewinfo_recommended'] = 'recommended item';
				$fields['reviewinfo_average_price'] = 'average price';
				$fields['reviewinfo_serving_hours'] = 'serving hours';
				$this->validation->set_fields($fields);

				// Run validation
				$errors = array();
				if ($this->validation->run())
				{
					if ($this->input->post('reviewinfo_deal_expires') != false)
					{
						if (!$this->input->post('reviewinfo_deal'))
							array_push($errors, 'Please enter deal information or remove the deal expiry date.');
						if (strtotime($this->input->post('reviewinfo_deal_expires')) == false)
							array_push($errors, 'Please enter the deal expiry date in the format yyyy-mm-dd');
					}

					// If there are no errors, insert data into database
					if (count($errors) == 0)
					{
						//The rating could have come from the nice js or the ugly drop down list, check which was being used.
						if($this->input->post('reviewinfo_use_js_rating')){
							$rating = $this->input->post('reviewinfo_js_rating');
						}else{
							$rating = $this->input->post('reviewinfo_rating');
						}
						if ($this->review_model->SetReviewContextContent(
							$organisation,
							$ContextType,
							$this->user_auth->entityId,
							$this->input->post('reviewinfo_about'),
							$this->input->post('reviewinfo_quote'),
							$this->input->post('reviewinfo_average_price'),
							$this->input->post('reviewinfo_recommended'),
							$rating,
							$this->input->post('reviewinfo_serving_hours')
						)) {
							$this->messages->AddMessage('success','Review information updated.');
						} else {
							$this->messages->AddMessage('error','Review information could not be updated.');
						}
					}
				}

				// If there are errors, display them
				if ($this->validation->error_string != '') {
					$this->messages->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
				} elseif (count($errors) > 0) {
					$temp_msg = '';
					foreach ($errors as $error) $temp_msg .= '<li>' . $error . '</li>';
					$this->messages->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
				}
			}

			// Get revision data from model
			$data['revisions'] = $this->review_model->GetReviewContextContentRevisions($organisation, $ContextType);
			$data['show_all_revisions'] = $show_all_revisions;
			$data['user_is_editor'] = $editor_level;
			
			//get assigned user stuff
			$data['reviewers'] = $this->requests_model->getReporters();
			
			$data['assigned_user_you'] = $this->pages_model->GetPropertyWikitext('assigned_user_you');
			$data['assigned_user_none'] = $this->pages_model->GetPropertyWikitext('assigned_user_none');
			$data['assigned_user_editor'] = $this->pages_model->GetPropertyWikitext('assigned_user_editor');
			
			// Get context contents from model
			$data['main_revision'] = $this->review_model->GetReviewContextContents($organisation, $ContextType, $revision_id);
			if ($data['main_revision'] == FALSE)
			{
				//Error is not needed, as the blanks make it obvious that no review context exists. Nse500
				//$this->messages->AddMessage('error', 'Review context '.$revision_id.' does not exist');
				$data['main_revision']['content_id'] = 0;
				$data['main_revision']['content_blurb'] = '';
				$data['main_revision']['content_quote'] = '';
				$data['main_revision']['average_price'] = '';
				$data['main_revision']['recommended_item'] = '';
				$data['main_revision']['content_rating'] = 5;
				$data['main_revision']['serving_times'] = '';
				$data['main_revision']['deal'] = '';
				$data['main_revision']['deal_expires'] = '';
			}
			//get reviews for areas for attention 
			$temp_reviews = $this->review_model->GetOrgReviews($ContextType, $data['organisation']['id']);
			if (is_array($temp_reviews)) {
				foreach($temp_reviews as $review)
				{
					$temp['writers'] = $this->requests_model->GetWritersForArticle($review['id']);
					$temp['article'] = $this->article_model->GetArticleHeader($review['id']);
					$temp['article']['id'] = $review['id'];
					$data['reviews'][] = $temp;
				}
			}
			// Set up the public frame
			$this->main_frame->SetContentSimple('reviews/office_review_information', $data);
		}

		$this->main_frame->SetTitleParameters(array(
			'organisation' => $data['organisation']['name'],
			'content_type' => ucfirst($ContextType),
		));

		// Load the public frame view
		$this->main_frame->Load();
	}

	function addtag()
	{
		if (!CheckPermissions('office')) return;
		$organisation_id = $this->review_model->TranslateDirectoryToID($_POST['organisation_name']);
		if(empty($_POST['tag'])){
			$this->messages->AddMessage('error','No tag was selected to add.');
		}else{
			$tag_id = $this->review_model->TranslateTagNameToID($_POST['tag']);
			$this->review_model->SetOrganisationTag($organisation_id,$tag_id);
		}
		redirect('/office/reviews/'.$_POST['organisation_name'].'/'.$_POST['context_type'].'/tags');
	}

	function deltag()
	{
		if (!CheckPermissions('office')) return;
		$organisation_id = $this->review_model->TranslateDirectoryToID($_POST['organisation_name']);
		if(empty($_POST['tag'])){
			$this->messages->AddMessage('error','No tag was selected to delete.');
		}else{
			$tag_id = $this->review_model->TranslateTagNameToID($_POST['tag']);
			$this->review_model->RemoveOrganisationTag($organisation_id,$tag_id);
		}
		redirect('/office/reviews/'.$_POST['organisation_name'].'/'.$_POST['context_type'].'/tags');
	}

	function tags($ContextType, $organisation)
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_review_tags');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('tags');

		$data['page_information'] = $this->pages_model->GetPropertyWikitext('page_information');

		//Pass organisation name to the page for submitting to add/del tag
		$data['organisation_name'] = $organisation;

		//Pass content type to the page for redirecting after submitting a tag
		$data['context_type'] = $ContextType;

		//Get a list of the tags for this organisation
		$data['existing_tags'] = $this->review_model->GetTagOrganisation($ContextType,$organisation);

		//Get a list of tags which can be added
		$data['new_tags'] = $this->review_model->GetTagWithoutOrganisation($ContextType,$organisation);

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_tags', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => ucfirst($ContextType)));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	function addleague()
	{
		if (!CheckPermissions('office')) return;
		$organisation_id = $this->review_model->TranslateDirectoryToID($_POST['organisation_name']);
		if(empty($_POST['league_id'])){
			$this->messages->AddMessage('error','No league was selected to add.');
		}else{
			$this->leagues_model->AddToLeague($_POST['league_id'], $organisation_id);
		}
		redirect('/office/reviews/'.$_POST['organisation_name'].'/'.$_POST['context_type'].'/leagues');
	}

	function delleague()
	{
		if (!CheckPermissions('office')) return;
		$organisation_id = $this->review_model->TranslateDirectoryToID($_POST['organisation_name']);
		if(empty($_POST['league_id'])){
			$this->messages->AddMessage('error','No league was selected to delete.');
		}else{
			$this->leagues_model->RemoveFromLeague($_POST['league_id'], $organisation_id);
		}
		redirect('/office/reviews/'.$_POST['organisation_name'].'/'.$_POST['context_type'].'/leagues');
	}
	
	function leagues($ContextType, $organisation)
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_review_leagues');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('leagues');

		$data['page_information'] = $this->pages_model->GetPropertyWikitext('page_information');

		//Pass organisation name to the page for submitting to add/del tag
		$data['organisation_name'] = $organisation;

		//Pass content type to the page for redirecting after submitting a tag
		$data['context_type'] = $ContextType;

		//Get a list of the tags for this organisation
		$data['existing_leagues'] = $this->leagues_model->GetVenuesLeagues($data['organisation']['id']);
		
		//Get a list of tags which can be added
		$all_leagues = $this->leagues_model->GetAllLeaguesSimple($data['organisation']['id']);
		$data['new_leagues'] = array();
		foreach ($all_leagues as $league){
			if($this->leagues_model->IsVenueInLeague($league['id'],$data['organisation']['id'])==false){
				$data['new_leagues'][] = $league;
			}
		}
		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_leagues', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => ucfirst($ContextType)));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function photos($ContextType, $organisation)
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_review_photos');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);

		// Insert main text from pages information (sample)

//jadded
		if (!empty($data)) {
			$action = $this->uri->segment(6);
			$photoID = $this->uri->segment(7);
			$operation = $this->uri->segment(8);
			$this->load->model('slideshow');
			$this->_SetupNavbar($organisation,$ContextType);
			$this->main_frame->SetPage('photos');
			$this->load->helper('url');
			$this->load->library('image');
			$this->load->library('image_upload');
			$data['ContextType'] = $ContextType;
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['disclaimer_text'] = $this->pages_model->GetPropertyWikitext('disclaimer_text');
			if ($action == 'move') { // Switch hates me, this should be case switch but i won't do it
				if ($operation == 'up') {
					$this->slideshow->pushUp($photoID, $data['organisation']['id']);
				} elseif ($operation == 'down') {
					$this->slideshow->pushDown($photoID, $data['organisation']['id']);
				}
			} elseif ($action == 'delete') {
				$this->slideshow->deletePhoto($photoID, $data['organisation']['id']);
				$this->messages->AddMessage('success', 'Photo Deleted');
			} elseif ($action == 'upload') {
				$this->xajax->processRequests();
				return $this->image_upload->recieveUpload('/office/reviews/'.$data['organisation']['shortname'].'/'.$ContextType.'/photos', array('slideshow'));
			} elseif (isset($_SESSION['img'])) {
				foreach ($_SESSION['img'] as $newID) {
					$this->slideshow->addPhoto($newID['list'], $data['organisation']['id']);
				}
				unset($_SESSION['img']);
			}

			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['disclaimer_text'] = $this->pages_model->GetPropertyWikitext('disclaimer_text');
			$data['oraganisation'] = $organisation; // why its spelt wrong? but def don't correct it!
			$data['images'] = $this->slideshow->getPhotos($data['organisation']['id']);

			// Set up the directory view
			$the_view = $this->frames->view('office/reviews/photos', $data);

			// Set up the public frame
			$this->main_frame->SetTitleParameters(
					array('organisation' => $data['organisation']['name']));
			$this->main_frame->SetPage('photos');
			$this->main_frame->IncludeJs('javascript/clone.js');
			$this->main_frame->SetContent($the_view);
		} else {
			$this->load->library('custom_pages');
			$this->main_frame->SetContent(new CustomPageView('directory_notindirectory','error'));
		}

		// Set up the view
		$the_view = $this->frames->view('office/reviews/photos', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
					  'content_type' => ucfirst($ContextType)));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function review($context_type, $organisation)
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('requests_model');
		$this->load->model('businesscards_model');
		$this->load->model('article_model');
		$this->pages_model->SetPageCode('office_review_reviews');
		

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$data['page_information'] = $this->pages_model->GetPropertyWikitext('page_information');
		$this->_SetupNavbar($organisation, $context_type);
		$this->main_frame->SetPage('reviews');

		/** store the parameters passed to the method so it can be
			used for links in the view */
		$data['parameters']['organistion'] = $organisation;
		$data['parameters']['context_type'] = $context_type;

		//$this->businesscards_model->NewBusinessCard(NULL, 3, 0, 'Default Name', 'Default Title', 'default blurb', 'Default Course', NULL,
		//	NULL, NULL, NULL, 'Default Address', 0, NULL, NULL);

		/*
		[a_review_author] => byline id of the author
		[a_review_text] => review data
		[r_submit_newreview] => submit button
		*/
		if (isset($_POST['r_submit_newreview']))
		{
			//create the article
			$article_id = $this->requests_model->CreateRequest(
				'request',
				$context_type,
				'',
				'',
				$this->user_auth->entityId,
				NULL);
			//set the articles organistion
			$this->requests_model->UpdateOrganisationID(
				$article_id,
				$data['organisation']['id']);
			//add the first revision from form data
			$this->requests_model->CreateArticleRevision(
				$article_id,
				$this->user_auth->entityId,
				'',
				'',
				'',
				$_POST['a_review_text'],
				'');
			//add the selected byline to the review
			$this->requests_model->AddUserToRequest(
				$article_id,
				$this->user_auth->entityId,
				$this->user_auth->entityId,
				$_POST['a_review_author']);
			//auto accept the review write request
			$this->requests_model->AcceptRequest(
				$article_id,
				$this->user_auth->entityId,
				$_POST['a_review_author']);
			//success
			$this->messages->AddMessage('success','Review Added.');
		}

		// Insert main text from pages information (sample)
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		//bylines
		$temp_bylines = $this->businesscards_model->GetBylines();
		foreach($temp_bylines as $byline)
		{
			if (is_null($byline['user_id']))
			{
				$data['bylines']['generic'][] = $byline;
			}
			else
			{
				$data['bylines']['user'][] = $byline;
			}
		}

		//reviews
		$data['reviews'] = array();
		$temp_reviews = $this->review_model->GetOrgReviews($context_type, $data['organisation']['id']);
		foreach($temp_reviews as $review)
		{
			$temp['writers'] = $this->requests_model->GetWritersForArticle($review['id']);
			$temp['article'] = $this->article_model->GetArticleHeader($review['id']);
			$temp['article']['id'] = $review['id'];
			$data['reviews'][] = $temp;
		}

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_reviews', $data);

		$this->main_frame->IncludeJs('javascript/wikitoolbar.js');

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => ucfirst($context_type)));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function reviewedit($context_type, $organisation, $article_id, $revision_id = -1)
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('requests_model');
		$this->load->model('businesscards_model');
		$this->load->model('article_model');
		$this->pages_model->SetPageCode('office_review_reviewedit');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$data['page_information'] = $this->pages_model->GetPropertyWikitext('page_information');
		$this->_SetupNavbar($organisation,$context_type);
		$this->main_frame->SetPage('reviews');

		if (isset($_POST['r_submit_save']))
		{
			$revision_id = $this->requests_model->CreateArticleRevision(
				$article_id,
				$this->user_auth->entityId,
				'',
				'',
				'',
				$_POST['a_review_text'],
				''
				)
				;
					$this->messages->AddMessage('success','New revision created for review.');
		}
		elseif (isset($_POST['r_submit_publish']))
		{
			$this->requests_model->UpdateRequestStatus(
				$article_id,
				'publish',
				array('content_id'=>$revision_id,
					'publish_date'=>date('y-m-d H:i:s'),
					'editor'=>$this->user_auth->entityId)
				);
			$this->messages->AddMessage('success','Review had been published.');
		}
		elseif (isset($_POST['r_submit_pull']))
		{
			$this->article_model->PullArticle($article_id, $this->user_auth->entityId);
			$this->requests_model->UpdatePulledToRequest($article_id, $this->user_auth->entityId);
			$this->messages->AddMessage('success','Review has been pulled from publication.');
		}
		elseif (isset($_POST['r_submit_delete']))
		{
			$this->requests_model->RejectSuggestion($article_id);
			$this->messages->AddMessage('success','Review has been deleted.');
			redirect('/office/reviews/'.$organisation.'/'.$context_type.'/review');
		}

		/** store the parameters passed to the method so it can be
			used for links in the view */
		$data['parameters']['article_id'] = $article_id;
		$data['parameters']['revision_id'] = $revision_id;
		$data['parameters']['organisation'] = $organisation;
		$data['parameters']['context_type'] = $context_type;

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		$writers = $this->requests_model->GetArticleWriters($article_id);
		$found = false;
		foreach($writers as $writer)
		{
			if ($writer['id'] == $data['user']['id'])
				$found = $data['user']['id'];
		}

		if ($found == false && $data['user']['officetype'] == 'Low')
		{
			$this->messages->AddMessage('error','Your are not a writer of this review. Can\'t edit.');
			redirect('/office/reviews/'.$organisation.'/'.$context_type.'/review');
		}

		/** get the article's header for the article id passed to
			the function */
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);

		if ($data['article']['header']['organisation'] != $data['organisation']['id'])
		{
			$this->messages->AddMessage('error','Specified review is for a different organisation. Can\'t edit.');
			redirect('/office/reviews/'.$organisation.'/'.$context_type.'/review');
		}

		//get the list of current question revisions
		$data['article']['revisions'] = $this->requests_model->GetArticleRevisions($article_id);

		//set the default revision to false
		$data['article']['displayrevision'] = FALSE;

		//if the revision id is set to the default
		if ($revision_id == -1)
		{
			/* is a published article, therefore
			   load the live content revision */
			if ($data['article']['header']['live_content'] != FALSE)
			{
				$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($article_id, $data['article']['header']['live_content']);
				$data['parameters']['revision_id'] = $data['article']['displayrevision']['id'];
				if($data['parameters']['revision_id']) {
					redirect('/office/reviews/'.$organisation.'/'.$context_type.'/reviewedit/'.$article_id.'/'.$data['parameters']['revision_id']);
				}
			}
			/* no live content, therefore is a
			   request, so load the latest
			   revision as default */
			else
			{
				//make sure a revision exists
				if (isset($data['article']['revisions'][0]))
				{
					$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($article_id, $data['article']['revisions'][0]['id']);
					$data['parameters']['revision_id'] = $data['article']['displayrevision']['id'];
					if($data['parameters']['revision_id']) {
						redirect('/office/reviews/'.$organisation.'/'.$context_type.'/reviewedit/'.$article_id.'/'.$data['parameters']['revision_id']);
					}
				}
			}
		}
		else
		{
			/* load the revision with the given
			   revision id */
			$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($article_id, $revision_id);
			/* if this revision doesn't exist
			   then return an error */
			if ($data['article']['displayrevision'] == FALSE)
			{
				$this->messages->AddMessage('error','Specified revision doesn\'t exist for this review. Default selected.');
				redirect('/office/reviews/'.$organisation.'/'.$context_type.'/reviewedit/'.$article_id.'/');
			}
		}

		$data['this_url'] = '/office/reviews/'.$organisation.'/'.$context_type.'/reviewedit/'.$article_id.'/'.$revision_id;

		//bylines
		$temp_bylines = $this->businesscards_model->GetBylines();
		foreach($temp_bylines as $byline)
		{
			if (is_null($byline['user_id']))
			{
				$data['bylines']['generic'][] = $byline;
			}
			else
			{
				$data['bylines']['user'][] = $byline;
			}
		}

		// Insert main text from pages information (sample)
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_reviewedit', $data);

		$this->main_frame->IncludeJs('javascript/wikitoolbar.js');

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => ucfirst($context_type)));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function comments($ContextType, $organisation, $Action = 'view', $IncludedComment = 0)
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_review_comments');
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('comments');

		//This needs to be altered to throw errors incase of unknown content_types...
		$content_id = $this->review_model->GetContentTypeID($ContextType);
		$data = $this->organisations->_GetOrgData($organisation);
		$data['context_type'] = $ContextType;
		$data['page_information'] = $this->pages_model->GetPropertyWikitext('page_information');
		
		$organisation_id = $data['organisation']['id'];

		$this->load->library('comment_views');
		$this->comment_views->SetUri('/office/reviews/'.$organisation.'/'.$ContextType.'/comments/view/');
		$thread = $this->review_model->GetReviewContextOfficeCommentThread($organisation_id, $content_id);
		$data['comments'] = $this->comment_views->CreateStandard($thread, $IncludedComment);

		$this->main_frame->SetContentSimple('reviews/office_review_comments', $data);
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => ucfirst($ContextType)));

		// Load the public frame view
		$this->main_frame->Load();
	}
}
?>
