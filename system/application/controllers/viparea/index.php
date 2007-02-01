<?php

/// Main viparea controller.
class Index extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
		$this->pages_model->SetPageCode('viparea_index');
	}
	
	function index()
	{
			$data = array(
					'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			);
		// Set up the public frame
		$this->frame_public->SetTitle('Vip Area');
		$this->frame_public->SetContentSimple('viparea/index', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>