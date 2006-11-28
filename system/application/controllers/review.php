<?php
/**
 * This controller is the default.
 * It currently displays only the prototype review page, in the prototype student frame
 *
 * \author Richard Rout
 */
class Review extends Controller {

	/**
	* Displays prototype homepage, in the prototype student frame
	*/
	function index()
	{
		$data = array(
			'content_view' => 'reviews/index',
		);
		$this->load->view('frames/student_frame',$data);
	}
	function food()
	{
		$data = array(
			'content_view' => 'reviews/food',
		);
		$this->load->view('frames/student_frame',$data);
	}
	function drink()
	{
		$data = array(
			'content_view' => 'reviews/drink',
		);
		$this->load->view('frames/student_frame',$data);
	}
	function culture()
	{
		$data = array(
			'content_view' => 'reviews/culture',
		);
		$this->load->view('frames/student_frame',$data);
	}
	function foodreview()
	{
		$data = array(
			'content_view' => 'reviews/foodreview',
		);
		$this->load->view('frames/student_frame',$data);
	}
	function culturereview()
	{
		$data = array(
			'content_view' => 'reviews/culturereview',
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>