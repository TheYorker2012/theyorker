<?php

/**
 * @file prindex.php
 * @brief Main PR page for an organisation.
 */

/// Main PR area for an organisation controller.
/**
 */
class Prindex extends controller
{
	/// Default constructor
	function __construct()
	{
		parent::controller();
	}

	/// Set up the navigation bar
	private function _SetupNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('summary', 'Summary',
				'/office/pr/summary');
		$navbar->AddItem('unnassigned', 'Unnassigned',
				'/office/pr/unnassigned');
		$navbar->AddItem('suggestions', 'Suggestions',
				'/office/pr/suggestions');
	}
	
	/// Index page (accessed through /office/pr/org/$organisation)
	function orgindex()
	{
		if (!CheckPermissions('pr')) return;
		$this->pages_model->SetPageCode('office_pr_main');
		$this->main_frame->SetTitleParameters(array(
			'organisation' => VipOrganisationName()
		));
		$this->main_frame->load();
	}

	function summary($type = NULL, $id = NULL)
	{
		// Not accessed through /office/pr/org/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;

		$this->_SetupNavbar();
		$this->main_frame->SetPage('summary');
		
		if ($type == NULL)
		{
			self::_SummaryOfficer();
		}
		else if ($type == 'rep')
		{
			self::_SummaryRep($id);
		}
		else if ($type == 'org')
		{
			self::_SummaryOrganisation($id);
		}
	}
	
	function _SummaryOfficer()
	{
		//navbar and page codes
		$this->main_frame->SetPage('summary');
		$this->pages_model->SetPageCode('office_pr_summary_officer');

		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['type'] = 'off';
		$data['parameters']['name'] = NULL;

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		// Set up the public frame
		$the_view = $this->frames->view('office/pr/summary_officer', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}
	
	function _SummaryRep($id)
	{
		//navbar and page codes
		$this->main_frame->SetPage('summary');
		$this->pages_model->SetPageCode('office_pr_summary_rep');
		
		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['type'] = 'rep';
		$data['parameters']['name'] = $id;

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
	
		// Set up the public frame
		$the_view = $this->frames->view('office/pr/summary_rep', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}
	
	function _SummaryOrganisation($id)
	{
		//navbar and page codes
		$this->main_frame->SetPage('summary');
		$this->pages_model->SetPageCode('office_pr_summary_org');
		
		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['type'] = 'rep';
		$data['parameters']['name'] = $id;
	
		// Set up the public frame
		$the_view = $this->frames->view('office/pr/summary_org', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}

	function unnassigned()
	{
		// Not accessed through /office/pr/org/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;
		
		//load the required models
		$this->load->model('pr_model','pr_model');
		$this->load->model('writers_model','writers_model');

		//setup navbar and set page code
		$this->_SetupNavbar();
		$this->main_frame->SetPage('unnassigned');
		$this->pages_model->SetPageCode('office_pr_unnassigned');
		
		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;
		
		//get all currently unassigned organisations
		$data['unassigned_orgs'] = $this->pr_model->GetUnassignedOrganisations();
		
		//get all currently pending organisations
		$data['pending_orgs'] = $this->pr_model->GetPendingOrganisations();		
		
		//get all reps who have asked to be rep for the unassigned organisations
		$data['reps'] = $this->pr_model->GetUnassignedOrganisationsReps();
		
		//get the list of office users
		$data['office_users'] = $this->writers_model->GetUsersWithOfficeAccess();

		// Set up the public frame
		$the_view = $this->frames->view('office/pr/unnassigned', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}

	function suggestions()
	{
		// Not accessed through /office/pr/org/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;
		
		//load the required models
		$this->load->model('pr_model','pr_model');
		$this->load->model('writers_model','writers_model');

		//setup navbar and set page code
		$this->_SetupNavbar();
		$this->main_frame->SetPage('suggestions');
		$this->pages_model->SetPageCode('office_pr_suggestions');

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		//get all currently suggested organisations
		$data['orgs'] = $this->pr_model->GetSuggestedOrganisations();
		
		//get the list of office users
		$data['office_users'] = $this->writers_model->GetUsersWithOfficeAccess();

		// Set up the public frame
		$the_view = $this->frames->view('office/pr/suggestions', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}

	function info($shortname)
	{
		// Not accessed through /office/pr/org/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;
		
		//load the required models/libraries/helpers
		$this->load->model('pr_model','pr_model');
		$this->load->model('writers_model','writers_model');
		$this->load->library('organisations');
		$this->load->model('directory_model');
		$this->load->helper('wikilink');

		//setup navbar and set page code
		$this->_SetupNavbar();
		$this->main_frame->SetPage('suggestions');
		$this->pages_model->SetPageCode('office_pr_suggestion');
		
		/** store the parameters passed to the method so it can be
		    used for links in the view */
		$data['parameters']['dir_name'] = $shortname;
		
		//get the organisation specified
		$data = $this->organisations->_GetOrgData($shortname);
		$data['status'] = $this->pr_model->GetOrganisationStatus($shortname);
		
		//if the organisation is unassigned, get the list of reps who have requested it
		if ($data['status'] == 'unassigned')
			$data['reps'] = $this->pr_model->GetOrganisationReps($shortname);
		
		//if the organisation is pending, get the rep who has been asked to look after it
		if ($data['status'] == 'pending')
			$data['rep'] = $this->pr_model->GetPendingOrganisationRep($shortname);
		
		//get the list of office users
		$data['office_users'] = $this->writers_model->GetUsersWithOfficeAccess();

		//get the current users id and office access
		$data['user']['id'] = $this->user_auth->entityId;
		$data['user']['officetype'] = $this->user_auth->officeType;

		// Set up the public frame
		$the_view = $this->frames->view('office/pr/info', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}
	
	function modify()
	{
		if (!CheckPermissions('office')) return;
		
		//load the required models
		$this->load->model('pr_model','pr_model');
		
		//reject the suggested organisation
		if (isset($_POST['r_submit_reject']))
		{
			$this->pr_model->SetOrganisationDeleted($_POST['r_direntryname']);
			$this->main_frame->AddMessage('success','Suggested organisation has been rejected.');
			redirect('/office/pr/suggestions');
		}
		//accepts a suggestion to the unassigned pool
		if (isset($_POST['r_submit_accept_unnassigned']))
		{
			$this->pr_model->SetOrganisationUnassigned($_POST['r_direntryname']);
			$this->main_frame->AddMessage('success','Organisation has been moved into the unassigned pool.');
			redirect($_POST['r_redirecturl']);
		}
		//accepts a suggestion to pending, requesting a rep to look after it
		if (isset($_POST['r_submit_accept_assign']))
		{
			$this->pr_model->SetOrganisationPending($_POST['r_direntryname'], $_POST['a_assign_to']);
			$this->main_frame->AddMessage('success','Organisation has been accepted and a rep has been requested to look after it.');
			redirect($_POST['r_redirecturl']);
		}
		//delete an organisation
		if (isset($_POST['r_submit_delete']))
		{
			$this->pr_model->SetOrganisationDeleted($_POST['r_direntryname']);
			$this->main_frame->AddMessage('success','Organisation has deleted.');
			redirect('/office/pr/unnassigned');
		}
		//requests a rep be responsible for the given organisation
		if (isset($_POST['r_submit_officer_request_rep']))
		{
			$this->pr_model->SetOrganisationPending($_POST['r_direntryname'], $_POST['a_assign_to']);
			$this->main_frame->AddMessage('success','A rep has been requested to look after the organisation.');
			redirect($_POST['r_redirecturl']);
		}
		//accept the request of a rep to be the rep for the given organisation
		if (isset($_POST['r_submit_accept_rep']))
		{
			$this->pr_model->SetOrganisationPending($_POST['r_direntryname'], $_POST['r_userid']);
			$this->pr_model->SetOrganisationAssigned($_POST['r_direntryname'], $_POST['r_userid']);
			$this->main_frame->AddMessage('success','The rep has been assgined to this organisation.');
			redirect($_POST['r_redirecturl']);
		}
		//reject the request of a rep to be the rep for the given organisation
		if (isset($_POST['r_submit_reject_rep']))
		{
			$this->pr_model->WithdrawRepFromUnassignedOrganisation($_POST['r_direntryname'], $_POST['r_userid']);
			$this->main_frame->AddMessage('success','The rep\'s request has been rejected.');
			redirect($_POST['r_redirecturl']);
		}
		//a rep requesting to be rep for the given organisation
		if (isset($_POST['r_submit_request_rep']))
		{
			$this->pr_model->RequestRepToUnassignedOrganisation($_POST['r_direntryname'], $this->user_auth->entityId);
			$this->main_frame->AddMessage('success','You have requested to be rep for this organisation.');
			redirect($_POST['r_redirecturl']);
		}
		//a rep withdrawing their request to be rep for the given organisation
		if (isset($_POST['r_submit_withdraw_rep']))
		{
			$this->pr_model->WithdrawRepFromUnassignedOrganisation($_POST['r_direntryname'], $this->user_auth->entityId);
			$this->main_frame->AddMessage('success','You have withdrawn your request to be rep for this organisation.');
			redirect($_POST['r_redirecturl']);
		}
		//an editor withdrawing their request for a rep to look after this organisation
		if (isset($_POST['r_submit_withdraw_request']))
		{
			$this->pr_model->WithdrawRepFromPendingOrganisation($_POST['r_direntryname'], $_POST['r_userid']);
			$this->main_frame->AddMessage('success','You have withdrawn your request for the rep to look after this organisation.');
			redirect($_POST['r_redirecturl']);
		}
		//rep accepts the request from the editor to look after the specified organisation
		if (isset($_POST['r_submit_accept_request']))
		{
			$this->pr_model->SetOrganisationAssigned($_POST['r_direntryname'], $_POST['r_userid']);
			$this->main_frame->AddMessage('success','You have been assigned to this organisation.');
			redirect($_POST['r_redirecturl']);
		}
		//rep rejects the request from the editor to look after the specified organisation
		if (isset($_POST['r_submit_reject_request']))
		{
			$this->pr_model->WithdrawRepFromPendingOrganisation($_POST['r_direntryname'], $_POST['r_userid']);
			$this->main_frame->AddMessage('success','You have rejected the editors request to look after this organisation.');
			redirect($_POST['r_redirecturl']);
		}
	}
}

?>