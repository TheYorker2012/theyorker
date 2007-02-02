<?php

/// Main viparea controller.
class Index extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		$this->load->model('pages_model');
	}
	
	function index()
	{
		$this->pages_model->SetPageCode('office_index');
		
		// Load the main frame
		if (SetupMainFrame('office')) {
			$data = array(
					'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			);
			// Set up the content
			$this->main_frame->SetContentSimple('office/index', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>