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
		if (!CheckPermissions('vip')) return;
		
		$this->pages_model->SetPageCode('viparea_members');		
		
		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			'organisation' => $this->members_model->GetMemberDetails($this->user_auth->organisationLogin),
			'team' => $this->members_model->GetTeams($this->user_auth->organisationLogin)
				/*		array(
						array(
							'id' => '1',
							'name' => 'Team 1',
						),
						array(
							'id' => '2',
							'name' => 'Team 2',
						),
						array(
							'id' => '3',
							'name' => 'Team 3',
						),
					),
			'members' => array(
							array(
								'id' => '1',
								'forename' => 'Example',
								'sirname' => 'Example',
								'email' => 'oj502@york.ac.uk',
								'paid' => 'Y',
								'mailing' => 'Y',
								'awaiting_reply' => 'Y',
								'vip' => 'N',
							),
							array(
								'id' => '2',
								'forename' => 'Example',
								'sirname' => 'Example',
								'email' => 'oj502@york.ac.uk',
								'paid' => 'Y',
								'mailing' => 'Y',
								'awaiting_reply' => 'Y',
								'vip' => 'N',
							),
						),*/
		);
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/members', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>