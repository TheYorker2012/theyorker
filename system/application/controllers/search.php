<?php
//TODO Write comments
class Search extends Controller {

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	function index()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Search');
		$this->frame_public->SetContentSimple('search/minibox');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	function submit($fd) {
		$objResponse = new xajaxResponse();
		if ($this->input->post('search')) {
			//TODO mysql queries
			//TODO ajax submit
		}
	}
	
	function get() {
		$this->load->helper('url');
		$this->load->library('session');
		$search_keywords = $this->input->post('search');
		$search_category = $this->input->post('category');
		$url = url_title($search_keywords).'/'.url_title($search_category);
		$data = array('search_keywords' => $search_keywords,
		              'search_category' => $search_category,
		              'url' => $url);
		$this->session->set_userdata($data);
		redirect('/search/results/'. $url, 'location');
	}
	
	function results() {
		$this->load->library('session');
		//TODO do mysql query on details stored in session
		//TODO display results
		$subdata = array(
			'result' => $result
		);
		
		// Set up the public frame
		$this->frame_public->SetTitle('Search Results');
		$this->frame_public->SetContentSimple('search/results', $subdata);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}


      // temp function that links to the layout of the search page
      function layout() {
		// Set up the public frame
		$this->frame_public->SetTitle('Search Results');
		$this->frame_public->SetContentSimple('search/search');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
      }

      function layout2() {
		// Set up the public frame
		$this->frame_public->SetTitle('Search Results');
		$this->frame_public->SetContentSimple('search/search2');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
      }
}
?>
