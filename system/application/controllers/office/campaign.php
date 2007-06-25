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
		$navbar->AddItem('article', 'Article',
				'/office/campaign/editarticle/'.$campaign_id);
		$navbar->AddItem('reports', 'Reports',
				'/office/campaign/editreports/'.$campaign_id);
		$navbar->AddItem('facts', 'Facts',
				'/office/campaign/editfacts/'.$campaign_id);
		$navbar->AddItem('related', 'Related',
				'/office/campaign/editrelated/'.$campaign_id);
		$navbar->AddItem('publish', 'Publish',
				'/office/campaign/editpublish/'.$campaign_id);
	}
	
	function index()
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
	
		//current page
		$this->pages_model->SetPageCode('office_campaign_index');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get the full campaign list
		$data['campaign_list_live'] = $this->campaign_model->GetCampaignList();
		$data['campaign_list_future'] = $this->campaign_model->GetFutureCampaignList();
		$data['campaign_list_expired'] = $this->campaign_model->GetExpiredCampaignList();

		// Set up the view
		$the_view = $this->frames->view('office/campaign/list', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editarticle($campaign_id, $revision_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
		$this->load->model('requests_model','requests_model');
		$this->load->model('article_model','article_model');
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('article');
		$this->pages_model->SetPageCode('office_campaign_edit');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get list of all campaigns
		$data['campaign_list'] = $this->campaign_model->GetFullCampaignList();
		
		//check for problems - incorrect campaign id
		if (!isset($data['campaign_list'][$campaign_id]))
		{
			$this->main_frame->AddMessage('error','Specified campaign does not exist.');
			redirect('/office/campaign/');
		}

		//
		$article_id = $data['campaign_list'][$campaign_id]['article'];

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['article_id'] = $article_id;
		$data['parameters']['revision_id'] = $revision_id;
		$data['parameters']['campaign_id'] = $campaign_id;
		
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
					$this->main_frame->AddMessage('error','Specified revision doesn\'t exist for this article. Default selected.');
					redirect('/office/campaign/editarticle/'.$article_id.'/');
				}
			}
		}	
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $data['campaign_list'][$campaign_id]['name']));
		$this->main_frame->SetContentSimple('office/campaign/editarticle', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editreports($campaign_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('reports');
		$this->pages_model->SetPageCode('office_campaign_reports');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get list of all campaigns
		$data['campaign_list'] = $this->campaign_model->GetFullCampaignList();
		
		//load specific campaign data
		if (isset($data['campaign_list'][$campaign_id]))
		{
			$data['selected_campaign'] = $campaign_id;
			$data['article'] = $this->news_model->GetFullArticle($data['campaign_list'][$campaign_id]['article']);
		}
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $data['campaign_list'][$campaign_id]['name']));
		$this->main_frame->SetContentSimple('office/campaign/edit', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editfacts($campaign_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('facts');
		$this->pages_model->SetPageCode('office_campaign_facts');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get list of all campaigns
		$data['campaign_list'] = $this->campaign_model->GetFullCampaignList();
		
		//load specific campaign data
		if (isset($data['campaign_list'][$campaign_id]))
		{
			$data['selected_campaign'] = $campaign_id;
			$data['article'] = $this->news_model->GetFullArticle($data['campaign_list'][$campaign_id]['article']);
		}
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $data['campaign_list'][$campaign_id]['name']));
		$this->main_frame->SetContentSimple('office/campaign/editfacts', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editrelated($campaign_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('related');
		$this->pages_model->SetPageCode('office_campaign_related');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get list of all campaigns
		$data['campaign_list'] = $this->campaign_model->GetFullCampaignList();
		
		//load specific campaign data
		if (isset($data['campaign_list'][$campaign_id]))
		{
			$data['selected_campaign'] = $campaign_id;
			$data['article'] = $this->news_model->GetFullArticle($data['campaign_list'][$campaign_id]['article']);
		}
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $data['campaign_list'][$campaign_id]['name']));
		$this->main_frame->SetContentSimple('office/campaign/edit', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function editpublish($campaign_id)
	{
		if (!CheckPermissions('office')) return;

		//load the required models
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
	
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar($campaign_id);
		$this->main_frame->SetPage('publish');
		$this->pages_model->SetPageCode('office_campaign_publish');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get list of all campaigns
		$data['campaign_list'] = $this->campaign_model->GetFullCampaignList();
		
		//load specific campaign data
		if (isset($data['campaign_list'][$campaign_id]))
		{
			$data['selected_campaign'] = $campaign_id;
			$data['article'] = $this->news_model->GetFullArticle($data['campaign_list'][$campaign_id]['article']);
		}
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name' => $data['campaign_list'][$campaign_id]['name']));
		$this->main_frame->SetContentSimple('office/campaign/edit', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}
}
?>