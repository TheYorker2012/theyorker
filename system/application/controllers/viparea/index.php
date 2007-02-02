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
		
		// Load the main frame
		SetupMainFrame('organisation');
		
		$this->pages_model->SetPageCode('viparea_index');
	}
	
	function index()
	{
			$data = array(
					'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			);
		// Set up the main frame
		$this->main_frame->SetContentSimple('viparea/index', $data);
		
		// Load the main frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>