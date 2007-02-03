<?php
class Charity extends Controller {

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	function index($charity_id = 1)
	{
		$this->load->model('news_model','news');
		$this->load->model('charity_model','charity');
		$this->pages_model->SetPageCode('ourcharity');

		$data['sections'] = array (
					'article'=>$this->news->GetFullArticle(20),
					'charity'=>$this->charity->GetCharity($charity_id),
					'progress_reports'=>array('title'=>$this->pages_model->GetPropertyText('section_progress_reports_title',TRUE)),
					'sidebar_related'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_related_title',TRUE)),
					'sidebar_external'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_external_title',TRUE))
					);
					
		//needs a general model as progress reports can be for campaigns and for charities
		$pr_temp = $this->charity->GetCharityProgressReports($charity_id, 0);
		if (count($pr_temp) > 0)
		{
			foreach ($pr_temp as $row)
			{
				$data['sections']['progress_reports']['entries'][$row] = $this->news->GetFullArticle($row);
			}
		}

		// Set up the public frame
		$this->frame_public->SetTitle($this->pages_model->GetTitle(array('name'=>$data['sections']['charity']['name'])));
		$this->frame_public->SetContentSimple('charity/ourcharity', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	function edit()
	{
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
		$this->frame_public->SetTitle('Our Sponsored Charity');
		$this->frame_public->SetContentSimple('charity/charityedit', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>