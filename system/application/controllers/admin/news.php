<?php

/// News admin controller.
class News extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	function index()
	{
		$admin_view_name = 'news/admin_news';
		
		// Set up the public frame
		$this->frame_public->SetTitle('News Admin');
		$this->frame_public->SetContentSimple($admin_view_name);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	function request($Segment4)
	{
		$data = array('message' => '');
		
		switch ($Segment4) {
			case 'view':
				$admin_view_name = 'news/admin_request_view';
				break;
			default:
				$admin_view_name = 'news/admin_request_new';
				
				/// Check if the form has already been submitted
				if ($this->input->post('r_title') != false && $this->input->post('r_brief') != false)
				{
    				/// Apply XSS filtering to text inputs
    				$title = $this->input->post('r_title', true);
    				$brief = $this->input->post('r_brief', true);
    				
    				/// Fix illegal deadline dates
    				$day = $this->input->post('r_deadline_day');
    				$month = $this->input->post('r_deadline_month');
    				switch ($month)
    				{
        				case 2:
    				    if ($day > 29) $day = 29;
    				    break;
    				    
        				case 4:
        				case 6:
        				case 9:
        				case 11:
    				    if ($day > 30) $day = 30;
    				    break;
    				}
    				
    				/// Collect/format rest of data for model
    				$deadline = date('Y-m-d H:i:s', mktime(0, 0, 0, $month, $day));
    				$box = $this->input->post('r_box');
    				$reporter = $this->input->post('r_reporter');
    				
    				// if ($this->Admin_model->InsertRequest($title, $brief, $deadline, $box, $reporter)) $data['message'] = 'Your request has been added.';
        				
				}
				
				break;
		}
		
		// Set up the public frame
		$this->frame_public->SetTitle('News Admin');
		$this->frame_public->SetContentSimple($admin_view_name, $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}

?>