<?php

class Contact extends Controller {

	function index()
	{
		$data = array(
			'test' => 'I set this variable from the controller!',
			'content_view' => 'about/contact'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
