<?php

/**
 * @brief Yorker directory.
 * @author Owen Jones (oj502@york.ac.uk)
 * 
 * The URI /admin maps to this controller this will be an admin menu when it is made
 *
 * Each admin page will be a function of this controler, so the pages will be /admin/pagename
 */
class Admin extends Controller {

	/**
	 * @brief Admin menu index page.
	 */
	function index()
	{
		$data = array(
			'content_view' => 'admin/admin'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	/**
	 * @admin page to edit the directory.
	 */
	function yorkerdirectory()
	{
		$data = array(
			'content_view' => 'directory/admin_directory'
		);
		$this->load->view('frames/student_frame',$data);
	}
	function yorkerdirectoryview()
	{
		$data = array(
			'content_view' => 'directory/admin_directory_view'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	/**
	* @admin pages for news system
	*/
	function news()
	{
		switch ($this->uri->segment(3)) {
			case "request":
				switch ($this->uri->segment(4)) {
					case "view":
						$data = array('content_view' => 'news/admin_request_view');
						break;
					default:
						$data = array('content_view' => 'news/admin_request_new');
						break;
				}
				break;
			default:
				$data = array('content_view' => 'news/admin_news');
				break;
		}
		$this->load->view('frames/student_frame',$data);
	}

	/**
	 * @admin page to add entry to faq.
	 */
	function addfaq()
	{
		$data = array(
			'content_view' => 'faq/addfaq'
		);
		$this->load->view('frames/student_frame',$data);
	}

	/**
	 * @admin page to add entry to howdoi
	 */
	function addhowdoi()
	{
		$data = array(
			'content_view' => 'faq/addhowdoi'
		);
		$this->load->view('frames/student_frame',$data);
	}

	/**
	 * @admin page to edit entry in faq
	 */
	function editfaq()
	{
		$data = array(
			'content_view' => 'faq/editfaq'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
