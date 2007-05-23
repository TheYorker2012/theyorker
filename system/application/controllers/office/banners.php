<?php
/*
 * Controller for homepage banners office pages
 * \author Nick Evans nse500
 */

class Banners extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
		$this->load->model('Banner_Model');
	}

	/// Default page.
	function index()
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('banner_list');

		$data = array();

		$data['banners'] = $this->Banner_Model->GetBanners();

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/banners/banner_list', $data);

		$this->main_frame->Load();
	}

	//Edit banner page
	function edit($banner_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/banners');
		}

		$data['banner'] = $this->Banner_Model->GetBanner($banner_id);

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/banners/banner_edit', $data);

		$this->main_frame->Load();
	}

	//Update banner
	function update($banner_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/banners');
		}

		$banner_title = htmlentities($this->input->post('banner_title'), ENT_NOQUOTES, 'UTF-8');
		$banner_scheduled = htmlentities($this->input->post('banner_scheduled'), ENT_NOQUOTES, 'UTF-8');
		$banner_schedule_date = htmlentities($this->input->post('banner_schedule_date'), ENT_NOQUOTES, 'UTF-8');

		$delete = ($this->input->post('name_delete_button') == 'Delete');

		$banner_last_displayed_timestamp = ($banner_scheduled ? $banner_schedule_date : null);

		$this->load->model('Banner_Model');
		if ($delete) {
				$this->load->library('image');
				$this->image->delete('image', $banner_id);
				$this->messages->AddMessage('success', 'Banner deleted successfully');
		} else {
			if ($banner_title){
				$this->Banner_Model->UpdateBanner($banner_id, $banner_title, $banner_last_displayed_timestamp);
				$this->messages->AddMessage('success', 'Banner update successfully');
			} else {
				$this->messages->AddMessage('error', 'Banner update failed: no data was provided.');
			}
		}
		redirect('/office/banners');
	}

	function upload() {
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->library('image_upload');
		$this->image_upload->automatic('office/banners', array('banner'), true, false);
	}

}

?>
