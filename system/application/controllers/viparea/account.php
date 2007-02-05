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
			$data['account_maintenance'] = $this->pages_model->GetPropertyWikitext('account_maintenance');
			$data['categories'] = array (
				array(
					'id' => '1',
					'name' => 'Organisations',
				),
				array(
					'id' => '2',
					'name' => 'College & Campus',
				),
			);
			$data['maintainer'] = array (
				'name' => 'John Smith',
				'email' => 'static@controler.com',
				'student' => 'yes',
			);
			
			// Set up the content
			$this->main_frame->SetContentSimple('viparea/account', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>