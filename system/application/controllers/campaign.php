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
						'current_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('section_list_title'),'blurb'=>$this->pages_model->GetPropertyWikitext('section_list_text'),'deadline_text'=>$this->pages_model->GetPropertyWikitext('section_list_deadline_text')),
						'vote_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('section_vote_title'),'blurb'=>$this->pages_model->GetPropertyWikitext('section_vote_text'))
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
		$this->pages_model->SetPageCode('campaign_details');

		$data['campaign_list'] = $this->campaign->GetCampaignList();
		$data['selected_campaign'] = $campaign_id;
		$data['sections'] = array (
					'sidebar_vote'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_vote_title'),'blurb'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_text')),
					'sidebar_other_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_other_campaigns_title')),
					'sidebar_more'=>array('title'=>$this->pages_model->GetPropertyText('custom:sidebar_more_title')),
					'sidebar_related'=>array('title'=>$this->pages_model->GetPropertyText('custom:sidebar_related_title')),
					'sidebar_external'=>array('title'=>$this->pages_model->GetPropertyText('custom:sidebar_external_title')),
					'sidebar_comments'=>array('title'=>$this->pages_model->GetPropertyText('custom:sidebar_comments_title'))
					);
		
		if (isset($data['campaign_list'][$campaign_id]))
		{
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
	
	function Test($campaign_id = 2)
	{
		$this->load->model('campaign_model','campaign');
		$this->pages_model->SetPageCode('campaign_petition');
		$data['sections'] = array (
					'sidebar_petition'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_petition_title'),'blurb'=>$this->pages_model->GetPropertyWikitext('sidebar_petition_text')),
					'sidebar_sign'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_sign_title'),'blurb'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_text')),
					'sidebar_more'=>array('title'=>$this->pages_model->GetPropertyText('custom:sidebar_more_title')),
					'sidebar_related'=>array('title'=>$this->pages_model->GetPropertyText('custom:sidebar_related_title')),
					'sidebar_external'=>array('title'=>$this->pages_model->GetPropertyText('custom:sidebar_external_title')),
					'sidebar_comments'=>array('title'=>$this->pages_model->GetPropertyText('custom:sidebar_comments_title'))
					);
		$data['campaign'] = $this->campaign->GetPetitionCampaign($campaign_id);

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