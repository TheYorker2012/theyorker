<?php

/// Review Leagues List
/**
 * @author Owen Jones
 *
 */
class Reviewlist extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->model('directory_model');
		$this->load->model('pr_model');
		$this->load->model('review_model');
		$this->load->model('prefs_model');

		$this->load->helper('text');
		$this->load->library('image');
		$this->load->helper('wikilink');
	}

	/// Review list index page.
	/**
	 * @note POST data:
	 *	- 'search' (search pattern, optional)
	 */
	function attentionlist($content_type_codename) {
		if (!CheckPermissions('office')) return;
		
		$content_type_id = $this->pr_model->GetContentTypeId($content_type_codename);
		$content_type_name = $this->pr_model->GetContentTypeNiceName($content_type_codename);
		if ($content_type_id == 0) {
			//show_404();
		}
		
		$data = array();
		
		$this->pages_model->SetPageCode('office_review_attention_list');
		$this->main_frame->SetTitleParameters(array(
			'content_type' => $content_type_name
		));
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');

		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well
		$data['organisations'] = $this->pr_model->GetReviewContextListFromId($content_type_id,'office/reviews/','/'.$content_type_codename.'/review');
		$data['content_type_codename'] = $content_type_codename;
		$data['search'] = $search_pattern;

		// Set up the public frame to use the directory view
		$this->main_frame->SetContentSimple('office/reviews/reviewlist', $data);

		// Include the javascript
		$this->main_frame->SetExtraHead('<script src="/javascript/directory.js" type="text/javascript"></script>');

		$this->main_frame->SetExtraCss('/stylesheets/directory.css');

		// Load the public frame view
		$this->main_frame->Load();
	}

}
?>
