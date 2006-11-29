<?php

class Search extends Controller {

	function index()
	{
		$data = array(
			'content_view' => 'search/search'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
