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
		$this->load->model('progressreports_model','progressreports_model');
	
		$this->main_frame->IncludeCss('stylesheets/campaign.css');

		$campaign_id = $this->campaign_model->GetPetitionStatus();
		//if petition preview set campaign id
		if (isset($_POST['r_submit_preview_petition']))
		{
			$campaign_id = $_POST['r_campaignid'];
		}
		
		if ($campaign_id == FALSE) //campaign list
		{
			$this->pages_model->SetPageCode('campaign_list');
			$data['campaign_list'] = $this->campaign_model->GetLiveCampaignList();
			$data['current_campaigns'] = array(
				'title'=>$this->pages_model->GetPropertyText('section_list_title'),
				'text'=>$this->pages_model->GetPropertyWikitext('section_list_text'));
			$data['vote_campaigns'] = array(
				'title'=>$this->pages_model->GetPropertyText('section_vote_title'),
				'text'=>$this->pages_model->GetPropertyWikitext('section_vote_text'));
			$data['votes'] = array(
				'title'=>$this->pages_model->GetPropertyText('section_current_votes_title'),
				'text'=>$this->pages_model->GetPropertyWikitext('section_current_votes_text'));
			$data['sidebar_about'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_campaign_about_title'),
				'text'=>$this->pages_model->GetPropertyWikitext('sidebar_campaign_about_text'));
			$data['sidebar_how'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_how_title'),
				'text'=>$this->pages_model->GetPropertyWikitext('sidebar_how_text'));

			$total_votes = 0;
	                foreach ($data['campaign_list'] as $campaigns)
			{
				$total_votes += $campaigns['votes'];
			}
	                foreach ($data['campaign_list'] as $key => $campaigns)
			{
				if ($total_votes == 0)
					$data['campaign_list'][$key]['percentage'] = 0;
				else {
					$data['campaign_list'][$key]['percentage'] = $campaigns['votes'] / $total_votes * 100;
				}
			}

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
			$this->main_frame->SetContentSimple('campaign/campaign_list', $data);
			
			// Load the public frame view (which will load the content view)
			$this->main_frame->Load();
		} 
		else //campaign petition
		{	
			$this->pages_model->SetPageCode('campaign_petition');
			$data['campaign'] = $this->campaign_model->GetPetitionCampaign($campaign_id);
			
			//special bits for preview mode
			if (isset($_POST['r_submit_preview_petition']))
			{
				//set preview message
				$this->main_frame->AddMessage('warning','<div class="Entry">Currently previewing campaign petition. Click <a href="'.$_POST['r_redirecturl'].'">here</a> go back to campaign office.</div>');
				//set vars for preview mode
				$data['article'] = $this->news_model->GetFullArticle($data['campaign']['article'], "", "%W, %D %M %Y", $_POST['r_revisionid']);
				$data['preview_mode'] = TRUE;
			}
			else
			{
				//set vars for normal mode
				$data['article'] = $this->news_model->GetFullArticle($data['campaign']['article']);
				$data['preview_mode'] = FALSE;
			}
	
			$data['selected_campaign'] = $campaign_id;
			$data['our_campaign'] = array(
				'title'=>$this->pages_model->GetPropertyText('section_our_campaign_title',FALSE));
			$data['progress_reports'] = array(
				'title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE));
			$data['sidebar_petition'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_petition_title'),
				'text'=>$this->pages_model->GetPropertyWikitext('sidebar_petition_text',FALSE, FALSE, array('count' => array($data['campaign']['signatures'], true))));
			$data['sidebar_more'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_more_title',TRUE),
				'text'=>$this->pages_model->GetPropertyWikitext('sidebar_more_text',TRUE));
			$data['sidebar_related'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_related_title',TRUE));
			$data['sidebar_external'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_external_title',TRUE));
			$data['comments'] = array(
				'title'=>$this->pages_model->GetPropertyText('section_comments_title',TRUE));
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
			$name_replacer = array('name' => array(xml_escape($data['user']['firstname'].' '.$data['user']['surname']), true));
			$data['sidebar_sign'] = array(
				'title'=>$this->pages_model->GetPropertyText('sidebar_sign_title'),
				'new_text'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_new_text', FALSE,FALSE,$name_replacer),
				'withdraw_text'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_withdraw_text',FALSE,FALSE,$name_replacer),
				'not_logged_in'=>$this->pages_model->GetPropertyWikitext('sidebar_sign_not_logged_in'));
			
			$data['sections']['progress_reports']['totalcount'] = $this->progressreports_model->GetCharityCampaignProgressReportCount($campaign_id, false);
	
			$pr_temp = $this->progressreports_model->GetCharityCampaignProgressReports($campaign_id, true, false);
			if (count($pr_temp) > 0)
			{
				foreach ($pr_temp as $row)
				{
					$data['sections']['progress_reports']['entries'][$row] = $this->news_model->GetFullArticle($row);
				}
			}

			// Set up the public frame
			$this->main_frame->SetTitleParameters(array(
				'campaign' => $data['campaign']['name']
			));
			$this->main_frame->SetContentSimple('campaign/campaign_petition', $data);
	
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
			$data['campaign_list'] = $this->campaign_model->GetLiveCampaignList();
			//if campaign with id = $campaign_id is live or preview is enabled
			if ((isset($data['campaign_list'][$campaign_id])) || (isset($_POST['r_submit_preview_details'])))
			{
				//special bits for preview mode
				if (isset($_POST['r_submit_preview_details']))
				{
					//set preview message
					$this->main_frame->AddMessage('warning','<div class="Entry">Currently previewing campaign details. Click <a href="'.$_POST['r_redirecturl'].'">here</a> go back to campaign office.</div>');
					//set vars for preview mode
					$campaign_name = $this->campaign_model->GetCampaignNameID($campaign_id);
					$article_id = $this->campaign_model->GetCampaignArticleID($campaign_id);
					$data['article'] = $this->news_model->GetFullArticle($article_id, "", "%W, %D %M %Y", $_POST['r_revisionid']);
					$data['preview_mode'] = TRUE;
				}
				else
				{
					//set vars for normal mode
					$campaign_name = $data['campaign_list'][$campaign_id]['name'];
					$article_id = $data['campaign_list'][$campaign_id]['article'];
					$data['article'] = $this->news_model->GetFullArticle($article_id);
					$data['preview_mode'] = FALSE;
				}
				$data['selected_campaign'] = $campaign_id;
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
				$name_replacer = array('name' => array(xml_escape($data['user']['firstname'].' '.$data['user']['surname']), true));
				$data['sidebar_vote'] = array(
					'title'=>$this->pages_model->GetPropertyText('sidebar_vote_title'),
					'newvote'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_new_text', false,false,$name_replacer),
					'changevote'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_change_text', false,false,$name_replacer),
					'withdrawvote'=>$this->pages_model->GetPropertyWikitext('sidebar_vote_withdraw_text', false,false,$name_replacer),
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
				$data['comments'] = array(
					'title'=>$this->pages_model->GetPropertyText('section_comments_title',TRUE));
				$data['parameters']['campaign'] = $campaign_id;
	
				// Set up the public frame
				$this->main_frame->SetTitleParameters(array('campaign' => $campaign_name));
				$this->main_frame->SetContentSimple('campaign/campaign_details', $data);
	
				// Load the public frame view (which will load the content view)
				$this->main_frame->Load();
			}
			else
				redirect('/campaign');
		}
		else
			redirect('/campaign');
	}
	
	function preports()
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->model('news_model','news');
		$this->load->model('progressreports_model','progressreports');
		$this->load->model('campaign_model','campaign');
		$this->pages_model->SetPageCode('campaign_pr');
		
		if (isset($_POST['r_submit_preview_preports']))
		{
			//set preview message
			$this->main_frame->AddMessage('warning','<div class="Entry">Currently previewing campaign petition progress reports. Click <a href="'.$_POST['r_redirecturl'].'">here</a> go back to campaign office.</div>');
			//set vars for preview mode
			$campaign_id = $_POST['r_campaignid'];
		}
		else
		{
			$campaign_id = $this->campaign->GetPetitionStatus();
		}

		$data['sections'] = array (
					'campaign'=>$this->campaign->GetPetitionCampaign($campaign_id),
					'progress_reports'=>array('title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE)),
					'sidebar_links'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_links_title',FALSE),'text'=>$this->pages_model->GetPropertyText('sidebar_links_text',FALSE))
					);	

		$data['sections']['progress_reports']['totalcount'] = $this->progressreports->GetCharityCampaignProgressReportCount($campaign_id, false);

		//needs a general model as progress reports can be for campaigns and for charities
		$pr_temp = $this->progressreports->GetCharityCampaignProgressReports($campaign_id, false, false);
		if (count($pr_temp) > 0)
		{
			foreach ($pr_temp as $row)
			{
				$data['sections']['progress_reports']['entries'][$row] = $this->news->GetFullArticle($row);
			}
		}

		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name'=>$data['sections']['campaign']['name']));
		$this->main_frame->SetContentSimple('campaign/campaign_pr', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
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
}
?>
