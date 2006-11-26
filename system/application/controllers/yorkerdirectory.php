<?php

class Yorkerdirectory extends Controller {

	function index()
	{
		$data = array(
			'content_view' => 'directory/directory'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	function view()
	{
		$data = array(
			'content_view' => 'directory/directory_view'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
