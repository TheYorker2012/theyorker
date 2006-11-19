<?php
/**
 * This controller is the default.
 * It currently displays only the prototype homepage, in the prototype student frame
 *
 * \author Nick Evans
 */
class Home extends Controller {

	/**
	* Displays prototype homepage, in the prototype student frame
	*/
	function index()
	{
		$data = array(
			'test' => 'I set this variable from the controller!',
			'content_view' => 'general/home'
		);
		$this->load->view('frames/student_frame',$data);
	}

}
?>