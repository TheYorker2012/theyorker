<?php

class Search extends Controller {

	function index()
	{
		$data = array(
			'content_view' => 'search/minibox'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	function submit() {
		//ajax calls to this
	}
	
	function results() {
		$data = array(
			'content_view' => 'search/results'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
