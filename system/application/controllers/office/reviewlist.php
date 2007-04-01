<?php

/// Yorker directory.
/**
 * @author Owen Jones (oj502@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The URI /directory maps to this controller (see config/routes.php).
 *
 * Any 2nd URI segment is sent to Yorkerdirectory::view (see config/routes.php).
 *
 * Any 3rd URI segment (e.g. events) is sent to the function with the same value.
 *	(see config/routes.php).
 */
class Reviewlist extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->library('organisations');

		$this->load->model('directory_model');
		$this->load->model('pr_model');
		$this->load->model('prefs_model');

		$this->load->helper('text');
		$this->load->helper('images');
		$this->load->helper('wikilink');
	}

	function _remap($method)
	{
		$content_type = $this->pr_model->GetContentTypeId($method);
	
		if ($content_type > 0) {
			$this->index($method, $content_type);
		} else {
			$this->$method();
		}
	}
	
	/// Review list index page.
	/**
	 * @note POST data:
	 *	- 'search' (search pattern, optional)
	 */
	function index($content_type_codename, $content_type_id)
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('review_list_index');

		//$this->main_frame->SetPage('list'); //??
		
		$data = array();

		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well
		$data['organisations'] = $this->pr_model->GetReviewContextListFromId($content_type_id,'office/reviews/','/'.$content_type_codename.'/review'); //$this->organisations->_GetOrgs($search_pattern);
		$data['search'] = $search_pattern;
		
		// Set up the directory view
		$directory_view = $this->frames->view('office/reviews/reviewlist', $data);

		// Set up the public frame to use the directory view
		$this->main_frame->SetContent($directory_view);

		// Include the javascript
		$this->main_frame->SetExtraHead('<script src="/javascript/directory.js" type="text/javascript"></script>');

		$this->main_frame->SetExtraCss('/stylesheets/directory.css');

		// Load the public frame view
		$this->main_frame->Load();
	}

}
?>
