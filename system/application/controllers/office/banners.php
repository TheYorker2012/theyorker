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
		$this->load->model('Home_Model');
	}

	/// Default page.
	function index()
	{
		if (!CheckPermissions('editor')) return;

		$this->pages_model->SetPageCode('office_banners');
		$data = array();
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['banners'] = $this->Banner_Model->GetAllCurrentHomepageBanners();

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/banners/banner_overview', $data);

		$this->main_frame->Load();
	}
	/// Default page.
	function section($section_codename)
	{
		if (!CheckPermissions('editor')) return;

		$this->pages_model->SetPageCode('office_banners_section');
		$data = array();
		//Get information for the page
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['no_banner_wikitext'] = $this->pages_model->GetPropertyWikiText('no_banner');
		$section_name = $this->Banner_Model->GetHomepageSectionNameFromCodename($section_codename);
		$data['section_name'] = $section_name['name'];
		$this->main_frame->SetTitleParameters(array('section_name' => $data['section_name']));
		
		//Get the current banner, so we know the id and can highlight it in the list
		$current_banner = $this->Home_Model->GetBannerImageForHomepage($section_codename);
		if (!empty($current_banner)) {
			$data['current_banner_id'] = $current_banner['id'];
		}
		
		//Get the banners
		$data['scheduled_banners'] = $this->Banner_Model->GetScheduledBannersByHomepage('banner',$section_codename);
		$data['pooled_banners'] = $this->Banner_Model->GetPoolBannersByHomepage('banner',$section_codename);
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
		
		//Check for post (this includes updating and deleting the banner)
		$delete_post = !empty($_POST['name_delete_button']);
		$update_post = !empty($_POST['name_update_button']);
		if ( $delete_post || $update_post ) {
			//Pickup all the post
			$banner_title = $this->input->post('banner_title');
			$banner_link = $this->input->post('banner_link');
			$new_homepage_id = $this->input->post('banner_homepage');
			
			$banner_scheduled = $this->input->post('banner_scheduled');
			$banner_scheduled_radio = $this->input->post('banner_schedule_radio');
			$banner_schedule_days = $this->input->post('banner_schedule_days');
			$banner_schedule_date = $this->input->post('banner_schedule_date');
			
			$old_banner = $this->Banner_Model->GetBanner($banner_id);

			$delete = ($this->input->post('name_delete_button') == 'Delete');
			$failed = false;
			
			//Before all the checks, see if the user wants to delete the banner
			if ($delete) {
					$this->load->library('image');
					$this->Banner_Model->DeleteAllLinksToImage($banner_id);
					$this->image->delete('image', $banner_id);
					$this->messages->AddMessage('success', 'Banner deleted successfully');
					redirect('/office/banners');
			}
			
			if ($banner_scheduled) {
				//User wants to schedule the banner
				//See what method the user is defining the date by
				if ($banner_scheduled_radio=='days') {
					//Use $banner_schedule_days, no value implys user has failed to enter anything
					if (!empty($banner_schedule_days)) {
						//Work out date for addition of X days
						$update_timestamp = date('Y-m-d H:i:s', time() + ( 86400 * $banner_schedule_days));//Number of seconds in a day (86400)
					} else {
						//user wanted a schedule by days but didnt select a number of days.
						$failed = true;
						$this->messages->AddMessage('error', 'Select the number of days for the schedule');
					}
				}
				if ($banner_scheduled_radio=='date') {
					//Use $banner_schedule_date, value of 'dd/mm/yy' implies user has failed to enter anything
					if ($banner_schedule_date == 'dd/mm/yy') {
						//user didnt change the default message but asked for a schedule by date.
						$failed = true;
						$this->messages->AddMessage('error', 'Please enter a date for the schedule');
					} else {
						$year = substr($banner_schedule_date, 6, 2);
						$month = substr($banner_schedule_date, 3, 2);
						$day = substr($banner_schedule_date, 0, 2);
						if (checkdate((int)$month,(int)$day,(int)$year)) {
							$update_timestamp = '20'.$year.'-'.$month.'-'.$day.' 00:00:00';
						} else {
							$failed = true;
							$this->messages->AddMessage('error', 'The scheduled date is invalid, please use the format dd/mm/yy.');
						}
					}
				}
			} else {
				//User either wants the banner in the pool or the timestamp left as it is.
				if (!empty($old_banner['banner_last_displayed_timestamp'])) {
					//Banner had a timestamp before, but now is being removed. The user wants it forced back into the pool.
					//Creating a timestamp for yesterday, this will force it into the pool.
					$update_timestamp = date('Y-m-d H:i:s', time() - 86400);//Number of seconds in a day (86400)
				} else {
					//The banner didnt have a schedule before, and is not giving it one now. Set the update to null and it will be ignored.
					$update_timestamp=null;
				}
			}
			if (empty($banner_title)){
				$failed = true;
				$this->messages->AddMessage('error', 'You need to include a title for the banner.');
			}
			
			if (!$failed) {
				//Update homepage
				if($new_homepage_id != $old_banner['homepage_id']){
					//check there is an old homepage and remove it
					if (!empty($old_banner['homepage_id'])) {
						$this->Banner_Model->DeleteImageHomepageLink($banner_id, $old_banner['homepage_id']);
					}
					//check the banner is not to just be unassigned
					if ($new_homepage_id!=-1) {
						$this->Banner_Model->LinkImageToHomepage($banner_id, $new_homepage_id, $banner_link);
					}
				}
				//Update banner
				if (!empty($update_timestamp)) {
					//As you are scheduling to a date, remove any other banners on that date
					$this->Banner_Model->PoolAllBannersWithThisDate($update_timestamp, $new_homepage_id, $banner_id);
				}
				$this->Banner_Model->UpdateBanner($banner_id, $banner_title, $update_timestamp);
				
				$this->messages->AddMessage('success', 'Banner updated successfully');
				//Success, go to the page for the banners homepage
				if ($new_homepage_id==-1) {
					redirect('/office/banners/');
				} else {
					$new_homepage = $this->Banner_Model->GetBannersHomepage($banner_id);
					redirect('/office/banners/section/'.$new_homepage['homepage_codename']);
				}
			}
			//Failure, continue to load the page, but with the partially completed data put back in
		}
		
		$data = array();
		$this->pages_model->SetPageCode('office_banners_edit');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		
		if (!empty($failed) && $failed) {
			$server_data = $this->Banner_Model->GetBanner($banner_id);
			$data['banner'] = 
				array(
					'banner_id' => $banner_id,
					'banner_type' => $server_data['banner_type'],
					'banner_title' => $banner_title,
					'link' => $banner_link,
					'banner_last_displayed_timestamp' => $server_data['banner_last_displayed_timestamp']
				);
			$new_homepage_names = $this->Banner_Model->GetHomepageSectionNameFromId($new_homepage_id);
			$data['current_homepage'] = 
				array(
					'homepage_id' => $new_homepage_id,
					'homepage_name' => $new_homepage_names['name'],
					'homepage_codename' => $new_homepage_names['codename'],
					);
		} else {
			$data['current_homepage'] = $this->Banner_Model->GetBannersHomepage($banner_id);
			$data['banner'] = $this->Banner_Model->GetBanner($banner_id);
		}
		
		//Get list of sections that could have a homepage
		$this->load->model('Article_model');
		$data['homepages'] = $this->Article_model->getMainArticleTypes(true);

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/banners/banner_edit', $data);

		$this->main_frame->Load();
	}

	function upload() {
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->library('image_upload');
		$this->image_upload->automatic('/office/banners', array('banner'), true, false);
	}

}

?>
