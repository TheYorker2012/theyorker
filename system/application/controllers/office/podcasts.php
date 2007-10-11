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

		$data = array();

		$this->main_frame->SetContentSimple('office/podcasts/list', $data);

		$this->main_frame->Load();
	}
}

?>
