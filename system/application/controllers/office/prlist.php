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
class Prlist extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->library('organisations');
		$this->load->helpers('images');
		$this->load->model('directory_model');
		$this->load->model('businesscards_model');

		$this->load->helper('text');
		$this->load->helper('images');
		$this->load->helper('wikilink');
	}

	/// Set up the navigation bar for the directory view.
	private function _SetupDirectoryNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('live', 'Live',
						'/office/prlist	');
		$navbar->AddItem('hidden', 'Hidden',
						'/office/prlist/hidden');
		$navbar->AddItem('suggested', 'Suggested',
						'/office/prlist/suggested');
	}

	function hidden() {
		$this->_showlist('hidden');
	}

	function suggested() {
		$this->_showlist('suggested');
	}

	function index()
	{
		$this->_showlist();
	}

	/**
	 * @note Shows error 404 when accessed from viparea
	 */
	private function _showlist($showmode = 'live')
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_directory_index');

		$this->_SetupDirectoryNavbar();
		$this->main_frame->SetPage($showmode);

		$data = array();

		$data['maintext'] = $this->pages_model->GetPropertyText('maintext');

		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well

		$linkurlpre = 'office/reviews/';
		$linkurlpost = '';
		if ($showmode=='suggested') {
			$linkurlpre = 'office/pr/org/';
			$linkurlpost = '/directory/information';
		}

		$data['organisations'] = $this->organisations->_GetOrgs($search_pattern, $linkurlpre, $linkurlpost, $showmode);
		$data['search'] = $search_pattern;

		// Get organisation types
		$data['organisation_types'] = $this->organisations->_GetOrganisationTypes($data['organisations'], TRUE);

		//Libary for AtoZ system
		$this->load->library('character_lib'); //This character libary is used by the view, so load it here

		// Set up the directory view
		$directory_view = $this->frames->view('directory/viparea_directory', $data);

		// Set up the public frame to use the directory view
		$this->main_frame->SetContent($directory_view);

		// Include the javascript
		$this->main_frame->SetExtraHead('<script src="/javascript/directory.js" type="text/javascript"></script>');
		$this->main_frame->SetExtraCss('/stylesheets/directory.css');

		// Load the public frame view
		$this->main_frame->Load();
	}
}