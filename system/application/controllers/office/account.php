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
		$this->load->model('orgaccount_model');
		$this->load->library('organisations');
		$this->load->helper('wikilink');
	}
	
	private function _GetMaintainer ($organisation) {
		//Get data from model
		$data = $this->orgaccount_model->GetDirectoryOrganisationMaintainer($organisation);
		
		foreach($data as $row){
			//If maintainer_user_entity_id is empty the maintainer is not a student the varible student is used so the view knows which varibles from the controler to show.
			if($row['organisation_maintainer_user_entity_id'] == null){
			$student = false;
			}else{
			$student = true;
			}
			if($row['organisation_maintainer_user_entity_id'] == null and $row['organisation_maintainer_name'] == null){
			$maintained = false;
			}else{
			$maintained = true;
			}
			// Construct array of information
			$maintainer = array(
								'entity_id' => $row['organisation_entity_id'],
								'maintainer_email' => $row['organisation_maintainer_email'],
								'maintainer_user_entity_id' => $row['organisation_maintainer_user_entity_id'],
								'maintainer_name' => $row['organisation_maintainer_name'],
								'maintainer_firstname' => $row['user_firstname'],
								'maintainer_surname' => $row['user_surname'],
								'student' => $student,
								'maintained' => $maintained,
								);
		}
		return $maintainer;
	}
	function update()
	{
		if (!CheckPermissions('vip+office')) return;
		
		$organisation = VipOrganisation();
		
		$this->pages_model->SetPageCode('viparea_account');
		
		$data = $this->organisations->_GetOrgData($organisation);
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
		$data['account_maintenance_text'] = $this->pages_model->GetPropertyWikitext('account_maintenance');
		$data['account_password_text'] = $this->pages_model->GetPropertyWikitext('account_password');
		$data['account_username_text'] = $this->pages_model->GetPropertyWikitext('account_username');
		$data['maintainer'] = $this->_GetMaintainer($organisation);
		
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/account', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function maintainer()
	{
		if (!CheckPermissions('vip+office')) return;
		
		$organisation = VipOrganisation();
		
		$this->pages_model->SetPageCode('viparea_account_maintainer');
		
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
		$data['account_maintenance_text'] = $this->pages_model->GetPropertyWikitext('account_maintenance');
		$data['maintainer'] = $this->_GetMaintainer($organisation);
		$data['user_fullname'] = $this->user_auth->firstname." ".$this->user_auth->surname;
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/account_maintainer', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>