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
		$navbar->AddItem('preports', 'Progress Reports',
				'/office/charity/');
		$navbar->AddItem('charities', 'Charities',
				'/office/charity/');
		$navbar->AddItem('about', 'About',
				'/office/charity/');
	}

	function index()
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_charities');
		$this->load->model('charity_model','charity_model');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('charities');

		// Setup XAJAX functions
		$this->load->library('xajax');
	        $this->xajax->registerFunction(array('_addCharity', &$this, '_addCharity'));
	        $this->xajax->processRequests();
		
		//get list of the charities
		$data['charities'] = $this->charity_model->GetCharities();

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the view
		$the_view = $this->frames->view('office/charity/office_charity_list', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));

		// Load the public frame view
		$this->main_frame->Load();
	}

	function _addCharity($name)
	{
		$this->load->model('charity_model','charity_model');
		//$id = $this->charity_model->CreateCharity($name);
		$id = 2;
		$xajax_response = new xajaxResponse();
		$xajax_response->addScriptCall('charityAdded',
						$name,
						$id);
		return $xajax_response;
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

	function progressreports()
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
		$the_view = $this->frames->view('office/charity/office_charity_progress_report', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
}