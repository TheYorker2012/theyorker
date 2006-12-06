<?php
//TODO Write comments
class Search extends Controller {

	function index()
	{
		$data = array(
			'title'        => 'Search',
			'content_view' => 'search/minibox'
		);
		$this->load->view('frames/student_frame',$data);
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
		)
		$data = array(
			'title'        => 'Search Results'
			'content_view' => 'search/results',
			'subdata'      => $subdata
		);
		$this->load->view('frames/student_frame',$data);
	}


      // temp function that links to the layout of the search page
      function layout() {
		$data = array(
			'title'        => 'Search Results',
			'content_view' => 'search/search'
		);
		$this->load->view('frames/student_frame',$data);
      }

      function layout2() {
		$data = array(
			'content_view' => 'search/search2'
		);
		$this->load->view('frames/student_frame',$data);
      }
}
?>
