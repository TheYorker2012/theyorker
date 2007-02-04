<?php

/// Main office controller.
class Index extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		SetupMainFrame('office');
	}
	
	function index()
	{
		$this->pages_model->SetPageCode('office_index');
		
		// Load the main frame
		if (CheckPermissions(array('student','office'))) {
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