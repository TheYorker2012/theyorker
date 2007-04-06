<?php

/**
 *	Provides the Yorker Office - News Functionality
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class News extends Controller
{
	/**
	 *	@brief Default constructor
	 */
	function __construct()
	{
		parent::Controller();

		/// Load news model
		$this->load->model('news_model');
		/// Load articles admin model
		$this->load->model('article_model');
		/// Load requests admin model
		$this->load->model('requests_model');
	}


	/**
	 *	@brief Determines which function is used depending on url
	 */
	function _remap($method)
	{
		$type_info = $this->news_model->getArticleTypeInformation($method);
		/// If url is a type then load box view
		if (count($type_info) > 0) {
			$this->index($type_info);
		/// If numeric then its an article
		} elseif (is_numeric($method)) {
			$this->article($method);
		/// Matches: /request
		} elseif ((method_exists($this, $method)) && ($method != 'index')) {
			$this->$method();
		/// Default behaviour: display uninews box
		} else {
			/// @TODO: Get logged in user's yorker journalist team and display that box
			$this->index($this->news_model->getArticleTypeInformation('uninews'));
		}
	}


	/**
	 *	@brief Displays request box for article type
	 */
	function index($type_info)
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		/// Get changeable page content
		$this->pages_model->SetPageCode('office_news_home');
		/// Get page content
		$data['tasks_heading'] = $this->pages_model->GetPropertyText('news_office:tasks_heading', TRUE);
		$data['mine_heading'] = $this->pages_model->GetPropertyText('news_office:my_jobs_heading', TRUE);
		$data['box_contents'] = $this->requests_model->GetRequestedArticles($type_info['codename']);
		$data['suggestions'] = $this->requests_model->GetSuggestedArticles($type_info['codename']);
		$data['parent_type'] = $type_info['has_children'];

		$data['my_requests'] = $this->requests_model->GetRequestsForUser($this->user_auth->entityId);
		$data['box_display_name'] = $type_info['name'];

		/// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
		/// Get different content based on access
		if ($data['user_level'] == 'editor') {
			$data['tasks']['request'] = $this->pages_model->GetPropertyText('news_office:tasks_request_editor', TRUE);
		} else {
			$data['tasks']['request'] = $this->pages_model->GetPropertyText('news_office:tasks_request', TRUE);
		}

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/news/home', $data);
		/// Set page title & load main frame with view
		$this->main_frame->SetTitleParameters(
			array('section' => $type_info['name'])
		);
		$this->main_frame->Load();
	}


	/**
	 *	@brief Create a new suggestion/request
	 */
	function request()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		/// Get changeable page content
		$this->pages_model->SetPageCode('office_news_request');
		/// Get page content
		$data['boxes'] = $this->requests_model->getBoxes();
		/// @TODO: this needs to get reporters only part of the article type yorker sub-team
		$data['reporters'] = $this->requests_model->getReporters();
		$data['tasks_heading'] = $this->pages_model->GetPropertyText('news_office:tasks_heading', TRUE);

		/// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
		/// Get different content based on access
		if ($data['user_level'] == 'editor') {
			$data['heading'] = $this->pages_model->GetPropertyText('heading_editor');
			$data['intro'] = $this->pages_model->GetPropertyWikitext('intro_editor');
			$data['status'] = 'request';
		} else {
			$data['heading'] = $this->pages_model->GetPropertyText('heading');
			$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
			$data['status'] = 'suggestion';
		}

		/// Perform validation checks on submitted data
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<li>','</li>');
		/// Validation rules
		$rules['r_title'] = 'trim|required|xss_clean';
		$fields['r_title'] = 'title';
		$rules['r_brief'] = 'trim|required|xss_clean';
		$fields['r_brief'] = 'brief';
		$rules['r_box'] = 'trim|required|xss_clean';
		$fields['r_box'] = 'box';
		if ($data['status'] == 'request') {
			$rules['r_deadline'] = 'trim|required|numeric';
			$fields['r_deadline'] = 'deadline';
			$rules['r_reporter'] = 'required';
			$fields['r_reporter'] = 'reporter';
		}
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		/// Run validation checks, if they pass proceed to conduct db integrity checks
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
				if (count($this->input->post('r_reporter')) == 0) {
					$valid = false;
				}
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

			/// If no db integrity errors then save request
			if (count($errors) == 0) {
				if ($deadline != NULL) {
					$deadline = date('Y-m-d H:i:s', $deadline);
				}
				$article_id = $this->requests_model->CreateRequest($data['status'],$this->input->post('r_box'),$this->input->post('r_title'),$this->input->post('r_brief'),$this->user_auth->entityId,$deadline);
				if ($data['status'] == 'request') {
					/// Assign reporters to request
					foreach ($this->input->post('r_reporter') as $reporter) {
						$this->requests_model->AddUserToRequest($article_id, $reporter, $this->user_auth->entityId);
					}
					/// Create initial revision
					$revision = $this->article_model->CreateNewRevision($article_id, $this->user_auth->entityId, '', '', '', '', '', '');
				}
				$this->main_frame->AddMessage('success','New article ' . $data['status'] . ' created.');
				redirect('/office/news/' . $article_id);
			}
		}

		/// Validation errors occured
		if ($this->validation->error_string != "") {
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
		} elseif (count($errors) > 0) {
			$temp_msg = '';
			foreach ($errors as $error) {
				$temp_msg .= '<li>' . $error . '</li>';
			}
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
		}

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/news/request', $data);
		/// Set page title & load main frame with view
		$this->main_frame->SetTitleParameters(
			array('action' => 'New', 'type' => $data['status'])
		);

		/// Load main frame
		$this->main_frame->SetData('extra_head', '<style type="text/css">@import url("/stylesheets/calendar_select.css");</style>');
		$this->main_frame->Load();
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
		$rules['r_box'] = 'trim|required|xss_clean';
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
			$this->validation->r_box = $data['article']['box_codename'];
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







	/**
	 *	@brief Mapping function for operations on a particular article id
	 */
	function article($article_id = 0)
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		/// Get article ID
		if ($article_id == 0) {
			if (is_numeric($this->uri->segment(4))) {
				redirect('/office/news/' . $this->uri->segment(4));
			} else {
				redirect('/office/news');
			}
		} else {
			/// Check article ID exists
			if (is_numeric($article_id)) {
				$article_info = $this->article_model->GetArticleHeader($article_id);
				if ($article_info !== FALSE) {
	
					/// Make it so we only have to worry about two levels of access as admins can do everything editors can
					$data['user_level'] = GetUserLevel();
					if ($data['user_level'] == 'admin') {
						$data['user_level'] = 'editor';
					}
	
					/// Determine what operation to perform
					switch ($article_info['status']) {
						case 'pulled':
							/// EVERYONE: View + Notice that article pulled
							break;
						case 'published':
							/// EVERYONE: View + Notice that already published
							/// EDITOR: Changes + Pull + Change publish date
							break;
						case 'request':
							/// If editor but also assigned reporter and not accepted then is reporter
							if ($this->input->post('publish') == 'Publish Article') {
								$this->_publishArticle($article_id);
							} else {
								$this->_showarticle($article_id);
							}
							break;
						case 'suggestion':
							$this->_editSuggestion($article_id,$data);
							break;
					}
	
				} else {
					redirect('/office/news');
				}
			} else {
				redirect('/office/news');
			}
		}
	}


	/**
	 *	@brief View/Edit suggestions
	 */
	function _editSuggestion ($article_id, $data)
	{
		/// Get changeable page content
		$this->pages_model->SetPageCode('office_news_request');
		/// Get page content
		$data['boxes'] = $this->requests_model->getBoxes();
		/// @TODO: this needs to get reporters only part of the article type yorker sub-team
		$data['reporters'] = $this->requests_model->getReporters();
		$data['tasks_heading'] = $this->pages_model->GetPropertyText('news_office:tasks_heading', TRUE);
		$data['status'] = 'suggestion';

		/// Get different content based on access
		if ($data['user_level'] == 'editor') {
			$data['heading'] = $this->pages_model->GetPropertyText('heading_editor');
			$data['intro'] = $this->pages_model->GetPropertyWikitext('intro_editor');
		} else {
			$data['heading'] = $this->pages_model->GetPropertyText('heading');
			$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		}

		/// Determine if current user can edit suggestion details
		$data['edit_enable'] = false;
		$data['article'] = $this->requests_model->GetSuggestedArticle($article_id);
		if (($data['user_level'] == 'editor') || ($data['article']['userid'] == $this->user_auth->entityId)) {
			$data['edit_enable'] = true;
		}

		/// Perform validation checks on submitted data
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<li>','</li>');
		/// Validation rules
		$rules['r_title'] = 'trim|required|xss_clean';
		$fields['r_title'] = 'title';
		$rules['r_brief'] = 'trim|required|xss_clean';
		$fields['r_brief'] = 'brief';
		$rules['r_box'] = 'trim|required|xss_clean';
		$fields['r_box'] = 'box';
		if ($data['user_level'] == 'editor') {
			$rules['r_deadline'] = 'trim|required|numeric';
			$fields['r_deadline'] = 'deadline';
			$rules['r_reporter'] = 'required';
			$fields['r_reporter'] = 'reporter';
		}
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		/// Run validation checks, if they pass proceed to conduct db integrity checks
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
				$errors[] = 'Please select the box you wish the suggestion to be submitted to';
			}

			/// If no db integrity errors then save request
			if (count($errors) == 0) {
				if ($deadline != NULL) {
					$deadline = date('Y-m-d H:i:s', $deadline);
				}
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
							$this->requests_model->AddUserToRequest($article_id, $reporter, $this->user_auth->entityId);
						}
						$revision = $this->article_model->CreateNewRevision($article_id, $this->user_auth->entityId, '', '', '', '', '', '');
						$this->main_frame->AddMessage('success','Suggestion accepted and request generated.');
					} else {
						$this->requests_model->RejectSuggestion($article_id);
						$this->main_frame->AddMessage('success','Suggestion successfully rejected.');
					}
				} else {
					$this->requests_model->UpdateSuggestion($article_id,array('title' => $this->input->post('r_title'), 'description' => $this->input->post('r_brief'), 'content_type' => $this->input->post('r_box')));
					$this->main_frame->AddMessage('success','Suggestion details saved.');
				}
				redirect('/office/news/' . $article_id);
			}
		}

		/// Validation errors occured
		if ($this->validation->error_string != "") {
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
		} elseif (count($errors) > 0) {
			$temp_msg = '';
			foreach ($errors as $error) {
				$temp_msg .= '<li>' . $error . '</li>';
			}
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
		} else {
			/// First time form has been loaded so populate fields
			$this->validation->r_title = $data['article']['title'];
			$this->validation->r_brief = $data['article']['description'];
			$this->validation->r_box = $data['article']['box_codename'];
			if ($data['status'] == 'request') {
				$this->validation->r_deadline = $data['article']['deadline'];
			}
		}

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/news/edit_request', $data);
		/// Set page title & load main frame with view
		$this->main_frame->SetTitleParameters(
			array('action' => 'Edit', 'type' => 'request')
		);

		/// Load main frame
		$this->main_frame->SetData('extra_head', '<style type="text/css">@import url("/stylesheets/calendar_select.css");</style>');
		$this->main_frame->Load();
	}


	/**
	 *	@brief Publish an article either setting a date for it to go live or adding it to the article pool
	 */
	function _publishArticle($article_id)
	{
		// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
		$data['article'] = $this->article_model->GetArticleDetails($article_id);
		if (count($data['article']) == 0) {
			$this->main_frame->AddMessage('error','The article you requested to publish does not exist, please try again.');
			redirect('/office/news/');
		} elseif ($data['user_level'] != 'editor') {
			$this->main_frame->AddMessage('error','Only editors may publish articles.');
			redirect('/office/news/' . $article_id);
		} else {
			/// @TODO: Allow adding to article pool
			if ($this->input->post('confirm_publish') == 'Publish') {
				if (!is_numeric($this->input->post('r_publish'))) {
					$this->main_frame->AddMessage('error','Please select a date and time to publish the article.');
//	Commented out for now so i can add previous articles!
//				} elseif ($this->input->post('r_publish') < mktime()) {
//					$this->main_frame->AddMessage('error','Please select a publish date in the future.');
				} elseif ($this->input->post('r_publish') > (mktime() + (60*60*24*365))) {
					$this->main_frame->AddMessage('error','Please select a publish date within the next year.');
				} else {
					/// Get revision to publish
					/// @TODO: Allow specifying of revision to publish
					$revision_id = $this->article_model->GetLatestRevision($article_id);
					$publish_date = date('Y-m-d H:i:s', $this->input->post('r_publish'));
					$this->requests_model->PublishArticle($article_id,$revision_id,$publish_date);
					$this->main_frame->AddMessage('success','The article was successfully published.');
					redirect('/office/news');
				}
			}

			// Get page content
			$this->pages_model->SetPageCode('office_news_publish');
			$data['heading'] = $this->pages_model->GetPropertyText('heading');
			$data['intro_text'] = $this->pages_model->GetPropertyWikitext('intro_text');

			// Set up the main frame
			$this->main_frame->SetContentSimple('office/news/publish', $data);
			$this->main_frame->SetData('extra_head', '<style type="text/css">@import url("/stylesheets/calendar_select.css");</style>');
			// Set page title & load main frame with view
			$this->main_frame->SetTitleParameters(
				array('title' => $data['article']['request_title'])
			);
			$this->main_frame->Load();
		}
	}









	function _showarticle($article_id = 0)
	{
		$data['article'] = $this->article_model->GetArticleDetails($article_id);
		if (count($data['article']) > 0) {
			// Is user requested for this article? i.e. can edit
			$data['user_requested'] = $this->requests_model->IsUserRequestedForArticle($article_id, $this->user_auth->entityId);
			// Show or hide accept/decline request buttons
			$data['user_requested'] = ($data['user_requested'] == 'requested');

			// Make it so we only have to worry about two levels of access as admins can do everything editors can
			$data['user_level'] = GetUserLevel();
			if ($data['user_level'] == 'admin') {
				$data['user_level'] = 'editor';
			}

			if ($data['user_requested']) {
				if ($this->input->post('accept') == 'Accept Request') {
					$this->requests_model->AcceptRequest($article_id, $this->user_auth->entityId);
					$this->main_frame->AddMessage('success','Article request accepted.');
					redirect('/office/news/' . $article_id);
				} elseif ($this->input->post('decline') == 'Decline Request') {
					$this->requests_model->DeclineRequest($article_id, $this->user_auth->entityId);
					$this->main_frame->AddMessage('success','Article request declined.');
					redirect('/office/news/' . $article_id);
				}
			 }

			// Setup XAJAX functions
			$this->load->library('xajax');
	        $this->xajax->registerFunction(array('_addComment', &$this, '_addComment'));
	        $this->xajax->registerFunction(array('_updateHeadlines', &$this, '_updateHeadlines'));
	        $this->xajax->registerFunction(array('_newFactbox', &$this, '_newFactbox'));
	        $this->xajax->registerFunction(array('_removeFactBox', &$this, '_removeFactBox'));
	        $this->xajax->processRequests();

			// Create menu
			$navbar = $this->main_frame->GetNavbar();
			$navbar->AddItem('revisions', 'revisions', 'javascript:tabs(\'revisions\');');
			$navbar->AddItem('comments', 'comments', 'javascript:tabs(\'comments\');');
			$navbar->AddItem('sidebar', 'sidebar', 'javascript:tabs(\'sidebar\');');
			$navbar->AddItem('article', 'body', 'javascript:tabs(\'article\');');
			$navbar->AddItem('request', 'request', 'javascript:tabs(\'request\');');
			$navbar->SetSelected('request');

			// Get page content
			$this->pages_model->SetPageCode('office_news_article');
			$data['request_heading'] = $this->pages_model->GetPropertyText('request_heading');

			/// @todo jh559,cdt502 ajaxify comments
			$this->load->library('comments');
			$thread = $this->news_model->GetPrivateThread($article_id);
			$this->comments->SetUri('/office/news/'.$article_id.'/');
			/// @todo jh559,cdt502 comment pages (page hardwired to 1 atm)
			$data['comments'] = $this->comments->CreateStandard($thread, /* included comment */ 0);
			
			$data['revisions'] = $this->requests_model->GetArticleRevisions($article_id);
			$revision = $this->article_model->GetLatestRevision($article_id);
			if (!$revision) {
				// There is no revision for this article yet... so create one
				$revision = $this->article_model->CreateNewRevision($article_id, $this->user_auth->entityId, '', '', '', '', '', '');
			}
			// Get latest revision's data
			$data['revision'] = $this->article_model->GetRevisionData($revision);

			// Set up the main frame
			$this->main_frame->SetContentSimple('office/news/article', $data);
			$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
			// Set page title & load main frame with view
			$this->main_frame->SetTitleParameters(
				array('title' => $data['article']['request_title'])
			);
			$this->main_frame->Load();
		}
	}


	function preview()
	{
		if (!CheckPermissions('office')) return;

		$_SESSION['office_news_preview'] = $this->uri->segment(6);
		redirect('/news/' . $this->uri->segment(5) . '/' . $this->uri->segment(4));
	}



	function _newFactbox($revision,$title,$text)
	{
		$xajax_response = new xajaxResponse();
		$article_id = $this->uri->segment(3);
		// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
		if (($data['user_level'] == 'editor') || ($this->requests_model->IsUserRequestedForArticle($article_id, $this->user_auth->entityId) == 'accepted')) {
			if (is_numeric($revision)) {
				$title = addslashes($this->input->xss_clean($title));
				$text = addslashes($this->input->xss_clean($text));
				if ($revision == 0) {
					$xajax_response->addScriptCall('factbox_created',0);
				} else {
					$fact_box_id = $this->article_model->InsertFactBox($revision, $title, $text);
					$xajax_response->addScriptCall('factbox_created',1);
				}
			 } else {
				$xajax_response->addAlert('Invalid revision number, please try reloading the page.');
			 }
		} else {
			$xajax_response->addAlert('You do not have the permissions required to edit the details for this article!');
		}
		return $xajax_response;
	}

	function _removeFactBox($revision)
	{
		$xajax_response = new xajaxResponse();
		// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
		if (($data['user_level'] == 'editor') || ($this->requests_model->IsUserRequestedForArticle($article_id, $this->user_auth->entityId) == 'accepted')) {
			if (is_numeric($revision)) {
				$this->article_model->DeleteRevisionFactBox($revision);
				$xajax_response->addScriptCall('factbox_deleted');
			 } else {
				$xajax_response->addAlert('Invalid revision number, please try reloading the page.');
			 }
		} else {
			$xajax_response->addAlert('You do not have the permissions required to edit the details for this article!');
		}
		return $xajax_response;
	}

	function _updateHeadlines($revision,$headline,$subheadline,$subtext,$blurb,$wiki,$create_cache,$fact_heading,$fact_text)
	{
		$xajax_response = new xajaxResponse();
		$article_id = $this->uri->segment(3);
		// Make it so we only have to worry about two levels of access as admins can do everything editors can
		$data['user_level'] = GetUserLevel();
		if ($data['user_level'] == 'admin') {
			$data['user_level'] = 'editor';
		}
		if (($data['user_level'] == 'editor') || ($this->requests_model->IsUserRequestedForArticle($article_id, $this->user_auth->entityId) == 'accepted')) {
			if (is_numeric($revision)) {
				$headline = htmlentities($this->input->xss_clean($headline));
				$subheadline = htmlentities($this->input->xss_clean($subheadline));
				$subtext = htmlentities($this->input->xss_clean($subtext));
				$blurb = htmlentities($this->input->xss_clean($blurb));
				$wiki = htmlentities($this->input->xss_clean($wiki));
				$fact_heading = htmlentities($this->input->xss_clean($fact_heading));
				$fact_text = htmlentities($this->input->xss_clean($fact_text));
				$revision = $this->article_model->GetArticleRevisionToEdit($article_id, $this->user_auth->entityId, $revision);
				$wiki_cache = '';
//				if ($create_cache) {
					$this->load->library('wikiparser');
					$wiki_cache = $this->wikiparser->parse($wiki);
//				}
				if ($revision == 0) {
					$revision = $this->article_model->CreateNewRevision($article_id, $this->user_auth->entityId, $headline, $subheadline, $subtext, $blurb, $wiki, $wiki_cache);
				} else {
					$this->article_model->UpdateRevision($revision,$headline,$subheadline,$subtext,$blurb,$wiki,$wiki_cache);
				}
				$this->article_model->UpdateRevisionFactBox($revision, $fact_heading, $fact_text);
				$xajax_response->addScriptCall('headlinesUpdates',$revision,date('H:i:s'));
			 } else {
				$xajax_response->addAlert('Invalid revision number, please try reloading the page.');
			 }
		} else {
			$xajax_response->addAlert('You do not have the permissions required to edit the details for this article!');
		}
		return $xajax_response;
	}

	function _addComment($comment_text)
	{
		$xajax_response = new xajaxResponse();
		if ($comment_text == '') {
			$xajax_response->addAlert('Please enter a comment to submit.');
			$xajax_response->addScriptCall('commentAdded','','','');
		} else {
			$new_comment = $this->article_model->InsertArticleComment($this->uri->segment(3), $this->user_auth->entityId, $comment_text);
			$xajax_response->addScriptCall('commentAdded',date('D jS F Y @ H:i',$new_comment['time']),$new_comment['name'],nl2br($comment_text));
		}
		return $xajax_response;
	}

}

?>