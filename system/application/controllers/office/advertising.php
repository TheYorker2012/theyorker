<?php

class Advertising extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
	}
	
	/// Default page.
	function index()
	{
		if (!CheckPermissions('office')) return;
		
		//load models
		$this->load->model('advert_model');
		
		if (isset($_POST['submit_add_advert'])) {
			if (trim($this->input->post('advert_name')) != '') {
				$this->advert_model->AddNewAdvert($this->input->post('advert_name'));
				$this->messages->AddMessage('success', 'The advert has been successfully added.');
			}
			else {
				$this->messages->AddMessage('error', 'You must enter a name for the advert.');
			}
		}
		/*
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();
		$this->advert_model->SelectLatestAdvert();*/
		
		//set up page code
		$this->pages_model->SetPageCode('advertising_list');
		
		$data = array(
			'adverts'=>$this->advert_model->GetAdverts()
		);

		// Set up the directory view
		$directory_view = $this->frames->view('office/advertising/list', $data);

		// Set up the public frame to use the directory view
		$this->main_frame->SetContent($directory_view);
		
		//load the page
		$this->main_frame->Load();
	}
	
	function view($advert_id = NULL)
	{
		if (!CheckPermissions('office')) return;
		
		//load models
		$this->load->model('advert_model');
		
		//set page
		$this->pages_model->SetPageCode('advertising_view');
		
		//get advert data
		$advert = $this->advert_model->AdvertExists($advert_id);
		
		if ($advert) {
		
			if(empty($advert['image_id'])) {
				$advert['has_image'] = false;
				$advert['image'] = 'No image available.';
			}
			else {
				$advert['has_image'] = true;
				$advert['image'] = '<img src="/image/advert/'.$advert['image_id'].'" alt="Image Preview" title="Image Preview" />';
			}
			
			$data = array(
				'advert'=>$advert
				);

			// Set up the directory view
			$directory_view = $this->frames->view('office/advertising/view', $data);

			// Set up the public frame to use the directory view
			$this->main_frame->SetTitleParameters(array(
				'name' => $advert['name']
				));
			$this->main_frame->SetContent($directory_view);
			
			//load the page
			$this->main_frame->Load();
		}
		else {
			$this->messages->AddMessage('error', 'The advert you specified doesn\'t exist');
			redirect('office/advertising');
		}
	}
	
	function edit($advert_id = NULL)
	{
		if (!CheckPermissions('office')) return;
		
		//load models
		$this->load->model('advert_model');
		
		//advert is being saved/updated
		if (isset($_POST['submit_save_advert'])) {
			$this->advert_model->SaveAdvert(
				$this->input->post('advert_id'),
				$this->input->post('advert_name'),
				$this->input->post('advert_url'),
				$this->input->post('advert_alt'),
				$this->input->post('advert_image'),
				$this->input->post('advert_max_views')
				);
			$this->messages->AddMessage('success', 'The changed to the advert have been saved.');
		}
		//delete the advert
		else if (isset($_POST['submit_delete_advert'])) {
			$this->advert_model->DeleteAdvert(
				$this->input->post('advert_id')
				);
			$this->messages->AddMessage('success', 'The advert has been deleted.');
			redirect('/office/advertising/');
		}
		//pull the advert
		else if (isset($_POST['submit_pull_advert'])) {
			$this->advert_model->PullAdvert(
				$this->input->post('advert_id')
				);
			$this->messages->AddMessage('success', 'The advert has been pulled from rotation on the public site.');
		}
		//make the advert live
		else if (isset($_POST['submit_make_advert_live'])) {
			$this->advert_model->MakeAdvertLive(
				$this->input->post('advert_id')
				);
			$this->messages->AddMessage('success', 'The advert has been added to the current rotation on the public site.');
		}	
		
		//set page
		$this->pages_model->SetPageCode('advertising_edit');
		
		//get advert data
		$advert = $this->advert_model->AdvertExists($advert_id);
		
		if ($advert) {
			
			$data = array(
				'advert'=>$advert
				);

			// Set up the directory view
			$directory_view = $this->frames->view('office/advertising/edit', $data);

			// Set up the public frame to use the directory view
			$this->main_frame->SetTitleParameters(array(
				'name' => $advert['name']
				));
			$this->main_frame->SetContent($directory_view);
			
			//load the page
			$this->main_frame->Load();
		}
		else {
			$this->messages->AddMessage('error', 'The advert you specified doesn\'t exist');
			redirect('office/advertising');
		}
	}
	
	function editimage($id)
	{
		//Get page properties information
		if (!CheckPermissions('editor')) return;
		$this->load->library('image_upload');
		$this->image_upload->automatic('/office/adverts/updateimage/'.$id, array('advert'), false, false);
	}
	
	//Store the id of from the image cropper to change an existing puffer image
	function updateimage($id)
	{
		//Get page properties information
		if (!CheckPermissions('editor')) return;
		//load models
		$this->load->model('advert_model');
		if(!empty($_SESSION['img'])){
			//There seems to be an image session, try to get id.
			foreach ($_SESSION['img'] as $Image) {
				$image_id='';
				if(empty($Image['list'])){
					//There is no id to use, upload must have failed
					//Clear image session so they can try again
					unset($_SESSION['img']);
					redirect('/office/adverts/editimage/'.$id);
				}else{
					//Success image id caught, so store
					$this->advert_model->UpdateAdvertImage($id,$Image['list']);
					//redirect back to the edit page where you started
					redirect('/office/adverts/view/'.$id);
				}
				//Image session no longer needed
				unset($_SESSION['img']);
			}
		}else{
			//session is empty, try getting image again
			redirect('/office/adverts/editimage/'.$id);
		}
	}
}

?>