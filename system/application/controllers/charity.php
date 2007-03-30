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
		$this->pages_model->SetPageCode('charity');
		
		$charity_id = $this->charity->GetCurrentCharity();

		$data['sections'] = array (
					'charity'=>$this->charity->GetCharity($charity_id),
					'progress_reports'=>array('title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE),'showmore'=>$this->pages_model->GetPropertyWikitext('section_preports_showmore',FALSE)),
					'funding'=>array('title'=>$this->pages_model->GetPropertyText('section_funding_title',FALSE),'text'=>$this->pages_model->GetPropertyWikitext('section_funding_text',FALSE)),
					'sidebar_about'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_about_title',FALSE),'subtitle'=>$this->pages_model->GetPropertyText('sidebar_about_subtitle',FALSE)),
					'sidebar_help'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_help_title',FALSE),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_help_text',FALSE)),
					'sidebar_related'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_related_title',TRUE)),
					'sidebar_external'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_external_title',TRUE))
					);	

		$data['sections']['article'] = $this->news->GetFullArticle($data['sections']['charity']['article']);			

		$data['sections']['progress_reports']['totalcount'] = $this->charity->GetCharityProgressReportCount($charity_id);					

		$data['sections']['funding']['text'] = str_replace(array("%%current%%","%%target%%"), array($data['sections']['charity']['current'],$data['sections']['charity']['target']), $data['sections']['funding']['text']);

		//needs a general model as progress reports can be for campaigns and for charities
		$pr_temp = $this->charity->GetCharityProgressReports($charity_id, true);
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
		$this->pages_model->SetPageCode('charity');

		$data['sections'] = array (
					'charity'=>$this->charity->GetCharity($charity_id),
					'progress_reports'=>array('title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE))
					);	

		$data['sections']['progress_reports']['totalcount'] = $this->charity->GetCharityProgressReportCount($charity_id);

		//needs a general model as progress reports can be for campaigns and for charities
		$pr_temp = $this->charity->GetCharityProgressReports($charity_id, false);
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
	
	//for testing
	function edit()
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('charity');
		
		$data = array(
			'Description' => '<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!',
			'Picture' => 'http://localhost/images/prototype/campaign/field.jpg',
			'ProgressItems' => array(
				array('good'=>'y','details'=>'Progress Report 1, Progress Report 1, Progress Report 1, Progress Report 1, Progress Report 1.'),
				array('good'=>'n','details'=>'Progress Report 2, Progress Report 2, Progress Report 2, Progress Report 2, Progress Report 2.'),
				array('good'=>'n','details'=>'Progress Report 3, Progress Report 3, Progress Report 3, Progress Report 3, Progress Report 3.')
				)
		);
		
		// Set up the public frame
		$this->main_frame->SetContentSimple('charity/charityedit', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>