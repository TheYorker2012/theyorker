<?php

/// Office Charity Pages.
/**
 * @author Richard Ingle (ri504@cs.york.ac.uk)
 */
class Charity extends Controller
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
		$navbar->AddItem('list', 'List',
				'/office/charity/');
		$navbar->AddItem('add', 'Add',
				'/office/charity/add');
		$navbar->AddItem('current', 'Current',
				'/office/charity/current');
	}

	/// Set up the navigation bar
	private function _SetupNavbar2($charity_id)
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('info', 'Info',
				'/office/charity/editinfo/'.$charity_id);
		$navbar->AddItem('article', 'Article',
				'/office/charity/editarticle/'.$charity_id);
		$navbar->AddItem('reports', 'Reports',
				'/office/charity/editreports/'.$charity_id);
		$navbar->AddItem('related', 'Related',
				'/office/charity/editrelated/'.$charity_id);
		$navbar->AddItem('options', 'Options',
				'/office/charity/editoptions/'.$charity_id);				
	}
	
	function index()
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_list');
		$this->load->model('charity_model','charity_model');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('list');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get list of the charities
		$data['charities'] = $this->charity_model->GetCharities();

		// Set up the view
		$the_view = $this->frames->view('office/charity/list', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function add()
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_add');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('add');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the view
		$the_view = $this->frames->view('office/charity/add', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function current()
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_current');
		$this->load->model('charity_model','charity_model');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('current');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get list of the charities
		$data['charities'] = $this->charity_model->GetCharities();

		// Set up the view
		$the_view = $this->frames->view('office/charity/current', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function editinfo($charity_id)
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_edit');
		$this->load->model('charity_model','charity_model');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar2($charity_id);
		$this->main_frame->SetPage('info');

		//get charity from given id
		$data['charity'] = $this->charity_model->GetCharity($charity_id);
		$data['charity']['id'] = $charity_id;

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the view
		$the_view = $this->frames->view('office/charity/info', $data);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array(
			'name' => $data['charity']['name']
		));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function editarticle($charity_id, $revision_id = NULL)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('charity_model','charity_model');
		$this->load->model('article_model','article_model');
		$this->load->model('requests_model','requests_model');
		
		//redirect the user away if an invalid charity is specified
		if (!$this->charity_model->CharityExists($charity_id))
		{
			$this->main_frame->AddMessage('error','Charity does not exist.');
			redirect('/office/charity/');
		}

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar2($charity_id);
		$this->main_frame->SetPage('article');
		$this->pages_model->SetPageCode('office_charity_article');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		//get charity from given id
		//$data['charity'] = $this->charity_model->GetCharity($charity_id);
		//$data['charity']['id'] = $charity_id;
		$charity_name = $this->charity_model->GetCharityName($charity_id);
		$article_id = $this->charity_model->GetCharityArticleID($charity_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['charity_id'] = $charity_id;
		$data['parameters']['revision_id'] = $revision_id;
		$data['parameters']['article_id'] = $article_id;
		
		//get the header of the charities article and revisions
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);
		$data['article']['revisions'] = $this->requests_model->GetArticleRevisions($article_id);
			
		//set the default revision to false
		$data['article']['displayrevision'] = FALSE;

		//if the revision id is set to the default
		if ($revision_id == NULL)
		{
			/* is a published article, therefore
			   load the live content revision */
			if ($data['article']['header']['live_content'] != FALSE)
			{
				$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($article_id, $data['article']['header']['live_content']);
				$data['parameters']['revision_id'] = $data['article']['header']['live_content'];
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
					$data['parameters']['revision_id'] = $data['article']['revisions'][0]['id'];
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
                $this->main_frame->AddMessage('error','Specified revision doesn\'t exist for this charity. Default selected.');
                redirect('/office/charity/editarticle/'.$charity_id.'/');
    		}
		}

		// Set up the view
		$the_view = $this->frames->view('office/charity/article', $data);

		// Set up the public frame
		$this->main_frame->SetTitleParameters(array(
			'name' => $charity_name
		));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editrequest($charity_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('charity_model','charity_model');
		$this->load->model('news_model','news_model');
		$this->load->model('article_model','article_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->charity_model->CharityExists($charity_id))
		{
			$this->main_frame->AddMessage('error','Charity does not exist.');
			redirect('/office/charity/');
		}		
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar2($charity_id);
		$this->main_frame->SetPage('article');
		$this->pages_model->SetPageCode('office_charity_request');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get campaign info
		$charity_name = $this->charity_model->GetCharityName($charity_id);
		$article_id = $this->charity_model->GetCharityArticleID($charity_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['charity_id'] = $charity_id;
		$data['parameters']['article_id'] = $article_id;
		
		/** get the article and its header */
		$data['article'] = $this->news_model->GetFullArticle($article_id);
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $charity_name));
		$this->main_frame->SetContentSimple('office/charity/request', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function modify($charity_id)
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_modify');
		$this->load->model('charity_model','charity_model');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar2($charity_id);
		$this->main_frame->SetPage('info');

		//get charity from given id
		$data['charity'] = $this->charity_model->GetCharity($charity_id);
		$data['charity']['id'] = $charity_id;

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the view
		$the_view = $this->frames->view('office/charity/modify', $data);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array(
			'name' => $data['charity']['name']
		));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function editreports($charity_id)
	{
		if (!CheckPermissions('office')) return;
		
		//load the required models
		$this->load->model('charity_model','charity_model');
		$this->load->model('progressreports_model','progressreports_model');
		$this->load->model('news_model','news_model');
		$this->load->model('article_model','article_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->charity_model->CharityExists($charity_id))
		{
			$this->main_frame->AddMessage('error','Charity does not exist.');
			redirect('/office/charity/');
		}

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar2($charity_id);
		$this->main_frame->SetPage('reports');
		$this->pages_model->SetPageCode('office_charity_reports');
		
		//get charity info
		$charity_name = $this->charity_model->GetCharityName($charity_id);
		$article_id = $this->charity_model->GetCharityArticleID($charity_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['charity_id'] = $charity_id;
		$data['parameters']['article_id'] = $article_id;

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get all progress reports for the campaign
		$pr_temp = $this->progressreports_model->GetCharityCampaignProgressReports(
			$charity_id,
			FALSE,
			FALSE
			);
			
		$data['progressreports'] = array();
			
		//get the data for each of the retrieved progress reports
		foreach($pr_temp as $key => $pr)
		{
			$data['progressreports'][$key]['id'] = $pr;
			/** get the article's header for the article id passed to
		            the function */
			$data['progressreports'][$key]['header'] = $this->article_model->GetArticleHeader($pr);
			if ($data['progressreports'][$key]['header']['live_content'] != FALSE)
				$data['progressreports'][$key]['article'] = $this->news_model->GetFullArticle($data['progressreports'][$key]['id']);
		}
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $charity_name));
		$this->main_frame->SetContentSimple('office/charity/reports', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editprogressreport($charity_id, $pr_article_id, $revision_id = NULL)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('charity_model','charity_model');
		$this->load->model('news_model','news_model');
		$this->load->model('article_model','article_model');
		$this->load->model('requests_model','requests_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->charity_model->CharityExists($charity_id))
		{
			$this->main_frame->AddMessage('error','Charity does not exist.');
			redirect('/office/charity/');
		}		
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar2($charity_id);
		$this->main_frame->SetPage('reports');
		$this->pages_model->SetPageCode('office_charity_reportsarticle');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get campaign info
		$charity_name = $this->charity_model->GetCharityName($charity_id);
		$article_id = $this->charity_model->GetCharityArticleID($charity_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['charity_id'] = $charity_id;
		$data['parameters']['article_id'] = $article_id;
		$data['parameters']['prarticle_id'] = $pr_article_id;
		$data['parameters']['revision_id'] = $revision_id;
		
		/** get the article's header for the article id passed to
			the function */
		$data['article']['header'] = $this->article_model->GetArticleHeader($pr_article_id);
		
		//get the list of current question revisions
		$data['article']['revisions'] = $this->requests_model->GetArticleRevisions($pr_article_id);

		//set the default revision to false
		$data['article']['displayrevision'] = FALSE;

		/** suggestions have no contents associated with them
			so don't try to load any, if it is not a
			suggestion then load the displayrevision */
		//if ($data['article']['header']['suggestion_accepted'] == 1) //progress reports can't currently be a suggestion
		//{
			//if the revision id is set to the default
			if ($revision_id == NULL)
			{
				/* is a published article, therefore
				   load the live content revision */
				if ($data['article']['header']['live_content'] != FALSE)
				{
					$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($pr_article_id, $data['article']['header']['live_content']);
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
						$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($pr_article_id, $data['article']['revisions'][0]['id']);
						$data['parameters']['revision_id'] = $data['article']['displayrevision']['id'];
					}
				}
			}
			else
			{
				/* load the revision with the given
				   revision id */
				$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($pr_article_id, $revision_id);
				/* if this revision doesn't exist
				   then return an error */
				if ($data['article']['displayrevision'] == FALSE)
				{
					$this->main_frame->AddMessage('error','Specified revision doesn\'t exist for this article. Default selected.');
					redirect('/office/charity/editprogressreport/'.$charity_id.'/'.$pr_article_id.'/');
				}
			}
		//}
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $charity_name));
		$this->main_frame->SetContentSimple('office/charity/reportsarticle', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editrelated($charity_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('charity_model','charity_model');
		$this->load->model('news_model','news_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->charity_model->CharityExists($charity_id))
		{
			$this->main_frame->AddMessage('error','Charity does not exist.');
			redirect('/office/campaign/');
		}		
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar2($charity_id);
		$this->main_frame->SetPage('related');
		$this->pages_model->SetPageCode('office_charity_related');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get charity info
		$charity_name = $this->charity_model->GetCharityName($charity_id);
		$article_id = $this->charity_model->GetCharityArticleID($charity_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['charity_id'] = $charity_id;
		$data['parameters']['article_id'] = $article_id;
		
		//get campaigns article
		$data['article'] = $this->news_model->GetFullArticle($article_id);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $charity_name));
		$this->main_frame->SetContentSimple('office/charity/related', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function editoptions($charity_id)
	{
		if (!CheckPermissions('office')) return;
		
		//load the required models
		$this->load->model('charity_model','charity_model');

		//set the page code
		$this->pages_model->SetPageCode('office_charity_options');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar2($charity_id);
		$this->main_frame->SetPage('options');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['charity_id'] = $charity_id;
		
		//get charity info
		$charity_name = $this->charity_model->GetCharityName($charity_id);
		
		//get the current charity
		$data['current_charity'] = $this->charity_model->GetCurrentCharity();

		// Set up the view
		$the_view = $this->frames->view('office/charity/options', $data);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $charity_name));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function setcurrent()
	{
		if (!CheckPermissions('office')) return;

		/* Sets the current charity
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - r_charityid => the name of the new charity
    		   - r_submit_makecurrent => the name of the submit button
		*/
		if (isset($_POST['r_submit_makecurrent']))
		{
			//load the required models
			$this->load->model('charity_model','charity_model');

			//set the new current charity
			$result = $this->charity_model->SetCharityCurrent($_POST['r_charityid']);

			if ($result == true)
			{
				//return to form submit page and pass success message
				$this->main_frame->AddMessage('success','Current charity set.');
				redirect($_POST['r_redirecturl']);
			}
			else
			{
				//return to form submit page and pass success message
				$this->main_frame->AddMessage('error','To set the current charity it must have a published article.');
				redirect($_POST['r_redirecturl']);
			}
		}
		/* Deletes the given charity
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - r_charityid => the name of the new charity
    		   - r_submit_delete => the name of the submit button
		*/
		else if (isset($_POST['r_submit_delete']))
		{
			//load the required models
			$this->load->model('charity_model','charity_model');

			//set the new current charity
			$this->charity_model->DeleteCharity($_POST['r_charityid']);

			//return to form submit page and pass success message
			$this->main_frame->AddMessage('success','Charity deleted.');
			redirect($_POST['r_redirecturl']);
		}

	}

	/**
	 * This function adds a new charity to the database.
	 */
	function addcharity()
	{
		if (!CheckPermissions('office')) return;

		/* Loads the category edit page
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - a_charityname => the name of the new charity
    		   - r_submit_add => the name of the submit button
		*/
		if (isset($_POST['r_submit_add']))
		{
			if (trim($_POST['a_charityname']) != '')
			{
				//load the required models
				$this->load->model('charity_model','charity_model');
				$this->load->model('requests_model','requests_model');
	
				//create the charity and its article
				$id = $this->requests_model->CreateRequest('request', 'ourcharity', '', '', $this->user_auth->entityId, time());
				$this->charity_model->CreateCharity($_POST['a_charityname'], $id);
	
				//return to form submit page and pass success message
				$this->main_frame->AddMessage('success','Charity added.');
				redirect($_POST['r_redirecturl']);
			}
			else
			{
				//return to form submit page and pass error message
				$this->main_frame->AddMessage('error','Must enter a name for the new charity.');
				redirect($_POST['r_redirecturl']);
			}
		}

	}

	/**
	 * Modifes the given charity.
	 */
	function domodify()
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('requests_model','requests_model');
		$this->load->model('article_model','article_model');
		$this->load->model('charity_model','charity_model');
		$this->load->model('progressreports_model','progressreports_model');
			
		/* Updates a charity information
		   $_POST data passed
		   - a_charityid => the id of the charity
		   - r_redirecturl => the url to redirect back to
		   - a_name => new name of the charity
		   - a_goal => the target ammount of money
		   - a_goaltext => the blurb about what we are aiming for
    		   - r_submit_add => the name of the submit button
		*/
		if (isset($_POST['r_submit_modify']))
		{
			if (trim($_POST['a_name']) != '')
			{
				if (is_numeric($_POST['a_goal']))
				{					
					//update the charity
					$this->charity_model->UpdateCharity(
						$_POST['a_charityid'], 
						$_POST['a_name'],
						$_POST['a_goal']);

					//return to form submit page and pass success message
					$this->main_frame->AddMessage('success','Charity updated.');
					redirect($_POST['r_redirecturl']);
				}
				else
				{
					//return to form submit page and pass error message
					$this->main_frame->AddMessage('error','You must enter a numeric value for the goal ammount.');
					redirect($_POST['r_redirecturl']);
				}
			}
			else
			{
				//return to form submit page and pass error message
				$this->main_frame->AddMessage('error','You must enter a name for the charity.');
				redirect($_POST['r_redirecturl']);
			}
		}
		/* Saves the new revision of the charities article
		   $_POST data passed
		   - r_charityid => the id of the article
		   - a_heading => the heading of the article revision
		   - a_content => the content of the article revision
    		   - r_submit_article_save => the name of the submit button
		*/
		else if (isset($_POST['r_submit_article_save']))
		{			
			//get the article id for the specified charity
			$article_id = $this->charity_model->GetCharityArticleId($_POST['r_charityid']);
			//create the new revision
			$revision_id = $this->requests_model->CreateArticleRevision(
				$article_id,
				$this->user_auth->entityId,
				$_POST['a_heading'],
				'',
				'',
				$_POST['a_content'],
				''
				);
			//report success
			$this->main_frame->AddMessage('success','New revision created for charity article.');
			redirect('/office/charity/editarticle/'.$_POST['r_charityid'].'/'.$revision_id.'/');
		}
		/* Publishes the specified article as the live content for the charity
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - r_charityid => the id of the charity
		   - r_revisionid => the id of the revision
		   - r_articleid => the article id of the charity
    		   - r_submit_article_publish => the name of the submit button
		*/
		else if (isset($_POST['r_submit_article_publish']))
		{
			//publish the current revision
			$this->requests_model->UpdateRequestStatus(
				$_POST['r_articleid'],
				'publish',
				array(
					'content_id'=>$_POST['r_revisionid'],
					'publish_date'=>time(),
					'editor'=>$this->user_auth->entityId
					)
				);
			//report success
			$this->main_frame->AddMessage('success','Published revision for charity article.');
			//redirect('/office/charity/editarticle/'.$_POST['r_charityid'].'/'.$_POST['r_revisionid'].'/');
			redirect($_POST['r_redirecturl']);
		}
		/* Removes the current published revision from being live for the charity
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - r_charityid => the id of the charity
		   - r_revisionid => the id of the revision
		   - r_articleid => the article id of the charity
    		   - r_submit_article_publish => the name of the submit button
		*/
		else if (isset($_POST['r_submit_article_unpublish']))
		{
			//unpublish current revision
			$this->requests_model->UpdateSetToUnpublished(
				$_POST['r_articleid'],
				$this->user_auth->entityId
				);
			//report success
			$this->main_frame->AddMessage('success','Article revision unpublished.');
			redirect($_POST['r_redirecturl']);
		}
		/* Saves the new information on the request
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - r_charityid => the id of the charity
		   - a_title => title of the request
		   - a_description => description of the request
    		   - r_submit_save_request => the name of the submit button
		*/
		else if (isset($_POST['r_submit_save_request']))
		{
			//get the article id for the campaign
			$article_id = $this->charity_model->GetCharityArticleID($_POST['r_charityid']);
			//update the title and description
			$this->requests_model->UpdateSuggestion(
				$article_id,
				array('title'=>$_POST['a_title'],
					'description'=>$_POST['a_description'],
					'content_type'=>'ourcharity')
				);
			$this->main_frame->AddMessage('success','Request has been modified.');
			redirect($_POST['r_redirecturl']);
		}
		/* Updates and saves new web links related to the charities article
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - r_charityid => the id of the charity
		   - a_title => title of the request
		   - a_description => description of the request
    		   - r_submit_save_request => the name of the submit button
		*/
		else if (isset($_POST['r_submit_save_links']))
		{
			//get the article id for the campaign
			$article_id = $this->charity_model->GetCharityArticleID($_POST['r_charityid']);
			//for all links work out what to do
			for($i=1;$i<=$_POST['r_linkcount'];$i++)
			{
				if (isset($_POST['a_delete_'.$i]))
				{
					//delete the link
					$this->requests_model->DeleteArticleLink(
						$article_id,
						$_POST['a_id_'.$i],
						$_POST['a_name_'.$i],
						$_POST['a_url_'.$i]
					);
				}
				else
				{
					//update the link
					$this->requests_model->UpdateArticleLink(
						$article_id,
						$_POST['a_id_'.$i],
						$_POST['a_name_'.$i],
						$_POST['a_url_'.$i]
					);
				}
			}
			if (($_POST['a_name_new'] != "") AND ($_POST['a_url_new'] != "") AND ($_POST['a_url_new'] != "http://"))
			{
				//add a new link if fields non blank and non default
				$this->requests_model->InsertArticleLink(
					$article_id,
					$_POST['a_name_new'],
					$_POST['a_url_new']
				);
			}
			//report success and redirect
			$this->main_frame->AddMessage('success','Links Saved.');
			redirect($_POST['r_redirecturl']);
		}
		/* Set the given charity as the current charity
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - r_charityid => the id of the charity
    		   - r_submit_set_current => the name of the submit button
		*/
		else if (isset($_POST['r_submit_set_current']))
		{
			//set the current charity
			$result = $this->charity_model->SetCharityCurrent($_POST['r_charityid']);
			if ($result == TRUE)
			{
				$this->main_frame->AddMessage('success','Charity has been set as the current one.');
				redirect($_POST['r_redirecturl']);
			}
			else
			{
				$this->main_frame->AddMessage('error','Could not set this charity as the current one.');
				redirect($_POST['r_redirecturl']);
			}
		}
		/* Set the given charity as the current charity
		   $_POST data passed
		   - r_redirecturl => the url to redirect back to
		   - r_charityid => the id of the charity
    		   - r_submit_set_current => the name of the submit button
		*/
		else if (isset($_POST['r_submit_remove_current']))
		{
			//unset the current charity
			$this->charity_model->RemoveCharityAsCurrent($_POST['r_charityid']);
			//set message and redirect
			$this->main_frame->AddMessage('success','Charity has been removed as the current one.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_pr_add']))
		{
			$article_id = $this->requests_model->CreateRequest(
				'request',
				'progressreports',
				"",
				"",
				$this->user_auth->entityId,
				$_POST['a_date']
				);
			$this->progressreports_model->AddCharityCampaignProgressReportLink(
				$article_id,
				FALSE,
				$_POST['r_charityid']
				);
			$this->main_frame->AddMessage('success','Progress report added to charity.');
			redirect('/office/charity/editprogressreport/'.$_POST['r_charityid'].'/'.$article_id);		
		}
		else if (isset($_POST['r_submit_pr_save']))
		{
			$revision_id = $this->requests_model->CreateArticleRevision(
				$_POST['r_articleid'],
				$this->user_auth->entityId,
				'',
				'',
				'',
				$_POST['a_report'],
				''
				);
			$this->main_frame->AddMessage('success','New revision created for progress report.');
			redirect('/office/charity/editprogressreport/'.$_POST['r_charityid'].'/'.$_POST['r_articleid'].'/'.$revision_id.'/');
		}
		else if (isset($_POST['r_submit_pr_publish']))
		{
			$this->requests_model->UpdateRequestStatus(
				$_POST['r_articleid'],
				'publish',
				array('content_id'=>$_POST['r_revisionid'],
					'publish_date'=>$_POST['r_date_set'],
					'editor'=>$this->user_auth->entityId)
				);
			$this->main_frame->AddMessage('success','Progress report revision set to published revision.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_pr_unpublish']))
		{
			$this->requests_model->UpdateSetToUnpublished(
				$_POST['r_articleid'],
				$this->user_auth->entityId
				);
			$this->main_frame->AddMessage('success','Progress report revision unpublished.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_pr_date']))
		{
			$this->requests_model->UpdatePublishDate(
				$_POST['r_articleid'],
				$_POST['a_date']
				);
			$this->main_frame->AddMessage('success','Progress report date updated.');
			redirect($_POST['r_redirecturl']);		
		}
		else if (isset($_POST['r_submit_pr_delete']))
		{
			$this->requests_model->DeleteArticle(
				$_POST['r_articleid']
				);
			$this->progressreports_model->DeleteCharityCampaignProgressReportLink(
				$_POST['r_articleid'],
				FALSE,
				$_POST['r_charityid']
				);
			$this->main_frame->AddMessage('success','Progress report deleted from charity.');
			redirect('/office/charity/editreports/'.$_POST['r_charityid']);	
		}
	}
}