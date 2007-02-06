<?php
//TODO Write comments
class Search extends Controller {

	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		// Set up the public frame
		$this->main_frame->SetTitle('Search');
		
		$data = array ('search_form' => 'TODO Searchform again');
		
		$this->main_frame->SetContentSimple('search/search', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
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
		if (!CheckPermissions('public')) return;
		
		$this->load->library('session');
		//TODO do mysql queries through model
		//TODO display results
		
		// Set up the public frame
		$this->main_frame->SetTitle('Search Results');
		
		$results[] = array('link' => 'TODO',
		                   'title' => 'TODO',
		                   'blurb' => 'TODO');
		
		$data = array('search_form' => 'TODO Searchform again',
		              'search_numbering' => '101',
		              'search_all' => '101',
		              'search_news' => '101',
		              'search_reviews' => '101',
		              'search_features' => '101',
		              'search_events' => '101',
		              'search_how' => '101',
		              'search_york' => '101',
		              'search_results' => $results);
		
		$this->main_frame->SetContentSimple('search/results', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

}
?>
