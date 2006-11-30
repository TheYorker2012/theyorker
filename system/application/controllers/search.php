<?php
//TODO Write comments
class Search extends Controller {

	function index()
	{
		$data = array(
			'content_view' => 'search/minibox'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	function submit() {
		//TODO ajax submit
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
		$data = array(
			'content_view' => 'search/results'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
