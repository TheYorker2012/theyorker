<?php

class Podcasts extends Controller
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

		$this->pages_model->SetPageCode('office_podcasts_list');
		$this->load->model('podcasts_model');

		$data = array(
			'podcasts' => $this->podcasts_model->GetPodcasts(),
			);

		$this->main_frame->SetContentSimple('office/podcasts/list', $data);

		$this->main_frame->Load();
	}
	
	function edit($id)
	{
		if (!CheckPermissions('office')) return;
		
		$this->messages->AddDumpMessage('POST', $_POST);

		$this->pages_model->SetPageCode('office_podcasts_edit');
		$this->load->model('podcasts_model');
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
}

?>
