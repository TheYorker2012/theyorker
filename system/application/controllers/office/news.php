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

	function _newRequest($data)
	{
		// Get different content based on access
		if ($data['user_level'] == 'editor') {
			$data['status'] = 'request';
			$data['heading'] = $this->pages_model->GetPropertyText('heading_editor');
			$data['intro'] = $this->pages_model->GetPropertyWikitext('intro_editor');
		} else {
			$data['status'] = 'suggestion';
			$data['heading'] = $this->pages_model->GetPropertyText('heading');
			$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		}

		// Perform validation checks on submitted data
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<li>','</li>');
		// Validation rules
		$rules['r_title'] = 'trim|required|xss_clean';
		$fields['r_title'] = 'title';
		$rules['r_brief'] = 'trim|required|xss_clean';
		$fields['r_brief'] = 'brief';
		$rules['r_box'] = 'trim|required|numeric';
		$fields['r_box'] = 'box';
		if ($data['status'] == 'request') {
			$rules['r_deadline'] = 'trim|required|numeric';
			$fields['r_deadline'] = 'deadline';
			$rules['r_reporter'] = 'required';
			$fields['r_reporter'] = 'reporter';
		}
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		// Run validation checks, if they pass proceed to conduct db integrity checks
		$errors = array();
		if ($this->validation->run()) {
			$deadline = NULL;
			if ($data['status'] == 'request') {
				if ($this->input->post('r_deadline') < mktime()) {
					$errors[] = 'Please select a deadline in the future';
				} elseif ($this->input->post('r_deadline') > (mktime() + (60*60*24*365))) {
					$errors[] = 'Please select a deadline within the next year';
				} else {
					$deadline = $this->input->post('r_deadline');
				}
				$valid = true;
				foreach ($this->input->post('r_reporter') as $reporter) {
					if (!is_numeric($reporter)) {
						$valid = false;
					}
				}
				if ((!$valid) || (!$this->requests_model->reportersExist($this->input->post('r_reporter')))) {
					$errors[] = 'Please choose the reporters you wish to assign the request to';
				}
			}
			if (!$this->requests_model->isBox($this->input->post('r_box'))) {
				$errors[] = 'Please select the box you wish the ' . $data['status'] . ' to be submitted to';
			}

			// If no db integrity errors then save request
			if (count($errors) == 0) {
				if ($deadline != NULL) {
					$deadline = date('Y-m-d H:i:s', $deadline);
				}
				$this->requests_model->CreateRequest($data['status'],$this->input->post('r_box'),$this->input->post('r_title'),$this->input->post('r_brief'),$this->user_auth->entityId,$deadline);
				$this->main_frame->AddMessage('success','New article ' . $data['status'] . ' created.');
				redirect('/office/news');
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
		$this->main_frame->SetTitleParameters(
			array('action' => 'New', 'type' => $data['status'])
		);
	}

	function _editRequest ($article_id, $data)
	{
		// Get different content based on access
		if ($data['user_level'] == 'editor') {
			$data['heading'] = $this->pages_model->GetPropertyText('heading_editor');
			$data['intro'] = $this->pages_model->GetPropertyWikitext('intro_editor');
		} else {
			$data['heading'] = $this->pages_model->GetPropertyText('heading');
			$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		}

		$data['edit_enable'] = false;
		if ($data['status'] == 'suggestion') {
			$data['article'] = $this->requests_model->GetSuggestedArticle($article_id);
			if (($data['user_level'] == 'editor') || ($data['article']['userid'] == $this->user_auth->entityId)) {
				$data['edit_enable'] = true;
			}
		} elseif ($data['status'] == 'request') {
			$data['article'] = $this->requests_model->GetRequestedArticle($article_id);
			$data['assigned_reporters'] = $this->requests_model->GetWritersForArticle($article_id);
			$data['isUserAssigned'] = $this->requests_model->IsUserRequestedForArticle($article_id, $this->user_auth->entityId);
			if ($data['user_level'] == 'editor') {
				$data['edit_enable'] = true;
			}
		}

		// Perform validation checks on submitted data
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<li>','</li>');
		// Validation rules
		$rules['r_title'] = 'trim|required|xss_clean';
		$fields['r_title'] = 'title';
		$rules['r_brief'] = 'trim|required|xss_clean';
		$fields['r_brief'] = 'brief';
		$rules['r_box'] = 'trim|required|numeric';
		$fields['r_box'] = 'box';
		if ($data['user_level'] == 'editor') {
			$rules['r_deadline'] = 'trim|required|numeric';
			$fields['r_deadline'] = 'deadline';
			$rules['r_reporter'] = 'required';
			$fields['r_reporter'] = 'reporter';
		}
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		// Run validation checks, if they pass proceed to conduct db integrity checks
		$errors = array();
		if ($this->validation->run()) {
			$deadline = NULL;
			if ($data['user_level'] == 'editor') {
				if ($this->input->post('r_deadline') < mktime()) {
					$errors[] = 'Please select a deadline in the future';
				} elseif ($this->input->post('r_deadline') > (mktime() + (60*60*24*365))) {
					$errors[] = 'Please select a deadline within the next year';
				} else {
					$deadline = $this->input->post('r_deadline');
				}
				$valid = true;
				foreach ($this->input->post('r_reporter') as $reporter) {
					if (!is_numeric($reporter)) {
						$valid = false;
					}
				}
				if ((!$valid) || (!$this->requests_model->reportersExist($this->input->post('r_reporter')))) {
					$errors[] = 'Please choose the reporters you wish to assign the request to';
				}
			}
			if (!$this->requests_model->isBox($this->input->post('r_box'))) {
				$errors[] = 'Please select the box you wish the ' . $data['status'] . ' to be submitted to';
			}

			// If no db integrity errors then save request
			if (count($errors) == 0) {
				if ($deadline != NULL) {
					$deadline = date('Y-m-d H:i:s', $deadline);
				}
				if ($data['status'] == 'suggestion') {
					if ($data['user_level'] == 'editor') {
						if ($this->input->post('accept') == 'Accept') {
							$accept_data = array(
								'editor' => $this->user_auth->entityId,
								'publish_date' => $deadline,
								'title' => $this->input->post('r_title'),
								'description' => $this->input->post('r_brief'),
								'content_type' => $this->input->post('r_box')
							);
							$this->requests_model->UpdateRequestStatus($article_id,'request',$accept_data);
							foreach ($this->input->post('r_reporter') as $reporter) {
								$this->requests_model->AddUserToRequest($article_id, $reporter);
							}
							$this->main_frame->AddMessage('success','Suggestion accepted and request generated.');
						} else {
							$this->requests_model->RejectSuggestion($article_id);
							$this->main_frame->AddMessage('success','Suggestion successfully rejected.');
						}
					} else {
                        $this->requests_model->UpdateSuggestion($article_id,array('title' => $this->input->post('r_title'), 'description' => $this->input->post('r_brief'), 'content_type' => $this->input->post('r_box')));
						$this->main_frame->AddMessage('success','Suggestion details saved.');
					}
				}
				redirect('/office/news/request/' . $data['article']['id']);
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
		} else {
			// First time form has been loaded so populate fields
			$this->validation->r_title = $data['article']['title'];
			$this->validation->r_brief = $data['article']['description'];
			$this->validation->r_box = $data['article']['box'];
			if ($data['status'] == 'request') {
				$this->validation->r_deadline = $data['article']['deadline'];
			}
		}

		// Set up the main frame
		$this->main_frame->SetContentSimple('office/news/edit_request', $data);
		// Set page title & load main frame with view
		$this->main_frame->SetTitleParameters(
			array('action' => 'Edit', 'type' => $data['status'])
		);
	}

	function request()
	{
		// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		// Get changeable page content
		$this->pages_model->SetPageCode('office_news_request');
		// Get page content
		$data['boxes'] = $this->requests_model->getBoxes();
		$data['reporters'] = $this->requests_model->getReporters();
		$data['tasks_heading'] = $this->pages_model->GetPropertyText('news_office:tasks_heading', TRUE);

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

		// Determine what operation we are performing on the request
		if (($this->uri->segment(4) === FALSE) || (!is_numeric($this->uri->segment(4)))) {
			$this->_newRequest($data);
		} else {
			$article_info = $this->article_model->GetArticleHeader($this->uri->segment(4));
			// Check article exists
			if ($article_info !== FALSE) {
				if ($article_info['status'] == 'published') {
					$this->main_frame->AddMessage('error','This article has already been published.');
					redirect('/office/news/');
				} else {
					$data['status'] = $article_info['status'];
					$this->_editRequest($this->uri->segment(4),$data);
				}
			} else {
				redirect('/office/news/');
			}
		}

		// Load main frame
		$this->main_frame->SetData('extra_head', '<style type=\'text/css\'>@import url(\'/stylesheets/calendar_select.css\');</style>');
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

}

?>