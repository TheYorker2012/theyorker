<?php
class Pingu extends Controller {

	function __construct()
	{
		parent::controller();
		
		// Load the public frame
	}

	function index()
	{
		if (!CheckPermissions('public')) return;

		$this->load->model('pingu_model','pingu_model');
		$this->pages_model->SetPageCode('campaign_selection');
		$data = array();

		// Set up the public frame
		$this->frame_public->SetTitle('test page');
		$this->frame_public->SetContentSimple('test/pingutest', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function campaign()
	{
		if (!CheckPermissions('public')) return;

		$this->load->model('pingu_model','pingu_model');
		$this->pages_model->SetPageCode('officecampaign');
		$data = array();

		// Set up the public frame
		$this->frame_public->SetTitle('Campaign Office Mockup');
		$this->frame_public->SetContentSimple('test/pingutest2', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function foodsearch()
	{
		if (!CheckPermissions('public')) return;

		$this->load->model('pingu_model','pingu_model');
		$this->pages_model->SetPageCode('foodsearch');
		$data = array();

		// Set up the public frame
		$this->frame_public->SetTitle('Food Search Mockup');
		$this->frame_public->SetContentSimple('test/pingutest3', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>