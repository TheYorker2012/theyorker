<?php

/// Main viparea controller.
class Members extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		$this->load->model('directory_model');
		$this->load->library('organisations');
		$this->load->helper('wikilink');
	}
	
	function view($organisation)
	{
		if (!CheckPermissions('vip')) return;
		
		$this->pages_model->SetPageCode('viparea_members');
		
		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
		);
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/members', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>