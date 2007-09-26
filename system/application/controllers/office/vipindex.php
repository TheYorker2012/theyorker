<?php

/// Main vip index controller.
class Vipindex extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
		if (!CheckPermissions('vip')) return;
		
		//load the required models and libraries
		$this->load->model('pr_model','pr_model');
		
		$organisation = VipOrganisation();
		if (empty($organisation)) {
			$organisation = VipOrganisation(TRUE);
			redirect('viparea/'.$organisation);
			return;
		}
		
		$this->pages_model->SetPageCode('viparea_index');
		
		//get the rep data from the pr model
		$rep_data = $this->pr_model->GetOrganisationRatings(VipOrganisation());
		if (isset($rep_data['rep'])) {
			$rep = $rep_data['rep'];
			$rep['email'] = $this->members_model->GetMemberEmail($rep['id']);
			$rep['has_rep'] = true;
		}
		else {
			$rep['name'] = $this->config->item('pr_officer_name');
			$rep['email'] = $this->config->item('pr_officer_email_address');
			$rep['has_rep'] = false;
		}
		
		$data = array(
				'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
				'organisation' => VipOrganisation(),
				'enable_members' => TRUE, //example for the moment change this to logged in organisation
				'rep' => $rep,
		);
		// Set up the content
		$this->main_frame->SetTitleParameters(
				array('organisation' => VipOrganisationName())
		);
		$this->main_frame->SetContentSimple('viparea/main', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>