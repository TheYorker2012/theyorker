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
			if($row['organisation_maintainer_user_entity_id'] == $this->user_auth->entityId){
			$is_user = true;
			}else{
			$is_user = false;
			}
			// Construct array of information
			$maintainer = array(
								'entity_id' => $row['organisation_entity_id'],
								'maintainer_email' => $row['organisation_maintainer_email'],
								'maintainer_user_entity_id' => $row['organisation_maintainer_user_entity_id'],
								'maintainer_name' => $row['organisation_maintainer_name'],
								'maintainer_firstname' => $row['user_firstname'],
								'maintainer_surname' => $row['user_surname'],
								'maintainer_student_email' => $row['user_email'],
								'student' => $student,
								'is_user' => $is_user,
								'maintained' => $maintained,
								);
		}
		return $maintainer;
	}
	function update()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$organisation = VipOrganisation();
		$this->pages_model->SetPageCode('viparea_account');
		
		//Do password checks before updating
		if(!empty($_POST["password_button"])){
			if($_POST["password_new1"]==$_POST["password_new2"]){
				if($this->user_auth->checkPassword($_POST["password_old"])){
					if(strlen($_POST["password_new1"]) >= 5){
						$this->user_auth->setPassword($_POST["password_new1"]);
						$this->main_frame->AddMessage('success','You have successfully changed your VipArea password.');
					}else{
					//password must be at least five characters long!
					$this->main_frame->AddMessage('error','The new password must be at least five characters long.');
					}
				}else{
				//not same password error
				$this->main_frame->AddMessage('error','The original password you entered was not correct.');
				}
			}else{
			//different new passwords error
			$this->main_frame->AddMessage('error','The new password you entered was not the same in both fields.');
			}
		}
		
		
		$data = $this->organisations->_GetOrgData($organisation);
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
		$data['account_maintenance_text'] = $this->pages_model->GetPropertyWikitext('account_maintenance');
		$data['account_password_text'] = $this->pages_model->GetPropertyWikitext('account_password');
		$data['account_username_text'] = $this->pages_model->GetPropertyWikitext('account_username');
		$data['maintainer'] = $this->_GetMaintainer($organisation);
		$data['is_student'] = $this->user_auth->isUser;
		
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/account', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function maintainer()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$organisation = VipOrganisation();
		$this->pages_model->SetPageCode('viparea_account_maintainer');
		
		//Send update if information is given
		if(!empty($_POST['maintainer_button'])){
			switch ($_POST['maintainer_type'])
			{
			case 'yorker':
				$Data = array(
							'maintainer_email' => null,
							'maintainer_user_entity_id' => null,
							'maintainer_name' => null
							);
				$this->orgaccount_model->UpdateDirectoryOrganisationMaintainer($organisation, $Data);
				$this->main_frame->AddMessage('success','Maintainer information updated.');
			break;  
			case 'student':
				$Data = array(
							'maintainer_email' => null,
							'maintainer_user_entity_id' => $this->user_auth->entityId,
							'maintainer_name' => null
							);
				$this->orgaccount_model->UpdateDirectoryOrganisationMaintainer($organisation, $Data);
				$this->main_frame->AddMessage('success','Maintainer information updated.');
			break;
			case 'nonstudent':
				if (!empty($_POST['maintainer_name']) and !empty($_POST['maintainer_email'])){
				$Data = array(
							'maintainer_email' => $_POST['maintainer_email'],
							'maintainer_user_entity_id' => null,
							'maintainer_name' => $_POST['maintainer_name']
							);
				$this->orgaccount_model->UpdateDirectoryOrganisationMaintainer($organisation, $Data);
				$this->main_frame->AddMessage('success','Maintainer information updated.');
				}else{
					$this->main_frame->AddMessage('error','Maintainer not updated, the name or email was left blank.');
				}
			break;
			default:
				$this->main_frame->AddMessage('error','Maintainer not updated, invalid form option submitted.');
			}
		}
		
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
		$data['account_maintenance_text'] = $this->pages_model->GetPropertyWikitext('account_maintenance');
		$data['maintainer'] = $this->_GetMaintainer($organisation);
		$data['user_fullname'] = $this->user_auth->firstname." ".$this->user_auth->surname;
		$data['is_student'] = $this->user_auth->isUser;
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/account_maintainer', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>