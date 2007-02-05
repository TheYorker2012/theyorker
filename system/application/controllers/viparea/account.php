<?php

/// Main viparea controller.
class Account extends Controller
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
	
	function update($organisation)
	{
		$this->pages_model->SetPageCode('viparea_account');
		
		// Ensure have permissions
		if (CheckPermissions('vip')) {
			$data = $this->organisations->_GetOrgData($organisation);
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			
			// Set up the content
			$this->main_frame->SetContentSimple('viparea/account', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>