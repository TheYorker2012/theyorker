<?php

class Podcasts extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
		$this->load->model('podcasts_model');
	}

	/// Default page.
	function index()
	{
		if (!CheckPermissions('office')) return;
		
		$this->load->library('xajax');
		$this->xajax->registerFunction(array("list_add", &$this, "_list"));
		$this->xajax->registerFunction(array("toggle_islive", &$this, "_toggle_islive"));
		$this->xajax->processRequests();

		$this->pages_model->SetPageCode('office_podcasts_list');

		$data = array(
			'podcasts' => $this->podcasts_model->GetPodcasts(),
			'page_info_title' =>
				$this->pages_model->GetPropertyText('section_podcasts_list_page_info_title'),
			'page_info_text' =>
				$this->pages_model->GetPropertyWikiText('section_podcasts_list_page_info_text'),
			'actions_title' =>
				$this->pages_model->GetPropertyText('section_podcasts_list_actions_title'),
			);

		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));

		$this->main_frame->SetContentSimple('office/podcasts/list', $data);

		$this->main_frame->Load();
	}
	
	function del($id)
	{
		if (!CheckPermissions('office')) return;
		if($this->podcasts_model->Del($id))
			{	
				$this->messages->AddMessage('success', 'Podcast Deleted');
			}else{
				$this->messages->AddMessage('error', 'Podcast Not Deleted');
			}
		redirect('/office/podcasts/');
	}
	
	function edit($id,$new=FALSE)
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_podcasts_edit');
//		$this->load->helper('file');

		$this->load->library('xajax');
		$this->xajax->registerFunction(array("list_files", &$this, "_list"));
		$this->xajax->registerFunction(array('change_file', &$this, '_change_file'));
		$this->xajax->processRequests();


		if (
			isset($_POST['a_name']) &&
			isset($_POST['a_description']))
		{
			if($this->podcasts_model->Edit_Podcast_Update(
					$id,
					$_POST['a_name'],
					$_POST['a_description'],
					isset($_POST['is_live'])
				))
			{
				$this->main_frame->AddMessage('success','Changes saved!',FALSE);
			} else {
				$this->main_frame->AddMessage('error','Update failed!',FALSE);
			}
		}

		$details = $this->podcasts_model->GetPodcastDetails($id);
		if (!isset($details[0])) {
			$this->messages->AddMessage('error', 'No podcast exists with that id.');
			redirect('/office/podcasts/');
		}
		else
		{
			$details = $details[0];
		}
		if($new){$details['is_live']=1;}
		$details['live_can_change']=$this->podcasts_model->CanLiveChange($id);
		$data = array(
			'podcast' => $details,
			'target' => '/office/podcasts/edit/'.$id,
//			'files' => get_filenames('static/podcasts/'),
//			'files2' => get_filenames('javascript/'),
			);
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));

		$this->main_frame->SetContentSimple('office/podcasts/edit', $data);

		$this->main_frame->Load();
	}

	function _list()
	{
		$this->load->model('static_model');
		$list = $this->static_model->GetDirectoryListing(
				$this->config->item('static_local_path').'/podcasts',
				'',
				array('mp3'));
		$db_list = $this->podcasts_model->Get_Fnames();
		$arguments = '';
		foreach ($list as $fname)
		{
			if(!in_array($fname,$db_list))
			{
				$arguments = $arguments.',"'.str_replace(array('/','\\'),'',$fname).'"';
			}
		}
		$objResponse = new xajaxResponse();
		$objResponse->addScript('list_response('.substr($arguments,1).');');
		return $objResponse;
	}
	
	function _change_file($id, $new_file_name)
	{
		$this->load->model('static_model');
		$file_size=$this->static_model->GetFileSize(
					$this->config->item('static_local_path').
					'/podcasts/'.
					$new_file_name);
		$this->podcasts_model->Change_File(
				$id, 
				$new_file_name,
				$file_size);
		$this->static_model->MoveFile(
			$this->config->item('static_local_path').'/podcasts/'.$new_file_name,
			$this->config->item('static_local_path').'/media/podcasts/');
		
		$objResponse = new xajaxResponse();
		$objResponse->addAssign(
				'file_address',
				'value',
				$this->config->item('static_web_address').
									'/media/podcasts/'.
									xml_escape($new_file_name));
		$objResponse->addAssign(
				'file_size',
				'value',
				round($file_size/1048576,1).' MBytes');
		return $objResponse;
	}
	
	function _toggle_islive($id)
	{
		$is_live=$this->podcasts_model->Toggle_Live($id);
		$objResponse = new xajaxResponse();
		$objResponse->addAssign('islive_'.$id,'innerHTML',($is_live==1?'Yes':'No'));
		return $objResponse;
	}
	
	function add_entry()
	{
		if(!isset($_POST['add_entry_file']))
		{
			redirect('office/podcasts');
		}
		if (!CheckPermissions('office')) return;
		
		$this->load->model('static_model');
		$podcast_id = $this->podcasts_model->Add_Entry(
			$_POST['add_entry_file'],
			$this->static_model->GetFileSize(
					$this->config->item('static_local_path').
					'/podcasts/'.
					$_POST['add_entry_file']));
		$this->static_model->MoveFile(
			$this->config->item('static_local_path').'/podcasts/'.$_POST['add_entry_file'],
			$this->config->item('static_local_path').'/media/podcasts/');
		if ($podcast_id==0)
		{
				$this->main_frame->AddMessage('error','Podcast Add Failed.');				
				redirect('office/podcasts');
		}								
		$this->main_frame->AddMessage('success','Podcast entry added successfully. Please complete the rest of the required information below.');
		$this->edit($podcast_id,TRUE);
	}

}

?>
