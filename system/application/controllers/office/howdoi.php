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
class Howdoi extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		// Make use of the public frame
		$this->load->library('frame_public');

		$this->load->helper('text');
		$this->load->helper('wikilink');
	}

	/// Set up the directory frame
	/**
	 * @param $OrganisationData Organisation data array.
	 * @pre @a $OrganisationData is valid organisation array.
	 * @post Frame_directory frame is loaded and ready to use.
	 */
	private function _SetupNavbar()
	{
		$navbar = $this->frame_public->GetNavbar();
		$navbar->AddItem('suggestions', 'Suggestions',
				'/office/howdoi/suggestions');
		$navbar->AddItem('categories', 'Categories',
				'/office/howdoi/categories');
		$navbar->AddItem('questions', 'Questions',
				'/office/howdoi');
	}

	/// Directory index page.
	// this is blank lol
	function index()
	{
		$this->pages_model->SetPageCode('office_howdoi_questions');

		//Get Data And toolbar
		$this->_SetupNavbar();

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the directory view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_questions', $data);

		// Load the main frame
		if (CheckPermissions(array('student','office'))) {
			// Set up the public frame
			$this->frame_public->SetContent($the_view);
		}

		// Load the public frame view
		$this->frame_public->Load();
	}
	/// Directory organisation page.
	function suggestions()
	{
		$this->pages_model->SetPageCode('office_howdoi_suggestions');

		//Get Data And toolbar
		$this->_SetupNavbar();

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the directory view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_suggestions', $data);

		// Load the main frame
		if (CheckPermissions(array('student','office'))) {
			// Set up the public frame
			$this->frame_public->SetContent($the_view);
		}

		// Load the public frame view
		$this->frame_public->Load();
	}
	
	/// Directory organisation page.
	function categories()
	{
			$this->pages_model->SetPageCode('office_howdoi_categories');

		//Get Data And toolbar
		$this->_SetupNavbar();

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the directory view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_categories', $data);

		// Load the main frame
		if (CheckPermissions(array('student','office'))) {
			// Set up the public frame
			$this->frame_public->SetContent($the_view);
		}

		// Load the public frame view
		$this->frame_public->Load();
	}
	
	/// Directory organisation page.
	function editquestion()
	{
			$this->pages_model->SetPageCode('office_howdoi_edit_question');

		//Get Data And toolbar
		$this->_SetupNavbar();

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the directory view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_question', $data);

		// Load the main frame
		if (CheckPermissions(array('student','office'))) {
			// Set up the public frame
			$this->frame_public->SetContent($the_view);
		}

		// Load the public frame view
		$this->frame_public->Load();
	}

}
?>
