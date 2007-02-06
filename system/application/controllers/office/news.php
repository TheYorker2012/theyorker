<?php

/**
 *	Provides the Yorker Office - News Functionality
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class News extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		// Load news model - is this needed for News Office?
		$this->load->model('news_model');
		// Load articles admin model
		$this->load->model('article_model');
	}

	function index()
	{
		// Nothing is here for the moment
		redirect('/office');
	}

	function request()
	{
		if (!CheckPermissions('office')) return;
		
		// Get changeable page content
		$this->pages_model->SetPageCode('office_news_request');

		// Get page content
		$data['heading'] = $this->pages_model->GetPropertyText('heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		//$data['reporters'] = $this->news_model->getReporters();

		// Set up the main frame
		$this->main_frame->SetContentSimple('office/news/request', $data);

		// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	function article()
	{
		if (!CheckPermissions('office')) return;
		
		// Get changeable page content
		$this->pages_model->SetPageCode('office_news_article');

		// Get page content
		$data['request_heading'] = $this->pages_model->GetPropertyText('request_heading');

		// Set up the main frame
		$this->main_frame->SetContentSimple('office/news/article', $data);

		// Set page title & load main frame with view
		$this->main_frame->SetTitleParameters(
			array('title' => 'REQUEST/ARTICLE HEADLINE')
		);
		$this->main_frame->Load();
	}

	// I've left this here (but renamed) just incase you want to use any of it when finishing off the new request function -Chris
	function request_old($Segment4)
	{
		if (!CheckPermissions('office')) return;
		
		$data = array('message' => '');
		
		switch ($Segment4) {
			case 'view':
				$admin_view_name = 'news/admin_request_view';
				break;
			default:
				$admin_view_name = 'news/admin_request_new';
				
				/// Check if the form has already been submitted
				if ($this->input->post('r_title') != false && $this->input->post('r_brief') != false)
				{
    				/// Apply XSS filtering to text inputs
    				$title = $this->input->post('r_title', true);
    				$brief = $this->input->post('r_brief', true);
    				
    				/// Fix illegal deadline dates
    				$day = $this->input->post('r_deadline_day');
    				$month = $this->input->post('r_deadline_month');
    				switch ($month)
    				{
        				case 2:
    				    if ($day > 29) $day = 29;
    				    break;
    				    
        				case 4:
        				case 6:
        				case 9:
        				case 11:
    				    if ($day > 30) $day = 30;
    				    break;
    				}
    				
    				/// Collect/format rest of data for model
    				$deadline = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $day));
    				$box = $this->input->post('r_box');
    				$reporter = $this->input->post('r_reporter');
    				
    				// if ($this->Admin_model->InsertRequest($title, $brief, $deadline, $box, $reporter)) $data['message'] = 'Your request has been added.';
        				
				}
				
				break;
		}
		
		// Set up the public frame
		$this->frame_public->SetTitle('News Admin');
		$this->frame_public->SetContentSimple($admin_view_name, $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>