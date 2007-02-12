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
		// Load requests admin model
		$this->load->model('requests_model');
	}

	function index()
	{
		// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		$link = 'news';
		$section = 'uninews';
		$section_formatted = 'Uni News';

		// Get changeable page content
		$this->pages_model->SetPageCode('office_news_home');
		// Get page content
		$data['tasks_heading'] = $this->pages_model->GetPropertyText('news_office:tasks_heading', TRUE);
		$data['mine_heading'] = $this->pages_model->GetPropertyText('news_office:my_jobs_heading', TRUE);
		$data['section'] = strtolower($section_formatted);
		$data['link'] = $link;

		// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
		// Get different content based on access
		if ($data['user_level'] == 'editor') {
			$data['tasks']['request'] = $this->pages_model->GetPropertyText('news_office:tasks_request_editor', TRUE);

		} else {
			$data['tasks']['request'] = $this->pages_model->GetPropertyText('news_office:tasks_request', TRUE);

		}


		// Set up the main frame
		$this->main_frame->SetContentSimple('office/news/home', $data);
		// Set page title & load main frame with view
		$this->main_frame->SetTitleParameters(
			array('section' => $section_formatted)
		);
		$this->main_frame->Load();
	}

	function request()
	{
		// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		$link = 'news';
		$section = 'uninews';
		$section_formatted = 'Uni News';

		// Get changeable page content
		$this->pages_model->SetPageCode('office_news_request');
		// Get page content
		$data['tasks_heading'] = $this->pages_model->GetPropertyText('news_office:tasks_heading', TRUE);
		$data['link'] = $link;
		$data['heading'] = $this->pages_model->GetPropertyText('heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		$data['boxes'] = $this->requests_model->getBoxes('news');
		$data['reporters'] = $this->requests_model->getReporters();

		// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
		// Get different content based on access
		if ($data['user_level'] == 'editor') {
			$data['tasks']['request'] = $this->pages_model->GetPropertyText('news_office:tasks_request_editor', TRUE);
			$status = 'request';
		} else {
			$data['tasks']['request'] = $this->pages_model->GetPropertyText('news_office:tasks_request', TRUE);
			$status = 'suggestion';
		}

		// Perform validation checks on submitted data
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<li>','</li>');
		// Validation rules
		$rules['r_title'] = 'trim|required|xss_clean';
		$rules['r_brief'] = 'trim|required|xss_clean';
		$rules['r_deadline'] = 'trim|required|numeric';
		$rules['r_box'] = 'trim|required|xss_clean';
		$rules['r_reporter'] = 'trim|required|xss_clean';
		$this->validation->set_rules($rules);
		// names of fields for error msgs
		$fields['r_title'] = 'title';
		$fields['r_brief'] = 'brief';
		$fields['r_deadline'] = 'deadline';
		$fields['r_box'] = 'box';
		$fields['r_reporter'] = 'reporter';
		$this->validation->set_fields($fields);
		// Run validation checks, if they pass proceed to conduct db integrity checks
		$errors = array();
		if ($this->validation->run()) {
			if ($this->input->post('r_deadline') < mktime()) {
				array_push($errors, 'Please select a deadline in the future');
			} elseif ($this->input->post('r_deadline') > (mktime() + (60*60*24*365))) {
				array_push($errors, 'Please select a deadline within the next year');
			}
			//TODO: Check box exists and check reporters exist
			//			if (!$this->prefs_model->collegeExists($_POST['college'])) {
			//				array_push($errors, 'Please select the college you are a member of');
			//			}

			// If no db integrity errors then save request
			if (count($errors) == 0) {
				//TODO: Where does deadline, box, reporters go?
				//$this->requests_model->CreateRequest($status,$section,$this->input->post('r_title'),$this->input->post('r_brief'),$this->user_auth->entityId,$this->input->post('r_deadline'));
				$this->main_frame->AddMessage('success','New article request created. (NOT REALLY AS IT DOESN\'T WORK YET!!!)');
				redirect('/office/' . $link);
			}
		}

		// Validation errors occured
		if ($this->validation->error_string != "") {
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
		} elseif (count($errors) > 0) {
			$temp_msg = '';
			foreach ($errors as $error) {
				$temp_msg .= '<li>' . $error . '</li>';
			}
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
		}

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

	function request_old($Segment4)
	{
		$data = array('message' => '');
		/// Check if the form has already been submitted
		if ($this->input->post('r_title') != false && $this->input->post('r_brief') != false) {
			/// Apply XSS filtering to text inputs
			$title = $this->input->post('r_title', true);
			$brief = $this->input->post('r_brief', true);
			/// Fix illegal deadline dates
			$day = $this->input->post('r_deadline_day');
			$month = $this->input->post('r_deadline_month');
			switch ($month) {
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
		}
		// Set up the public frame
		$this->main_frame->SetTitle('News Admin');
		$this->main_frame->SetContentSimple($admin_view_name, $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>