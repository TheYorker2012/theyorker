<?php

class About extends Controller {

	function index()
	{
		$data = array(
			'test' => 'I set this variable from the controller!',
			'content_view' => 'about/about'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
