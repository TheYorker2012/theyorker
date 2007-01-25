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
		
		$data = array ('searchform' => 'TODO Searchform again');
		
		$this->frame_public->SetContentSimple('search/search', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	function reply($fd) {
		$objResponse = new xajaxResponse();
		if ($this->input->post('search')) {
			//TODO mysql queries though model
			//TODO ajax submit
		}
	}
	
//	function get() {
//		$this->load->helper('url');
//		$this->load->library('session');
//		$search_keywords = $this->input->post('search');
//		$search_category = $this->input->post('category');
//		$url = url_title($search_keywords).'/'.url_title($search_category);
//		$data = array('search_keywords' => $search_keywords,
//		              'search_category' => $search_category,
//		              'url' => $url);
//		$this->session->set_userdata($data);
//		redirect('/search/results/'. $url, 'location');
//	}
	
	function results() {
		$this->load->library('session');
		//TODO do mysql queries through model
		//TODO display results
		
		// Set up the public frame
		$this->frame_public->SetTitle('Search Results');
		
		$results[] = array('link' => 'TODO',
		                   'title' => 'TODO',
		                   'blurb' => 'TODO');
		
		$data = array('searchform' => 'TODO Searchform again',
		              'search_numbering' => '101',
		              'search_all' => '101',
		              'search_news' => '101',
		              'search_reviews' => '101',
		              'search_features' => '101',
		              'search_events' => '101',
		              'search_how' => '101',
		              'search_york' => '101',
		              'search_results' => $results);
		
		$this->frame_public->SetContentSimple('search/results', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

}
?>
