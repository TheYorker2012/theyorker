<?php

/// Office How Do I Pages.
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

		$this->load->helper('text');
		$this->load->helper('wikilink');
	}

	/// Set up the navigation bar
	private function _SetupNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
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
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_howdoi_questions');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('questions');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_questions', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function suggestions()
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_howdoi_suggestions');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('suggestions');
		
		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_suggestions', $data);

		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	

	function categories()
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_howdoi_categories');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('categories');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_categories', $data);

		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	

	function editquestion()
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_howdoi_edit_question');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('questions');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_question', $data);

		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

}
?>
