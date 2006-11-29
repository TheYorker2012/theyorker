<?php

class Faq extends Controller {

	function index()
	{
		$data = array(
			'content_view' => 'faq/faq'
		);
		$this->load->view('frames/student_frame',$data);
	}

	function howdoi()
	{
		$data = array(
			'content_view' => 'faq/howdoi'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
