<?php

class Howdoi extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
    function index()
    {
		$this->load->model('howdoi_model','howdoi');
		$this->pages_model->SetPageCode('howdoi_list');

		$data['sections'] = array (
					'sidebar_ask'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_ask_title',TRUE),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_ask_text',TRUE)),
					'section_howdoi'=>array('title'=>$this->pages_model->GetPropertyText('section_howdoi_title',FALSE),'text'=>$this->pages_model->GetPropertyWikitext('section_howdoi_text',FALSE))
					);

		// Set up the public frame
		$this->frame_public->SetContentSimple('howdoi/howdoi', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }

    function view()
    {
		$this->load->model('howdoi_model','howdoi');
		$this->pages_model->SetPageCode('howdoi_view');

		$data['sections'] = array (
					'sidebar_ask'=>array('title'=>$this->pages_model->GetPropertyText('sidebar_ask_title',TRUE),'text'=>$this->pages_model->GetPropertyWikitext('sidebar_ask_text',TRUE))
					);

		// Set up the public frame
		//$this->frame_public->SetTitle('How do I?');
		$this->frame_public->SetContentSimple('howdoi/view', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }

}
?>
