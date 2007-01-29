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
						'current_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('campaign_list_title'),'blurb'=>$this->pages_model->GetPropertyWikitext('campaign_list_text'),'deadline_text'=>$this->pages_model->GetPropertyWikitext('campaign_list_deadline_text')),
						'vote_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('campaign_vote_title'),'blurb'=>$this->pages_model->GetPropertyWikitext('campaign_vote_text'))
						);

			// Set up the public frame
			$this->frame_public->SetContentSimple('campaign/CampaignSelection', $data);
			
			// Load the public frame view (which will load the content view)
			$this->frame_public->Load();
		} else { // Campaign is chosen
			//curently the test fuction
		}
	}

	function Details($SelectedCampaign)
	{
		$this->load->model('campaign_model','campaign');
		$this->pages_model->SetPageCode('campaign_details');

		$data['campaign_list'] = $this->campaign->GetCampaignList();
		$data['selected_campaign'] = $SelectedCampaign;
		$data['sections'] = array (
					'sidebar_vote'=>array('title'=>$this->pages_model->GetPropertyText('campaign_details_sign_title'),'blurb'=>$this->pages_model->GetPropertyWikitext('campaign_details_sign_text')),
					'sidebar_other_campaigns'=>array('title'=>$this->pages_model->GetPropertyText('campaign_details_other_campaigns_title'))
					);
		
		if (isset($data['campaign_list'][$SelectedCampaign]))
		{
			// Set up the public frame
			$this->frame_public->SetTitle($this->pages_model->GetTitle(array('campaign'=>$data['campaign_list'][$SelectedCampaign]['name'])));
			$this->frame_public->SetContentSimple('campaign/CampaignDetails', $data);
			
			// Load the public frame view (which will load the content view)
			$this->frame_public->Load();
		}
		else
		{
			//load an invalid campaign page
		}
	}
	
	function Test($SelectedCampaign = 'Pie Eating')
	{
		$data = array(
			'Title' => $SelectedCampaign,
			'Picture' => 'http://localhost/images/prototype/campaign/field.jpg',
			'Summery' => 'Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text.',
			'NumOfSignatures' => '89546',
			'Username' => 'Tom Jones',
			'ProgressItems' => array(
				array('good'=>'y','details'=>'Progress Report 1, Progress Report 1, Progress Report 1, Progress Report 1, Progress Report 1.'),
				array('good'=>'n','details'=>'Progress Report 2, Progress Report 2, Progress Report 2, Progress Report 2, Progress Report 2.'),
				array('good'=>'n','details'=>'Progress Report 3, Progress Report 3, Progress Report 3, Progress Report 3, Progress Report 3.')
				)
		);
		
		// Set up the public frame
		$this->frame_public->SetTitle('Campaign - '.$SelectedCampaign);
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