<?php

/// Office How Do I Pages.
/**
 * @author Richard Ingle (ri504@cs.york.ac.uk)
 */

/* TODO

	*can't publish a request with no revisions

*/
class Howdoi extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->helper('text');
		$this->load->helper('wikilink');
	}

	/// Set up the navigation bar
	private function _SetupNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('suggestions', 'Suggestions',
				'/office/howdoi/suggestions');
		$navbar->AddItem('requests', 'Articles',
				'/office/howdoi/requests');
		$navbar->AddItem('published', 'Live',
				'/office/howdoi/published');
		$navbar->AddItem('categories', 'Categories',
				'/office/howdoi/categories');
	}

	/**
	 * Loads the default howdoi office page, depending
	 * on the type of the user.
	 */
	function index()
	{
		if (!CheckPermissions('office')) return;

		if ($this->user_auth->officeType == 'Low')
			self::published();
		if ($this->user_auth->officeType == 'High')
			self::suggestions();
		if ($this->user_auth->officeType == 'Admin')
			self::categories();

	}

	/**
	 * Calls the getdata method on suggestions.
	 */
	function suggestions()
	{
		self::getdata('suggestions');
	}

	/**
	 * Calls the getdata method on requests.
	 */
	function requests()
	{
		self::getdata('requests');
	}

	/**
	 * Calls the getdata method on published questions.
	 */
	function published()
	{
		self::getdata('published');
	}

	/**
	 * This sets up the view for the 3 types of questions:
	 * - Suggestions
	 * - Requests
	 * - Published (unpublished, published and pulled)
	 * @param $page is the type of the question to get the data for
	 */
	function getdata($page)
	{
		//has the user got access to the office
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_howdoi_questions');
		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('requests_model','requests_model');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		if ($page == 'suggestions')
		{
			$this->main_frame->SetPage('suggestions');
			$this->pages_model->SetPageCode('office_howdoi_suggestions');
		}
		else if ($page == 'requests')
		{
			$this->main_frame->SetPage('requests');
			$this->pages_model->SetPageCode('office_howdoi_requests');
		}
		else if ($page == 'published')
		{
			$this->main_frame->SetPage('published');
			$this->pages_model->SetPageCode('office_howdoi_published');
		}

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		/** This is added up later on to count the number of questions
		    which require attention. */
		$data['status_count'] = array('suggestions'=>0,
						'requests'=>0,
						'unpublished'=>0,
						'published'=>0,
						'pulled'=>0
						);

		//get the howdoi parent type id
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		// get the list of howdoi content type categories
		$data['categories'] = $this->howdoi_model->GetContentCategories($howdoi_type_id);

		// add the unassigned type
		$data['categories'][$howdoi_type_id] = array(
					'codename'=>'howdoi',
					'name'=>'Unassigned',
					'suggestions'=>$this->requests_model->GetSuggestedArticles('howdoi')
					);
					/*
			echo '<pre>';
			echo print_r($data['categories']);
			echo '</pre>';
			*/

		//create empty arrays for the user writers status
		$data['user']['writer']['requested'] = array();
		$data['user']['writer']['accepted'] = array();


		/* go through all categories and get its data for the
		    different question types */

		foreach ($data['categories'] as $category_id => $category)
		{
			//suggestions
			$data['categories'][$category_id]['suggestions'] = $this->requests_model->GetSuggestedArticles($category['codename'], FALSE);
			$data['status_count']['suggestions'] = $data['status_count']['suggestions'] + count($data['categories'][$category_id]['suggestions']);
			//due to the change in requests model must now get each article header in full
			foreach ($data['categories'][$category_id]['suggestions'] as &$suggestion)
			{
				$suggestion = $this->requests_model->GetSuggestedArticle($suggestion['id']);
			}
			//requests
			$data['categories'][$category_id]['requests'] = $this->requests_model->GetRequestedArticles($category['codename'], FALSE);
			$data['status_count']['requests'] = $data['status_count']['requests'] + count($data['categories'][$category_id]['requests']);
			//due to the change in requests model must now get each article header in full
			foreach ($data['categories'][$category_id]['requests'] as &$request)
			{
				$request = $this->requests_model->GetRequestedArticle($request['id']);
			}
			//unpublished
			$data['categories'][$category_id]['unpublished'] = $this->requests_model->GetPublishedArticles($category['codename'], FALSE);
			$data['status_count']['unpublished'] = $data['status_count']['unpublished'] + count($data['categories'][$category_id]['unpublished']);
			//published
			$data['categories'][$category_id]['published'] = $this->requests_model->GetPublishedArticles($category['codename'], TRUE);
			$data['status_count']['published'] = $data['status_count']['published'] + count($data['categories'][$category_id]['published']);
			//pulled
			$data['categories'][$category_id]['pulled'] = $this->requests_model->GetPublishedArticles($category['codename'], TRUE, TRUE);
			$data['status_count']['pulled'] = $data['status_count']['pulled'] + count($data['categories'][$category_id]['pulled']);
			//article writer requests
			$temp_array = $this->requests_model->GetHowdoiWriterRequests($this->user_auth->entityId, $category_id, 'requested');
			$data['user']['writer']['requested'] = array_merge($data['user']['writer']['requested'], $temp_array);
			$temp_array = $this->requests_model->GetHowdoiWriterRequests($this->user_auth->entityId, $category_id, 'accepted');
			$data['user']['writer']['accepted'] = array_merge($data['user']['writer']['accepted'], $temp_array);
		}

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the view
		if ($page == 'suggestions')
			$the_view = $this->frames->view('office/howdoi/office_howdoi_suggestions', $data);
		else if ($page == 'requests')
			$the_view = $this->frames->view('office/howdoi/office_howdoi_requests', $data);
		else if ($page == 'published')
			$the_view = $this->frames->view('office/howdoi/office_howdoi_published', $data);

		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	/**
	 * This gets the list of categories from the model, so that they can
	 * be displayed in the view.
	 */
	function categories()
	{
		//has the user got access to the office
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_howdoi_categories');
		$this->load->model('howdoi_model','howdoi_model');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('categories');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		//get the howdoi parent type id
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		// get the list of howdoi content type categories
                $data['categories'] = $this->howdoi_model->GetCategoryNames($howdoi_type_id);

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_categories', $data);

		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	/**
	 * This get the question and revision data and finds the status of a
	 * question so that the different view boxes that are needed can be
	 * loaded in the view.
	 * @param $article_id is the id of the question to edit.
	 * @param $revision_id is the id of the revision to edit, if this is
	 *        set to -1 then the default revision is loaded.
	 */
	function questionedit($article_id, $revision_id)
	{
		//has the user got access to the office
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_howdoi_edit_question');
		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('requests_model','requests_model');
		$this->load->model('article_model','article_model');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		//get the howdoi parent type id
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('questions');

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['article_id'] = $article_id;
		$data['parameters']['revision_id'] = $revision_id;

		// get the list of howdoi content type categories
		$data['categories'] = $this->howdoi_model->GetCategoryNames($howdoi_type_id);

		// add the unassigned type
		$data['categories'][$howdoi_type_id] = array(
					'codename'=>'unassigned',
					'name'=>'Unassigned',
					);
					
		/** get the article's header for the article id passed to 
		    the function */
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);
		
		/** this checks to see if the article id given is for a how
		    do i question or is an article of another type */
		$correct_content_type = FALSE;
		foreach ($data['categories'] as $category_id => $category)
		{
                	if ($data['article']['header']['content_type'] == $category_id)
			$correct_content_type = TRUE;
		}

		//if the article is a how do i question
		if ($correct_content_type == TRUE)
		{
			//get the list of current question revisions
			$data['article']['revisions'] = $this->requests_model->GetArticleRevisions($article_id);
			
			//set the default revision to false
			$data['article']['displayrevision'] = FALSE;

			/** suggestions have no contents associated with them
			    so don't try to load any, if it is not a
			    suggestion then load the displayrevision */
			if ($data['article']['header']['suggestion_accepted'] == 1)
			{
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
	                			$this->main_frame->AddMessage('error','Specified revision doesn\'t exist for this question. Default selected.');
	                			redirect('/office/howdoi/editquestion/'.$article_id.'/');
	    				}
				}
			}

			//get the current users id and office access
			$data['user']['id'] = $this->user_auth->entityId;
			$data['user']['officetype'] = $this->user_auth->officeType;
			
			/* finds whether the current user is requested to
			   write for this question */
			$data['article']['hasarticlerequest'] = $this->requests_model->IsUserRequestedForArticle($article_id, $this->user_auth->entityId);
	
			// Set up the view
			$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_question', $data);

			// Set up the public frame
			$this->main_frame->SetContent($the_view);
	
			// Load the public frame view
			$this->main_frame->Load();
		}
		//otherwise for an invalid article id
		else
		{
                	$this->main_frame->AddMessage('error','Specified article is not editable.');
                	redirect('/office/howdoi/');
		}
	}

	/**
	 * This allows the editors to modify the request information
	 * and assign writers to the question
	 * @param $article_id is the id of the question to edit.
	 */
	function editrequest($article_id)
	{
		//has the user got access to the office
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_howdoi_edit_request');
		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('requests_model','requests_model');
		$this->load->model('article_model','article_model');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('requests');

		//get the howdoi parent type id
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['article_id'] = $article_id;

		// get the list of howdoi content type categories
                $data['categories'] = $this->howdoi_model->GetCategoryNames($howdoi_type_id);

                // add the unassigned type
		$data['categories'][$howdoi_type_id] = array(
					'codename'=>'unassigned',
					'name'=>'Unassigned',
					);
		/** get the article's header for the article id passed to
	            the function */
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);
		
		/** this checks to see if the article id given is for a how
 	            do i question or is an article of another type */
		$correct_content_type = FALSE;
		foreach ($data['categories'] as $category_id => $category)
		{
                	if ($data['article']['header']['content_type'] == $category_id)
			$correct_content_type = TRUE;
		}

		//if the article is a how do i question
		if ($correct_content_type == TRUE)
		{
			//get the current users id and office access
			$data['user']['id'] = $this->user_auth->entityId;
			$data['user']['officetype'] = $this->user_auth->officeType;

			//get the how do i writers
			$data['writers']['all'] = $this->requests_model->GetWritersForType('howdoi');

			//get writers for the current question
			$data['writers']['article'] = $this->requests_model->GetWritersForArticle($article_id);

			//set a count for the available writers
			$data['writers']['availcount'] = 0;
			
			/* loop though the possible writers and remove the
			   writers that are already involved with this
			   article */
			foreach ($data['writers']['all'] as $writer)
			{
				$inselection = FALSE;
				foreach ($data['writers']['article'] as $articlewriter)
				{
					if ($articlewriter['id'] == $writer['id'])
						$inselection = TRUE;
				}
				if ($inselection == FALSE)
				{
					$data['writers']['available'][] = $writer;
					$data['writers']['availcount'] = $data['writers']['availcount'] + 1;
				}
			}

			// Set up the view
			$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_request', $data);

			// Set up the public frame
			$this->main_frame->SetContent($the_view);
	
			// Load the public frame view
			$this->main_frame->Load();
		}
		//otherwise for an invalid article id
		else
		{
                	//$this->main_frame->AddMessage('error','Specified request is not editable.');
                	//redirect('/office/howdoi/');
		}
	}

	/**
	 * This function contains all the form submit processing for
	 * category editing.
	 */
	function categorymodify()
	{
		//has the user got access to the office
		if (!CheckPermissions('office')) return;

		//load the required model
		$this->load->model('howdoi_model','howdoi_model');

		//get the howdoi parent type id
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		/* Loads the category edit page
		   $_POST data passed
		   - r_redirecturl => the url the form submit came from
    		   - r_categoryid => the category id to edit
    		   - r_submit_edit => the name of the submit button
		*/
		if (isset($_POST['r_submit_edit']))
		{
			//set the page code
			$this->pages_model->SetPageCode('office_howdoi_edit_category');

			//Get navigation bar and tell it the current page
			$this->_SetupNavbar();
			$this->main_frame->SetPage('categories');
	
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			
			/* get the data for the category provided in the post 
			   data */
			$data['category'] = $this->howdoi_model->GetContentCategory($_POST['r_categoryid']);
			$data['category']['id'] = $_POST['r_categoryid'];
	
			// Set up the view
			$this->main_frame->SetTitleParameters(array('category'=>$data['category']['name']));
			$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_category', $data);
	
			// Set up the public frame
			$this->main_frame->SetContent($the_view);
	
			// Load the public frame view
			$this->main_frame->Load();
		}
		/* Deletes the category with the id passed
		   $_POST data passed
		   - r_redirecturl => the url the form submit came from
    		   - r_categoryid => the category id to delete
    		   - r_submit_delete => the name of the submit button
		*/
		else if (isset($_POST['r_submit_delete']))
		{
			$this->howdoi_model->DeleteCategory($_POST['r_categoryid']);
                	$this->main_frame->AddMessage('success','Category deleted.');
			redirect($_POST['r_redirecturl']);
		}
		/* Saves the updated data for the given category
		   $_POST data passed
		   - r_redirecturl => the url the form submit came from
    		   - r_categoryid => the category id to update
    		   - a_name => the name of the category
    		   - a_codename => the codename used in the address bar
    		   - a_blurb => a general blurb about the category
    		   - r_submit_save => the name of the submit button
    		   ##TODO: check to see if name/codename already exists
		*/
		else if (isset($_POST['r_submit_save']))
		{
			$this->howdoi_model->UpdateCategory($_POST['r_categoryid'],
						$_POST['a_name'],
						$_POST['a_codename'],
						$_POST['a_blurb']
						);
                	$this->main_frame->AddMessage('success',$_POST['a_name'].' has been updated.');
			redirect($_POST['r_redirecturl']);
		}
		/* Adds a new category
		   $_POST data passed
		   - r_redirecturl => the url the form submit came from
    		   - a_categoryname => the name of the category to create
    		   - r_submit_add => the name of the submit button
		   ##TODO: add checking for name exists
		*/
		else if (isset($_POST['r_submit_add']))
		{
			if (strlen(trim($_POST['a_categoryname'])) > 0)
			{
				$this->howdoi_model->AddNewCategory($_POST['a_categoryname']);
	                	$this->main_frame->AddMessage('success',$_POST['a_categoryname'].' has been added to the category list.');
				redirect($_POST['r_redirecturl']);
			}
			//if the name is only whitespace then cause an error
			else
			{
	                	$this->main_frame->AddMessage('error','You must enter a name for the category.');
				redirect($_POST['r_redirecturl']);
			}
		}
		/* Moves a category up one in the category list
		   $_POST data passed
		   - r_redirecturl => the url the form submit came from
    		   - r_sectionorder => the current position to move up
    		   - r_submit_up => the name of the submit button
    		   ##TODO: must pass category id as well, and check to see if it has been moved by someone else
		*/
		else if (isset($_POST['r_submit_up']))
		{
			$this->howdoi_model->SwapCategoryOrder($_POST['r_sectionorder'], $_POST['r_sectionorder'] - 1);
                	$this->main_frame->AddMessage('success','Category moved up.');
			redirect($_POST['r_redirecturl']);
		}
		/* Moves a category down one in the category list
		   $_POST data passed
		   - r_redirecturl => the url the form submit came from
    		   - r_sectionorder => the current position to move down
    		   - r_submit_down => the name of the submit button
    		   ##TODO: must pass category id as well, and check to see if it has been moved by someone else
		*/
		else if (isset($_POST['r_submit_down']))
		{
			$this->howdoi_model->SwapCategoryOrder($_POST['r_sectionorder'], $_POST['r_sectionorder'] + 1);
                	$this->main_frame->AddMessage('success','Category moved down.');
			redirect($_POST['r_redirecturl']);
		}
	}

	function suggestionmodify()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('requests_model','requests_model');
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		if (isset($_POST['r_submit_ask']))
		{
                	$article_header_id = $this->requests_model->CreateRequest(
						'suggestion',
						$_POST['a_category'],
						$_POST['a_question'],
						$_POST['a_description'],
						$this->user_auth->entityId,
						NULL);
	                $this->main_frame->AddMessage('success','Suggestion Created.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_request']))
		{
			if ($this->user_auth->officeType != 'Low')
			{
	                	$request_id = $this->requests_model->CreateRequest(
							'request',
							$_POST['a_category'],
							$_POST['a_question'],
							$_POST['a_description'],
							$this->user_auth->entityId,
							$_POST['a_deadline']);
		                $this->main_frame->AddMessage('success','Request Created.');
				redirect('/office/howdoi/editrequest/'.$request_id);
			}
			else
			{
		                $this->main_frame->AddMessage('error','You don\'t have access to create a request.');
				redirect('/office/howdoi/');
			}
		}
	}
	
	function questionmodify()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('requests_model','requests_model');
		$this->load->model('article_model','article_model');

		if (isset($_POST['r_submit_save']))
		{
			$revision_id = $this->requests_model->CreateArticleRevision(
				$_POST['r_articleid'],
				$this->user_auth->entityId,
				$_POST['a_question'],
				'',
				'',
				$_POST['a_answer'],
				''
				)
				;
	                $this->main_frame->AddMessage('success','New revision created for article.');
			redirect('/office/howdoi/editquestion/'.$_POST['r_articleid'].'/'.$revision_id.'/');
		}
		//for forms that are editor only
		if ($this->user_auth->officeType != 'Low')
		{
			if (isset($_POST['r_submit_modify']))
			{
				$this->requests_model->UpdateSuggestion(
					$_POST['r_articleid'], 
					array('title'=>$_POST['a_title'],
						'description'=>$_POST['a_description'],
						'content_type'=>$_POST['a_category'])
					);
		                $this->main_frame->AddMessage('success',$_POST['r_status'].' has been modified.');
				redirect('/office/howdoi/editquestion/'.$_POST['r_articleid']);
			}
			else if (isset($_POST['r_submit_accept']))
			{
				$this->requests_model->UpdateRequestStatus(
					$_POST['r_articleid'],
					'request',
					array('editor'=>$this->user_auth->entityId,
						'publish_date'=>$_POST['a_deadline'],
						'title'=>$_POST['a_title'],
						'description'=>$_POST['a_description'],
						'content_type'=>$_POST['a_category'])
					);
		                $this->main_frame->AddMessage('success','Suggestion has been updated to a request.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_reject']))
			{
				$this->requests_model->RejectSuggestion($_POST['r_articleid']);
		                $this->main_frame->AddMessage('success','Suggestion has been deleted.');
				redirect('/office/howdoi/suggestions');
			}
			else if (isset($_POST['r_submit_rejectrequest']))
			{
				$this->requests_model->RejectSuggestion($_POST['r_articleid']);
		                $this->main_frame->AddMessage('success','Request has been deleted.');
				redirect('/office/howdoi/requests');
			}
			else if (isset($_POST['r_submit_rejectpulled']))
			{
				$this->requests_model->RejectSuggestion($_POST['r_articleid']);
		                $this->main_frame->AddMessage('success','Pulled article has been deleted.');
				redirect('/office/howdoi/published');
			}
			else if (isset($_POST['r_submit_publishnow']))
			{
				$this->requests_model->UpdateRequestStatus(
					$_POST['r_articleid'],
					'publish',
					array('content_id'=>$_POST['r_revisionid'],
						'publish_date'=>date('y-m-d H:i:s'),
						'editor'=>$this->user_auth->entityId)
					);
		                $this->main_frame->AddMessage('success','Question had been published.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_publishon']))
			{
				$publish = strtotime($_POST['a_publishdate']);
				$publishformat = date('F jS Y', $publish).' at '.date('g.i A', $publish);
				$this->requests_model->UpdateRequestStatus(
					$_POST['r_articleid'],
					'publish',
					array('content_id'=>$_POST['r_revisionid'],
						'publish_date'=>$_POST['a_publishdate'],
						'editor'=>$this->user_auth->entityId)
					);
		                $this->main_frame->AddMessage('success','Question had been set for publication on '.$publishformat.'.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_pull']))
			{
				$this->article_model->PullArticle($_POST['r_articleid'], $this->user_auth->entityId);
		                $this->main_frame->AddMessage('success','Question has been pulled from publication.');
				redirect('/office/howdoi/editquestion/'.$_POST['r_articleid']);
			}
			else if (isset($_POST['r_submit_category']))
			{
				$this->requests_model->UpdateContentType($_POST['r_articleid'], $_POST['a_category']);
		                $this->main_frame->AddMessage('success','Questions category has been updated.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_makerequest']))
			{
				$this->requests_model->UpdatePulledToRequest($_POST['r_articleid'], $this->user_auth->entityId);
				$this->requests_model->RemoveAllUsersFromRequest($_POST['r_articleid']);
		                $this->main_frame->AddMessage('success','Question has been converted to a request.');
				redirect('/office/howdoi/editquestion/'.$_POST['r_articleid']);
			}
		}
	}
	
	function writermodify()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('requests_model','requests_model');

		if ($this->user_auth->officeType != 'Low')
		{
			if (isset($_POST['r_submit_remove']))
			{
				$this->requests_model->RemoveUserFromRequest(
					$_POST['r_articleid'],
					$_POST['r_userid']
					);
		                $this->main_frame->AddMessage('success','User has been removed from writing an answer to this question.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_add']))
			{
				$this->requests_model->AddUserToRequest(
					$_POST['r_articleid'],
					$_POST['a_addwriter'],
					$this->user_auth->entityId
					);
		                $this->main_frame->AddMessage('success','User has been added to write an answer to this question.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_accept']))
			{
				$this->requests_model->AcceptRequest(
					$_POST['r_articleid'],
					$_POST['r_userid']
					);
		                $this->main_frame->AddMessage('success','You have accepted the request to write an answer for this article.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_decline']))
			{
				$this->requests_model->DeclineRequest(
					$_POST['r_articleid'],
					$_POST['r_userid']
					);
		                $this->main_frame->AddMessage('success','You have declined the request to write an answer for this article.');
				redirect($_POST['r_redirecturl']);
			}
		}

	}
}
?>
