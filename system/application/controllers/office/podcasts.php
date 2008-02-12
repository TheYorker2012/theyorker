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
		$this->xajax->registerFunction(array("list_ftp", &$this, "_list_ftp"));
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
	
	function edit($id)
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_podcasts_edit');
		$this->load->helper('file');

		$details = $this->podcasts_model->GetPodcastDetails($id);
		if (!isset($details[0])) {
			$this->messages->AddMessage('error', 'No podcast exists with that id.');
			redirect('/office/podcasts/');
		}
		else
		{
			$details = $details[0];
		}

		$data = array(
			'podcast' => $details,
			'target' => '/office/podcasts/edit/'.$id,
			'files' => get_filenames('static/podcasts/'),
			'files2' => get_filenames('javascript/'),
			);

		$this->main_frame->SetContentSimple('office/podcasts/edit', $data);

		$this->main_frame->Load();
	}

	function _list_ftp()
	{
		$this->load->model('static_ftp_model');
		$conn_id = $this->static_ftp_model->Connect();
		$list = $this->static_ftp_model->GetList($conn_id,'podcasts');
		$this->static_ftp_model->Close($conn_id);
		$db_list = $this->podcasts_model->Get_Fnames();
		$arguments = '';
		foreach ($list as $fname)
		{
			if(
				!stristr($fname,".htm") and
				($fname[0] !='.') and 
				!in_array($fname,$db_list) and
				!stristr($fname,".php") and
				!stristr($fname,".xml"))
			{
				
				$arguments = $arguments.',"'.$fname.'"';
			}
		}
		$objResponse = new xajaxResponse();
		$objResponse->addScript('list_response('.substr($arguments,1).');');
		return $objResponse;
	}

	function add_entry()
	{
		if(!isset($_POST['add_entry_file']))
		{
			redirect('office/podcasts');
		}
		if (!CheckPermissions('office')) return;
		
		$this->load->model('static_ftp_model');
		$conn_id = $this->static_ftp_model->Connect();
		$game_id = $this->podcasts_model->Add_Entry(
			$_POST['add_entry_file'],
			ftp_size($conn_id,'podcasts/'.$_POST['add_entry_file']));
		$this->static_ftp_model->Close($conn_id);
		if ($game_id ==0)
		{
				$this->main_frame->AddMessage('error','Podcast Add Failed.');				
				redirect('office/podcasts');
		}								
		$this->main_frame->AddMessage('success','Podcast entry added successfully. Please complete the rest of the required information below.');
		redirect('office/podcasts/edit/'.$game_id);	
	}

}

?>
