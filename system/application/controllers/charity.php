<?php
class Charity extends Controller {

	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->model('news_model','news');
		$this->load->model('charity_model','charity');
		$this->load->model('progressreports_model','progressreports');
		$this->pages_model->SetPageCode('charity');

		$data['sections'] = array (
					'progress_reports'=>array('title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE),'showmore'=>$this->pages_model->GetPropertyWikitext('section_preports_showmore',FALSE)),
					'funding'=>array('title'=>$this->pages_model->GetPropertyText('section_funding_title',FALSE),'text'=>$this->pages_model->GetPropertyWikitext('section_funding_text',FALSE)),
					'sidebar_about'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_about_title',FALSE),'subtitle'=>$this->pages_model->GetPropertyText('sidebar_about_subtitle',FALSE)),
					'sidebar_help'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_help_title',FALSE),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_help_text',FALSE)),
					'sidebar_related'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_related_title',TRUE)),
					'sidebar_external'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_external_title',TRUE))
					);
			
		//special bits for preview mode
		if (isset($_POST['r_submit_preview']))
		{
			//set preview message
			$this->main_frame->AddMessage('warning','<div class="Entry">Currently previewing charity. Click <a href="'.$_POST['r_redirecturl'].'">here</a> go back to charity office.</div>');
			//set vars for preview mode
			$charity_id = $_POST['r_charityid'];
			$data['sections']['charity'] = $this->charity->GetCharity($charity_id);
			$data['sections']['article'] = $this->news->GetFullArticle($data['sections']['charity']['article'], "", "%W, %D %M %Y", $_POST['r_revisionid']);
			$data['preview_mode'] = TRUE;
		}
		else
		{
			//set vars for normal mode
			$charity_id = $this->charity->GetCurrentCharity();
			$data['sections']['charity'] = $this->charity->GetCharity($charity_id);
			$data['sections']['article'] = $this->news->GetFullArticle($data['sections']['charity']['article']);
			$data['preview_mode'] = FALSE;
		}
		
		if ($charity_id == false)
		{
			//no current charity send to main page
			$this->main_frame->AddMessage('error','There is no current charity.');
			redirect('');
		}

		$data['sections']['progress_reports']['totalcount'] = $this->progressreports->GetCharityCampaignProgressReportCount($charity_id, true);

		$data['sections']['funding']['text'] = str_replace(array("%%current%%","%%target%%"), array($data['sections']['charity']['current'],$data['sections']['charity']['target']), $data['sections']['funding']['text']);

		//needs a general model as progress reports can be for campaigns and for charities
		$pr_temp = $this->progressreports->GetCharityCampaignProgressReports($charity_id, true, true);
		if (count($pr_temp) > 0)
		{
			foreach ($pr_temp as $row)
			{
				$data['sections']['progress_reports']['entries'][$row] = $this->news->GetFullArticle($row);
			}
		}

		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name'=>$data['sections']['charity']['name']));
		$this->main_frame->SetContentSimple('charity/charity', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function preports($charity_id = 1)
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->model('news_model','news');
		$this->load->model('charity_model','charity');
		$this->pages_model->SetPageCode('charity_pr');

		$data['sections'] = array (
					'charity'=>$this->charity->GetCharity($charity_id),
					'progress_reports'=>array('title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE)),
					'sidebar_links'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_links_title',FALSE),'text'=>$this->pages_model->GetPropertyText('sidebar_links_text',FALSE))
					);	

		$data['sections']['progress_reports']['totalcount'] = $this->progressreports->GetCharityCampaignProgressReportCount($charity_id, true);

		//needs a general model as progress reports can be for campaigns and for charities
		$pr_temp = $this->progressreports->GetCharityCampaignProgressReports($charity_id, false, true);
		if (count($pr_temp) > 0)
		{
			foreach ($pr_temp as $row)
			{
				$data['sections']['progress_reports']['entries'][$row] = $this->news->GetFullArticle($row);
			}
		}

		// Set up the public frame
		$this->main_frame->SetTitleParameters(array('name'=>$data['sections']['charity']['name']));
		$this->main_frame->SetContentSimple('charity/charitypr.php', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>