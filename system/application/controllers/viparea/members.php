<?php

/// Main viparea controller.
class Members extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		$this->load->model('directory_model');
		$this->load->library('organisations');
		$this->load->helper('wikilink');
		SetupMainFrame('vip');
	}
	
	function view($organisation)
	{
		$this->pages_model->SetPageCode('viparea_members');
		
		// Ensure have permissions
		if (CheckPermissions('vip')) {
			$data = array(
				'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			);
			// Set up the content
			$this->main_frame->SetContentSimple('viparea/members', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>