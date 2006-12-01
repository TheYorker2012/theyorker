<?php

/**
 * @brief Yorker directory.
 * @author Owen Jones (oj502@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Yorkerdirectory extends Controller {

	/**
	 * @brief Main remapping function.
	 *
	 * @param $organisation Second URL segment.
	 *	(this will be 'index' if none is specified).
	 *
	 * This allows the second segment of the URL to be the organisation.
	 * Additional segments can be obtained using $this->uri->segment(n).
	 */
	function _remap($organisation)
	{
		if ($organisation === 'index') {
			$this->_index();
		} else {
			$this->_view($organisation);
		}
	}
	
	/**
	 * @brief Directory index page.
	 */
	function _index()
	{
		$data = array(
			'content_view' => 'directory/directory'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	/**
	 * @brief Directory organisation page.
	 */
	function _view($organisation)
	{
		$data = array(
			'content_view' => 'directory/directory_view'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
