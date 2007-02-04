<?php

/// Main viparea controller.
class Account extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct($organisation='theyorker')
	{
		parent::Controller();
		
		SetupMainFrame('vip');
	}
	
	function index()
	{
		$this->pages_model->SetPageCode('viparea_account');
		
		// Ensure have permissions
		if (CheckPermissions('vip')) {
			$data = array(
					'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
					'organisation' => $organisation,
			);
			// Set up the content
			$this->main_frame->SetContentSimple('viparea/account', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>