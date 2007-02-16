<?php

/// Main viparea controller.
class Organisationstub extends Controller
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
	
	function add()
	{
		$this->pages_model->SetPageCode('organisation_stub_add');
		
		// Ensure have permissions
		if (CheckPermissions('office')) {
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['new_mode'] = true;
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
			
			// Set up the content
			$this->main_frame->SetContentSimple('office/directory/organisationstub', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}

	
	function edit($organisation)
	{
		$this->pages_model->SetPageCode('organisation_stub_edit');
		
		// Ensure have permissions
		if (CheckPermissions('office')) {
			$data = $this->organisations->_GetOrgData($organisation);
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			$data['new_mode'] = false;
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
			
			// Set up the content
			$this->main_frame->SetContentSimple('office/directory/organisationstub', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>