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
}
?>
