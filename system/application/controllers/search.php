<?php
//TODO Write comments
class Search extends Controller {

	function __construct()
	{
		parent::Controller();
		define("ORDER_RELEVANCE", 		1);
		define("ORDER_EARLY", 			2);
		define("ORDER_LATE", 			3);

		define("FILTER_ALL", 			16777216);
		define("FILTER_ALL_ARTICLES", 	511);
		define("FILTER_NEWS", 			1);
		define("FILTER_FEATURES", 		2);
		define("FILTER_LIFESTYLE", 		4);
		define("FILTER_FOOD",			8);
		define("FILTER_DRINK", 			16);
		define("FILTER_unused",			32);	//unused
		define("FILTER_ARTS",			64);
		define("FILTER_SPORTS",			128);
		define("FILTER_BLOGS",			256);
		define("FILTER_YORK",			512);
		define("FILTER_DIR",			1024);
		define("FILTER_EVENTS",			2048);
		$this->load->model('search');
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
	
//	function reply($fd) {
//		$objResponse = new xajaxResponse();
//		if ($this->input->post('search')) {
//			//TODO mysql queries though model
//			//TODO ajax submit
//		}
//	}
//	
////	function get() {
////		$this->load->helper('url');
////		$this->load->library('session');
////		$search_keywords = $this->input->post('search');
////		$search_category = $this->input->post('category');
////		$url = url_title($search_keywords).'/'.url_title($search_category);
////		$data = array('search_keywords' => $search_keywords,
////		              'search_category' => $search_category,
////		              'url' => $url);
////		$this->session->set_userdata($data);
////		redirect('/search/results/'. $url, 'location');
////	}
//	
//	function results() {
//		if (!CheckPermissions('public')) return;
//		
//		$this->load->library('session');
//		//TODO do mysql queries through model
//		//TODO display results
//		
//		// Set up the public frame
//		$this->main_frame->SetTitle('Search Results');
//		
//		$results[] = array('link' => 'TODO',
//		                   'title' => 'TODO',
//		                   'blurb' => 'TODO');
//		
//		
//		$data = array('search_form' => 'TODO Searchform again',
//		              'search_numbering' => '101',
//		              'search_all' => 		'1111111111111111',
//		              'search_news' => 		'0000000000000001',
//		              'search_features' => 	'0000000000000010',
//		              'search_lifestyle' =>	'0000000000000100',
//		              'search_food' =>	 	'0000000000001000',
//		              'search_drink' =>		'0000000000010000',
//		              'search_culture' =>	'0000000000100000',
//		              'search_york' => 		'0000000001000000',
//		              'search_results' => $results);
//		
//		$this->main_frame->SetContentSimple('search/results', $data);
//		
//		// Load the public frame view (which will load the content view)
//		$this->main_frame->Load();
//	}

}
?>
