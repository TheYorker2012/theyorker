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

}
?>
