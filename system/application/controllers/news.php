<?php

class News extends Controller {

	function index()
	{
		$data = array(
			'content_view' => 'news/news.php'
		);
		$this->load->view('frames/student_frame',$data);
	}

	function national()
	{
		$data = array(
			'content_view' => 'news/national.php'
		);
		$this->load->view('frames/student_frame',$data);
	}

	function features()
	{
		$data = array(
			'content_view' => 'news/news.php'
		);
		$this->load->view('frames/student_frame',$data);
	}

	function lifestyle()
	{
		$data = array(
			'content_view' => 'news/lifestyle.php'
		);
		$this->load->view('frames/student_frame',$data);
	}

	function article()
	{
		$data = array(
			'content_view' => 'news/article.php'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	function archive()
	{
		$data = array(
			  'content_view' => 'news/archive.php'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
