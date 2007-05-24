<?php
/*
 * Controller for links office pages
 * \author Nick Evans nse500
 */

class Links extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
		$this->load->model('Links_Model');
	}

	/// Default page.
	function index()
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('link_list');

		$data = array();

		$data['officiallinks'] = $this->Links_Model->GetAllOfficialLinks()->result_array();
		$data['nominatedlinks'] = $this->Links_Model->GetAllNominatedLinks()->result_array();

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/links/link_list', $data);

		$this->main_frame->Load();
	}

	//Edit link page
	function edit($link_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/links');
		}

		$data['link'] = $this->Links_Model->GetLink($link_id);

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/links/link_edit', $data);

		$this->main_frame->Load();
	}

	//Promote link
	function promote($link_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/links');
		}

		$this->load->model('Links_Model');
		$this->Links_Model->PromoteLink($this->user_auth->entityId, $link_id);
		$this->messages->AddMessage('success', 'Link promoted successfully');

		redirect('/office/links');
	}

	//Reject link
	function reject($link_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/links');
		}

		$this->load->model('Links_Model');
		$this->Links_Model->RejectLink($this->user_auth->entityId, $link_id);
		$this->messages->AddMessage('success', 'Link rejected successfully');

		redirect('/office/links');
	}

	/**
	 *	@brief	Allows setting of links and other homepage related settings
	 */
	function customlink()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		$this->load->model('Links_Model');

		if ($this->input->post('lurl') && $this->input->post('lname') && $this->input->post('lname') != 'http://') {
			$id = $this->Links_Model->AddLink($this->input->post('lname'), $this->input->post('lurl'), 1);
			redirect('/office/links', 'location');
		} else if($this->input->post('lurl')) {
			$this->messages->AddMessage('error', 'Please enter a name for your link.');
		}

		$data = array();

		/// Get custom page content
		$this->pages_model->SetPageCode('account_customlinks');

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/links/link_custom', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	//Update link
	function update($link_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/links');
		}

		$link_name = htmlentities($this->input->post('link_name'), ENT_NOQUOTES, 'UTF-8');
		$link_url = $this->input->post('link_url');
		$delete = ($this->input->post('name_delete_button') == 'Delete');

		$this->load->model('Links_Model');
		if ($delete) {
			$this->Links_Model->DeleteOfficialLink($link_id);
			$this->messages->AddMessage('success', 'Link deleted successfully');
		} else {
			$this->Links_Model->UpdateLink($link_id, $link_name, $link_url);
			$this->messages->AddMessage('success', 'Link updated successfully');
		}

		redirect('/office/links');
	}

	function upload() {
		$this->load->library('image_upload');
		$this->image_upload->automatic('office/links', array('link'), true, false);
	}
}

?>
