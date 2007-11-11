<?php
/**
 *	@brief		Management of bylines for Yorker Staff
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Ticker extends Controller
{

	// Variable declarations
	var $access;

	/// Default constructor
	function __construct()
	{
		parent::Controller();
		// Load models
		$this->load->model('facebookticker_model');

		// All functionality in this section requires office access or above
		if (!CheckPermissions('office')) return;
		// Retrieve access level
		$this->access = GetUserLevel();
		// Make it so that we only need to worry about two levels of access (admin == editor)
		if ($this->access == 'admin') $this->access = 'editor';
	}

	function index ()
	{
		$data = array();
		$data['settings'] = $this->facebookticker_model->GetTickerSettings();
		$this->load->library('facebook_ticker');
		$data['preview'] = $this->facebook_ticker->TickerHTML();

		// Get page properties information
		$this->pages_model->SetPageCode('office_facebook_index');
		$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
		$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

		// Load the page
		$this->main_frame->SetContentSimple('office/facebook/index', $data);
		$this->main_frame->Load();
	}

	function edit ($slot_id)
	{
		if ($this->access != 'editor') {
			$this->main_frame->AddMessage('error', 'Only editors may edit an article slot.');
			redirect('/office/ticker/');
		} elseif (!is_numeric($slot_id)) {
			$this->main_frame->AddMessage('error', 'You need to specify which article slot you want to edit.');
			redirect('/office/ticker/');
		} else {
			$data['slot_info'] = $this->facebookticker_model->GetArticleSlot($slot_id);
			if (count($data['slot_info']) == 0) {
				$this->main_frame->AddMessage('error', 'Unable to find the article slot you want to edit.');
				redirect('/office/ticker/');
			} else {
				// Get options for which articles to display
				//$this->load->model('news_model');
				//$data['latest_articles'] = $this->news_model->GetArchive('search', array(), 0, 25);
				$data['content_types'] = $this->facebookticker_model->GetAllArticleContentTypes();
				// Populate form inputs
				$data['form_content_type_id'] = $data['slot_info']['facebook_article_content_type_id'];

				// Check for POST
				if (isset($_POST['type'])) {
					if ((isset($_POST['cancel'])) && (!empty($_POST['cancel']))) {
						redirect('/office/ticker/');
					} elseif ((!is_numeric($_POST['type'])) && ($_POST['type'] != 'NULL')) {
						$this->main_frame->AddMessage('error', 'Unrecognised content type, please try again.');
					} elseif (!$this->facebookticker_model->UpdateArticleSlot($slot_id, $_POST['type'], NULL)) {
						$this->main_frame->AddMessage('error', 'There was an error updating the article slot, please try again.');
					} else {
						$this->main_frame->AddMessage('success', 'The article slot was successfully updated.');
						redirect('/office/ticker/edit/' . $slot_id . '/');
					}
					$data['form_content_type_id'] = $_POST['type'];
				}

				// Get page properties information
				$this->pages_model->SetPageCode('office_facebook_index');
				$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
				$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

				// Load the page
				$this->main_frame->SetContentSimple('office/facebook/edit', $data);
				$this->main_frame->Load();
			}
		}
	}

	function add ()
	{
		if ($this->access != 'editor') {
			$this->main_frame->AddMessage('error', 'Only editors may add a new article slot.');
		} else {
			if ($this->facebookticker_model->AddArticleSlot()) {
				$this->main_frame->AddMessage('success', 'A new article slot was successfully added.');
			} else {
				$this->main_frame->AddMessage('error', 'There was an error adding a new article slot, please try again.');
			}
		}
		redirect('/office/ticker/');
	}

	function delete ($slot_id = NULL)
	{
		if ($this->access != 'editor') {
			$this->main_frame->AddMessage('error', 'Only editors may delete an article slot.');
		} elseif (!is_numeric($slot_id)) {
			$this->main_frame->AddMessage('error', 'You need to specify which article slot you want to delete.');
		} else {
			if ($this->facebookticker_model->DeleteArticleSlot($slot_id)) {
				$this->main_frame->AddMessage('success', 'The article slot was successfully deleted.');
			} else {
				$this->main_frame->AddMessage('error', 'There was an error deleting the article slot, please try again.');
			}
		}
		redirect('/office/ticker/');
	}

	function update ()
	{
		if ($this->access != 'editor') {
			$this->main_frame->AddMessage('error', 'Only editors may manually update The Yorker\'s News Ticker Facebook Application.');
		} else {
			$this->load->library('facebook_ticker');
			if ($this->facebook_ticker->TickerUpdate()) {
				$this->main_frame->AddMessage('success', 'The Yorker\'s News Ticker Facebook Application was successfully updated.');
			} else {
				$this->main_frame->AddMessage('error', 'There was an error updating The Yorker\'s News Ticker Facebook Application, please try again.');
			}
		}
		redirect('/office/ticker/');
	}

}
?>