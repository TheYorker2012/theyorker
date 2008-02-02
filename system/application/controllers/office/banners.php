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
		$this->load->model('Article_model');
	}

	/// Default page.
	function index()
	{
		if (!CheckPermissions('editor')) return;

		$this->pages_model->SetPageCode('office_banners');
		$data = array();
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['banners'] = $this->Banner_Model->GetBannersByHomepage();
		$data['unused_banners'] = $this->Banner_Model->GetBannersWithNoHompage();

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/banners/banner_list', $data);

		$this->main_frame->Load();
	}
	//Edit banner page
	function edit($banner_id)
	{
		//has user got access to office
		if (!CheckPermissions('editor')) return;
		$this->pages_model->SetPageCode('office_banners');
		$data = array();
		
		//make sure a banner entry exists, so the image shows and gets related to the link
		if (!$this->Banner_Model->HasHomepageEntry($banner_id)) {
			$this->Banner_Model->LinkImageToHomepage($banner_id);
		}
		
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');	
		$data['banner'] = $this->Banner_Model->GetBanner($banner_id);
		$data['current_homepage_id'] = $this->Banner_Model->GetBannersHomepageId($banner_id);
		$data['homepages'] = $this->Article_model->getMainArticleTypes(true);

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/banners/banner_edit', $data);

		$this->main_frame->Load();
	}

	//Update banner
	function update($banner_id)
	{
		//has user got access to office
		if (!CheckPermissions('editor')) return;

		$banner_title = xml_escape($this->input->post('banner_title'), ENT_NOQUOTES, 'UTF-8');
		$banner_link = xml_escape($this->input->post('banner_link'), ENT_NOQUOTES, 'UTF-8');
		$banner_scheduled = xml_escape($this->input->post('banner_scheduled'), ENT_NOQUOTES, 'UTF-8');
		$banner_schedule_date = xml_escape($this->input->post('banner_schedule_date'), ENT_NOQUOTES, 'UTF-8');
		$new_homepage_id = xml_escape($this->input->post('banner_homepage'), ENT_NOQUOTES, 'UTF-8');
		$old_homepage_id = $this->Banner_Model->GetBannersHomepageId($banner_id);

		$delete = ($this->input->post('name_delete_button') == 'Delete');

		$banner_last_displayed_timestamp = ($banner_scheduled ? $banner_schedule_date : null);

		$this->load->model('Banner_Model');
		if ($delete) {
				$this->load->library('image');
				$this->Banner_Model->DeleteAllLinksToImage($banner_id);
				$this->image->delete('image', $banner_id);
				$this->messages->AddMessage('success', 'Banner deleted successfully');
		} else {
			//update
			if (!empty($banner_title)){
				//Update homepages Currently only supporting one homepage per image
				if($new_homepage_id != $old_homepage_id){
					//remove old homepage
					$this->Banner_Model->DeleteImageHomepageLink($banner_id, $old_homepage_id);
					//create new one
					$this->Banner_Model->LinkImageToHomepage($banner_id, $new_homepage_id, $banner_link);
				}
				//Update banner
				$this->Banner_Model->UpdateBanner($banner_id, $banner_title, $banner_last_displayed_timestamp, $banner_link);
				$this->messages->AddMessage('success', 'Banner updated successfully');
			} else {
				$this->messages->AddMessage('error', 'Banner update failed, you need to include a title.');
			}
		}
		redirect('/office/banners');
	}

	function upload() {
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->library('image_upload');
		$this->image_upload->automatic('/office/banners', array('banner'), true, false);
	}

}

?>
