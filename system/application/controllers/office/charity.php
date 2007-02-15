<?php

/// Office Charity Pages.
/**
 * @author Richard Ingle (ri504@cs.york.ac.uk)
 */
class Charity extends Controller
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
		$navbar->AddItem('current', 'Current',
				'/office/charity/current');
		$navbar->AddItem('charities', 'Charities',
				'/office/charity/');
	}

	function index()
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_charities');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('charities');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the view
		$the_view = $this->frames->view('office/charity/office_charity_list', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function modify()
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_charities');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('charities');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the view
		$the_view = $this->frames->view('office/charity/office_charity_modify', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function edit()
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_charities');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('charities');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the view
		$the_view = $this->frames->view('office/charity/office_charity_edit', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
}