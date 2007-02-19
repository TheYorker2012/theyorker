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
		$this->load->model('members_model');
		$this->load->library('organisations');
		$this->load->helper('wikilink');
	}
	
	function view()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$this->pages_model->SetPageCode('viparea_members');		
		
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'user'         => $this->user_auth->entityId,
			'organisation' => $this->members_model->GetAllMemberDetails($this->user_auth->organisationLogin),
			'team'         => $this->members_model->GetTeams($this->user_auth->organisationLogin)
		);
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/members', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function edit($member_id)
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$this->pages_model->SetPageCode('viparea_members');	
			
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'organisation' => $this->members_model->GetAllMemberDetails($this->user_auth->organisationLogin),
			'member'       => $this->members_model->GetMemberDetails($member_id)

		);
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/editmembers', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function add()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$this->pages_model->SetPageCode('viparea_members');	
			
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'organisation' => $this->members_model->GetAllMemberDetails($this->user_auth->organisationLogin)
		);
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/addmembers', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}	
	
}

?>