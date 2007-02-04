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
			);
			// Set up the content
			$this->main_frame->SetContentSimple('viparea/index', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>