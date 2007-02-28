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

	/**
	 * This function adds a new charity to the database.
	 */
	function addcharity()
	{
		if (!CheckPermissions('office')) return;

		/* Loads the category edit page
		   $_POST data passed
		   - a_charityname => the name of the new charity
    		   - r_submit_add => the name of the submit button
		*/
		if (isset($_POST['r_submit_add']))
		{
			if (trim($_POST['a_charityname']) != '')
			{
				//load the required models
				$this->load->model('charity_model','charity_model');
				$this->load->model('requests_model','requests_model');
	
				//create the charity and its article
				$id = $this->requests_model->CreateRequest('request', 'ourcharity', '', '', $this->user_auth->entityId, time());
				$this->charity_model->CreateCharity($_POST['a_charityname'], $id);
	
				//return to form submit page and pass success message
				$this->main_frame->AddMessage('success','Charity added.');
				redirect($_POST['r_redirecturl']);
			}
			else
			{
				//return to form submit page and pass error message
				$this->main_frame->AddMessage('error','Must enter a name for the new charity.');
				redirect($_POST['r_redirecturl']);
			}
		}

	}

	function modify($charity_id)
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_charities');
		$this->load->model('charity_model','charity_model');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('charities');

		//get charity from given id
		$data['charity'] = $this->charity_model->GetCharity($charity_id);
		$data['charity']['id'] = $charity_id;

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

	function edit($charity_id)
	{
		if (!CheckPermissions('office')) return;

		//set the page code and load the required models
		$this->pages_model->SetPageCode('office_charity_charities');
		$this->load->model('charity_model','charity_model');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('charities');

		//get charity from given id
		$data['charity'] = $this->charity_model->GetCharity($charity_id);
		$data['charity']['id'] = $charity_id;

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