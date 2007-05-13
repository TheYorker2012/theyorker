<?php

/// Office Reviews
/**
 * @author Nick Evans (nse500@cs.york.ac.uk)
 * @author Frank Burton (frb501@cs.york.ac.uk)
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
		$navbar->AddItem('comments', 'Comments',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/comments');
		$navbar->AddItem('reviews', 'Reviews',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/review');
		$navbar->AddItem('photos', 'Photos',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/photos');
		$navbar->AddItem('tags', 'Tags',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/tags');
		$navbar->AddItem('information', 'Information',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/information');
	}

	/// Main reviews page
	function index()
	{
		if (!CheckPermissions('office')) return;
		
		$this->main_frame->load();
	}

	/// Reviews Overview Page
	function overview($organisation)
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_reviews_overview');

		$data = $this->organisations->_GetOrgData($organisation);

		// Insert main text from pages information (sample)
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_overview', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	// Reviews information page
	function information($ContextType, $organisation)
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_reviews_information');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$data['context_type'] = $ContextType;
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('information');

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
    		$rules['reviewinfo_quote'] = 'trim|required|xss_clean';
    		$rules['reviewinfo_recommended'] = 'trim|xss_clean';
    		$rules['reviewinfo_average_price'] = 'trim|numeric';
    		$rules['reviewinfo_serving_hours'] = 'trim|xss_clean';
    		$rules['reviewinfo_deal'] = 'trim|xss_clean';
    		$rules['reviewinfo_deal_expires'] = 'trim|xss_clean';
    		$this->validation->set_rules($rules);
    		
    		// Set field names for displaying in error messages
    		$fields['reviewinfo_about'] = 'blurb';
    		$fields['reviewinfo_rating'] = 'rating';
    		$fields['reviewinfo_quote'] = 'quote';
    		$fields['reviewinfo_recommended'] = 'recommended item';
    		$fields['reviewinfo_average_price'] = 'average price';
    		$fields['reviewinfo_serving_hours'] = 'serving hours';
    		$fields['reviewinfo_deal'] = 'deal';
    		$fields['reviewinfo_deal_expires'] = 'deal expiry date';
    		$this->validation->set_fields($fields);
    		
    		// Run validation
    		$errors = array();
    		if ($this->validation->run())
    		{
        		if ($this->input->post('reviewinfo_deal_expires') != false)
        		{
            		if (!$this->input->post('reviewinfo_deal')) array_push($errors, 'Please enter deal information or remove the deal expiry date.');
            	    if (strtotime($this->input->post('reviewinfo_deal_expires')) == false) array_push($errors, 'Please enter the deal expiry date in the format yyyy-mm-dd');
        	    }
    
    			// If there are no errors, insert data into database
    			if (count($errors) == 0) 
    			{
        			$this->review_model->SetReviewContextContent(
        			    $organisation,
        			    $ContextType,
        			    $this->user_auth->entityId,
        			    $this->input->post('reviewinfo_about'),
        			    $this->input->post('reviewinfo_quote'),
        			    $this->input->post('reviewinfo_average_price'),
        			    $this->input->post('reviewinfo_recommended'),
        			    $this->input->post('reviewinfo_rating'),
        			    $this->input->post('reviewinfo_serving_hours'),
        			    $this->input->post('reviewinfo_deal'),
        			    $this->input->post('reviewinfo_deal_expires')
        			);
        			$this->main_frame->AddMessage('success','Review information updated.');
    			}
    		}

    		// If there are errors, display them
    		if ($this->validation->error_string != '') $this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
    		elseif (count($errors) > 0) {
    			$temp_msg = '';
    			foreach ($errors as $error) $temp_msg .= '<li>' . $error . '</li>';
    			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
    		}
		}
		
		// Get revision data from model
		$data['revisions'] = $this->review_model->GetReviewContextContentRevisions($organisation, $ContextType);
		
		// Get context contents from model
		$context_contents = $this->review_model->GetReviewContextContents($organisation, $ContextType);
		if (isset($context_contents[0])) $data = array_merge($data, $context_contents[0]);
		else
		{
    		$data['content_blurb'] = '';
    		$data['content_quote'] = '';
    		$data['average_price'] = '';
    		$data['recommended_item'] = '';
    		$data['content_rating'] = 5;
    		$data['serving_times'] = '';
    		$data['deal'] = '';
    		$data['deal_expires'] = '';
		}

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_information', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => ucfirst($ContextType)));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function addtag()
	{
		$organisation_id = $this->review_model->TranslateDirectoryToID($_POST['organisation_name']);
		$tag_id = $this->review_model->TranslateTagNameToID($_POST['tag']);

		$this->review_model->SetOrganisationTag($organisation_id,$tag_id);
		redirect('/office/reviews/'.$_POST['organisation_name'].'/'.$_POST['context_type'].'/tags');
	}

	function deltag()
	{
		$organisation_id = $this->review_model->TranslateDirectoryToID($_POST['organisation_name']);
		$tag_id = $this->review_model->TranslateTagNameToID($_POST['tag']);
		$this->review_model->RemoveOrganisationTag($organisation_id,$tag_id);
		redirect('/office/reviews/'.$_POST['organisation_name'].'/'.$_POST['context_type'].'/tags');
	}

	function delcomment()
	{
		$context = $this->uri->segment(4);
		$organisation = $this->uri->segment(5);
		$comment_id = $this->uri->segment(6);
		$this->review_model->DeleteComment($comment_id);
		redirect('/office/reviews/'.$organisation.'/'.$context.'/comments');
	}

	function tags($ContextType, $organisation)
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_reviews_tags');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('tags');
		
		// Insert main text from pages information (sample)
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

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
						'content_type' => $ContextType));
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
			$this->load->helper(array('images', 'url'));
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
				if ($operation == 'confirm') {
					$this->slideshow->deletePhoto($photoID, $data['organisation']['id']);
					$this->messages->AddMessage('info', 'Photo Deleted');
				} else {
					$this->messages->AddMessage('info', 'Are you sure? <a href="'.$photoID.'/confirm">Click to delete</a>');
				}
			} elseif ($action == 'upload') {
				$this->xajax->processRequests();
				return $this->image_upload->recieveUpload('office/reviews/'.$data['organisation']['shortname'].'/'.$ContextType.'/photos', array('slideshow'));
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

		// Set up the view
		$the_view = $this->frames->view('directory/viparea_directory_photos', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
				      'content_type' => $ContextType));
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
				$_POST['a_review_author'],
				$this->user_auth->entityId,
				$this->user_auth->entityId);
			//auto accept the review write request
			$this->requests_model->AcceptRequest(
				$article_id,
				$this->user_auth->entityId,
				$_POST['a_review_author']);
			//success
			$this->main_frame->AddMessage('success','Review Added.');
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
	
		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => $context_type));
		$this->main_frame->SetContent($the_view);
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function reviewedit($context_type, $organisation, $article_id, $revision_id)
	{
		if (!CheckPermissions('office')) return;
		
		$this->load->model('requests_model');
		$this->load->model('businesscards_model');
		$this->load->model('article_model');
		$this->pages_model->SetPageCode('office_review_reviewedit');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
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
	                $this->main_frame->AddMessage('success','New revision created for review.');
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
			$this->main_frame->AddMessage('success','Review had been published.');
		}
		elseif (isset($_POST['r_submit_pull']))
		{
			$this->article_model->PullArticle($article_id, $this->user_auth->entityId);
			$this->requests_model->UpdatePulledToRequest($article_id, $this->user_auth->entityId);
			$this->main_frame->AddMessage('success','Review has been pulled from publication.');
		}
		elseif (isset($_POST['r_submit_delete']))
		{
			$this->requests_model->RejectSuggestion($article_id);
			$this->main_frame->AddMessage('success','Review has been deleted.');
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
		
		if ($found == false && $data['user']['officetype'] = 'Low')
		{
			$this->main_frame->AddMessage('error','Your are not a writer of this review. Can\'t edit.');
			redirect('/office/reviews/'.$organisation.'/'.$context_type.'/review');
		}
		
		/** get the article's header for the article id passed to 
		    the function */
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);
		
		if ($data['article']['header']['organisation'] != $data['organisation']['id'])
		{
			$this->main_frame->AddMessage('error','Specified review is for a different organisation. Can\'t edit.');
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
				$this->main_frame->AddMessage('error','Specified revision doesn\'t exist for this review. Default selected.');
				redirect('/office/reviews/'.$organisation.'/'.$context_type.'/reviewedit/'.$article_id.'/');
			}
		}
		
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

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => $context_type));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function comments($ContextType, $organisation)
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_review_comments');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('comments');

		// Insert main text from pages information (sample)
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		//Find last article id
		$article_id = $this->review_model->GetArticleID($organisation,$this->review_model->FindContentID($ContextType));

		if (isset($article_id[0])) //Check that a article exists
		{
			$article_id = $article_id[0];

			//Get user comments for moderation
			$data['comments'] 	= $this->review_model->GetComments($organisation,$this->review_model->FindContentID($ContextType),$article_id);

		}

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_comments', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => $ContextType));
		$this->main_frame->SetContent($the_view);
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	
	// These are all the edit pages for the admin panel
	// Additional controllers will be required
	
	/*function edit()
	{
		if (!CheckPermissions('public')) return;
		
		$data['title_image'] = 'images/prototype/reviews/reviews_01.gif';
		
		// Set up the public frame
		$this->main_frame->SetTitle('Edit');
		$this->main_frame->SetContentSimple('reviews/mainedit', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	function editsection()
	{
		if (!CheckPermissions('public')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('Edit Section');
		$this->main_frame->SetContentSimple('reviews/sectionedit');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	function editreview()
	{
		if (!CheckPermissions('public')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('Edit Review');
		$this->main_frame->SetContentSimple('reviews/reviewedit');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}*/
}
?>
