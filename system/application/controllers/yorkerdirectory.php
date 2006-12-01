<?php

/**
 * @brief Yorker directory.
 * @author Owen Jones (oj502@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 * 
 * The URI /directory maps to this controller (see config/routes.php).
 *
 * Any 2nd URI segment is sent to Yorkerdirectory::view (see config/routes.php).
 */
class Yorkerdirectory extends Controller {
	
	/**
	 * @brief Directory index page.
	 */
	function index()
	{
		$data = array(
			'content_view' => 'directory/directory'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	/**
	 * @brief Directory organisation page.
	 */
	function view($organisation)
	{
		$data = array(
			'content_view' => 'directory/directory_view'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
