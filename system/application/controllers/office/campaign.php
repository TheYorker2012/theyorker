<?php

/// Office Campaign Pages.
/**
 * @author Richard Ingle (ri504@cs.york.ac.uk)
 */
class Campaign extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->helper('text');
		$this->load->helper('wikilink');
	}

	/// Set up the navigation bar
	private function _SetupNavbar($campaign_id)
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('info', 'Info',
				'/office/campaign/editinfo/'.$campaign_id);
		$navbar->AddItem('article', 'Article',
				'/office/campaign/editarticle/'.$campaign_id);
		$navbar->AddItem('reports', 'Reports',
				'/office/campaign/editreports/'.$campaign_id);
		$navbar->AddItem('related', 'Related',
				'/office/campaign/editrelated/'.$campaign_id);
		//only editors/admins can see this page
		if ($this->user_auth->officeType != 'Low')
		{
			$navbar->AddItem('publish', 'Publish',
				'/office/campaign/editpublish/'.$campaign_id);
		}
	}

	/// Set up the navigation bar
	private function _SetupMainNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('list', 'List',
				'/office/campaign/');
		$navbar->AddItem('add', 'Add New',
				'/office/campaign/add/');
		$navbar->AddItem('options', 'Options',
				'/office/campaign/options/');
	}
	
	function index()
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
	
		//Get navigation bar and tell it the current page
		if ($this->user_auth->officeType != 'Low')//only editors/admins can see the navigation bar
		{
			$this->_SetupMainNavbar();
			$this->main_frame->SetPage('list');
		}
		$this->pages_model->SetPageCode('office_campaign_list');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get the full campaign list
		$data['campaign_list_live'] = $this->campaign_model->GetLiveCampaignList();
		$data['campaign_list_future'] = $this->campaign_model->GetFutureCampaignList();
		$data['campaign_list_unpublished'] = $this->campaign_model->GetUnpublishedCampaignList();
		$data['campaign_list_expired'] = $this->campaign_model->GetExpiredCampaignList();

		// Set up the view
		$the_view = $this->frames->view('office/campaign/list', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function add()
	{
		if (!CheckPermissions('office')) return;
		
		//only editors/admins can see this page
		if ($this->user_auth->officeType != 'Low')
		{
			//Get navigation bar and tell it the current page
			$this->_SetupMainNavbar();
			$this->main_frame->SetPage('add');
			$this->pages_model->SetPageCode('office_campaign_add');

			//get the current users id and office access
			$data['user']['id'] = $this->user_auth->entityId;
			$data['user']['officetype'] = $this->user_auth->officeType;

			// Set up the view
			$the_view = $this->frames->view('office/campaign/add', $data);
			
			// Set up the public frame
			$this->main_frame->SetContent($the_view);

			// Load the public frame view
			$this->main_frame->Load();
		}
		else
		{
			$this->main_frame->AddMessage('error','You do not have access to view this page.');
			redirect('/office/campaign/');
		}
	}
	
	function options()
	{
		if (!CheckPermissions('office')) return;
		
		if ($this->user_auth->officeType != 'Low')
		{
			//load the required models
			$this->load->model('campaign_model','campaign_model');
		
			//Get navigation bar and tell it the current page
			$this->_SetupMainNavbar();
			$this->main_frame->SetPage('options');
			$this->pages_model->SetPageCode('office_campaign_options');

			//get the current users id and office access
			$data['user']['id'] = $this->user_auth->entityId;
			$data['user']['officetype'] = $this->user_auth->officeType;
			
			$petition = $this->campaign_model->GetPetitionStatus();
			if ($petition == FALSE)
			{
				$data['campaign']['is_petition'] = FALSE;
				$data['campaign']['can_start_petition'] = $this->campaign_model->CanStartPetition();
				$data['campaign']['live_campaign_count'] = $this->campaign_model->GetLiveCampaignCount();
				$data['campaign']['future_campaign_count'] = $this->campaign_model->GetFutureCampaignCount();
			}
			else
			{
				$data['campaign']['is_petition'] = TRUE;
				$data['campaign']['future_campaign_count'] = $this->campaign_model->GetFutureCampaignCount();
			}

			// Set up the view
			$the_view = $this->frames->view('office/campaign/options', $data);
			
			// Set up the public frame
			$this->main_frame->SetContent($the_view);

			// Load the public frame view
			$this->main_frame->Load();
		}
		else
		{
			$this->main_frame->AddMessage('error','You do not have access to view this page.');
			redirect('/office/campaign/');
		}
	}

	function editinfo($campaign_id)
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_campaign_edit');
		$this->load->model('campaign_model','campaign_model');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('info');

		//get charity from given id
		$data['campaign']['name'] = $this->campaign_model->GetCampaignNameID($campaign_id);
		$data['campaign']['id'] = $campaign_id;

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['is_editor'] = PermissionsSubset('editor', GetUserLevel());

		// Set up the view
		$the_view = $this->frames->view('office/campaign/info', $data);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array(
			'name' => $data['campaign']['name']
		));
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editarticle($campaign_id, $revision_id = NULL)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
		$this->load->model('requests_model','requests_model');
		$this->load->model('article_model','article_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->campaign_model->CampaignExists($campaign_id))
		{
			$this->main_frame->AddMessage('error','Campaign does not exist.');
			redirect('/office/campaign/');
		}
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('article');
		$this->pages_model->SetPageCode('office_campaign_edit');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get campaign info
		$campaign_name = $this->campaign_model->GetCampaignNameID($campaign_id);
		$article_id = $this->campaign_model->GetCampaignArticleID($campaign_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['campaign_id'] = $campaign_id;
		$data['parameters']['revision_id'] = $revision_id;
		$data['parameters']['article_id'] = $article_id;
		
		/** get the article's header for the article id passed to
			the function */
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);
		
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
			if ($revision_id == NULL)
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
					$this->main_frame->AddMessage('error','Specified revision doesn\'t exist for this article. Default selected.');
					redirect('/office/campaign/editarticle/'.$article_id.'/');
				}
			}
		}
		
		//get fact box
		$data['article']['displayrevision']['fact_box'] = $this->requests_model->GetFactBoxForArticleContent($data['parameters']['revision_id']);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $campaign_name));
		$this->main_frame->SetContentSimple('office/campaign/article', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editrequest($campaign_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
		$this->load->model('article_model','article_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->campaign_model->CampaignExists($campaign_id))
		{
			$this->main_frame->AddMessage('error','Campaign does not exist.');
			redirect('/office/campaign/');
		}		
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('article');
		$this->pages_model->SetPageCode('office_campaign_request');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get campaign info
		$campaign_name = $this->campaign_model->GetCampaignNameID($campaign_id);
		$article_id = $this->campaign_model->GetCampaignArticleID($campaign_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['campaign_id'] = $campaign_id;
		$data['parameters']['article_id'] = $article_id;
		
		/** get the article and its header */
		$data['article'] = $this->news_model->GetFullArticle($article_id);
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $campaign_name));
		$this->main_frame->SetContentSimple('office/campaign/request', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editreports($campaign_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('progressreports_model','progressreports_model');
		$this->load->model('news_model','news_model');
		$this->load->model('article_model','article_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->campaign_model->CampaignExists($campaign_id))
		{
			$this->main_frame->AddMessage('error','Campaign does not exist.');
			redirect('/office/campaign/');
		}		
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('reports');
		$this->pages_model->SetPageCode('office_campaign_reports');
		
		//get campaign info
		$campaign_name = $this->campaign_model->GetCampaignNameID($campaign_id);
		$article_id = $this->campaign_model->GetCampaignArticleID($campaign_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['campaign_id'] = $campaign_id;
		$data['parameters']['article_id'] = $article_id;

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get all progress reports for the campaign
		$pr_temp = $this->progressreports_model->GetCharityCampaignProgressReports(
			$campaign_id,
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
		$this->main_frame->SetTitleParameters(array('name' => $campaign_name));
		$this->main_frame->SetContentSimple('office/campaign/reports', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editprogressreport($campaign_id, $pr_article_id, $revision_id = NULL)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
		$this->load->model('article_model','article_model');
		$this->load->model('requests_model','requests_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->campaign_model->CampaignExists($campaign_id))
		{
			$this->main_frame->AddMessage('error','Campaign does not exist.');
			redirect('/office/campaign/');
		}		
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('reports');
		$this->pages_model->SetPageCode('office_campaign_reportsarticle');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get campaign info
		$campaign_name = $this->campaign_model->GetCampaignNameID($campaign_id);
		$article_id = $this->campaign_model->GetCampaignArticleID($campaign_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['campaign_id'] = $campaign_id;
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
					redirect('/office/campaign/editprogressreport/'.$campaign_id.'/'.$pr_article_id.'/');
				}
			}
		//}
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $campaign_name));
		$this->main_frame->SetContentSimple('office/campaign/reportsarticle', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editrelated($campaign_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
		
		//redirect the user away if an invalid campaign is specified
		if (!$this->campaign_model->CampaignExists($campaign_id))
		{
			$this->main_frame->AddMessage('error','Campaign does not exist.');
			redirect('/office/campaign/');
		}		
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('related');
		$this->pages_model->SetPageCode('office_campaign_related');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get campaign info
		$campaign_name = $this->campaign_model->GetCampaignNameID($campaign_id);
		$article_id = $this->campaign_model->GetCampaignArticleID($campaign_id);

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['campaign_id'] = $campaign_id;
		$data['parameters']['article_id'] = $article_id;
		
		//get campaigns article
		$data['article'] = $this->news_model->GetFullArticle($article_id);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $campaign_name));
		$this->main_frame->SetContentSimple('office/campaign/related', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editpublish($campaign_id)
	{
		if (!CheckPermissions('office')) return;
		
		//only editors/admins can see this page
		if ($this->user_auth->officeType != 'Low')
		{
			//load the required models
			$this->load->model('campaign_model','campaign_model');
			$this->load->model('news_model','news_model');
			
			//redirect the user away if an invalid campaign is specified
			if (!$this->campaign_model->CampaignExists($campaign_id))
			{
				$this->main_frame->AddMessage('error','Campaign does not exist.');
				redirect('/office/campaign/');
			}		
		
			//Get navigation bar and tell it the current page
			$this->_SetupNavbar($campaign_id);
			$this->main_frame->SetPage('publish');
			$this->pages_model->SetPageCode('office_campaign_publish');

			//get the current users id and office access
			$data['user']['id'] = $this->user_auth->entityId;
			$data['user']['officetype'] = $this->user_auth->officeType;
			
			//get campaign info
			$campaign_name = $this->campaign_model->GetCampaignNameID($campaign_id);
			$article_id = $this->campaign_model->GetCampaignArticleID($campaign_id);
			$data['campaign']['status'] = $this->campaign_model->GetCampaignStatusID($campaign_id);

			/** store the parameters passed to the method so it can be
			    used for links in the view */
			$data['parameters']['campaign_id'] = $campaign_id;
			$data['parameters']['article_id'] = $article_id;
			
			// Set up the public frame
			$this->main_frame->SetTitleParameters(array('name' => $campaign_name));
			$this->main_frame->SetContentSimple('office/campaign/publish', $data);

			// Load the public frame view
			$this->main_frame->Load();
		}
		else
		{
			$this->main_frame->AddMessage('error','You do not have access to view this page.');
			redirect('/office/campaign/editarticle/'.$campaign_id);
		}
	}
	
	function articlemodify()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('requests_model','requests_model');
		$this->load->model('article_model','article_model');
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('progressreports_model','progressreports_model');

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
				);
			$this->requests_model->CreateFactBoxForArticleContent(
				$revision_id,
				$_POST['a_facts_title'],
				$_POST['a_facts']
			);
			$this->main_frame->AddMessage('success','New revision created for article.');
			redirect('/office/campaign/editarticle/'.$_POST['r_campaignid'].'/'.$revision_id.'/');
		}
		else if (isset($_POST['r_submit_publish']))
		{
			$this->requests_model->UpdateRequestStatus(
				$_POST['r_articleid'],
				'publish',
				array('content_id'=>$_POST['r_revisionid'],
					'publish_date'=>date('y-m-d H:i:s'),
					'editor'=>$this->user_auth->entityId)
				);
			$this->main_frame->AddMessage('success','Article revision set to published revision.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_unpublish']))
		{
			$this->requests_model->UpdateSetToUnpublished(
				$_POST['r_articleid'],
				$this->user_auth->entityId
				);
			$this->main_frame->AddMessage('success','Article revision unpublished.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_save_links']))
		{
			//get the article id for the campaign
			$article_id = $this->campaign_model->GetCampaignArticleID($_POST['r_campaignid']);
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
		else if (isset($_POST['r_submit_save_request']))
		{
			//get the article id for the campaign
			$article_id = $this->campaign_model->GetCampaignArticleID($_POST['r_campaignid']);
			//update the title and description
			$this->requests_model->UpdateSuggestion(
				$article_id,
				array('title'=>$_POST['a_title'],
					'description'=>$_POST['a_description'],
					'content_type'=>'campaigns')
				);
			$this->main_frame->AddMessage('success','Request has been modified.');
			redirect($_POST['r_redirecturl']);
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
			redirect('/office/campaign/editprogressreport/'.$_POST['r_campaignid'].'/'.$_POST['r_articleid'].'/'.$revision_id.'/');
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
				$_POST['r_campaignid']
				);
			$this->main_frame->AddMessage('success','Progress report added to campaign.');
			redirect('/office/campaign/editprogressreport/'.$_POST['r_campaignid'].'/'.$article_id);		
		}
		else if (isset($_POST['r_submit_pr_delete']))
		{
			$this->requests_model->DeleteArticle(
				$_POST['r_articleid']
				);
			$this->progressreports_model->DeleteCharityCampaignProgressReportLink(
				$_POST['r_articleid'],
				FALSE,
				$_POST['r_campaignid']
				);
			$this->main_frame->AddMessage('success','Progress report deleted from campaign.');
			redirect('/office/campaign/editreports/'.$_POST['r_campaignid']);	
		}
	}
	
	function campaignmodify()
	{
		if (!CheckPermissions('office')) return;
		
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('requests_model','requests_model');
		
		if (isset($_POST['r_submit_add_campaign']))
		{
			$article_id = $this->requests_model->CreateRequest(
				'request',
				'campaigns',
				"",
				"",
				$this->user_auth->entityId,
				NULL
				);
			$campaign_id = $this->campaign_model->AddNewCampaign(
				$_POST['a_campaign_name'],
				$article_id
				);
			$this->main_frame->AddMessage('success','Campaign has been added.');
			redirect('/office/campaign/editarticle/'.$campaign_id);
		}
		else if (isset($_POST['r_submit_set_to_future']))
		{
			$this->campaign_model->SetCampaignStatus(
				$_POST['r_campaignid'],
				'future'
				);
			$this->main_frame->AddMessage('success','Campaign has been set to future.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_set_to_expired']))
		{
			$this->campaign_model->SetCampaignStatus(
				$_POST['r_campaignid'],
				'expired'
				);
			$this->main_frame->AddMessage('success','Campaign has been expired.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_set_to_unpublished']))
		{
			$this->campaign_model->SetCampaignStatus(
				$_POST['r_campaignid'],
				'unpublished'
				);
			$this->main_frame->AddMessage('success','Campaign has been set to unpublished.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_set_to_deleted']))
		{
			$this->campaign_model->SetCampaignDeleted(
				$_POST['r_campaignid']
				);
			$this->main_frame->AddMessage('success','Campaign has been deleted.');
			redirect('/office/campaign');
		}
		else if (isset($_POST['r_submit_start']))
		{
			$started = $this->campaign_model->StartPetition();
			if ($started == TRUE)
			{
				$this->main_frame->AddMessage('success','Campaign petition started.');
				redirect($_POST['r_redirecturl']);
			}
			else
			{
				$this->main_frame->AddMessage('error','Can\'t start petition.');
				redirect($_POST['r_redirecturl']);
			}
		}
		else if (isset($_POST['r_submit_end']))
		{
			$this->campaign_model->EndPetition();
			$this->main_frame->AddMessage('success','Petition ended and new campaigns set for voting.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_set_campaign_name']))
		{
			$this->campaign_model->SetCampaignName($_POST['a_campaignid'], $_POST['a_name']);
			$this->main_frame->AddMessage('success','Campaign name has been updated.');
			redirect($_POST['r_redirecturl']);
		}
	}
}
?>