<?php
class Campaign extends Controller {

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}

	function index()
	{
		if(1==1){ // change to if deadline not passed then...

			$this->load->model('campaign_model','campaign');
			$this->pages_model->SetPageCode('campaign_selection');
			$data['campaign_list'] = $this->campaign->GetCampaignList();
			$data['sections'] = array (
						'current_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('section_list_title'),'text'=>$this->pages_model->GetPropertyWikitext('section_list_text'),'deadline_text'=>$this->pages_model->GetPropertyWikitext('section_list_deadline_text')),
						'vote_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('section_vote_title'),'text'=>$this->pages_model->GetPropertyWikitext('section_vote_text')),
						'sidebar_about'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_campaign_about_title'),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_campaign_about_text')),
						'sidebar_what_now'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_campaign_what_now_title'),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_campaign_what_now_text'))
						);

			// Set up the public frame
			$this->frame_public->SetContentSimple('campaign/CampaignSelection', $data);
			
			// Load the public frame view (which will load the content view)
			$this->frame_public->Load();
		} else { // Campaign is chosen
			//curently the test fuction
		}
	}

	function Details($campaign_id)
	{
		$this->load->model('campaign_model','campaign');
		$this->load->model('news_model','news');
		$this->pages_model->SetPageCode('campaign_details');

		$data['campaign_list'] = $this->campaign->GetCampaignList();
		if (isset($data['campaign_list'][$campaign_id]))
		{
			$data['selected_campaign'] = $campaign_id;
			$data['sections'] = array (
					'article'=>$this->news->GetFullArticle($data['campaign_list'][$campaign_id]['article']),
					'sidebar_vote'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_vote_title'),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_text'),'not_logged_in'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_not_logged_in')),
					'sidebar_other_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_other_campaigns_title')),
					'sidebar_more'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_more_title',TRUE),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_more_text',TRUE)),
					'sidebar_related'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_related_title',TRUE)),
					'sidebar_external'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_external_title',TRUE)),
					'sidebar_comments'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_comments_title',TRUE))
					);
			if ($this->user_auth->isLoggedIn == TRUE)
			{
				$data['user']['id'] = $this->user_auth->entityId;
				$data['user']['firstname'] = $this->user_auth->firstname;
				$data['user']['surname'] = $this->user_auth->surname;
			}
			else
			{
				$data['user'] = FALSE;
			}

			// Set up the public frame
			$this->frame_public->SetTitle($this->pages_model->GetTitle(array('campaign'=>$data['campaign_list'][$campaign_id]['name'])));
			$this->frame_public->SetContentSimple('campaign/CampaignDetails', $data);
			
			// Load the public frame view (which will load the content view)
			$this->frame_public->Load();
		}
		else
		{
			//load an invalid campaign page
		}
	}
	
	function Test($campaign_id = 1)
	{
		$this->load->model('campaign_model','campaign');
		$this->load->model('news_model','news');
		$this->pages_model->SetPageCode('campaign_petition');
		$data['campaign'] = $this->campaign->GetPetitionCampaign($campaign_id);

		$data['sections'] = array (
					'article'=>$this->news->GetFullArticle($data['campaign']['article']),
					'our_campaign'=>array('title'=>$this->pages_model->GetPropertyText('section_our_campaign_title',FALSE)),
					'progress_reports'=>array('title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE)),
					'sidebar_petition'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_petition_title'),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_petition_text')),
					'sidebar_sign'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_sign_title'),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_text'),'not_logged_in'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_not_logged_in')),
					'sidebar_more'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_more_title',TRUE),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_more_text',TRUE)),
					'sidebar_related'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_related_title',TRUE)),
					'sidebar_external'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_external_title',TRUE)),
					'sidebar_comments'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_comments_title',TRUE))
					);
		if ($this->user_auth->isLoggedIn == TRUE)
		{
			$data['user']['id'] = $this->user_auth->entityId;
			$data['user']['firstname'] = $this->user_auth->firstname;
			$data['user']['surname'] = $this->user_auth->surname;
		}
		else
		{
			$data['user'] = FALSE;
		}
					
		$pr_temp = $this->campaign->GetCampaignProgressReports($campaign_id, 0);
		if (count($pr_temp) > 0)
		{
			foreach ($pr_temp as $row)
			{
				$data['sections']['progress_reports']['entries'][$row] = $this->news->GetFullArticle($row);
			}
		}

		// Set up the public frame
		$this->frame_public->SetTitle($this->pages_model->GetTitle(array('campaign'=>$data['campaign']['name'])));
		$this->frame_public->SetContentSimple('campaign/CampaignVote', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	function Edit($SelectedCampaign = '')
	{
		if($SelectedCampaign == ''){
			$this->frame_public->SetContentSimple('campaign/CampaignsEditSelect');
		} else {
			$data = array(
				'CampaignTitle' => $SelectedCampaign
			);
			$this->frame_public->SetContentSimple('campaign/CampaignsEditDetails',$data);
		}
		
		// Set up the public frame
		$this->frame_public->SetTitle('Campaign Edit');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function modeltest()
	{

		$this->load->model('campaign_model','campaign');
		$this->pages_model->SetPageCode('campaign_selection');
		$get_campaign_list = $this->campaign->GetCampaignList();
		$data['Campaign_List'] = $get_campaign_list;

		// Set up the public frame
		$this->frame_public->SetContentSimple('campaign/test', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

}
?>