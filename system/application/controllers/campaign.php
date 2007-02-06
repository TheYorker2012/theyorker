<?php
class Campaign extends Controller {

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
		$campaign_id = $this->campaign_model->GetPetitionStatus();
		if ($campaign_id == FALSE)
		{
			$this->pages_model->SetPageCode('campaign_selection');
			$data['campaign_list'] = $this->campaign_model->GetCampaignList();
			$data['current_campaigns'] = array(
					'title'=>$this->pages_model->GetPropertyText('section_list_title'),
					'text'=>$this->pages_model->GetPropertyWikitext('section_list_text'));
			$data['vote_campaigns'] = array(
					'title'=>$this->pages_model->GetPropertyText('section_vote_title'),
					'text'=>$this->pages_model->GetPropertyWikitext('section_vote_text'));
			$data['sidebar_about'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_campaign_about_title'),
					'text'=>$this->pages_model->GetPropertyWikitext('sidebar_campaign_about_text'));
			$data['sidebar_what_now'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_campaign_what_now_title'),
					'text'=>$this->pages_model->GetPropertyWikitext('sidebar_campaign_what_now_text'));

			if ($this->user_auth->isLoggedIn == TRUE)
			{
				$data['user']['id'] = $this->user_auth->entityId;
				$data['user']['firstname'] = $this->user_auth->firstname;
				$data['user']['surname'] = $this->user_auth->surname;
				$data['user']['vote_id'] = $this->campaign_model->GetUserVoteSignature($data['user']['id']);
			}
			else
			{
				$data['user'] = FALSE;
			}

			// Set up the public frame
			$this->main_frame->SetContentSimple('campaign/CampaignSelection', $data);
			
			// Load the public frame view (which will load the content view)
			$this->main_frame->Load();
		} 
		else
		{
			$this->pages_model->SetPageCode('campaign_petition');
			$data['campaign'] = $this->campaign_model->GetPetitionCampaign($campaign_id);
	
			$data['article'] = $this->news_model->GetFullArticle($data['campaign']['article']);
			$data['our_campaign'] = array(
				'title'=>$this->pages_model->GetPropertyText('section_our_campaign_title',FALSE));
			$data['progress_reports'] = array(
				'title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE));
			$data['sidebar_petition'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_petition_title'),
				'text'=>$this->pages_model->GetPropertyWikitext('sidebar_petition_text'));
			$data['sidebar_sign'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_sign_title'),
				'new_text'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_new_text'),
				'withdraw_text'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_withdraw_text'),
				'not_logged_in'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_not_logged_in'));
			$data['sidebar_more'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_more_title',TRUE),
				'text'=>$this->pages_model->GetPropertyWikitext('sidebar_more_text',TRUE));
			$data['sidebar_related'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_related_title',TRUE));
			$data['sidebar_external'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_external_title',TRUE));
			$data['sidebar_comments'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_comments_title',TRUE));
			if ($this->user_auth->isLoggedIn == TRUE)
			{
				$data['user']['id'] = $this->user_auth->entityId;
				$data['user']['firstname'] = $this->user_auth->firstname;
				$data['user']['surname'] = $this->user_auth->surname;
				$data['user']['vote_id'] = $this->campaign_model->GetUserVoteSignature($data['user']['id']);
			}
			else
			{
				$data['user'] = FALSE;
			}
						
			$pr_temp = $this->campaign_model->GetCampaignProgressReports($campaign_id, 0);
			if (count($pr_temp) > 0)
			{
				foreach ($pr_temp as $row)
				{
					$data['sections']['progress_reports']['entries'][$row] = $this->news->GetFullArticle($row);
				}
			}
	
			// Set up the public frame
			$this->main_frame->SetTitle($this->pages_model->GetTitle(array(
				'campaign'=>$data['campaign']['name']))
				);
			$this->main_frame->SetContentSimple('campaign/CampaignVote', $data);
	
			// Load the public frame view (which will load the content view)
			$this->main_frame->Load();
		}
	}

	function details($campaign_id)
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('news_model','news_model');
		$this->pages_model->SetPageCode('campaign_details');

		$cur_campaign_id = $this->campaign_model->GetPetitionStatus();
		if ($cur_campaign_id == FALSE)
		{
			$data['campaign_list'] = $this->campaign_model->GetCampaignList();
			if (isset($data['campaign_list'][$campaign_id]))
			{
				$data['selected_campaign'] = $campaign_id;
				$data['article'] = $this->news_model->GetFullArticle($data['campaign_list'][$campaign_id]['article']);
				$data['sidebar_vote'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_vote_title'),
					'newvote'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_new_text'),
					'changevote'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_change_text'),
					'withdrawvote'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_withdraw_text'),
					'not_logged_in'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_not_logged_in'));
				$data['sidebar_other_campaigns'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_other_campaigns_title'));
				$data['sidebar_more'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_more_title',TRUE),
					'text'=>$this->pages_model->GetPropertyWikitext('sidebar_more_text',TRUE));
				$data['sidebar_related'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_related_title',TRUE));
				$data['sidebar_external'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_external_title',TRUE));
				$data['sidebar_comments'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_comments_title',TRUE));
				if ($this->user_auth->isLoggedIn == TRUE)
				{
					$data['user']['id'] = $this->user_auth->entityId;
					$data['user']['firstname'] = $this->user_auth->firstname;
					$data['user']['surname'] = $this->user_auth->surname;
					$data['user']['vote_id'] = $this->campaign_model->GetUserVoteSignature($data['user']['id']);
				}
				else
				{
					$data['user'] = FALSE;
				}
				$data['parameters']['campaign'] = $campaign_id;
	
				// Set up the public frame
				$this->main_frame->SetTitle($this->pages_model->GetTitle(array(
					'campaign'=>$data['campaign_list'][$campaign_id]['name']))
					);
				$this->main_frame->SetContentSimple('campaign/CampaignDetails', $data);
	
				// Load the public frame view (which will load the content view)
				$this->main_frame->Load();
			}
			else
				redirect('/campaign');
		}
		else
			redirect('/campaign');
	}

	function castvote()
	{
		if (!CheckPermissions('student')) return;

		$this->load->model('campaign_model','campaign_model');
		$user_id = $this->user_auth->entityId;
		$this->campaign_model->SetUserVote($_POST['a_campaignid'], $user_id);
		$cur_campaign_id = $this->campaign_model->GetUserVoteSignature($user_id);
		if ($cur_campaign_id == FALSE)
                	$this->main_frame->AddMessage('success','Your vote has been cast.');
                else
                	$this->main_frame->AddMessage('success','Your vote has been changed to this campaign.');
		redirect($_POST['r_redirecturl']);
	}

	function signpetition()
	{
		if (!CheckPermissions('student')) return;

		$this->load->model('campaign_model','campaign_model');
		$user_id = $this->user_auth->entityId;
		if ($this->user_auth->checkPassword($_POST['a_password']))
		{
                        $this->campaign_model->SetUserSignature($this->campaign_model->GetPetitionID(), $user_id);
                	$this->main_frame->AddMessage('success','Your signature has been added to the petition.');
			redirect($_POST['r_redirecturl']);
		}
		else
		{
                	$this->main_frame->AddMessage('error','Incorrect Password.');
			redirect($_POST['r_redirecturl']);
		}
	}

	function withdrawvote()
	{
		if (!CheckPermissions('student')) return;
		
		$this->load->model('campaign_model','campaign_model');
		$user_id = $this->user_auth->entityId;
		$cur_campaign_id = $this->campaign_model->GetUserVoteSignature($user_id);
		if ($cur_campaign_id == FALSE)
		{
			//no vote, can't withdraw
	                $this->main_frame->AddMessage('error','You have not voted, therefore can\'t withdraw your vote.');
			redirect($_POST['r_redirecturl']);
		}
		else
		{
			if ($_POST['a_campaignid'] == $cur_campaign_id)
			{
				//has vote, can withdraw
				$this->campaign_model->WithdrawVote($user_id);
		                $this->main_frame->AddMessage('success','Your vote has been withdrawn.');
				redirect($_POST['r_redirecturl']);
			}
			else
			{
				//has vote, but not for this campaign, can't withdraw
		                $this->main_frame->AddMessage('error','You can\'t withdraw your vote for this campaign as you have voted for another campaign.');
				redirect($_POST['r_redirecturl']);

			}
		}
	}

	function withdrawsignature()
	{
		if (!CheckPermissions('student')) return;

		$this->load->model('campaign_model','campaign_model');
		$user_id = $this->user_auth->entityId;
		$cur_campaign_id = $this->campaign_model->GetUserVoteSignature($user_id);
		if ($cur_campaign_id == FALSE)
		{
			//no vote, can't withdraw
	                $this->main_frame->AddMessage('error','You have not signed, therefore can\'t withdraw your signature.');
			redirect($_POST['r_redirecturl']);
		}
		else
		{
			//has vote, can withdraw
			$this->campaign_model->WithdrawSignature($user_id);
	                $this->main_frame->AddMessage('success','Your signature has been withdrawn.');
			redirect($_POST['r_redirecturl']);
		}
	}
	
	function Edit($SelectedCampaign = '')
	{
		if (!CheckPermissions('office')) return;
		
		if($SelectedCampaign == ''){
			$this->main_frame->SetContentSimple('campaign/CampaignsEditSelect');
		} else {
			$data = array(
				'CampaignTitle' => $SelectedCampaign
			);
			$this->main_frame->SetContentSimple('campaign/CampaignsEditDetails',$data);
		}
		
		// Set up the public frame
		$this->main_frame->SetTitle('Campaign Edit');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>