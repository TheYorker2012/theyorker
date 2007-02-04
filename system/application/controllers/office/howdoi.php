<?php

/// Yorker directory.
/**
 * @author Nick Evans (nse500@cs.york.ac.uk)
 * @author Richard Ingle (ri504@cs.york.ac.uk)
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

	/// Set up the navigation bar
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

	/// index page.
	function index()
	{
		$this->pages_model->SetPageCode('office_howdoi_questions');
		
		// Check permissions
		if (CheckPermissions('office')) {
			//Get toolbar
			$this->_SetupNavbar();
	
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('office/howdoi/office_howdoi_questions', $data);
			
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

		// Check permissions
		if (CheckPermissions('office')) {

			//Get toolbar
			$this->_SetupNavbar();
	
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('office/howdoi/office_howdoi_suggestions', $data);

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

		// Check permissions and load main frame
		if (CheckPermissions('office')) {
		
			//Get toolbar
			$this->_SetupNavbar();
	
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('office/howdoi/office_howdoi_categories', $data);

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

		// Check permissions
		if (CheckPermissions('office')) {
		
			//Get toolbar
			$this->_SetupNavbar();
	
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_question', $data);

			// Set up the public frame
			$this->frame_public->SetContent($the_view);
		}

		// Load the public frame view
		$this->frame_public->Load();
	}

}
?>
