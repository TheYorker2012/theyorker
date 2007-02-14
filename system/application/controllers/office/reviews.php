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

	// this is blank 
	function index()
	{
	
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
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('photos');

		// Insert main text from pages information (sample)
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

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
	
	function review($ContextType, $organisation)
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_review_reviews');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('reviews');

		// Insert main text from pages information (sample)
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_reviews', $data);
	
		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => $ContextType));
		$this->main_frame->SetContent($the_view);
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function reviewedit($ContextType, $organisation, $ArticleId)
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_review_reviewedit');

		//Get navigation bar and tell it the current page
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupNavbar($organisation,$ContextType);
		$this->main_frame->SetPage('reviews');

		// Insert main text from pages information (sample)
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('reviews/office_review_reviewedit', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
						'content_type' => $ContextType));
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
}
?>
