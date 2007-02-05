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
		
		SetupMainFrame('vip');
	}
	
	function index()
	{
		$this->pages_model->SetPageCode('viparea_index');
		
		// Ensure have permissions
		if (CheckPermissions('vip')) {
			$data = array(
					'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
					'organisation' => 'theyorker', //example for the moment change this to logged in organisation
			);
			// Set up the content
			$this->main_frame->SetContentSimple('viparea/main', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>